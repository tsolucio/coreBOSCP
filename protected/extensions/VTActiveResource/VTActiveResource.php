<?php
/*************************************************************************************************
 * coreBOSCP - web based coreBOS Customer Portal
 * Copyright 2011-2014 JPL TSolucio, S.L.   --   This file is a part of coreBOSCP.
 * Licensed under the GNU General Public License (the "License") either
 * version 3 of the License, or (at your option) any later version; you may not use this
 * file except in compliance with the License. You can redistribute it and/or modify it
 * under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
 * granted by the License. coreBOSCP distributed by JPL TSolucio S.L. is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
 * applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
 * either express or implied. See the License for the specific language governing
 * permissions and limitations under the License. You may obtain a copy of the License
 * at <http://www.gnu.org/licenses/>
 *************************************************************************************************
 *  Author       : JPL TSolucio, S. L.
 *************************************************************************************************/
/**
 * VTActiveResource is ment to be used similar to an ActiveRecord model in Yii. In difference to ActiveRecord
 * the persistent storage of the model isn't a database but a coreBOS RESTful service. The code is influenced by
 * the EActiveResource extension for YII, version 0.1 developed by Johannes "Haensel" Bauer (thank you)
 * found at @link http://www.yiiframework.com/extension/activeresource
 */
abstract class VTActiveResource extends CModel
{

    //const IS_PROPERTY='IS_PROPERTY';
    const IS_ONE='IS_ONE';
    const IS_MANY='IS_MANY';
    
    private static $_models=array();
    
    private $_md;               // The metadata object for this resource (e.g.: field names, default values)
    private $_criteria;
    private $_new;
    private $_attributes=array();
    private $_fieldinfo=array();
    private $_related=array();
    private $_embedded=array();
    private $_lasterror;
    public static $db;
    
    // coreboscp properties
    private $module;
    private $clientvtiger;
    private $count;
    public $defaultCacheTimeout = 3000;
    public $doDereference=true;

    /**
     * Constructor.
     * @param string $scenario scenario name. See {@link CModel::scenario} for more details about this parameter.
     */
    public function __construct($scenario='insert',$moduleParam='')
    {
    	if($scenario===null) // internally used by populateRecord() and model()
    		return;

    	$this->setScenario($scenario);
    	$this->setIsNewResource(true);
    	//$this->_criteria=new CDbCriteria();
    	if (empty($moduleParam))
    	$module=Yii::app()->getRequest()->getParam('module');
    	else
    	$module=$moduleParam;
    	$this->setModule($module);
    	if (!$this->validModule())
    		Yii::app()->endJson(Yii::t('core','invalidEntity'));
    	$this->init($module);
    	$this->attachBehaviors($this->behaviors());
    	$this->afterConstruct();
    }

    /**
     * Initializes this model.
     * This method is invoked when an instance is newly created and has
     * its {@link scenario} set.
     * You may override this method to provide code that is needed to initialize the model (e.g. setting
     * initial property values.)
     */
    public function init()
    {
    }

    /**
     * Sets the parameters about query caching.
     * It changes the query caching parameter of the REST instance.
     * @param integer $duration the number of seconds that query results may remain valid in cache.
     * If this is 0, the caching will be disabled.
     * @param CCacheDependency $dependency the dependency that will be used when saving the query results into cache.
     * @param integer $queryCount number of SQL queries that need to be cached after calling this method. Defaults to 1,
     * meaning that the next SQL query will be cached.
     * @return CActiveRecord the active record instance itself.
     * @since 1.1.7
     */
    public function cache($duration, $dependency=null, $queryCount=1)
    {
    	// FIXME ****
    	// Set cache duration and query count on rest connection
    	return $this;
    }

    /**
     * PHP sleep magic method.
     */
    public function __sleep()
    {
    	return array_keys((array)$this);
    }
    
    /**
     * PHP getter magic method.
     * This method is overridden so that node/relationship properties can be accessed.
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute
     */
    public function __get($name)
    {
    	if(isset($this->_attributes[$name]))
    		return $this->_attributes[$name];
    	else if(isset($this->getMetaData()->properties[$name]))
    		return null;
    	else if(isset($this->_related[$name]))
    		return $this->_related[$name];
    	else if(isset($this->getMetaData()->relations[$name]))
    		return $this->getRelated($name);
    	else if(!$this->getMetaData()->schema)
    		return null;
    	else
    		return parent::__get($name);
    }
    
    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name,$value)
    {
    	if($this->setAttribute($name,$value)===false)
    	{
    		if(isset($this->getMetaData()->relations[$name]))
    			$this->_related[$name]=$value;
    		else
    			parent::__set($name,$value);
    	}
    }
    
    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking
     * if the named attribute is null or not.
     * @param string $name the property name or the event name
     * @return boolean whether the property value is null
     */
    public function __isset($name)
    {
    	if(isset($this->_attributes[$name]))
    		return true;
    	else if(isset($this->getMetaData()->properties[$name]))
    		return false;
    	else if(isset($this->_related[$name]))
    		return true;
    	else if(isset($this->getMetaData()->relations[$name]))
    		return $this->getRelated($name)!==null;
    	else
    		return parent::__isset($name);
    }
    
    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     */
    public function __unset($name)
    {
    	if(isset($this->getMetaData()->properties[$name]))
    		unset($this->_attributes[$name]);
    	else if(isset($this->getMetaData()->relations[$name]))
    		unset($this->_related[$name]);
    	else
    		parent::__unset($name);
    }

    /**
     * Calls the named method which is not a class method.
     * Do not call this method. This is a PHP magic method that we override
     * @param string $name the method name
     * @param array $parameters method parameters
     * @return mixed the method return value
     */
    public function __call($name,$parameters)
    {
    	if(isset($this->getMetaData()->relations[$name]))
    	{
    		if(empty($parameters))
    			return $this->getRelated($name,false);
    		else
    			return $this->getRelated($name,false,$parameters[0]);
    	}
    	
    	$scopes=$this->scopes();
    	if(isset($scopes[$name]))
    	{
    		$this->getDbCriteria()->mergeWith($scopes[$name]);
    		return $this;
    	}

    	return parent::__call($name,$parameters);
    }

    /**
     * FIXME ****
     * Returns the related record(s).
     * This method will return the related record(s) of the current record.
     * If the relation is HAS_ONE or BELONGS_TO, it will return a single object
     * or null if the object does not exist.
     * If the relation is HAS_MANY or MANY_MANY, it will return an array of objects
     * or an empty array.
     * @param string $name the relation name (see {@link relations})
     * @param boolean $refresh whether to reload the related objects from database. Defaults to false.
     * @param array $params additional parameters that customize the query conditions as specified in the relation declaration.
     * @return mixed the related object(s).
     * @throws CDbException if the relation is not specified in {@link relations}.
     */
    public function getRelated($name,$refresh=false,$params=array())
    {
    	if(!$refresh && $params===array() && (isset($this->_related[$name]) || array_key_exists($name,$this->_related)))
    		return $this->_related[$name];
    
    	$md=$this->getMetaData();
    	if(!isset($md->relations[$name]))
    		throw new CDbException(Yii::t('yii','{class} does not have relation "{name}".',
    				array('{class}'=>get_class($this), '{name}'=>$name)));
    
    	Yii::trace('lazy loading '.get_class($this).'.'.$name,'system.db.ar.CActiveRecord');
    	$relation=$md->relations[$name];
    	if($this->getIsNewRecord() && !$refresh && ($relation instanceof CHasOneRelation || $relation instanceof CHasManyRelation))
    		return $relation instanceof CHasOneRelation ? null : array();
    
    	if($params!==array()) // dynamic query
    	{
    		$exists=isset($this->_related[$name]) || array_key_exists($name,$this->_related);
    		if($exists)
    			$save=$this->_related[$name];
    		$r=array($name=>$params);
    	}
    	else
    		$r=$name;
    	unset($this->_related[$name]);
    
    	$finder=new CActiveFinder($this,$r);
    	$finder->lazyFind($this);
    
    	if(!isset($this->_related[$name]))
    	{
    		if($relation instanceof CHasManyRelation)
    			$this->_related[$name]=array();
    		else if($relation instanceof CStatRelation)
    			$this->_related[$name]=$relation->defaultValue;
    		else
    			$this->_related[$name]=null;
    	}
    
    	if($params!==array())
    	{
    		$results=$this->_related[$name];
    		if($exists)
    			$this->_related[$name]=$save;
    		else
    			unset($this->_related[$name]);
    		return $results;
    	}
    	else
    		return $this->_related[$name];
    }

    /**
     * Returns a value indicating whether the named related object(s) has been loaded.
     * @param string $name the relation name
     * @return boolean a value indicating whether the named related object(s) has been loaded.
     */
    public function hasRelated($name)
    {
    	return isset($this->_related[$name]) || array_key_exists($name,$this->_related);
    }

    /**
     * Returns the query criteria associated with this model.
     * @param boolean $createIfNull whether to create a criteria instance if it does not exist. Defaults to true.
     * @return CDbCriteria the query criteria that is associated with this model.
     */
    public function getDbCriteria($createIfNull=true)
    {
    	if($this->_criteria===null)
    	{
    		if(($c=$this->defaultScope())!==array() || $createIfNull)
    			$this->_criteria=new CDbCriteria($c);
    	}
    	return $this->_criteria;
    }

    public function getCriteria()
    {
    	return $this->getDbCriteria(false);
    }

    /**
     * Sets the query criteria for the current model.
     * @param CDbCriteria $criteria the query criteria
     * @since 1.1.3
     */
    public function setDbCriteria($criteria)
    {
    	$this->_criteria=$criteria;
    }
    
    public function setCriteria($criteria)
    {
    	$this->setDbCriteria($criteria);
    }

    /**
     * Returns the default named scope that should be implicitly applied to all queries for this model.
     * Note, default scope only applies to SELECT queries. It is ignored for INSERT, UPDATE and DELETE queries.
     * The default implementation simply returns an empty array. You may override this method
     * if the model needs to be queried with some default criteria (e.g. only active records should be returned).
     * @return array the query criteria. This will be used as the parameter to the constructor
     * of {@link CDbCriteria}.
     * See also: Vtentity::getRelationInformation()
     */
    public function defaultScope()
    {
    	if (Yii::app()->vtyiicpngScope=='vtigerCRM') {
    		return array();
    	} else {
    		switch ($this->getModule()) {
    			case 'Contacts':
    				if (Yii::app()->user->accountId=='1x1') {  // empty account
    					$condition = array('condition'=>"id='".Yii::app()->user->contactId."'");
    				} else {
    					$condition = array('condition'=>"account_id='".Yii::app()->user->accountId."'");
    				}
    				break;
    			case 'Accounts':
    				$condition = array('condition'=>"id='".Yii::app()->user->accountId."'");
    				break;
    			case 'Quotes':
    				$condition = array('condition'=>"account_id='".Yii::app()->user->accountId."' or contact_id='".Yii::app()->user->contactId."'");
    				break;
    			case 'SalesOrder':
    				$condition = array('condition'=>"account_id='".Yii::app()->user->accountId."' or contact_id='".Yii::app()->user->contactId."'");
    				break;
    			case 'ServiceContracts':
    				$condition = array('condition'=>"sc_related_to='".Yii::app()->user->accountId."' or sc_related_to='".Yii::app()->user->contactId."'");
    				break;
                case 'Invoice':
                    $condition = array('condition'=>"account_id='".Yii::app()->user->accountId."' or contact_id='".Yii::app()->user->contactId."'");
                    break;
                case 'HelpDesk':
                    //Get contacts in account
                    if(Yii::app()->company_tickets === true){
                        $clientvtiger=$this->getClientVtiger();
                        $contacts_res = $clientvtiger->doQuery("Select id from Contacts where account_id='".Yii::app()->user->accountId."'");
                        if(!empty($contacts_res)){
                            $contacts_arr = array();
                            foreach($contacts_res as $contact){
                                $contacts_arr[] = $contact['id'];
                            }
                            $contacts = "'".implode("','",$contacts_arr)."'";
                        }
                        $condition = array('condition'=>"parent_id IN (".$contacts.",'".Yii::app()->user->accountId."')");
                    }else{
                        $condition = array('condition'=>"parent_id IN ('".Yii::app()->user->accountId."','".Yii::app()->user->contactId."')");
                    }
                    break;
                case 'Assets':
                    $condition = array('condition'=>"account='".Yii::app()->user->accountId."'");
                    break;
                case 'Project':
                    $condition = array('condition'=>"linktoaccountscontacts='".Yii::app()->user->accountId."' or linktoaccountscontacts='".Yii::app()->user->contactId."'");
                    break;
                case 'Products':
                    $condition = array('condition'=>"related.Contacts='".Yii::app()->user->contactId."'");
                    break;
                case 'Services':
                    $condition = array('condition'=>"related.Contacts='".Yii::app()->user->contactId."'");
                    break;
                case 'Faq':
                    $condition = array('condition'=>"faqstatus='Published'");
                    break;
                case 'CobroPago':
                    $condition = array('condition'=>"parent_id='".Yii::app()->user->accountId."' or parent_id='".Yii::app()->user->contactId."'");
                    break;
                case 'Documents':
                    // the way the related enhancement is done I know I can filter on crm2, but that is REALLY dependent and basically wrong
                    $condition = array('condition'=>"related.Contacts='".Yii::app()->user->contactId."' or crm2.crmid = '".Yii::app()->user->accountId."'");
                    break;
    			case 'Timecontrol':
    				$condition = array('condition'=>"relatedto IN ('".Yii::app()->user->accountId."','".Yii::app()->user->contactId."')");
    				break;
                default:
                    $condition = array();
            }
            return $condition;
        }
    }
    
    /**
     * Resets all scopes and criterias applied including default scope.
     *
     * @return CActiveRecord
     * @since 1.1.2
     */
    public function resetScope()
    {
    	$this->_criteria=new CDbCriteria();
    	return $this;
    }

    /**
     * Returns the static model of the specified EAR class.
     * The model returned is a static instance of the EAR class.
     * It is provided for invoking class-level methods (something similar to static class methods.)
     *
     * EVERY derived ActiveResource class must override this method as follows,
     * <pre>
     * public static function model($className=__CLASS__)
     * {
     *     return parent::model($className);
     * }
     * </pre>
     *
     * @param string $className active resource class name.
     * @return EAR active resource model instance.
     */
    public static function model($className=__CLASS__)
    {
    	if(isset(self::$_models[$className]))
    		return self::$_models[$className];
    	else
    	{
    		$module = Yii::app()->getRequest()->getParam('module');
    		$model=self::$_models[$className]=new $className($module);
    		$model->init($module);
    		$model->_md=new VTActiveResourceMetaData($model);
    		$model->setAttributes((empty($_REQUEST[$className]) ? null : $_REQUEST[$className]));
    		$model->attachBehaviors($model->behaviors());
    		return $model;
    	}
    }
    
    /**
     * Returns the meta-data for this ActiveResource
     * @return VTActiveResourceMetaData the meta for this ActiveResource class.
     */
    public function getMetaData()
    {
    	if($this->_md!==null)
    		return $this->_md;
    	else
    		return $this->_md=self::model(get_class($this))->_md;
    }

    /**
     * Refreshes the meta data for this AR class.
     * By calling this method, this AR class will regenerate the meta data needed.
     * This is useful if the table schema has been changed and you want to use the latest
     * available table schema. Make sure you have called {@link CDbSchema::refresh}
     * before you call this method. Otherwise, old table schema data will still be used.
     */
    public function refreshMetaData()
    {
    	$this->refresh();
    }

    /**
     * Repopulates this resource with the latest data.
     * @return boolean whether the row still exists in the database. If true, the latest data will be populated to this active resource.
     */
    public function refresh()
    {
    	Yii::trace(get_class($this).'.refresh()','ext.VTActiveResource');
    	if(!$this->getIsNewRecord() && ($resource=$this->findById($this->getPrimaryKey()))!==null)
    	{
    		$this->_attributes=array();
    		$this->_related=array();
    		foreach($this->getMetaData()->properties as $name=>$value)
    		{
    			if(property_exists($this,$name))
    				$this->$name=$resource->$name;
    			else
    				$this->_attributes[$name]=$resource->$name;
    		}
    		return true;
    	}
    	else
    		return false;
    }

    /**
     * Returns the name of the associated REST entity
     * @return string the REST entity
     */
    public function tableName()
    {
    	return $this->getModule();
    }

    /**
     * Returns the primary key of the associated database table.
     * This method is meant to be overridden in case when the table is not defined with a primary key
     * (for some legency database). If the table is already defined with a primary key,
     * you do not need to override this method. The default implementation simply returns null,
     * meaning using the primary key defined in the database.
     * @return mixed the primary key of the associated database table.
     * If the key is a single column, it should return the column name;
     * If the key is a composite one consisting of several columns, it should
     * return the array of the key column names.
     */
    public function primaryKey()
    {
    	return 'id';  // vtiger always uses this for all entities
    }

    /**
     * Returns the primary key value.
     * @return mixed the primary key value.
     * If primary key is not defined, null will be returned.
     */
    public function getPrimaryKey()
    {
		return $this->getId();
    }

    /**
     * Returns the id of this ActiveResource model. You need an id in order to send update requests.
     * @return string
     */
    public function getId()
    {
    	return $this->id;
    }

    /**
     * This method should be overridden to declare related objects.
     *
     * There are four types of relations that may exist between two active record objects:
     * <ul>
     * <li>BELONGS_TO: e.g. a member belongs to a team;</li>
     * <li>HAS_ONE: e.g. a member has at most one profile;</li>
     * <li>HAS_MANY: e.g. a team has many members;</li>
     * <li>MANY_MANY: e.g. a member has many skills and a skill belongs to a member.</li>
     * </ul>
     *
     * Besides the above relation types, a special relation called STAT is also supported
     * that can be used to perform statistical query (or aggregational query).
     * It retrieves the aggregational information about the related objects, such as the number
     * of comments for each post, the average rating for each product, etc.
     *
     * Each kind of related objects is defined in this method as an array with the following elements:
     * <pre>
     * 'varName'=>array('relationType', 'className', 'foreign_key', ...additional options)
     * </pre>
     * where 'varName' refers to the name of the variable/property that the related object(s) can
     * be accessed through; 'relationType' refers to the type of the relation, which can be one of the
     * following four constants: self::BELONGS_TO, self::HAS_ONE, self::HAS_MANY and self::MANY_MANY;
     * 'className' refers to the name of the active record class that the related object(s) is of;
     * and 'foreign_key' states the foreign key that relates the two kinds of active record.
     * Note, for composite foreign keys, they can be either listed together, separated by commas or specified as an array
     * in format of array('key1','key2'). In case you need to specify custom PK->FK association you can define it as
     * array('fk'=>'pk'). For composite keys it will be array('fk_c1'=>'pk_с1','fk_c2'=>'pk_c2').
     * and for foreign keys used in MANY_MANY relation, the joining table must be declared as well
     * (e.g. 'join_table(fk1, fk2)').
     *
     * Additional options may be specified as name-value pairs in the rest array elements:
     * <ul>
     * <li>'select': string|array, a list of columns to be selected. Defaults to '*', meaning all columns.
     *   Column names should be disambiguated if they appear in an expression (e.g. COUNT(relationName.name) AS name_count).</li>
     * <li>'condition': string, the WHERE clause. Defaults to empty. Note, column references need to
     *   be disambiguated with prefix 'relationName.' (e.g. relationName.age&gt;20)</li>
     * <li>'order': string, the ORDER BY clause. Defaults to empty. Note, column references need to
     *   be disambiguated with prefix 'relationName.' (e.g. relationName.age DESC)</li>
     * <li>'with': string|array, a list of child related objects that should be loaded together with this object.
     *   Note, this is only honored by lazy loading, not eager loading.</li>
     * <li>'joinType': type of join. Defaults to 'LEFT OUTER JOIN'.</li>
     * <li>'alias': the alias for the table associated with this relationship.
     *   It defaults to null,
     *   meaning the table alias is the same as the relation name.</li>
     * <li>'params': the parameters to be bound to the generated SQL statement.
     *   This should be given as an array of name-value pairs.</li>
     * <li>'on': the ON clause. The condition specified here will be appended
     *   to the joining condition using the AND operator.</li>
     * <li>'index': the name of the column whose values should be used as keys
     *   of the array that stores related objects. This option is only available to
     *   HAS_MANY and MANY_MANY relations.</li>
     * <li>'scopes': scopes to apply. In case of a single scope can be used like 'scopes'=>'scopeName',
     *   in case of multiple scopes can be used like 'scopes'=>array('scopeName1','scopeName2').
     *   This option has been available since version 1.1.9.</li>
     * </ul>
     *
     * The following options are available for certain relations when lazy loading:
     * <ul>
     * <li>'group': string, the GROUP BY clause. Defaults to empty. Note, column references need to
     *   be disambiguated with prefix 'relationName.' (e.g. relationName.age). This option only applies to HAS_MANY and MANY_MANY relations.</li>
     * <li>'having': string, the HAVING clause. Defaults to empty. Note, column references need to
     *   be disambiguated with prefix 'relationName.' (e.g. relationName.age). This option only applies to HAS_MANY and MANY_MANY relations.</li>
     * <li>'limit': limit of the rows to be selected. This option does not apply to BELONGS_TO relation.</li>
     * <li>'offset': offset of the rows to be selected. This option does not apply to BELONGS_TO relation.</li>
     * <li>'through': name of the model's relation that will be used as a bridge when getting related data. Can be set only for HAS_ONE and HAS_MANY. This option has been available since version 1.1.7.</li>
     * </ul>
     *
     * Below is an example declaring related objects for 'Post' active record class:
     * <pre>
     * return array(
     *     'author'=>array(self::BELONGS_TO, 'User', 'author_id'),
     *     'comments'=>array(self::HAS_MANY, 'Comment', 'post_id', 'with'=>'author', 'order'=>'create_time DESC'),
     *     'tags'=>array(self::MANY_MANY, 'Tag', 'post_tag(post_id, tag_id)', 'order'=>'name'),
     * );
     * </pre>
     *
     * @return array list of related object declarations. Defaults to empty array.
     */
    public function relations()
    {
    	return array();
    }

    /**
     * Returns the declaration of named scopes.
     * A named scope represents a query criteria that can be chained together with
     * other named scopes and applied to a query. This method should be overridden
     * by child classes to declare named scopes for the particular AR classes.
     * For example, the following code declares two named scopes: 'recently' and
     * 'published'.
     * <pre>
     * return array(
     *     'published'=>array(
     *           'condition'=>'status=1',
     *     ),
     *     'recently'=>array(
     *           'order'=>'create_time DESC',
     *           'limit'=>5,
     *     ),
     * );
     * </pre>
     * If the above scopes are declared in a 'Post' model, we can perform the following
     * queries:
     * <pre>
     * $posts=Post::model()->published()->findAll();
     * $posts=Post::model()->published()->recently()->findAll();
     * $posts=Post::model()->published()->with('comments')->findAll();
     * </pre>
     * Note that the last query is a relational query.
     *
     * @return array the scope definition. The array keys are scope names; the array
     * values are the corresponding scope definitions. Each scope definition is represented
     * as an array whose keys must be properties of {@link CDbCriteria}.
     */
    public function scopes()
    {
    	return array();
    }

    /**
     * Returns the list of all attribute names of the model.
     * @return array list of attribute names.
     */
    public function attributeNames()
    {
    	// this is what yii CAR does  FIXME??
    	//return array_keys($this->getMetaData()->columns);
    	$attributes=array(); 
        $all_attributes=is_array($this->_attributes)?$this->_attributes:$this->getFieldsInfo();
    	foreach($all_attributes as $attribute)
    	{
    		if (!is_array($attribute) || count($attribute)<1 || !isset($attribute['name'])) continue;         
    		array_push($attributes,$attribute['name']);
    	}       
    	return $attributes;
    }

    /**
     * Returns the text label for the specified attribute.
     * This method overrides the parent implementation by supporting
     * returning the label defined in relational object.
     * In particular, if the attribute name is in the form of "post.author.name",
     * then this method will derive the label from the "author" relation's "name" attribute.
     * @param string $attribute the attribute name
     * @return string the attribute label
     * @see generateAttributeLabel
     * @since 1.1.4
     */
    public function getAttributeLabel($attribute)
    {
    	$labels=$this->attributeLabels();
    	if(isset($labels[$attribute]))
    		return $labels[$attribute];
    	else
    		return $this->generateAttributeLabel($attribute);
    }

    /**
     * Returns the REST connection used by active record.
     * @return CDbConnection the database connection used by active record.
     */
    public function getDbConnection()
    {
    	if(self::$db!==null)
    		return self::$db;
    	else
    	{
   			return getClientVtiger();
    	}
    }

    public function setClientVtiger($clientvtiger)
    {
        if($this->clientvtiger==null || $clientvtiger==null)
        {
            $clientvtiger=$this->loginREST();
            $this->clientvtiger=$clientvtiger;
        }
        self::$db=$this->clientvtiger;
    }

     public function getClientVtiger()
    {
         if($this->clientvtiger==null)
         	 $this->setClientVtiger(null);
         return $this->clientvtiger;
    }
  
    /**
     * Use this function to define the communication between this class and the REST service.
     * <p>
     * <b>site</b>: Defines the baseUri of the REST service. Example.: http://iamaRESTapi/apiversion
     * <p>
     * <b>loginuser</b>: coreBOS user to access the main application with
     * <p>
     * <b>accesskey</b>: coreBOS user's access key to use with webservice interface
     * <p>
     * <b>contenttype</b>: Defines the content type that is send via HTTP header and is used to determine how the data has to be converted from php. If you use 'application/json' then data will automatically be converted to JSON.
     * <p>
     * <b>accepttype</b>: Defines the accept type send via HTTP header. It is also used to convert the response back to a php readable format like an array of attributes. Define application/json to automatically convert JSON responses to PHP arrays.
     * <p>
     * <b>fileExtension</b>: This is used to append something like '.json' to every GET request. This can be useful if the service doesn't respect headers but uses a formatextension to know what type of response you are looking for. Always remember to use a '.' in front of the extension!
     * <p>
     * <b>container</b>: Sometimes all responses include additional meta information about a request or the number of hits etc and the actual modelobject is contained within a container like 'result'. If this is the case you can specify this container here to allow ActiveResource to only load attributes specified within this container (e.g.: "results").
     * <p>
     * <b>embedded</b>: Some services respond with an complex object containing other resources (like Twitter does by also returning user objects when requesting statuses). If you know that a certain field (like 'user') contains another object that you defined already defined as a subclass of VTActiveResource than use the following syntax:
     * <ul>
     * <li>array('user'=>array(self::IS_ONE,'MyUserModelClassName')), --> if user is always a single user object
     * <li>array('user'=>array(self::IS_MANY,'MyUserModelClassName')) --> if user contains an ARRAY of users
     * </ul>
     * This will cause the class to automatically load the User object/objects. It enables you to use magic getters like: $tweet->user->name where tweet is your main model object and user is a ActiveResource contained within a tweet response.
     * @return array The configuration of this classed as used by VTActiveResourceMetaData.
     */
    public function rest()
    {
        return array(
			'site'=>Yii::app()->site,
			'loginuser'=>Yii::app()->loginuser,
			'accesskey'=>Yii::app()->accesskey,
			'contenttype'=>'application/json',
			'accepttype'=>'application/json',
			'fileextension'=>'.json',
        );
    }

    static public function loginREST()
    {
        $url = Yii::app()->site;
        $client = new WSClient($url);
        $login = $client->doLogin(Yii::app()->loginuser, Yii::app()->accesskey);
        if (!$login) {
        	throw new CHttpException(550,Yii::t('core', 'customerPortalNotConfigured'));
        }
        return $client;
    }

    /**
     * Returns the named relation declared for this AR class.
     * @param string $name the relation name
     * @return CActiveRelation the named relation declared for this AR class. Null if the relation does not exist.
     */
    public function getActiveRelation($name)
    {
    	return isset($this->getMetaData()->relations[$name]) ? $this->getMetaData()->relations[$name] : null;
    }
    
    /**
     * Returns the metadata of the table that this AR belongs to
     * @return CDbTableSchema the metadata of the table that this AR belongs to
     */
    public function getTableSchema()
    {
    	return $this->module;
    }
    
    /**
     * Returns the command builder used by this AR.
     * @return CDbCommandBuilder the command builder used by this AR
     */
    public function getCommandBuilder()
    {
    	return $this;  // use $this->createVtigerSQLCommand()
    }

    /**
     * Checks whether this ActiveResource has the named attribute
     * @param string $name attribute name
     * @return boolean whether this ActiveResource has the named attribute.
     */
    public function hasAttribute($name)
    {
    	return isset($this->getMetaData()->properties[$name]);
    }

    /**
     * Returns the named attribute value.
     * @param string $name the attribute name
     * @return mixed the attribute value. Null if the attribute is not set or does not exist.
     * @see hasAttribute
     */
    public function getAttribute($name)
    {
    	if (strpos($name, ',')>0) {
    		$showValue='';
    		$lookup_fields=explode(',', $name);
    		foreach ($lookup_fields as $field) {
    			$showValue.=getAttribute($field).' ';
    		}
    		$showValue=trim($showValue);
    	} else {
    		$showValue='';
	    	if(property_exists($this,$name))
	    		$showValue=$this->$name;
	    	else if(isset($this->_attributes[$name]))
	    		$showValue=$this->_attributes[$name];
    	}
    	return $showValue;
    }

    /**
     * Sets the named attribute value.
     * @param string $name the attribute name
     * @param mixed $value the attribute value.
     * @return void
     */
    public function setAttribute($name,$value)
    {
    	if(property_exists($this,$name))
    		$this->$name=$value;
    	$this->_attributes[$name]=$value;
    }

    /**
     * Do not call this method. This method is used internally by {@link CActiveFinder} to populate
     * related objects. This method adds a related object to this record.
     * @param string $name attribute name
     * @param mixed $record the related record
     * @param mixed $index the index value in the related object collection.
     * If true, it means using zero-based integer index.
     * If false, it means a HAS_ONE or BELONGS_TO object and no index is needed.
     */
    public function addRelatedRecord($name,$record,$index)
    {
    	if($index!==false)
    	{
    		if(!isset($this->_related[$name]))
    			$this->_related[$name]=array();
    		if($record instanceof CActiveRecord)
    		{
    			if($index===true)
    				$this->_related[$name][]=$record;
    			else
    				$this->_related[$name][$index]=$record;
    		}
    	}
    	else if(!isset($this->_related[$name]))
    		$this->_related[$name]=$record;
    }

    /**
     * Generates a user friendly attribute label.
     * This is done by replacing underscores or dashes with blanks and
     * changing the first letter of each word to upper case.
     * For example, 'department_name' or 'DepartmentName' becomes 'Department Name'.
     * @param string $name the column name
     * @return string the attribute label
     */
    public function generateAttributeLabel($name)
    {
    	return ucwords(trim(strtolower(str_replace(array('-','_','.'),' ',preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name)))));
    }

    /**
     * Returns all attribute values.
     * @param mixed $names names of attributes whose value needs to be returned.
     * If this is true (default), then all attribute values will be returned
     * If this is null, all attributes will be returned.
     * @return array attribute values indexed by attribute names.
     */
    public function getAttributes($names=true)
    {
    	$attributes=$this->_attributes;

    	foreach($this->getMetaData()->properties as $name=>$type)
    	{
    		if(property_exists($this,$name))
    			$attributes[$name]=$this->$name;
    		else if($names===true && !isset($attributes[$name]))
    			$attributes[$name]=null;
    	}
    	if(is_array($names))
    	{
    		$attrs=array();
    		foreach($names as $name)
    		{
    			if(property_exists($this,$name))
    				$attrs[$name]=$this->$name;
    			else
    				$attrs[$name]=isset($attributes[$name])?$attributes[$name]:null;
    		}
    		return $attrs;
    	}
    	else
    		return $attributes;
    
    }

    /**
     * Saves the current resource.
     *
     * A post request to the resource will be send if its {@link isNewresource}
     * property is true (usually the case when the resource is created using the 'new'
     * operator). Otherwise, it will be used to update the resource
     * (usually the case if the resource is obtained using one of those 'find' methods.)
     *
     * Validation will be performed before saving the resource. If the validation fails,
     * the resource will not be saved. You can call {@link getErrors()} to retrieve the
     * validation errors.
     *
     * If the resource is saved via insertion, its {@link isNewRecord} property will be
     * set false, and its {@link scenario} property will be set to be 'update'.
     *
     * @param boolean $runValidation whether to perform validation before saving the resource.
     * If the validation fails, the resource will not be saved to database.
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from the service will be saved.
     * @return boolean whether the saving succeeds
     */
    public function save($runValidation=true,$attributes=null)
    {
		// before saving we have to make sure that all date fields are in Portal User format
		$this->formatDateFieldsForSaving($attributes);
		if(!$runValidation || $this->validate($attributes))
			return $this->getIsNewResource() ? $this->create($attributes) : $this->update($attributes);
		else
			return false;
    }

	/**
	 * Format a date attribute field to the Poral User's date format (which is the one used for saving)
	 * This field can come in two formats:
	 *   - ISO (yyyy-mm-dd) which means that it has been retrieved directly from coreBOS REST with no manipulation
	 *   - the desired format, which means that it is comming from screen
	 * coreBOS REST always sends the fields in ISO, but expects to recieve them in the user's format
	 * Parameters:
	 *   datevalue is the date value to format
	 *   dateformat is the format in which the value is given
	 */
	public function formatDateFieldForSaving($datevalue,$dateformat) {
		if (empty($datevalue)) return $datevalue;
		switch ($dateformat) {
			case 'mm-dd-yyyy':
				list($m,$d,$y) = preg_split("/\/|-|\./", $datevalue);
			break;
			case 'dd-mm-yyyy':
				list($d,$m,$y) = preg_split("/\/|-|\./", $datevalue);
			break;
			default: /// 'yyyy-mm-dd'
				list($y,$m,$d) = preg_split("/\/|-|\./", $datevalue);
			break;
		}
		switch (Yii::app()->user->userDateFormat) {  // save format
			case 'mm-dd-yyyy':
				$retval = "$m-$d-$y";
				break;
			case 'dd-mm-yyyy':
				$retval = "$d-$m-$y";
				break;
			default: /// 'yyyy-mm-dd'
				$retval = "$y-$m-$d";
				break;
		}
		return $retval;
	}

	/**
	 * Format all date fields in attributes and given array to Portal User's format for saving
	 */
	public function formatDateFieldsForSaving($attributes) {
		$fldinfo = $this->getFieldsInfo();
		if (is_array($fldinfo)) {
			foreach ($fldinfo as $finfo) {
				if ($finfo['type']['name'] == 'date') {
					$dvalue = $this->getAttribute($finfo['name']);
					// we have to guess the date format:
					$first4 = substr($dvalue,0,4);
					if (strpos($first4,'-') or strpos($first4,'/') or strpos($first4,'.')) {
						// it isn't ISO so it has to be the Portal User's format or incorrect
						$dformat = $finfo['type']['format'];
					} else {
						// it is ISO
						$dformat = 'yyyy-mm-dd';
					}
					$fmtdate = $this->formatDateFieldForSaving($dvalue,$dformat);
					$this->setAttribute($finfo['name'],$fmtdate);
					$attributes[$finfo['name']] = $fmtdate;
				}
			}
		}
	}

    /**
     * Returns if the current resource is new.
     * @return boolean whether the resource is new and should be inserted when calling {@link save}.
     * This property is automatically set in constructor and {@link populateRecord}.
     * Defaults to false, but it will be set to true if the instance is created using
     * the new operator.
     */
    public function getIsNewRecord()
    {
    	return $this->getIsNewResource();
    }

    /**
     * Returns if the current resource is new.
     * @return boolean whether the resource is new and should be created when calling {@link save}.
     * This property is automatically set in constructor and {@link populateRecord}.
     * Defaults to false, but it will be set to true if the instance is created using
     * the new operator.
     */
    public function getIsNewResource()
    {
    	return $this->_new;
    }

    /**
     * Sets if the resource is new.
     * @param boolean $value whether the resource is new and should be inserted when calling {@link save}.
     * @see getIsNewRecord
     */
    public function setIsNewRecord($value)
    {
    	$this->setIsNewResource($value);
    }

    /**
     * Sets if the resource is new.
     * @param boolean $value whether the resource is new and should be created when calling {@link save}.
     * @see getIsNewResource
     */
    public function setIsNewResource($value)
    {
    	$this->_new=$value;
    }

    /**
     * This event is raised before the resource is saved.
     * By setting {@link CModelEvent::isValid} to be false, the normal {@link save()} process will be stopped.
     * @param CModelEvent $event the event parameter
     * @since 1.0.2
     */
    public function onBeforeSave($event)
    {
    	$this->raiseEvent('onBeforeSave',$event);
    }
    
    /**
     * This event is raised after the resource is saved.
     * @param CEvent $event the event parameter
     * @since 1.0.2
     */
    public function onAfterSave($event)
    {
    	$this->raiseEvent('onAfterSave',$event);
    }
    
    /**
     * This event is raised before the resource is deleted.
     * By setting {@link CModelEvent::isValid} to be false, the normal {@link delete()} process will be stopped.
     * @param CModelEvent $event the event parameter
     * @since 1.0.2
     */
    public function onBeforeDelete($event)
    {
    	$this->raiseEvent('onBeforeDelete',$event);
    }
    
    /**
     * This event is raised after the resource is deleted.
     * @param CEvent $event the event parameter
     * @since 1.0.2
     */
    public function onAfterDelete($event)
    {
    	$this->raiseEvent('onAfterDelete',$event);
    }
    
    /**
     * This event is raised before an AR finder performs a find call.
     * In this event, the {@link CModelEvent::criteria} property contains the query criteria
     * passed as parameters to those find methods. If you want to access
     * the query criteria specified in scopes, please use {@link getDbCriteria()}.
     * You can modify either criteria to customize them based on needs.
     * @param CModelEvent $event the event parameter
     * @see beforeFind
     * @since 1.0.9
     */
    public function onBeforeFind($event)
    {
    	$this->raiseEvent('onBeforeFind',$event);
    }
    
    /**
     * This event is raised after the resource is instantiated by a find method.
     * @param CEvent $event the event parameter
     * @since 1.0.2
     */
    public function onAfterFind($event)
    {
    	$this->raiseEvent('onAfterFind',$event);
    }
    
    /**
     * This method is invoked before saving a resource (after validation, if any).
     * The default implementation raises the {@link onBeforeSave} event.
     * You may override this method to do any preparation work for resource saving.
     * Use {@link isNewRecord} to determine whether the saving is
     * for inserting or updating resource.
     * Make sure you call the parent implementation so that the event is raised properly.
     * @return boolean whether the saving should be executed. Defaults to true.
     */
    protected function beforeSave()
    {
    	if($this->hasEventHandler('onBeforeSave'))
    	{
    		$event=new CModelEvent($this);
    		$this->onBeforeSave($event);
    		return $event->isValid;
    	}
    	else
    		return true;
    }
    
    /**
     * This method is invoked after saving a resource successfully.
     * The default implementation raises the {@link onAfterSave} event.
     * You may override this method to do postprocessing after resource saving.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterSave()
    {
    	if($this->hasEventHandler('onAfterSave'))
    		$this->onAfterSave(new CEvent($this));
    }
    
    /**
     * This method is invoked before deleting a resource.
     * The default implementation raises the {@link onBeforeDelete} event.
     * You may override this method to do any preparation work for resource deletion.
     * Make sure you call the parent implementation so that the event is raised properly.
     * @return boolean whether the resource should be deleted. Defaults to true.
     */
    protected function beforeDelete()
    {
    	if($this->hasEventHandler('onBeforeDelete'))
    	{
    		$event=new CModelEvent($this);
    		$this->onBeforeDelete($event);
    		return $event->isValid;
    	}
    	else
    		return true;
    }
    
    /**
     * This method is invoked after deleting a resource.
     * The default implementation raises the {@link onAfterDelete} event.
     * You may override this method to do postprocessing after the resource is deleted.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterDelete()
    {
    	if($this->hasEventHandler('onAfterDelete'))
    		$this->onAfterDelete(new CEvent($this));
    }
    
    /**
     * This method is invoked before an AR finder executes a find call.
     * The find calls include {@link find}, {@link findAll}, {@link findByPk},
     * {@link findAllByPk}, {@link findByAttributes} and {@link findAllByAttributes}.
     * The default implementation raises the {@link onBeforeFind} event.
     * If you override this method, make sure you call the parent implementation
     * so that the event is raised properly.
     *
     * Starting from version 1.1.5, this method may be called with a hidden {@link CDbCriteria}
     * parameter which represents the current query criteria as passed to a find method of AR.
     *
     * @since 1.0.9
     */
    protected function beforeFind()
    {
    	if($this->hasEventHandler('onBeforeFind'))
    	{
    		$event=new CModelEvent($this);
    		// for backward compatibility
    		$event->criteria=func_num_args()>0 ? func_get_arg(0) : null;
    		$this->onBeforeFind($event);
    	}
    }
    
    /**
     * This method is invoked after each resource is instantiated by a find method.
     * The default implementation raises the {@link onAfterFind} event.
     * You may override this method to do postprocessing after each newly found resource is instantiated.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterFind()
    {
    	if($this->hasEventHandler('onAfterFind'))
    		$this->onAfterFind(new CEvent($this));
    }
    
    /**
     * Calls {@link beforeFind}.
     * This method is internally used.
     * @since 1.0.11
     */
    public function beforeFindInternal()
    {
    	$this->beforeFind();
    }
    
    /**
     * Calls {@link afterFind}.
     * This method is internally used.
     * @since 1.0.3
     */
    public function afterFindInternal()
    {
    	$this->afterFind();
    }

    /**
     * "Inserts" a new resource in the collection.
     * The id will be populated with the actual value after insertion.
     * Note, validation is not performed in this method. You may call {@link validate} to perform the validation.
     * After the resource is inserted to the service successfully, its {@link isNewRecord} property will be set false,
     * and its {@link scenario} property will be set to be 'update'.
     * @param array $properties list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from the service will be saved.
     * @return boolean whether the attributes are valid and the resource is inserted successfully.
     * @throws CHttpException if the resource is not new
     */
    public function create($attributes)
    {
    	if(!$this->getIsNewResource())
    		throw new CHttpException(Yii::t('yii','The active record cannot be inserted to database because it is not new.'));
    	if($this->beforeSave()) {
    		Yii::trace(get_class($this).'.insert()','ext.VTActiveResource');
	    	$module = $this->getModule();
	    	$clientvtiger=$this->getClientVtiger();
	    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
	    	else {
	    		if (empty($attributes)) $attributes=$this->getAttributesArray();
	    		$attributes['id']='';
	    		$done=$clientvtiger->doCreate($module,$attributes);
	    		if($done) {
	    			$newId=$done['id'];
                                $this->__set('id',$newId);
		    		$this->afterSave();
		    		$this->setIsNewRecord(false);
		    		$this->setScenario('update');
	    		} else {
	    			Yii::log(CVarDumper::dumpAsString($clientvtiger->lastError()),CLogger::LEVEL_ERROR);
	    			$newId=0;
	    			$lerr=$clientvtiger->lastError();
	    			$this->setLastError($lerr['code'].'::'.$lerr['message']);
	    		}
	    		return $newId;
	    	}
    	}
    	return false;
    }

    /**
     * @see create
     */
    public function insert($attributes=null)
    {
    	return $this->create($attributes);
    }
    
    /**
     * Updates the row represented by this active resource.
     * All loaded attributes will be saved to the service.
     * Note, validation is not performed in this method. You may call {@link validate} to perform the validation.
     * @param array $attributes list of attributes that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from the service will be saved.
     * @return boolean whether the update is successful
     * @throws CHttpException if the resource is new
     */
    public function update($attributes=null)
    {
    	if($this->getIsNewResource())
    		throw new CHttpException(Yii::t('yii','The active record cannot be updated because it is new.'));
    	if($this->beforeSave())
    	{
    		Yii::trace(get_class($this).'.update()','ext.VTActiveResource');
    		$this->updateById($this->getId(),$this->getAttributes($attributes));
    		return true;
    	}
    	else
    		return false;
    }

    /**
     * Updates resources with the specified id
     * Note, the attributes are not checked for safety and validation is NOT performed.
     * @param mixed $id the id of the resource
     * @param array $attributes list of attributes (name=>$value) to be updated
     */
    public function updateById($id,$attributes)
    {
    	$module = $this->getModule();
    	$clientvtiger=$this->getClientVtiger();
    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else {
    		if (empty($attributes)) $attributes=$this->getAttributesArray();
    		$attributes['id']=$id;
    		$module = $this->getModule();
    		$done=$clientvtiger->doUpdate($module,$attributes);
    		if($done) {
    			$done=true;
    			$this->afterSave();
    			$this->setIsNewRecord(false);
    			$this->setScenario('update');
    		} else {
    			Yii::log(CVarDumper::dumpAsString($clientvtiger->lastError()),CLogger::LEVEL_ERROR);
    			$done=false;
    			$lerr=$clientvtiger->lastError();
    			$this->setLastError($lerr['code'].'::'.$lerr['message']);
    		}
    	}
    	return $done;
    }

    /**
     * Updates record with the specified primary key.
     * $condition and $params are ignored as coreBOS does not permit mass update through REST
     * Note, the attributes are not checked for safety and validation is NOT performed.
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param array $attributes list of attributes (name=>$value) to be updated
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows being updated
     */
    public function updateByPk($pk,$attributes,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.updateByPk()','ext.VTActiveResource');
    	return $this->updateById($pk,$attributes);
    }
    
    /**
     * Updates records with the specified condition.
     * See {@link find()} for detailed explanation about $condition and $params.
     * Note, the attributes are not checked for safety and no validation is done.
     * @param array $attributes list of attributes (name=>$value) to be updated
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows being updated
     */
    public function updateAll($attributes,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.updateAll()','ext.VTActiveResource');
    	// We can't do this in coreBOS webservice yet
    	return false;
    }
    
    /**
     * Deletes the row corresponding to this active record.
     * @return boolean whether the deletion is successful.
     * @throws CHttpException if the record is new
     */
    public function delete()
    {
    	if(!$this->getIsNewRecord())
    	{
    		Yii::trace(get_class($this).'.delete()','ext.VTActiveResource');
    		if($this->beforeDelete())
    		{
    			$result=$this->deleteById($this->getId());
    			$this->afterDelete();
    			return $result;
    		}
    		else
    			return false;
    	}
    	else
    		throw new CHttpException(Yii::t('yii','The active record cannot be deleted because it is new.'));
    }

    /**
     * Deletes rows with the specified id.
     * @param integer $id primary key value(s).
     */
    public function deleteById($id)
    {
    	Yii::trace(get_class($this).'.deleteById()','ccc');       
    	$clientvtiger=$this->getClientVtiger();
    
    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else {
    		$done=$clientvtiger->doInvoke('delete',array('id'=>$id));
    		if(!$clientvtiger->lastError())
    			$result=array(0=>'OK',1=>Yii::t('core', 'successDeleteRow'));
    		else $result=array(0=>'NOK',1=>Yii::t('core', 'errorDeleteRow').': '.$clientvtiger->lastError());
    	}
    	return $result;
    }

    /**
     * Deletes row with the specified primary key.
     * $condition and $params are ignored
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows deleted
     */
    public function deleteByPk($pk,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.deleteByPk()','ext.VTActiveResource');
    	return $this->deleteById($pk);
    }
    
    /**
     * Deletes rows with the specified condition.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows deleted
     */
    public function deleteAll($condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.deleteAll()','ext.VTActiveResource');
    	// We can't do this in coreBOS webservice yet
    	return false;
    }
    
    /**
     * Deletes rows which match the specified attribute values.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param array $attributes list of attribute values (indexed by attribute names) that the active records should match.
     * An attribute value can be an array which will be used to generate an IN condition.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer number of rows affected by the execution.
     */
    public function deleteAllByAttributes($attributes,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.deleteAllByAttributes()','ext.VTActiveResource');
    	// We can't do this in coreBOS webservice yet
    	return false;
    }
    
    /**
     * Saves a selected list of attributes.
     * Unlike {@link save}, this method only saves the specified attributes
     * of an existing row dataset and does NOT call either {@link beforeSave} or {@link afterSave}.
     * Also note that this method does neither attribute filtering nor validation.
     * So do not use this method with untrusted data (such as user posted data).
     * You may consider the following alternative if you want to do so:
     * <pre>
     * $postRecord=Post::model()->findById($postID);
     * $postRecord->attributes=$_POST['post'];
     * $postRecord->save();
     * </pre>
     * @param array $attributes attributes to be updated. Each element represents an attribute name
     * or an attribute value indexed by its name. If the latter, the resource's
     * attribute will be changed accordingly before saving.
     * @return boolean whether the update is successful
     * @throws CHttpException if the resource is new
     */
    public function saveAttributes($attributes)
    {
    	if(!$this->getIsNewResource())
    	{
    		Yii::trace(get_class($this).'.saveAttributes()','ccc');
    		$values=array();
    		foreach($attributes as $name=>$value)
    		{
    			if(is_integer($name))
    				$values[$value]=$this->$value;
    			else
    				$values[$name]=$this->$name=$value;
    		}
    
    		if($this->updateById($this->getId(),$values)>0)
    		{
    			return true;
    		}
    		else
    			return false;
    	}
    	else
    		throw new CHttpException(Yii::t('yii','The active record cannot be updated because it is new.'));
    }

	/**
     * Saves one or several counter columns for the current AR object.
     * Note that this method differs from {@link updateCounters} in that it only
     * saves the current AR object.
     * An example usage is as follows:
     * <pre>
     * $postRecord=Post::model()->findByPk($postID);
     * $postRecord->saveCounters(array('view_count'=>1));
     * </pre>
     * Use negative values if you want to decrease the counters.
     * @param array $counters the counters to be updated (column name=>increment value)
     * @return boolean whether the saving is successful
     * @see updateCounters
     * @since 1.1.8
     */
    public function saveCounters($counters)
    {
    	// We don't use this in coreBOS, but it could be implemented
    	Yii::trace(get_class($this).'.saveCounters()','ext.VTActiveResource');
   		return true;
    }

    /**
     * Updates one or several counter columns.
     * Note, this updates all rows of data unless a condition or criteria is specified.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param array $counters the counters to be updated (column name=>increment value)
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows being updated
     * @see saveCounters
     */
    public function updateCounters($counters,$condition='',$params=array())
    {
    	// We don't use this in coreBOS, but it could be implemented    	
    	Yii::trace(get_class($this).'.updateCounters()','ext.VTActiveResource');
    	return true;
    }

    /**
     * Compares current active resource with another one.
     * The comparison is made by comparing collection name, site and id values of the two active resources.
     * @param VTActiveResource $resource resource to compare to
     * @return boolean whether the two active resources refer to the same service entry.
     */
    public function equals($resource)
    {
    	return $this->getSite()===$resource->getSite() && $this->getModule()===$resource->getModule() && $this->getId()===$resource->getId();
    }
    
    /**
     * Performs the actual REST query and populates the AR objects with the query result.
     * This method is mainly internally used by other AR query methods.
     * @param CDbCriteria $criteria the query criteria
     * @param boolean $all whether to return all data
     * @return mixed the AR objects populated with the query result
     * @since 1.1.7
     */
    protected function query($criteria,$all=false)
    {
    	$this->beforeFind();
    	$this->applyScopes($criteria);
    	if(!$all)
    		$criteria->limit=1;
    	$command=$this->createVtigerSQLCommand($this->getTableSchema(), $criteria);
    	$recordInfo = $this->getClientVtiger()->doQuery($command);
    	return $all ? $this->populateRecords($recordInfo, true, $criteria->index) : $this->populateRecord($recordInfo);
    }

    
    /**
     * Applies the query scopes to the given criteria.
     * This method merges {@link dbCriteria} with the given criteria parameter.
     * It then resets {@link dbCriteria} to be null.
     * @param CDbCriteria $criteria the query criteria. This parameter may be modified by merging {@link dbCriteria}.
     */
    public function applyScopes(&$criteria)
    {
    	if(!empty($criteria->scopes))
    	{
    		$scs=$this->scopes();
    		$c=$this->getDbCriteria();
    		foreach((array)$criteria->scopes as $k=>$v)
    		{
    			if(is_integer($k))
    			{
    				if(is_string($v))
    				{
    					if(isset($scs[$v]))
    					{
    						$c->mergeWith($scs[$v],true);
    						continue;
    					}
    					$scope=$v;
    					$params=array();
    				}
    				else if(is_array($v))
    				{
    					$scope=key($v);
    					$params=current($v);
    				}
    			}
    			else if(is_string($k))
    			{
    				$scope=$k;
    				$params=$v;
    			}
    
    			call_user_func_array(array($this,$scope),(array)$params);
    		}
    	}
    
    	if(isset($c) || ($c=$this->getDbCriteria(false))!==null)
    	{
    		$c->mergeWith($criteria);
    		$criteria=$c;
    		$this->_c=null;
    	}
    }

    /**
     * Returns the table alias to be used by the find methods.
     * In relational queries, the returned table alias may vary according to
     * the corresponding relation declaration. Also, the default table alias
     * set by {@link setTableAlias} may be overridden by the applied scopes.
     * @param boolean $quote whether to quote the alias name
     * @param boolean $checkScopes whether to check if a table alias is defined in the applied scopes so far.
     * This parameter must be set false when calling this method in {@link defaultScope}.
     * An infinite loop would be formed otherwise.
     * @return string the default table alias
     * @since 1.1.1
     */
    public function getTableAlias($quote=false, $checkScopes=true)
    {
    	if($checkScopes && ($criteria=$this->getDbCriteria(false))!==null && $criteria->alias!='')
    		$alias=$criteria->alias;
    	else
    		$alias=$this->module;
    	return $quote ? "'$alias'" : $alias;
    }
    
    /**
     * Sets the table alias to be used in queries.
     * @param string $alias the table alias to be used in queries. The alias should NOT be quoted.
     * @since 1.1.3
     */
    public function setTableAlias($alias)
    {
    	// vtiger REST doesn't support alias
    	//$this->_alias=$alias;
    }

    public function setCount($count){
        $this->count=$count;
    }
    public function getCount(){
        return $this->count;
     
//    	$module = $this->getModule();
//    	$clientvtiger=$this->getClientVtiger();
//
//    	// If the results were false, then we have no valid data, so load it
//    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
//    	else {
//    		$q=$this>createVtigerSQLCommand($module,$this->getCriteria(),'count(*)');
//    		$countquery = $clientvtiger->doQuery($q);
//    		$count=$countquery[0]['count'];
//    	}
//    	return  $count;
 
      
    }
    /**
     * Finds a single active record with the specified condition.
     * @param mixed $condition query condition or criteria.
     * If a string, it is treated as query condition (the WHERE clause);
     * If an array, it is treated as the initial values for constructing a {@link CDbCriteria} object;
     * Otherwise, it should be an instance of {@link CDbCriteria}.
     * @param array $params parameters to be bound to an SQL statement.
     * This is only used when the first parameter is a string (query condition).
     * In other cases, please use {@link CDbCriteria::params} to set parameters.
     * @return CActiveRecord the record found. Null if no record is found.
     */
    public function find($condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.find()','ext.VTActiveResource');
    	return $this->query($condition);
    }

    public function findAll($criteria='',$cols='')
    {
    	$module=$this->getModule();    	
    
    	if(empty($criteria)){
	    $criteria=$this->getcriteria();
	}
    	$pageSize=Yii::app()->user->settings->get('pageSize');
    	$q=$this->createVtigerSQLCommand($module,$criteria,$cols);
    	$api_cache_id='findall'.$q;
    	$findall = Yii::app()->cache->get( $api_cache_id  );

    	// If the results were false, then we have no valid data, so load it
    	if($findall===false){
                $clientvtiger=$this->getClientVtiger();
    		if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    		else {
    			$findall = $clientvtiger->doQuery($q);
    		}
    		Yii::app()->cache->set( $api_cache_id , $findall, $this->defaultCacheTimeout, new vtDbCacheDependency("select modifiedtime from $module order by modifiedtime desc limit 1") );
    	}
        $this->setCount(count($findall));
        $findall=$this->dereferenceIds($findall);
        $findall=$this->translatePicklistValues($findall);
    	return $this->populateRecords($findall,false);
    }

	public function translatePicklistValues($recinfo) {
		foreach ($recinfo as $idx => $rinf) {
			foreach ($rinf as $fname => $fvalue) {
				foreach ($this->_fieldinfo as $finfo) {
					if ($fname==$finfo['name'] && isset($finfo['uitype']) && in_array($finfo['uitype'], array(15,16))) {
						if (isset($finfo['type']['picklistValues']) && !empty($finfo['type']['picklistValues'])) {
							foreach ($finfo['type']['picklistValues'] as $plvalue) {
								if ($plvalue['value']==$fvalue) {
									$recinfo[$idx][$fname] = $plvalue['label'];
									break;
								}
							}
						}
					}
				}
			}
		}
		return $recinfo;
	}

    public function dereferenceIds($recinfo,$htmlreference=true) {
    	if (!$this->doDereference) return $recinfo;
    	$all_attachments=array();
    	$simplerdo=false;
    	if (empty($recinfo['0']) or !is_array($recinfo['0'])) {
    		$simplerdo=true;
    		$recinfo=array('0'=>$recinfo);
    	}
    	$module=$this->getModule();
    	$tobelook=array();
    	$tobelookfields=array();
    	$ids=array();
    	foreach($recinfo as $rec){
		if (!is_array($rec)) continue;
    		foreach($rec as $key=>$val){
    			if($key=='id')
    			{
    				if($module == 'Documents') {
    					array_push($ids,$val);
    					break;
    				}
    				else continue;
    			}
    			$ls=explode('x',$val);
    			if(is_array($ls) && count($ls)>1) {
    				list($void,$field)=$ls;
    				if(is_numeric($void) && is_numeric($field)){
    					if(!in_array($val, $tobelook) && $val!=='') {
    						array_push($tobelook,$val);
    					}
    					if(!in_array($key,$tobelookfields)) array_push($tobelookfields,$key);
    				}
    	
    			}
    		}
    	}
    	if(count($ids)>0) {
    		$all_attachments=$this->getDocumentAttachment (implode(',',$ids),false);
    	}
    	if(count($tobelook)>0 || $module == 'Documents'){
    		$respvalues=unserialize($this->getComplexAttributeValues($tobelook));
    		$nr=count($recinfo);
    		for( $i=0;$i<$nr;$i++){
    			foreach($tobelookfields as $fld){
    				$tm=$recinfo[$i][$fld];
    				if($tm!=='') {
    					if (((Yii::app()->vtyiicpngScope=='CPortal' and in_array($respvalues[$tm]['module'],Yii::app()->notSupportedModules[Yii::app()->vtyiicpngScope]))
    					 or (Yii::app()->vtyiicpngScope=='vtigerCRM' and !in_array($respvalues[$tm]['module'],Yii::app()->notSupportedModules[Yii::app()->vtyiicpngScope])))
    					 and $htmlreference) {
    						$recinfo[$i][$fld]=CHtml::link($respvalues[$tm]['reference'],'#vtentity/'.$respvalues[$tm]['module']."/view/$tm");
    					} else {
    						$recinfo[$i][$fld]=html_entity_decode($respvalues[$tm]['reference'],ENT_QUOTES,'UTF-8');
    					}
    				}
    			}
    			if($module == 'Documents') {
                    if(!empty($recinfo)){
        				$idatt=$recinfo[$i]['id'];
        				if(is_array($all_attachments) && in_array($idatt,array_keys($all_attachments))) {
        					if (!empty($all_attachments[$idatt]['filetype'])) {
        						$value='<a href=\'javascript: filedownload.download("'.yii::app()->baseUrl.'/index.php/vtentity/'.$this->getModule().'/download/'.$idatt.'?fn='.CHtml::encode($all_attachments[$idatt]['filename']).'&ft='.CHtml::encode($all_attachments[$idatt]['filetype']).'","")\'>'.CHtml::encode($all_attachments[$idatt]['filename'])."</a>";
        					} else {
        						$fname = (empty($all_attachments[$idatt]['filename']) ? yii::t('core', 'none') : $all_attachments[$idatt]['filename']);
        						$value=CHtml::encode($fname);
        					}
        					$recinfo[$i]['filename']=$value;
        				}
                    }
    			}
    		}
    	}
    	if ($simplerdo) {
    		$recinfo=$recinfo['0'];
    	}
    	return $recinfo;
    }

    public function findAllSearch($query,$search_onlyin)
    {
    	$module=$this->getModule();
    	$clientvtiger=$this->getClientVtiger();
    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else {
    		$findall =unserialize($clientvtiger->doInvoke('getSearchResults',
    				array('query'=>$query,'search_onlyin'=>$search_onlyin,
    						'restrictionids'=>json_encode(array(
    							'userId'=>Yii::app()->user->userId,
    							'accountId'=>Yii::app()->user->accountId,
    							'contactId'=>Yii::app()->user->contactId
    								)))));
    	}
    	return $findall;
    }

    /**
     * Finds a single active record with the specified primary key.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return CActiveRecord the record found. Null if none is found.
     */
    public function findByPk($pk,$condition='',$params=array())
    {
    	return $this->findById($pk);
    }

    /**
     * Finds a single active resource with the specified id.
     * @param mixed $id The id.
     * @return VTActiveResource the resource found. Null if none is found.
     */
    public function findById($record)
    {
    	$module = $this->getModule();    	
    	$clientvtiger=$this->getClientVtiger();
    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else {
    		$recordInfo = $clientvtiger->doRetrieve($record);
    	}
    	$recordInfo=$this->dereferenceIds($recordInfo);
    	return $this->populateRecord($recordInfo);
    }

    /**
     * Finds a single active resource with the specified id without dereferencing IDs
     * @param mixed $id The id.
     * @return VTActiveResource the resource found. Null if none is found.
     */
    public function findById_Raw($record)
    {
    	$module = $this->getModule();    	
    	$clientvtiger=$this->getClientVtiger();
    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else {
    		$recordInfo = $clientvtiger->doRetrieve($record);
    	}
    	return $this->populateRecord($recordInfo);
    }

    /**
     * Finds all active records with the specified primary keys.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return array the records found. An empty array is returned if none is found.
     */
    public function findAllByPk($pk,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.findAllByPk()','ext.VTActiveResource');
    	$q=$this->createFindSQLCommand($this->getModule(),'where id in ('.explode(',',$pk).')');
    	$findall = $clientvtiger->doQuery($q);
    	return $findall;
    }

    
    /**
     * Finds a single active record that has the specified attribute values.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param array $attributes list of attribute values (indexed by attribute names) that the active records should match.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return CActiveRecord the record found. Null if none is found.
     */
    public function findByAttributes($attributes,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.findByAttributes()','ext.VTActiveResource');
    	$criteria=new CDbCriteria;
    	$criteria->addCondition($condition);
    	$attrs=$this->getAttributesArray();
    	foreach ($attrs as $key=>$attr) {
    		if (!is_array($attr)) continue;  // we can only search simple values, not IN
    		// FIXME: remove attributes that should not be searched.
    		$criteria->compare($attr['name'],$this->getAttribute($attr['name']),true);
    	}
    	$this->setCriteria($criteria);
    	return $this->query($criteria);
    }
    
    /**
     * Finds all active records that have the specified attribute values.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param array $attributes list of attribute values (indexed by attribute names) that the active records should match.
     * An attribute value can be an array which will be used to generate an IN condition.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return array the records found. An empty array is returned if none is found.
     */
    public function findAllByAttributes($attributes,$condition='',$params=array())
    {
    	return $this->findByAttributes($attributes,$condition='',$params=array());
    }

    /**
     * Finds a single active record with the specified SQL statement.
     * @param string $sql the SQL statement
     * @param array $params parameters to be bound to the SQL statement
     * @return CActiveRecord the record found. Null if none is found.
     */
    public function findBySql($sql,$params=array())
    {
    	Yii::trace(get_class($this).'.findBySql()','ext.VTActiveResource');
    	$this->beforeFind();
    	$query=$sql;
    	foreach ($params as $clv=>$val) {
    		$query=str_replace($clv, $this->quoteValue($val), $cond);
    	}
    	return $this->populateRecord($query);
    }
    
    /**
     * Finds all active records using the specified SQL statement.
     * @param string $sql the SQL statement
     * @param array $params parameters to be bound to the SQL statement
     * @return array the records found. An empty array is returned if none is found.
     */
    public function findAllBySql($sql,$params=array())
    {
    	Yii::trace(get_class($this).'.findAllBySql()','ext.VTActiveResource');
   		return $this->findBySql($sql,$params=array());
    }

    public function count($criteria)
    {
    	$module = $this->getModule();
    	$clientvtiger = $this->getClientVtiger();
    	
    	// If the results were false, then we have no valid data, so load it
    	if (!$clientvtiger)
    		Yii::log('login failed', CLogger::LEVEL_ERROR);
    	else {
    		$q = $this->createVtigerSQLCommand($module, $criteria, 'count(*)');
    		$countquery = $clientvtiger->doQuery($q);
    		$count = $countquery[0]['count'];
    	}
    	return $count;
    	//return  $this->getCount();
    }

    /**
     * Finds the number of rows that have the specified attribute values.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param array $attributes list of attribute values (indexed by attribute names) that the active records should match.
     * An attribute value can be an array which will be used to generate an IN condition.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return string the number of rows satisfying the specified query condition. Note: type is string to keep max. precision.
     * @since 1.1.4
     */
    public function countByAttributes($attributes,$condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.countByAttributes()','ext.VTActiveResource');
    	$criteria=new CDbCriteria;
    	$criteria->addCondition($condition);
    	$this->applyScopes($criteria);
    	if (!is_array($attributes)) $attributes=array();
    	foreach ($attributes as $key=>$attr) {
    		if (!is_array($attr)) continue;  // we can only search simple values, not IN
    		// FIXME: remove attributes that should not be searched.
    		$criteria->compare($attr['name'],$this->getAttribute($attr['name']),true);
    	}
    	$this->setCriteria($criteria);
    	return $this->count($criteria);
    }
    
    /**
     * Finds the number of rows using the given SQL statement.
     * This is equivalent to calling {@link CDbCommand::queryScalar} with the specified
     * SQL statement and the parameters.
     * @param string $sql the SQL statement
     * @param array $params parameters to be bound to the SQL statement
     * @return string the number of rows using the given SQL statement. Note: type is string to keep max. precision.
     */
    public function countBySql($sql,$params=array())
    {
    	Yii::trace(get_class($this).'.countBySql()','ext.VTActiveResource');
    	$this->beforeFind();
    	$query=$sql;
    	foreach ($params as $clv=>$val) {
    		$query=str_replace($clv, $this->quoteValue($val), $cond);
    	}
    	$countquery = $this->getClientVtiger()->doQuery($query);
    	$count=$countquery[0]['count'];
    	return $count;
    }

    /**
     * Checks whether there is row satisfying the specified condition.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return boolean whether there is row satisfying the specified condition.
     */
    public function exists($condition='',$params=array())
    {
    	Yii::trace(get_class($this).'.exists()','ext.VTActiveResource');
    	return ($this->countByAttributes(array(),$condition,$params)>0);
    }

    /**
     * Specifies which related objects should be eagerly loaded.
     * This method takes variable number of parameters. Each parameter specifies
     * the name of a relation or child-relation. For example,
     * <pre>
     * // find all posts together with their author and comments
     * Post::model()->with('author','comments')->findAll();
     * // find all posts together with their author and the author's profile
     * Post::model()->with('author','author.profile')->findAll();
     * </pre>
     * The relations should be declared in {@link relations()}.
     *
     * By default, the options specified in {@link relations()} will be used
     * to do relational query. In order to customize the options on the fly,
     * we should pass an array parameter to the with() method. The array keys
     * are relation names, and the array values are the corresponding query options.
     * For example,
     * <pre>
     * Post::model()->with(array(
     *     'author'=>array('select'=>'id, name'),
     *     'comments'=>array('condition'=>'approved=1', 'order'=>'create_time'),
     * ))->findAll();
     * </pre>
     *
     * @return CActiveRecord the AR object itself.
     */
    public function with()
    {
		// this is not supported by coreBOS REST
    	return $this;
    }

    /**
     * Sets {@link CDbCriteria::together} property to be true.
     * This is only used in relational AR query. Please refer to {@link CDbCriteria::together}
     * for more details.
     * @return CActiveRecord the AR object itself
     * @since 1.1.4
     */
    public function together()
    {
    	// this is not supported by coreBOS REST
    	//$this->getDbCriteria()->together=true;
    	return $this;
    }

    /**
     * Creates an active resource with the given attributes.
     * This method is internally used by the find methods.
     * @param array $attributes attribute values (column name=>column value)
     * @param boolean $callAfterFind whether to call {@link afterFind} after the resource is populated.
     * This parameter is added in version 1.0.3.
     * @return VTActiveResource the newly created active resource. The class of the object is the same as the model class.
     * Null is returned if the input data is false.
     */
    public function populateRecord($attributes,$callAfterFind=true) {
    		if(is_array($attributes) && array_key_exists($this->getContainer(),$attributes))
    		{
    			$attributes=$this->extractDataFromResponse($attributes);
    			Yii::log('Container field found: '.$this->getContainer().'. Repopulating!',CLogger::LEVEL_INFO);
    		}
    		if(isset($attributes[$this->getContainer()]))
    		{
    			Yii::log('Container field found: '.$this->getContainer().'. Repopulating!',CLogger::LEVEL_INFO);
    			//this array position is the actual object so try again
    			return $this->populateRecord($attributes[$this->getContainer()]);
    		}
    		if ($attributes!==false && is_array($attributes))
    		{
    			$resource=$this->instantiate($attributes);
    			$resource->setScenario('update');
    			$resource->init();
    			$resource->_attributes = $attributes;
    			$resource->attachBehaviors($resource->behaviors());
    			if($callAfterFind)
    				$resource->afterFind();
    			return $resource;
    		} else {
    			return null;
    		}
    }

    static public function getFolderNameFromFolderID($fldid) {
    	$api_cache_id=Yii::app()->user->getState('prefix').'getFolderNameFromFolderID'.$fldid;
    	$fname = Yii::app()->cache->get($api_cache_id);
    	if ($fname===false) {
	    	$clientvt = VTActiveResource::loginREST();
	    	$command="select foldername from documentfolders where folderid='$fldid'";
	    	$recordInfo = $clientvt->doQuery($command);
    		if ($recordInfo) {
    			$fname = $recordInfo[0]['foldername'];
    			Yii::app()->cache->set($api_cache_id , $fname);
    		}
    	}
    	return $fname;
    }

    /**
     * Creates a list of active resources based on the input data.
     * This method is internally used by the find methods.
     * @param array $data list of attribute values for the active resources.
     * @param boolean $callAfterFind whether to call {@link afterFind} after each resource is populated.
     * @param string $index the name of the attribute whose value will be used as indexes of the query result array.
     * If null, it means the array will be indexed by zero-based integers.
     * @return array list of active resources.
     */
    public function populateRecords($data,$callAfterFind=true,$index=null)
    {
    	$resources=array();
    	$i=0;
    	//if($this->getContainer())                    $data=$this->extractDataFromResponse($data);
    	if (is_array($data))
	foreach($data as $attributes)
    	{
    		if(($resource=$this->populateRecord($attributes,$callAfterFind))!==null)
    		{
    			if($index===null)
    			{
    				$resources[]=$resource;
    			}
    			else
    				$resources[$resource->$index]=$resource;
    		}
    	}
    	return $resources;
    }

    /**
     * This method is used internally by the finder methods to instantiate models.
     * @param array $attributes The attributes the model has to be instantiated with
     * @return VTActiveResource The instantiated model
     */
    protected function instantiate($attributes)
    {
    	$class=get_class($this);
    	$model=new $class($this->getModule());
    	return $model;
    }
    
    public function offsetExists($offset)
    {
    	return $this->__isset($offset);
    }

    /**
     * This method is used in EActiveMetaData to recive the attributes of the object without the complex logic of the CModel getAttributes() function
     * @return array All attributes of this model.
     */
    public function getAttributesArray()
    {
    	return (is_array($this->_attributes) ? $this->_attributes : $this->attributeNames());
    }

    public function getWritableFieldsArray()
    {
    	$fields=$this->getFieldsInfo();
    	$result=array();
    	foreach($fields as $field)
    	{
    		if (!is_array($field)) continue;
    		if ($field['editable']) array_push($result,$field);
    	}
    	return $result;
    }

    /**
     * Returns the content type as specified within Configuration()
     * @return string
     */
    public function getContentType()
    {
        return $this->getMetaData()->contenttype;
    }

    /**
     * Returns the accept type as specified within Configuration()
     * @return string
     */
    public function getAcceptType()
    {
        return $this->getMetaData()->accepttype;
    }

    /**
     * Returns the site as specified within Configuration()
     * @return string
     */
    public function getSite()
    {
        return $this->getMetaData()->site;
    }

    /**
     * Returns the file extension as specified within Configuration()
     * @return string
     */
    public function getFileExtension()
    {
        return $this->getMetaData()->fileextension;
    }

    /**
     * Returns the container field as specified within Configuration()
     * @return string
     */
    public function getContainer()
    {
        return $this->getMetaData()->container;
    }

    /**
     * Returns the embedded fields as specified within Configuration()
     * @return string
     */
    public function getEmbedded()
    {
        return $this->getMetaData()->embedded;
    }

    /**
     * Returns the idProperty as specified within Configuration()
     * @return string
     */
    public function idProperty()
    {
        return $this->getMetaData()->idProperty;
    }

	/**
	 * Sets the provider ID.
	 * @param string $value the unique ID that uniquely identifies the data provider among all data providers.
	 */
	public function setId($value)
	{
		$this->_id=$value;
		$this->id=$value;
	}        

    public function getItemCount($refresh=false)
    {
        return $this->count($this->_criteria);
    }

    public function getTotalItemCount()
    {
        return $this->count('');
    }

    public function getKeys()
    {
    	$keys=$this->attributeNames();
        return $keys;
    }

    /**
     * Overrides the CModel method in order to provide schemaless assignments.
     * @param array $values
     * @param boolean $safeOnly
     */
    public function setAttributes($values,$safeOnly=true)
    {
        if(!is_array($values))
            return;
		$attributes=array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
		foreach($values as $name=>$value) {        
			if(isset($attributes[$name]))
				$this->setAttribute($name,$value);
			else if($safeOnly)
				$this->onUnsafeAttribute($name,$value);
		}
    }

    public function getLookupFieldValue($lookupfield,$values) {
		if (strpos($lookupfield, ',')>0) {
			$showValue='';
			$lookup_fields=explode(',', $lookupfield);
			foreach ($lookup_fields as $field) {
				$showValue.=$values[$field].' ';
			}
			$showValue=trim($showValue);
		} else {
			if ($this->getModule()=='HelpDesk' and $lookupfield=='title')
				$showValue=$values['ticket_title'];
			else if ($this->getModule()=='Documents' and $lookupfield=='title')
				$showValue=$values['notes_title'];
			else 
				$showValue=$values[$lookupfield];
		}
		return $showValue;
	} 

	public function getLookupField()
	{		
		$module=$this->getModule();
	
		$api_cache_id='getLookupField'.$module;
		$labelFields = Yii::app()->cache->get($api_cache_id);
	
		// If the results were false, then we have no valid data, so load it
		if($labelFields===false){
                        $clientvtiger=$this->getClientVtiger();
			if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
			else {
				$moduledata = $clientvtiger->doDescribe($module);
				if ($module=='HelpDesk') {
					$labelFields='ticket_no';
				} elseif ($module=='Documents') {
					$labelFields='notes_title';
				} else {
				$labelFields=$moduledata["labelFields"];
				}
				$moduleAccessInformation = Yii::app()->cache->get('moduleAccessInformation');
				if($moduleAccessInformation===false){
					$moduleAccessInformation=array();
				}
				if (Yii::app()->vtyiicpngScope=='CPortal' and $module=='Accounts') {  // cuentas es especial así que lo ponemos a mano
					$moduleAccessInformation[$module]=array(
							'createable' => false,
							'updateable' => true,
							'deleteable' => false,
							'retrieveable' => true,
					);
				} else {
					$moduleAccessInformation[$module]=array(
						'createable' => $moduledata['createable'],
						'updateable' => $moduledata['updateable'],
						'deleteable' => $moduledata['deleteable'],
						'retrieveable' => $moduledata['retrieveable'],
						);
				}
				Yii::app()->cache->set( 'moduleAccessInformation' , $moduleAccessInformation);
			}
			Yii::app()->cache->set( $api_cache_id , $labelFields);
		}
		return $labelFields;
	}

    public function getMandatoryFields()
    {
		$fields=$this->getFieldsInfo();
		$arr=array();
		foreach($fields as $field) {
			if (!is_array($field) or empty($field['type'])) continue;
			if($field['mandatory'] and $field['editable']) array_push($arr,$field['name']);
		}
		$res=implode(',',$arr);
		return $res;
    }

    public function getNumericalFields()
    {
            $fields=$this->getFieldsInfo();
            $arrint=$arrdbl=array();
            foreach($fields as $field) {
            	if (!is_array($field) or empty($field['type'])) continue;
               if($field['type']['name']=='integer' and $field['editable']) array_push($arrint,$field['name']);
               if(($field['type']['name']=='double' or $field['type']['name']=='float') and $field['editable']) array_push($arrdbl,$field['name']);
            }
            $resint=implode(',',$arrint);
            $resdbl=implode(',',$arrdbl);
            return array('entero'=>$resint,'real'=>$resdbl);
	}

	public function getFieldsGroupedByType()
	{
		$fields=$this->getFieldsInfo();
		$arrint=$arrdbl=$arrurl=$arrdate=$arreml=$arrman=array();
		$fmt = 'yyyy-mm-dd';
		foreach($fields as $field) {
			if (!is_array($field) or empty($field['type'])) continue;
			if($field['type']['name']=='integer' and $field['editable']) { array_push($arrint,$field['name']); continue; }
			if(($field['type']['name']=='double' or $field['type']['name']=='float') and $field['editable']) { array_push($arrdbl,$field['name']); continue; }
			if($field['type']['name']=='date' and $field['editable']) {
				array_push($arrdate,$field['name']);
				$fmt = $field['type']['format'];
				continue;
			}
			if($field['mandatory'] and $field['editable']) { array_push($arrman,$field['name']); continue; }
			if($field['type']['name']=='email' and $field['editable']) { array_push($arreml,$field['name']); continue; }
			if($field['type']['name']=='url' and $field['editable']) { array_push($arrurl,$field['name']); continue; }
		}
		return array(
		'entero'=>implode(',',$arrint),
		'real'=>implode(',',$arrdbl),
		'url'=>implode(',',$arrurl),
		'fecha'=>array('fields'=>implode(',',$arrdate),'format'=>$fmt),
		'email'=>implode(',',$arreml),
		'obligatorio'=>implode(',',$arrman),
		);
	}

    public function getDateFields()
    {
    	$fields=$this->getFieldsInfo();
    	$arr=array();
    	$fmt = 'yyyy-mm-dd';
    	foreach($fields as $field)
    	{
    		if (!is_array($field) or empty($field['type'])) continue;
    		if($field['type']['name']=='date' and $field['editable']) {
    			array_push($arr,$field['name']);
    			$fmt = $field['type']['format'];
    		}
    	}
    	$res=implode(',',$arr);
    	return array('fields'=>$res,'format'=>$fmt);
    }

    public function getEmailFields()
    {
		$fields=$this->getFieldsInfo();
		$arr=array();
		foreach($fields as $field) {
			if (!is_array($field) or empty($field['type'])) continue;
			if($field['type']['name']=='email' and $field['editable']) array_push($arr,$field['name']);
		}
		$res=implode(',',$arr);
		return $res;
    }

    /**
     * Deletes the resource
     * @return boolean whether the deletion is successful.
     * @throws CHttpException if the resource is new
     */
    public function destroy()
    {
    	if(!$this->getIsNewResource())
    	{
    		Yii::trace(get_class($this).'.destroy()','ext.VTActiveResource');
    		if($this->beforeDelete())
    		{
    			$result=$this->deleteById($this->getId())>0;
    			$this->afterDelete();
    			return $result;
    		}
    		else
    			return false;
    	}
    	else
    		throw new CHttpException(Yii::t('ext.VTActiveResource','The resource cannot be deleted because it is new.'));
    }

    public function getData($cols='')
    {
		return $this->findAll($this->getCriteria(),$cols);
    }

	public function getFieldsInfo($sortthem=false)
	{
		if (empty($this->_fieldinfo) or count($this->_fieldinfo)==0)
			$this->setFieldsInfo($sortthem);
		return $this->_fieldinfo;
	}

	public function setFieldsInfo($sortthem=false)
	{
		$module=$this->getModule();

		$api_cache_id='getFieldsInfo'.$module;
		$Fields = Yii::app()->cache->get( $api_cache_id  );

		// If the results were false, then we have no valid data, so load it
		if($Fields===false){
			$clientvtiger=$this->getClientVtiger();
			if(!$clientvtiger) {
				Yii::log('login failed',CLogger::LEVEL_ERROR);
				$this->_fieldinfo=array();
			} else {
				$Fieldsdata = $clientvtiger->doDescribe($module);
				$Fields=$Fieldsdata['fields'];
				if ($sortthem)
					usort($Fields,"VTActiveResource::sortFieldsData");
				$this->_fieldinfo=$Fields;
				$this->translateAttributeLabels();
				Yii::app()->cache->set( $api_cache_id , $this->_fieldinfo, $this->defaultCacheTimeout );
			}
		} else {
			$this->_fieldinfo=$Fields;
		}
	}

	static public function sortFieldsData($field1,$field2) {
		if(!isset($field1['block'])) {
			if (!isset($field2['block'])) {
				return 0;
			} else {
				return 1;
			}
		} else {
			if (!isset($field2['block'])) {
				return -1;
			} else {
				if ($field1['block']['blocksequence']>$field2['block']['blocksequence']) {
					return 1;
				} elseif ($field1['block']['blocksequence']<$field2['block']['blocksequence']) {
					return -1;
				} elseif ($field1['sequence']>$field2['sequence']) {
					return 1;
				} elseif ($field1['sequence']<$field2['sequence']) {
					return -1;
				} else {
					return 0;
				}
			}
		}
	}

	public function getListViewFields()
	{
		$module = $this->getModule();		
		$api_cache_id='getListViewFields'.$module;
		$ListViewFields = Yii::app()->cache->get( $api_cache_id  );
		// If the results were false, then we have no valid data, so load it
		if($ListViewFields===false)
		{ // No valid cached data was found, so we will generate it.
			$clientvtiger=$this->getClientVtiger();
			if(!$clientvtiger)
				Yii::log('login failed',CLogger::LEVEL_ERROR);
			else {
				$ListViewFields = $clientvtiger->doGetFilterFields($module);
			}
			Yii::app()->cache->set( $api_cache_id , $ListViewFields, $this->defaultCacheTimeout );
		}
		return $ListViewFields;
	}
 
	public function getUItype()
	{
                $uitypeFields=array();
                $fields=$this->getFieldsInfo();
                foreach($fields as $field){
                    if(!is_array($field) || $field['name'] == 'id') continue;
                    $name=$field["name"];
                    $uitype=$field["uitype"];
                    $uitypeFields[$name]=$uitype;
                }
		return $uitypeFields;
	}

	public function getComplexAttributeValues($arrayofids){
		$rdo = array();
		$toget = array();
		foreach ($arrayofids as $val) {
			$complexattributevalue = Yii::app()->cache->get('getComplexAttributeValues'.$val);
			if ($complexattributevalue===false) {
				$toget[] = $val;
			} else{
				$rdo[$val] = $complexattributevalue;
			}
		}
		if(count($toget)>0) {
			$clientvtiger=$this->getClientVtiger();
			if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
			else {
				$complexattributevalue = $clientvtiger->doInvoke('getReferenceValue',array('id'=>serialize($arrayofids)));
			}
			$cpxarr = unserialize($complexattributevalue);
			if (is_array($cpxarr)) {
				foreach ($cpxarr as $wsid=>$cpxval) {
					Yii::app()->cache->set( 'getComplexAttributeValues'.$wsid , $cpxval, $this->defaultCacheTimeout );
					$rdo[$wsid] = $cpxval;
				}
			}
		}
		return serialize($rdo);
    }
        public function getComplexAttributeValue($fieldvalue) {
			$tr=unserialize($this->getComplexAttributeValues(array($fieldvalue)));
			return $tr[$fieldvalue];
        }

        public function getDocumentAttachment($ids,$getfile=true){
		$api_cache_id='getDocumentAttachment'.$ids;
		$documentAttachment = Yii::app()->cache->get( $api_cache_id  );
		// If the results were false, then we have no valid data, so load it
		if($documentAttachment===false or $getfile){
			$clientvtiger=$this->getClientVtiger();
			if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
			else {
				$documentAttachment = $clientvtiger->doInvoke('retrievedocattachment',array('id'=>$ids,'returnfile'=>$getfile));
			}
			Yii::app()->cache->set( $api_cache_id , $documentAttachment, $this->defaultCacheTimeout );
		} 

		return $documentAttachment;
        }

        public function writeAttachment2Cache($id,$filecontent){
		$saveasfile = "protected/runtime/cache/_$id";
		$fh = fopen($saveasfile, 'wb');
		fwrite($fh, base64_decode($filecontent));
		fclose($fh);
		return $saveasfile;
        }
	public function getUsersInSameGroup()
	{
		$api_cache_id='getUsersInSameGroup';
		$usersinsamegroup = Yii::app()->cache->get( $api_cache_id  );

		// If the results were false, then we have no valid data, so load it
		if($usersinsamegroup===false){
			$clientvtiger=$this->getClientVtiger();
			if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
			else {
				list($id,$usr) = explode('x', Yii::app()->user->userId);
				$usersinsamegroup = $clientvtiger->doInvoke('getUsersInSameGroup',array('id'=>$usr));
				$usersinsamegroup[$usr] = Yii::app()->user->username;
			}
			$moduleid = $clientvtiger->doInvoke('vtyiicpng_getWSEntityId',array('entityName'=>'Users'));
			$usrsingroup = array();
			foreach($usersinsamegroup as $key=>$value) {
				$usrsingroup[$moduleid.$key]=trim($usersinsamegroup[$key]);
			}
			$usersinsamegroup = $usrsingroup;
			Yii::app()->cache->set( $api_cache_id , $usersinsamegroup, $this->defaultCacheTimeout );
		}
		return $usersinsamegroup;
	}

	public function getPagination()
	{
		if($this->_pagination===null)
		{
			$this->_pagination=new CPagination;
			if(($id=$this->getId())!='')
				$this->_pagination->pageVar=$id.'_page';
		}
		return $this->_pagination;
	}

    /**
     * Translate array against coreBOS
     * @param integer $id primary key value(s).
     */
    public function vtGetTranslation($strs,$module='',$language='')
    {
		$tr=$strs;
		
		$api_cache_id='getTr'.md5(serialize($strs)).$module;
		$tr = Yii::app()->cache->get( $api_cache_id  );
		// If the results were false, then we have no valid data,
		// so load it
		if($tr===false){		
		$clientvtiger=$this->getClientVtiger();
                if(!$clientvtiger)
			Yii::log('login failed',CLogger::LEVEL_ERROR);
		else {
			if (empty($language)) $language = Yii::app()->getLanguage();
			$tr = $clientvtiger->doTranslate($strs, $language, $module);
                        Yii::app()->cache->set( $api_cache_id , $tr, $this->defaultCacheTimeout );
		}
                }                
		return $tr;
    }

    /**
     * This method tries to extract a subarray within an response that contains a field that is recognized as container field (as specified within Configuration())
     * @param array $array The array containing the data
     * @return array The array only containing the relevant fields.
     */
    public function extractDataFromResponse($array)
    {
    	if (is_array($array))
    	{
    		if (isset($array[$this->getContainer()]))
    			return $array[$this->getContainer()];
    		foreach ($array as $item)
    		{
    			$return = $this->extractDataFromResponse($item);
    			if (!is_null($return))
    				return $return;
    		}
    	}
    	else
    		return $array;
    }

    public function createFindCommand($module, $criteria='')
    {
    	Yii::log('criter'.$criteria.' '.$module,CLogger::LEVEL_INFO);
    	$clientvtiger=$this->getClientVtiger();

    	if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else{
    		if (trim($criteria)!='where')
    		{
    			$recordInfo = $clientvtiger->doQuery("Select * from $module ".$criteria);
    		}
    		else
    		{
    			$recordInfo = $clientvtiger->doQuery("Select * from $module ");
    		}
    	}
    	return $this->populateRecords($recordInfo);
    }

    public function createFindSQLCommand($module, $criteria='') {
    	if (trim($criteria)!='where') {
    		$query="Select * from $module ".$criteria;
    	} else {
    		$query="Select * from $module ";
    	}
    	return $query;
    }

    public function createVtigerSQLCommand($module, $criteria='', $cols='*') {
    	if (is_object($criteria) and get_class($criteria)=='CDbCriteria') {
    		$this->applyScopes($criteria);
    		$this->setCriteria($criteria);
    		$cond=$criteria->condition;
    		foreach ($criteria->params as $clv=>$val) {
    			$cond=str_replace($clv, $this->quoteValue($val), $cond);
    		}
    		// coreBOS does not support parenthesis in conditionals so we eliminate here, any that yii may have put
    		// except for related record queries and IN condition which DO support parenthesis
            if (stripos($cond,'related')===false && stripos($cond,' IN ')===false)
                $cond = str_replace(array('(',')'),'',$cond);
    		if (stripos($cond,'NOT LIKE')!==false){
                $cond = str_replace('NOT LIKE','!=',$cond);
    			$cond = str_replace('%','',$cond);
            }
    		if (!empty($cond)) $cond=' where '.$cond;
    		$cond=$this->applyOrder($cond, trim($criteria->order,' "'));
    		$cond=$this->applyLimit($cond, $criteria->limit,$criteria->offset);
    	} else {
    		$cond=$criteria;
    	}

    	if (empty($cols)) $cols='*';
    	$query="Select $cols from $module $cond";
    	return $query;
    }

    /**
     * Alters the SQL to apply ORDER BY.
     * @param string $sql SQL statement without ORDER BY.
     * @param string $orderBy column ordering
     * @return string modified SQL applied with ORDER BY.
     */
    public function applyOrder($sql,$orderBy)
    {
    	if($orderBy!='')
    		return $sql.' ORDER BY '.$orderBy;
    	else
    		return $sql;
    }

    /**
     * Alters the SQL to apply LIMIT and OFFSET.
     * Default implementation is applicable for PostgreSQL, MySQL and SQLite.
     * @param string $sql SQL query string without LIMIT and OFFSET.
     * @param integer $limit maximum number of rows, -1 to ignore limit.
     * @param integer $offset row offset, -1 to ignore offset.
     * @return string SQL with LIMIT and OFFSET
     */
    public function applyLimit($sql,$limit,$offset)
    {
    	if($limit>0) {
    		$sql.=' LIMIT ';
    		if($offset>0)
    			$sql.=(int)$offset.',';
    		$sql.=(int)$limit;
    	}
    	return $sql;
    }

    public function quoteValue($str) {
    	if(is_int($str) || is_float($str))
    		return $str;
    	return $this->qstr($str);
    }

    /**
     * Correctly quotes a string so that all strings are escaped. We prefix and append
     * to the string single-quotes.
     * An example is  $db->qstr("Don't bother",magic_quotes_runtime());
     *
     * @param s			the string to quote
     * @param [magic_quotes]	if $s is GET/POST var, set to get_magic_quotes_gpc().
     *				This undoes the stupidity of magic quotes for GPC.
     *
     * @return  quoted string to be sent back to database
     */
    function qstr($s,$magic_quotes=false)
    {
    	if (!$magic_quotes) {
    		if ($this->replaceQuote[0] == '\\'){
    			// only since php 4.0.5
    			$s = adodb_str_replace(array('\\',"\0"),array('\\\\',"\\\0"),$s);
    			//$s = str_replace("\0","\\\0", str_replace('\\','\\\\',$s));
    		}
    		return  "'".str_replace("'",$this->replaceQuote,$s)."'";
    	}
    		
    	// undo magic quotes for "
    	$s = str_replace('\\"','"',$s);
    		
    	if ($this->replaceQuote == "\\'")  // ' already quoted, no need to change anything
    		return "'$s'";
    	else {// change \' to '' for sybase/mssql
    		$s = str_replace('\\\\','\\',$s);
    		return "'".str_replace("\\'",$this->replaceQuote,$s)."'";
    	}
    }

    public function getPicklistValues($fieldname)
    {    	
    	$allpicklists=$this->getAllPicklistsValuesModule();
        return (empty($allpicklists[$fieldname]) ? array() : $allpicklists[$fieldname]);
    }
    public function getAllPicklistsValuesModule(){
        $module = $this->getModule();
    	

    	$api_cache_id='getPicklistValues'.$module;
    	$PickListValues = Yii::app()->cache->get( $api_cache_id  );

    	// If the results were false, then we have no valid data, so load it
    	if($PickListValues===false){
    		$clientvtiger=$this->getClientVtiger();
                if(!$clientvtiger) Yii::log('login failed',CLogger::LEVEL_ERROR);
    		else{
    			$PickListValues =unserialize($clientvtiger->doInvoke('getPicklistValues',array('module'=>$module)));
    			$wasError = $clientvtiger->lastError();
    			if($wasError) {
    				Yii::log(CVarDumper::dumpAsString($clientvtiger->lastError()),CLogger::LEVEL_ERROR);
    			}

    		}
    		Yii::app()->cache->set( $api_cache_id , $PickListValues, $this->defaultCacheTimeout );
    	}
    	return $PickListValues;
    }
    public function getModule() {
    	return $this->module;
    }

    public function setModule($module) {
    	$this->module=$module;
    }
    public function getModuleName() {
    	if(($cache=Yii::app()->cache)!==null) {
    		if (($modname=$cache->get('yiicpng.sidebar.listmodules'))!==false) {
    			$modname=$modname[$this->module];
    		} else {
    			$modname=Yii::t('core', $this->module);
    		}
    	} else {
    		$modname=Yii::t('core', $this->module);
    	}
    	return $modname;
    }

    public function translateAttributeLabels() {
    	$al=array();
    	$ga=$this->_fieldinfo;
    	if (empty($ga) or count($ga)==0) return $ga;
    	foreach ($ga as $finfo) {
    		if (is_array($finfo) and !empty($finfo['label'])) // this is to send just labels
    			$al[$finfo['name']]=$finfo['label'];
    	}
    	$al=$this->vtGetTranslation($al,$this->getModule());
    	foreach ($this->_fieldinfo as &$finfo) {
    		if (is_array($finfo) and !empty($finfo['label'])) // this is to not overwrite values, just labels
    			$finfo['label']=$al[$finfo['name']];
    	}
    	return $al;
    }

    public function validModule() {
    	$module=$this->getModule();    
        $api_cache_id='yiicpng.sidebar.availablemodules';
    	$valid = Yii::app()->cache->get( $api_cache_id  );

        if($valid===false){
    	$clientvtiger=$this->getClientVtiger();
        if(!$clientvtiger)
    		Yii::log('login failed',CLogger::LEVEL_ERROR);
    	else {
			$listModules = $clientvtiger->doListTypes();
			// flatten array
			$flatlm=array();
			foreach($listModules AS $key=>$schema) {
				$flatlm[$key]=$schema['name'];
			}
			$listModules = $clientvtiger->doTranslate($flatlm, Yii::app()->getLanguage(), '');
			if (is_array($listModules)) {
				reset($flatlm);
				foreach($listModules AS $moduleName) {
					if ((Yii::app()->vtyiicpngScope=='CPortal' and in_array(current($flatlm),Yii::app()->notSupportedModules[Yii::app()->vtyiicpngScope]))
					 or (Yii::app()->vtyiicpngScope=='vtigerCRM' and !in_array(current($flatlm),Yii::app()->notSupportedModules[Yii::app()->vtyiicpngScope])))
						$valid[current($flatlm)] = array('module'=>current($flatlm),'name'=>$moduleName);
					next(($flatlm));
				}
				// cache until next execution
				Yii::app()->cache->set('yiicpng.sidebar.listmodules',$listModules);
				Yii::app()->cache->set('yiicpng.sidebar.availablemodules',$valid);
			} else {
				$valid=array(array('module'=>'notranslate','name'=>Yii::t('core','errNoTranslateFunction')));
			}
    		}
    	}
    	if (is_array($valid)) {
			if(isset($valid[$module])) return true;
    	}
    	return false;
    }

    public function setLastError($msg) {
    	$this->_lasterror=$msg;
    }

    public function getLastError() {
    	$error = '';
    	$le=$this->getErrors();
    	if (is_array($le)) {
    		foreach ($le as $err) {
    			if (is_array($err)) {
    				$error.=$err[0].'<br/>';
    			} else {
    				$error.=$err.'<br/>';
    			}
    		}
    	}
    	$error.=$this->_lasterror.'<br/>';
    	return $error;
    }

	public function getAttachmentFolderId() {
		$clientvt = $this->getClientVtiger();
		// First we look for the folder defined in configuration
		$command="select id from documentfolders where foldername='".Yii::app()->attachment_folder."'";
		$recordInfo = $clientvt->doQuery($command);
		if ($recordInfo)
			return $recordInfo[0]['id'];
		// Next we look for the Default folder
		$command="select id from documentfolders where foldername='Default'";
		$recordInfo = $clientvt->doQuery($command);
		if ($recordInfo)
			return $recordInfo[0]['id'];
		// Finally get the first folder we can find
		$command="select id from documentfolders limit 1";
		$recordInfo = $clientvt->doQuery($command);
		if ($recordInfo)
			return $recordInfo[0]['id'];
		return false;  // error
	}
}
?>

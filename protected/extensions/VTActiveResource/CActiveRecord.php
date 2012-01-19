<?php
/**
 * CActiveRecord class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CActiveRecord is the base class for classes representing relational data.
 *
 * It implements the active record design pattern, a popular Object-Relational Mapping (ORM) technique.
 * Please check {@link http://www.yiiframework.com/doc/guide/database.ar the Guide} for more details
 * about this class.
 *
 * @property CDbCriteria $dbCriteria The query criteria that is associated with this model.
 * This criteria is mainly used by {@link scopes named scope} feature to accumulate
 * different criteria specifications.
 * @property CActiveRecordMetaData $metaData The meta for this AR class.
 * @property CDbConnection $dbConnection The database connection used by active record.
 * @property CDbTableSchema $tableSchema The metadata of the table that this AR belongs to.
 * @property CDbCommandBuilder $commandBuilder The command builder used by this AR.
 * @property array $attributes Attribute values indexed by attribute names.
 * @property boolean $isNewRecord Whether the record is new and should be inserted when calling {@link save}.
 * This property is automatically set in constructor and {@link populateRecord}.
 * Defaults to false, but it will be set to true if the instance is created using
 * the new operator.
 * @property mixed $primaryKey The primary key value. An array (column name=>column value) is returned if the primary key is composite.
 * If primary key is not defined, null will be returned.
 * @property mixed $oldPrimaryKey The old primary key value. An array (column name=>column value) is returned if the primary key is composite.
 * If primary key is not defined, null will be returned.
 * @property string $tableAlias The default table alias.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
abstract class CActiveRecord extends VTActiveResource
{
	const BELONGS_TO='CBelongsToRelation';
	const HAS_ONE='CHasOneRelation';
	const HAS_MANY='CHasManyRelation';
	const MANY_MANY='CManyManyRelation';
	const STAT='CStatRelation';

}


/**
 * CBaseActiveRelation is the base class for all active relations.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 */
class CBaseActiveRelation extends CComponent
{
	/**
	 * @var string name of the related object
	 */
	public $name;
	/**
	 * @var string name of the related active record class
	 */
	public $className;
	/**
	 * @var mixed the foreign key in this relation
	 */
	public $foreignKey;
	/**
	 * @var mixed list of column names (an array, or a string of names separated by commas) to be selected.
	 * Do not quote or prefix the column names unless they are used in an expression.
	 * In that case, you should prefix the column names with 'relationName.'.
	 */
	public $select='*';
	/**
	 * @var string WHERE clause. For {@link CActiveRelation} descendant classes, column names
	 * referenced in the condition should be disambiguated with prefix 'relationName.'.
	 */
	public $condition='';
	/**
	 * @var array the parameters that are to be bound to the condition.
	 * The keys are parameter placeholder names, and the values are parameter values.
	 */
	public $params=array();
	/**
	 * @var string GROUP BY clause. For {@link CActiveRelation} descendant classes, column names
	 * referenced in this property should be disambiguated with prefix 'relationName.'.
	 */
	public $group='';
	/**
	 * @var string how to join with other tables. This refers to the JOIN clause in an SQL statement.
	 * For example, <code>'LEFT JOIN users ON users.id=authorID'</code>.
	 * @since 1.1.3
	 */
	public $join='';
	/**
	 * @var string HAVING clause. For {@link CActiveRelation} descendant classes, column names
	 * referenced in this property should be disambiguated with prefix 'relationName.'.
	 */
	public $having='';
	/**
	 * @var string ORDER BY clause. For {@link CActiveRelation} descendant classes, column names
	 * referenced in this property should be disambiguated with prefix 'relationName.'.
	 */
	public $order='';

	/**
	 * Constructor.
	 * @param string $name name of the relation
	 * @param string $className name of the related active record class
	 * @param string $foreignKey foreign key for this relation
	 * @param array $options additional options (name=>value). The keys must be the property names of this class.
	 */
	public function __construct($name,$className,$foreignKey,$options=array())
	{
		$this->name=$name;
		$this->className=$className;
		$this->foreignKey=$foreignKey;
		foreach($options as $name=>$value)
			$this->$name=$value;
	}

	/**
	 * Merges this relation with a criteria specified dynamically.
	 * @param array $criteria the dynamically specified criteria
	 * @param boolean $fromScope whether the criteria to be merged is from scopes
	 */
	public function mergeWith($criteria,$fromScope=false)
	{
		if($criteria instanceof CDbCriteria)
			$criteria=$criteria->toArray();
		if(isset($criteria['select']) && $this->select!==$criteria['select'])
		{
			if($this->select==='*')
				$this->select=$criteria['select'];
			else if($criteria['select']!=='*')
			{
				$select1=is_string($this->select)?preg_split('/\s*,\s*/',trim($this->select),-1,PREG_SPLIT_NO_EMPTY):$this->select;
				$select2=is_string($criteria['select'])?preg_split('/\s*,\s*/',trim($criteria['select']),-1,PREG_SPLIT_NO_EMPTY):$criteria['select'];
				$this->select=array_merge($select1,array_diff($select2,$select1));
			}
		}

		if(isset($criteria['condition']) && $this->condition!==$criteria['condition'])
		{
			if($this->condition==='')
				$this->condition=$criteria['condition'];
			else if($criteria['condition']!=='')
				$this->condition="({$this->condition}) AND ({$criteria['condition']})";
		}

		if(isset($criteria['params']) && $this->params!==$criteria['params'])
			$this->params=array_merge($this->params,$criteria['params']);

		if(isset($criteria['order']) && $this->order!==$criteria['order'])
		{
			if($this->order==='')
				$this->order=$criteria['order'];
			else if($criteria['order']!=='')
				$this->order=$criteria['order'].', '.$this->order;
		}

		if(isset($criteria['group']) && $this->group!==$criteria['group'])
		{
			if($this->group==='')
				$this->group=$criteria['group'];
			else if($criteria['group']!=='')
				$this->group.=', '.$criteria['group'];
		}

		if(isset($criteria['join']) && $this->join!==$criteria['join'])
		{
			if($this->join==='')
				$this->join=$criteria['join'];
			else if($criteria['join']!=='')
				$this->join.=' '.$criteria['join'];
		}

		if(isset($criteria['having']) && $this->having!==$criteria['having'])
		{
			if($this->having==='')
				$this->having=$criteria['having'];
			else if($criteria['having']!=='')
				$this->having="({$this->having}) AND ({$criteria['having']})";
		}
	}
}


/**
 * CStatRelation represents a statistical relational query.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 */
class CStatRelation extends CBaseActiveRelation
{
	/**
	 * @var string the statistical expression. Defaults to 'COUNT(*)', meaning
	 * the count of child objects.
	 */
	public $select='COUNT(*)';
	/**
	 * @var mixed the default value to be assigned to those records that do not
	 * receive a statistical query result. Defaults to 0.
	 */
	public $defaultValue=0;

	/**
	 * Merges this relation with a criteria specified dynamically.
	 * @param array $criteria the dynamically specified criteria
	 * @param boolean $fromScope whether the criteria to be merged is from scopes
	 */
	public function mergeWith($criteria,$fromScope=false)
	{
		if($criteria instanceof CDbCriteria)
			$criteria=$criteria->toArray();
		parent::mergeWith($criteria,$fromScope);

		if(isset($criteria['defaultValue']))
			$this->defaultValue=$criteria['defaultValue'];
	}
}


/**
 * CActiveRelation is the base class for representing active relations that bring back related objects.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
class CActiveRelation extends CBaseActiveRelation
{
	/**
	 * @var string join type. Defaults to 'LEFT OUTER JOIN'.
	 */
	public $joinType='LEFT OUTER JOIN';
	/**
	 * @var string ON clause. The condition specified here will be appended to the joining condition using AND operator.
	 */
	public $on='';
	/**
	 * @var string the alias for the table that this relation refers to. Defaults to null, meaning
	 * the alias will be the same as the relation name.
	 */
	public $alias;
	/**
	 * @var string|array specifies which related objects should be eagerly loaded when this related object is lazily loaded.
	 * For more details about this property, see {@link CActiveRecord::with()}.
	 */
	public $with=array();
	/**
	 * @var boolean whether this table should be joined with the primary table.
	 * When setting this property to be false, the table associated with this relation will
	 * appear in a separate JOIN statement.
	 * If this property is set true, then the corresponding table will ALWAYS be joined together
	 * with the primary table, no matter the primary table is limited or not.
	 * If this property is not set, the corresponding table will be joined with the primary table
	 * only when the primary table is not limited.
	 */
	public $together;
	/**
	 * @var mixed scopes to apply
	 * Can be set to the one of the following:
	 * <ul>
	 * <li>Single scope: 'scopes'=>'scopeName'.</li>
	 * <li>Multiple scopes: 'scopes'=>array('scopeName1','scopeName2').</li>
	 * </ul>
	 * @since 1.1.9
	 */
	 public $scopes;

	/**
	 * Merges this relation with a criteria specified dynamically.
	 * @param array $criteria the dynamically specified criteria
	 * @param boolean $fromScope whether the criteria to be merged is from scopes
	 */
	public function mergeWith($criteria,$fromScope=false)
	{
		if($criteria instanceof CDbCriteria)
			$criteria=$criteria->toArray();
		if($fromScope)
		{
			if(isset($criteria['condition']) && $this->on!==$criteria['condition'])
			{
				if($this->on==='')
					$this->on=$criteria['condition'];
				else if($criteria['condition']!=='')
					$this->on="({$this->on}) AND ({$criteria['condition']})";
			}
			unset($criteria['condition']);
		}

		parent::mergeWith($criteria);

		if(isset($criteria['joinType']))
			$this->joinType=$criteria['joinType'];

		if(isset($criteria['on']) && $this->on!==$criteria['on'])
		{
			if($this->on==='')
				$this->on=$criteria['on'];
			else if($criteria['on']!=='')
				$this->on="({$this->on}) AND ({$criteria['on']})";
		}

		if(isset($criteria['with']))
			$this->with=$criteria['with'];

		if(isset($criteria['alias']))
			$this->alias=$criteria['alias'];

		if(isset($criteria['together']))
			$this->together=$criteria['together'];
	}
}


/**
 * CBelongsToRelation represents the parameters specifying a BELONGS_TO relation.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
class CBelongsToRelation extends CActiveRelation
{
}


/**
 * CHasOneRelation represents the parameters specifying a HAS_ONE relation.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
class CHasOneRelation extends CActiveRelation
{
	/**
	 * @var string the name of the relation that should be used as the bridge to this relation.
	 * Defaults to null, meaning don't use any bridge.
	 * @since 1.1.7
	 */
	public $through;
}


/**
 * CHasManyRelation represents the parameters specifying a HAS_MANY relation.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
class CHasManyRelation extends CActiveRelation
{
	/**
	 * @var integer limit of the rows to be selected. It is effective only for lazy loading this related object. Defaults to -1, meaning no limit.
	 */
	public $limit=-1;
	/**
	 * @var integer offset of the rows to be selected. It is effective only for lazy loading this related object. Defaults to -1, meaning no offset.
	 */
	public $offset=-1;
	/**
	 * @var string the name of the column that should be used as the key for storing related objects.
	 * Defaults to null, meaning using zero-based integer IDs.
	 */
	public $index;
	/**
	 * @var string the name of the relation that should be used as the bridge to this relation.
	 * Defaults to null, meaning don't use any bridge.
	 * @since 1.1.7
	 */
	public $through;

	/**
	 * Merges this relation with a criteria specified dynamically.
	 * @param array $criteria the dynamically specified criteria
	 * @param boolean $fromScope whether the criteria to be merged is from scopes
	 */
	public function mergeWith($criteria,$fromScope=false)
	{
		if($criteria instanceof CDbCriteria)
			$criteria=$criteria->toArray();
		parent::mergeWith($criteria,$fromScope);
		if(isset($criteria['limit']) && $criteria['limit']>0)
			$this->limit=$criteria['limit'];

		if(isset($criteria['offset']) && $criteria['offset']>=0)
			$this->offset=$criteria['offset'];

		if(isset($criteria['index']))
			$this->index=$criteria['index'];
	}
}


/**
 * CManyManyRelation represents the parameters specifying a MANY_MANY relation.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
class CManyManyRelation extends CHasManyRelation
{
}


/**
 * CActiveRecordMetaData represents the meta-data for an Active Record class.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: CActiveRecord.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.ar
 * @since 1.0
 */
class CActiveRecordMetaData extends VTActiveResourceMetaData
{
	/**
	 * @var CDbTableSchema the table schema information
	 */
	public $tableSchema;
	/**
	 * @var array table columns
	 */
	public $columns;
	/**
	 * @var array list of relations
	 */
	public $relations=array();
	/**
	 * @var array attribute default values
	 */
	public $attributeDefaults=array();

	private $_model;

	/**
	 * Adds a relation.
	 *
	 * $config is an array with three elements:
	 * relation type, the related active record class and the foreign key.
	 *
	 * @throws CDbException
	 * @param string $name $name Name of the relation.
	 * @param array $config $config Relation parameters.
     * @return void
	 * @since 1.1.2
	 */
	public function addRelation($name,$config)
	{
		if(isset($config[0],$config[1],$config[2]))  // relation class, AR class, FK
			$this->relations[$name]=new $config[0]($name,$config[1],$config[2],array_slice($config,3));
		else
			throw new CDbException(Yii::t('yii','Active record "{class}" has an invalid configuration for relation "{relation}". It must specify the relation type, the related active record class and the foreign key.', array('{class}'=>get_class($this->_model),'{relation}'=>$name)));
	}

	/**
	 * Checks if there is a relation with specified name defined.
	 *
	 * @param string $name $name Name of the relation.
	 * @return boolean
	 * @since 1.1.2
	 */
	public function hasRelation($name)
	{
		return isset($this->relations[$name]);
	}

	/**
	 * Deletes a relation with specified name.
	 *
	 * @param string $name $name
	 * @return void
	 * @since 1.1.2
	 */
	public function removeRelation($name)
	{
		unset($this->relations[$name]);
	}
}

<?php
/**
 * @author Johannes "Haensel" Bauer
 * @since version 0.1
 * @version 0.1
 */
/**
 * This is the ActiveResource version of the CActiveMetaData class. It is used by ActiveResources to define
 * vital parameters for a RESTful communication between Yii and the service.
 */
class VTActiveResourceMetaData
{

    public $properties;     //The properties of the resource according to the schema configuration
    public $attributeDefaults=array('salutationtype','firstname','contact_no','phone','lastname','mobile','account_id','homephone',
        'leadsource','otherphone','title','fax','department','birthday','email','contact_id','assistant','yahooid','assistantphone',
        'donotcall','emailoptout','assigned_user_id','reference','notify_owner','createdtime','modifiedtime','portal','support_start_date',
        'mailingstreet','otherstreet','mailingcity','othercity','mailingstate','otherstate','mailingzip','otherzip','mailingcountry',
        'othercountry','mailingpobox','otherpobox','description','posizione_','mail_region','other_region','id');

    public $schema; 
    public $site;
    public $container;
    public $embedded;
    public $fileextension;
    public $idProperty;
    public $contenttype;
    public $accepttype;

    public $username;
    public $password;


    private $_model;

    public function __construct($model)
    {
    	$this->_model=$model;

    	$this->schema=null;
    	foreach($model->rest() as $option=>$value)
    	  if(property_exists($this, $option))
    		$this->$option=$value;

    	$this->properties=$this->getProperties();
    }

    /**
     * Define the attributes of the model. These are set to all public properties by default.
     * Override this method if you want to allow specific properties only.
     * @return an array of properties
     */
    protected function getProperties()
    {
        $names=array();
        $attrs=$this->_model->getAttributesArray();
        if (is_array($attrs))
        foreach($attrs as $attribute=>$value) {
          if (is_array($value)) continue;
          $names[$attribute]=$value;
        }

        return $this->properties=$names;
    }

}

?>

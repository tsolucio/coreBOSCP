<h1><?php 
$elookup=Entity::getLookupFieldValue($this->lookupfield, $data);
echo $this->table.' : '.$elookup; ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$data, 
	'attributes'=>Entity::model($this->table)->getDetailViewFieldsID($data[$this->entityid]),
	'cssFile'=>BASEURL.'/themes/'.Yii::app()->getTheme()->getName().'/css/list.css',
	));
?>
<script type="text/javascript">
var table = '<?php  echo $this->table; ?>';
var viewEntityId = '<?php echo $data[$this->entityid]; ?>';
breadCrumb.add({ icon: 'view', href: 'javascript:chive.goto(\'entity/'+table+'/view/<?php echo $data[$this->entityid]; ?>\')', text: '<?php echo $elookup; ?>'});
sideBar.activate(0);
tableBrowse.setup();
</script>
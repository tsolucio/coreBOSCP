<?php include Yii::app()->layoutPath.'/_tableMenu.php';?>

<div id="content-inner">
	<?php echo $content; ?>
</div>

<script type="text/javascript">
var schema = '<?php echo $this->schema; ?>';
var table = '<?php echo $this->table; ?>';
breadCrumb.add({ icon: 'browse', href: 'javascript:chive.goto(\'entity/' + table + '/browse\')', text: table});
breadCrumb.show();
sideBar.activate(0);
</script>
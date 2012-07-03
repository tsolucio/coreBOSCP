<div class="yiiForm">
<?php $form=$this->beginWidget('vtyiicpngActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?><fieldset>
    <legend><?php echo Yii::t('core','search') ?></legend>
    <input type="hidden" id='fnumrows' name='fnumrows' value=1>
    <table id="search_table">
    <tr>
    <td align="center"><button type="button"><?php echo yii::t('core','addcondition'); ?></button></td>
    <td colspan="4"></td>
    </tr>
    <tr>
	<td style="padding: 3px;">
	<input type="hidden" id='fdelete1' name='fdelete1' value=0>
	<select name='fcol1' id='fcol1' onchange='updatefOptions(this);'>
	<option value=''><?php echo yii::t('core', 'none'); ?></option>
    <?php
	foreach($fields as $field) {
		if (!is_array($field) || empty($field['name'])) continue;  // jump values, process only fields
		if($field['name'] !='id') {
			$fieldname=$field['name'];
			$fieldtype=substr($field['typeofdata'],0,1);
			$fieldlabel=$field['label'];
			$uitype=intval($uitypes[$fieldname]);
			echo "<option value='$fieldname' fieldtype='$fieldtype'>$fieldlabel</option>";
		}
	}; ?>
	</select>
	</td>
	<td style="padding: 3px;">
	<select name='fop1' id='fop1''>
	<option value=''><?php echo yii::t('core', 'none'); ?></option>
	</select>
	</td>
	<td style="padding: 3px;">
	<input name="fval1" id="fval1" class="repBox" type="text" value="">
	</td>
	<td style="padding: 3px;width:60px">
	<select name='fglue1' id='fglue1' style="width:80%;margin:auto;">
	<option value='and' selected><?php echo yii::t('core', 'and'); ?></option>
	<option value='or'><?php echo yii::t('core', 'or'); ?></option>
	</select>
	</td>
	<td style="padding: 3px;">
	<a onclick="jQuery(this).closest('tr').find('td input:hidden').val(1);jQuery(this).closest('tr').hide();" href="javascript:;">
	<img src="<?php echo ICONPATH . '/16/delete.png'; ?>" align="absmiddle" title="<?php echo yii::t('core','delete'); ?>..." border="0" style="display: none;"></a>
	</td>
    </tr></table>
<script type="text/javascript">
		var i = 2;
$("button").click(function() {
    var newrow = $("#search_table tr:first").next().clone();
    newrow.find("input,select").each(function() {
        $(this).attr({
            'id': function(_, id) { return id.substring(0,id.length-1) + i },
            'name': function(_, name) { return name.substring(0,name.length-1) + i },
            'value': function(_, id) {
                idname = $(this).attr('id');
                if (idname.substring(0,5) == 'fglue') return 'gluey';
                else if (idname.substring(0,7) == 'fdelete') return 0;
                else return ''; }
          });
    });
    newrow.find("img").show();
    newrow.appendTo("#search_table");
    $("#fnumrows").val($("#search_table tr").length-1);
    i++;
});</script>
    
	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>
</fieldset>
<?php $this->endWidget(); ?>

</div><!-- search-form -->
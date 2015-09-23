<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */
?>
<h2>
  <?php echo $this->translate('Compare Membership Packages') ?>
</h2>
<?php
	if (!count($this->levels)):
?>
	<ul class="form-errors">
    	<li>
      		<?php echo $this->translate('There are no plans yet.');?>
      	</li>
  	</ul>
<?php 
	return; endif; 
?>
<?php if ($this->check):?>
	<ul class="form-notices"><li><?php echo $this->translate('Your changes have been saved.');?></li></ul>
<?php endif;?>
<?php
	$level_feature = 0;
	if (key_exists($this->feature,$this->levels))
		$level_feature = $this->feature;
	else 
	{
		foreach ($this->levels as $id=>$level)
			if (!$level_feature)
				$level_feature = $id;
	}	
?>
<p>
	(<?php echo $this->translate('Select a member level to display the "Most Popular" icon');?>)
</p>
<form id="form_compare" action="" method="post">
	<div id="div_compare_wrapper" class="compare_admincp">
		<table id="tbl_compare" cellpadding="0" cellspacing="0">
			<tbody>
				<tr id="trHeader">
					<th class="th_title"></th>
					<?php foreach ($this->levels as $id=>$level):?>
						<th class="th_package_title">
							<input type="radio" name="feature" value="<?php echo $id;?>" title="<?php echo $this->translate('Select most popular');?>" <?php if ($id == $level_feature) echo "checked='checked'";?>>
							<?php echo $level;?>
						</th>
					<?php endforeach;?>
				</tr>
				<tr id="tr_features_template" style="display:none;">
					<td class="td_comparison_title">
						<input type="text" class="txt_title required" value="" name="compare[99999999][title]">
						<img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/delete_16.png" class="img_delete" onclick="deleteFeature(this);">
					</td>
					<?php foreach ($this->levels as $level_id => $level):?>
						<td class="td_feature" id="td_feature_<?php echo $level_id;?>">					
							<div>
								<span class="switch_type js_hover_title" onclick="switchType(this);"><img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/arrow_switch.png" title="<?php echo $this->translate('Toggle Comparison Value (Yes, No or Text Field)');?>"></span>								
								<span class="div_text">
									<input type="text" class="txt_package_feature" id="txt_package_feature_1" name="compare[99999999][package][<?php echo $level_id;?>][text]" style="width:60px;">
								</span>
								
								<span class="div_radio js_hover_title" style="display: none;" title="<?php echo $this->translate('Toggle Yes or No');?>">
									<input type="hidden" class="hid_input" id="hid_input_<?php echo $level_id;?>" name="compare[99999999][package][<?php echo $level_id?>][radio]" value="0">
									<img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/check_16.png" class="img_accept" style="" onclick="toggleAccept(this);">						
									<img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/un_check_16.png" class="img_reject" style="display:none;" onclick="toggleAccept(this);">
								</span>							
							</div>
						</td>
					<?php endforeach;?>
				</tr>
				<?php if (count($this->array_compares)):?>
					<?php foreach ($this->array_compares as $i=>$compare):?>
						<tr class="tr tr_feature">
							<td class="td_comparison_title">
								<input type="text" class="txt_title required" value="<?php echo $compare['title']?>" name="compare[<?php echo $i+1;?>][title]">
								<img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/delete_16.png" class="img_delete" onclick="deleteFeature(this);">
							</td>
							<?php foreach ($this->levels as $level_id => $level):?>
								<td class="td_feature" id="td_feature_<?php echo $level_id;?>">					
									<div>
										<span class="switch_type js_hover_title" onclick="switchType(this);"><img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/arrow_switch.png" title="<?php echo $this->translate('Toggle Comparison Value (Yes, No or Text Field)');?>"></span>								
										<span class="div_text" style="<?php if ($compare['package'][$level_id]['radio'] != '0') echo "display: none;";?>">
											<input type="text" class="txt_package_feature" value="<?php echo $compare['package'][$level_id]['text']?>" id="txt_package_feature_<?php echo $i+1;?>" name="compare[<?php echo $i+1;?>][package][<?php echo $level_id;?>][text]" style="width:60px;">
										</span>
										
										<span class="div_radio js_hover_title" style="<?php if ($compare['package'][$level_id]['radio'] == '0') echo "display: none;";?>" title="<?php echo $this->translate('Toggle Yes or No');?>">
											<input type="hidden" class="hid_input" id="hid_input_<?php echo $level_id;?>" name="compare[<?php echo $i+1;?>][package][<?php echo $level_id?>][radio]" value="<?php echo $compare['package'][$level_id]['radio']?>">
											<img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/check_16.png" class="img_accept" style="<?php if ($compare['package'][$level_id]['radio']=='2') echo "display:none;";?>" onclick="toggleAccept(this);">						
											<img src="<?php echo $this->baseUrl();?>/application/modules/Sladvsubscription/externals/images/un_check_16.png" class="img_reject" style="<?php if ($compare['package'][$level_id]['radio'] == '0' || $compare['package'][$level_id]['radio']=='1') echo "display:none;";?>" onclick="toggleAccept(this);">
										</span>
									</div>
								</td>
							<?php endforeach;?>
						</tr>
					<?php endforeach;;?>
				<?php endif;?>
				<tr id="tr_last">
					<td colspan="<?php echo count($this->levels) + 1;?>" class="td_center" onclick="addRow();">
						<?php echo $this->translate('Add new feature');?>		
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<button type="submit"><?php echo $this->translate('Save');?></button>
</form>
<script>
function addRow()
{
	var oDate = new Date();
	var sNewId = 'js_new_' + oDate.getTime();
	
	var sTemplate = '<tr class="tr tr_feature" id="' + sNewId + '">' + $('tr_features_template').clone().get('html') + '</tr>';
	
	Elements.from(sTemplate).inject($('tr_last'),'before');
	
	/* replace the _ID_ part of the name*/
	var iIterator = 0;
	var sNew = '';
	$$('.tr_feature').each(function(e){		
		e.getElements('.txt_package_feature, .txt_title, .hid_input').each(function(el){
			sNew = el.get('name').replace(/compare\[[0-9]+\]/, 'compare[' + iIterator + ']');				
			el.set('name', sNew);		
		});
		iIterator++;
	});
}

function switchType(oObj)
{
	$(oObj).getParent().getElements('span').each(function(e){
		if (!e.hasClass('switch_type') && !e.hasClass('js_hover_info'))
		{
			e.toggle();
		}
	});
	
	if ($(oObj).getParent().getElements('.div_text').getStyle('display') != 'none')
	{
		$(oObj).getParent().getElements('.hid_input').set('value','0');
	}
	else
	{
		$(oObj).getParent().getElements('.hid_input').set('value', $(oObj).getParent().getElements('.img_accept').getStyle('display') != 'none' ? '1' : '2' );
	}
}
function toggleAccept(oObj)
{
	$(oObj).getParent().getElements('img').toggle();
	$(oObj).getParent().getElements('.hid_input').set('value', $(oObj).getParent().getElements('.img_accept').getStyle('display') != 'none' ? '1' : '2' );
}
function deleteFeature(oObj)
{
	$(oObj).getParent().getParent().destroy();

	/* replace the _ID_ part of the name*/
	var iIterator = 0;
	var sNew = '';
	$$('.tr_feature').each(function(e){		
		e.getElements('.txt_package_feature, .txt_title, .hid_input').each(function(el){
			sNew = el.get('name').replace(/compare\[[0-9]+\]/, 'compare[' + iIterator + ']');				
			el.set('name', sNew);		
		});
		iIterator++;
	});
}

new Form.Validator.Inline($('form_compare'), {		    
	useTitles: true
});
</script>
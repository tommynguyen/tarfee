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
<?php
	$api = Engine_Api::_()->sladvsubscription();
	$levels = $api->getLevels(true);
	$compares = $api->getCompares();
	$settings = $api->getSettings();
	$plans = $this->form->getPackages();
	$level_plans = array();	
	foreach ($plans as $plan)
	{
		$level_plans[$plan->level_id][] = $plan; 
	}
	
	$level_feature = 0;
	$feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('advsubscription.popular', '0');
	if (key_exists($feature,$levels))
		$level_feature = $feature;
	else 
	{
		foreach ($levels as $id=>$level)
			if (!$level_feature)
				$level_feature = $id;
	}	
?>

<div class="plan">
	<div class="plan-table">
		<table cellpadding="0" cellspacing="1px">
			<tr>
				<th class="title-width"><?php echo $this->translate('Choose Plan');?></th>
				<?php $index = 0;?>
				<?php foreach ($levels as $id=>$level):?>
					<th class="detail-width" style="background-color: <?php if ($index % 2 == 0) echo $settings['odd_header_column_color']; else echo $settings['even_header_column_color'];$index++;?>;<?php echo $api->getStyle('header');?>">
						<?php echo $level?>
						<?php if ($id == $level_feature):?>
							<img class="popular_icon" src="<?php echo $this->baseUrl().'/'.$settings['most_popular_icon']?>" alt="<?php echo $this->translate('most popular');?>" />
						<?php endif;?>
					</th>
				<?php endforeach;?>
			</tr>
			<?php $index = 0;
			// Add Tips
			$tips = array(
			1 => $this -> translate('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ratione pariatur nihil, voluptatum magni voluptatem voluptate atque nobis tenetur omnis eos quisquam quis nulla animi quia sunt neque, accusamus rem officia.'),
			2 => $this -> translate('Tip for message to non follower'),
			// add more the same with above
			);?>
			<?php foreach ($compares as $compare):?>				
				<tr style="background-color: <?php if ($index % 2 == 0) echo $settings['odd_row_color']; else echo $settings['even_row_color'];$index++;?>;">
					<td style="<?php echo $api->getStyle('row');?>">
						<?php echo $compare['title']?>
						<?php if(!empty($tips[$index])):?>
						<div class="tf-settings-info">
							<?php echo $tips[$index];?>
						</div>
						<?php endif;?>
					</td>
					<?php foreach ($levels as $id=>$level):?>
						<td>
							<?php if ($compare['package'][$id]['radio'] == '0'):?>
								<span style="<?php echo $api->getStyle('cell');?>"><?php echo $compare['package'][$id]['text'];?></span>
							<?php elseif ($compare['package'][$id]['radio'] == '1'):?>
								<img src="<?php echo $this->baseUrl().'/'.$settings['ticker_image_link']?>" alt="<?php echo $this->translate('yes');?>"/>
							<?php else :?>
								<img src="<?php echo $this->baseUrl().'/'.$settings['x_image_link']?>" alt="<?php echo $this->translate('no');?>"/>
							<?php endif;?>
						</td>
					<?php endforeach;?>
				</tr>
			<?php endforeach;?>
			<tr>
				<td class="no-border">&nbsp;</td>
				<?php foreach ($levels as $id=>$level):?>
					<td>
						<form method="post">
							<?php if (isset($level_plans[$id])):?>
								<select name="package_id" onchange="changePackage(this);" style="<?php if (count($level_plans[$id])<2) echo "display:none;"; ?>">
									<?php foreach ($level_plans[$id] as $plan):?>
										<option value="<?php echo $plan->getIdentity();?>"><?php echo $plan->getTitle();?></option>
									<?php endforeach;?>
								</select>
								<br/>
								<p class="price_title">
									<?php $index = 0;?>
									<?php foreach ($level_plans[$id] as $plan):?>
										<span id="plan_<?php echo $plan->getIdentity();?>" class="plan" style="<?php if ($index != 0) echo 'display:none;';$index++; ?>"><?php echo $api->getTextPrice($plan);?></span>
									<?php endforeach;?>
								</p>
								<button name="submit" id="submit" type="submit" tabindex="4"><?php echo $this->translate('Signup Now!');?></button>
							<?php endif;?>
						</form>
					</td>
				<?php endforeach;?>
			</tr>
		</table>
	</div>
</div>
<script>
function changePackage(element)
{
	var parent = $(element).getParent();
	parent.getElements('.plan').each(function(e)
	{
		if (e.get('id') == 'plan_' + $(element).value)
			e.show();
		else
			e.hide();
	});
}
</script>
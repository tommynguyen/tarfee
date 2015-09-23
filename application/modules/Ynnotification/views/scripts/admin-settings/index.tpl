<?php
/**
 * YouNet Company
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2012 YouNet Company
 * @version    $Id: index.tpl
 * @author     Luan Nguyen
 */
?>
<h2>
  <?php echo $this->translate('Adv Notification') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>   
<style type="text/css">
#head_main_menu-label,
#head_notifications-label
{
	color: Black;
	text-transform: uppercase;
	margin-bottom: -10px;
}
.settings #clear-label 
{
	display: none;
}
#clear-wrapper
{
	margin-top: -46px;
	
	margin-left: -66px;
}
#submit-wrapper
{
	float: left;
	padding-right: 10px;
}
</style>
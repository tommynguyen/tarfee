<?php
/**
 * SocialEngine
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

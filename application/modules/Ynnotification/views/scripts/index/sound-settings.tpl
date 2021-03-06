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
<div class="headline">
  <h2>
    <?php echo $this->translate('My Settings');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>   

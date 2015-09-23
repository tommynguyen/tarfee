<h2><?php echo $this->translate('Advanced Comments Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<?php endif; ?>

<?php
    $ynfeed = Engine_Api::_()->yncomment()->getEnabledModule(array('resource_type' => 'ynfeed', 'checkModuleExist' => true));
    if(($ynfeed && Engine_Api::_()->yncomment()->getEnabledModule(array('resource_type' => 'ynfeed')))):?>
    <div class="tip">
        <span> 
         <?php echo $this->translate("Below settings is not applied to Advanced Feeds plugin, you may go to “Advanced Activity Settings” to configure respective settings."); ?>
        </span>
    </div>
    <?php endif;?> 

<div class='yncomment_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
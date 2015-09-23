<?php if (count($this->navigation)):
    ?>
    <div class="headline">
        <h2>
            <?php echo $this -> translate('Invite Your Friends'); ?>
        </h2>
        <div class="tabs">
            <?php
			// Render the menu
			echo $this -> navigation() -> menu() -> setContainer($this -> navigation) -> render();
            ?>
        </div>
    </div>
<?php endif; ?>
<?php echo $this->translate('Success! Please continue to  '); ?><a href="<?php echo $this->url(array('module' => 'contactimporter', 'controller' => 'index'), 'default', true);?>"><?php echo $this->translate('Import Connection'); ?></a><?php echo $this->translate(' or return to ');?><?php  echo $this->htmlLink(array('route'=>'default'), $this->translate('home'), array('class'=>'buttonlink icon_back'));    ?> 
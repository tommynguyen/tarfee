<?php if( count($this->quickNavigation) > 0 ): ?>
	<?php //echo $this->formFilter->setAttrib('class', 'filters')->render($this) ?>
    
  <div class="quicklinks">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->quickNavigation)
        ->render();
    ?>
  </div>
<?php endif; ?>

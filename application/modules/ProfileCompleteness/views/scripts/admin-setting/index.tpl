<h2><?php echo $this->translate("Percent Profile Info Completed")?></h2>

<br />

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<br />
<div class='settings'>
    <?php echo $this->form->render($this); ?>
</div>





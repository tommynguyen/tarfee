<h2><?php echo $this->translate('Advanced Comments Plugin') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>
<div class="tip">
        <span> 
         <?php echo $this->translate("Below settings is only applied to Advanced Feeds plugin. For special settings per content page, you may edit at ‘Advanced Comments & Replies” widget in Layout Editor."); ?>
        </span>
    </div>

<div class="settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<script type="text/javascript">
    en4.core.runonce.add(function() {
      hideOptions('<?php echo $this->showAsLike;?>');
   })
    
    function hideOptions(option) { 
      if(option == 1) 
      {
        $('showDislikeUsers-wrapper').style.display = 'none';
      } else {
        $('showDislikeUsers-wrapper').style.display = 'block';
      }
    }
</script>    
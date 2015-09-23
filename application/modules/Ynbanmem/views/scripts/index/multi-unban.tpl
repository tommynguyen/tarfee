<form method="post" class="global_form_popup" action="<?php echo $this->url(array()); ?>">
  <div>
    <h3><?php echo $this->translate("Unban") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to unban?") ?>
    </p>
    <br />
    <p>
    	<input type="hidden" name="unbanList" value="<?php echo $this->unbanList ?>" />
		<input type="hidden" name="type" value="<?php echo $this->type ?>" />
      <button type='submit'><?php echo $this->translate("Unaban") ?></button>
      <?php echo $this->translate("or") ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
      <?php echo $this->translate("cancel") ?></a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>

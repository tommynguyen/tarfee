<form method="post" class="global_form_popup">
  <div>      
    <h3><?php echo $this->translate("Notice") ?></h3>
    <p>
      <?php echo $this->translate("Do you want to verify this member?") ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->user_id?>"/>
      <button type='submit'><?php echo $this->translate("Yes") ?></button>
      <?php echo $this->translate(" or ") ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
      <?php echo $this->translate("Cancel") ?></a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
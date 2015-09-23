<?php if($this->canDelete):?>
<form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Group Category?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete this category? It will not be recoverable after being deleted.") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->category_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate("or") ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
<?php else:?>
<form method="post" class="global_form_popup">
    <div>
      <p>
        <?php echo $this->translate("This category contains sub-categories. Please move the sub-categories to other category before deleting.") ?>
      </p>
      <br />
      <p>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
<?php endif;?>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>

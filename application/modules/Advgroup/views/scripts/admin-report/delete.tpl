<form method="post" class="global_form_popup" action="<?php echo $this->url(array()) ?>">
  <div>
    <h3><?php echo $this->translate("Delete Report?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to delete this report? It will not be recoverable after being deleted.") ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->report_id?>"/>
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
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

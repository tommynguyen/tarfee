<form method="post" class="global_form_popup" action="<?php echo $this->url(array()) ?>">
  <div>
    <h3><?php echo $this->translate("Un-verify Club?") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to un-verify this club?") ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate("Un-verify") ?></button>
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

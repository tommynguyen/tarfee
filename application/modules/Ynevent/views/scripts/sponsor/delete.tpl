<div class='global_form'>
  <form method="post" class="global_form" action="<?php echo $this->url() ?>">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Delete Event Sponsor?');?>
        </h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the event sponsor? It will not be recoverable after being deleted.'); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete');?></button>
          or <a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('Cancel');?></a>
        </p>
      </div>
    </div>
  </form>
</div>
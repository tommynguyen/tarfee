<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Reject Request?") ?></h3>
        <p><?php echo $this->translate("Are you sure that you want to reject this request? It will not be recoverable.") ?></p>
        <br />
        <div class="form-wrapper">
        	<div class="form-label">
        		<label><?php echo $this->translate('Message to requester:')?></label>
        	</div>
        	<div class="form-element">
        		<textarea name="message"></textarea>
        	</div>
        </div>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->request_id?>"/>
            <button type='submit'><?php echo $this->translate("Reject") ?></button>
            <?php echo $this->translate(" or ") ?> 
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
            <?php echo $this->translate("cancel") ?></a>
        </p>
    </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
    TB_close();
</script>
<?php endif; ?>
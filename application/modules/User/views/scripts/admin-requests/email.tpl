<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Email to Requester") ?></h3>
        <p><?php echo $this->translate("Send email to requester for extra details or specific information.") ?></p>
        <br />
        <div class="form-wrapper">
        	<div class="form-label">
        		<label><?php echo $this->translate('Message')?></label>
        	</div>
        	<div class="form-element">
        		<textarea name="message" required></textarea>
        	</div>
        </div>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->request_id?>"/>
            <button type='submit'><?php echo $this->translate("Send") ?></button>
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
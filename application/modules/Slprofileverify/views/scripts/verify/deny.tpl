
<form method="post" id="popup-deny" class="global_form_popup" style="width: 500px; height: <?php echo 150 + (count($this->reasons)*15);?>px">
    <div>
        <h3><?php echo $this->translate("Notice") ?></h3>
        <p>
            <?php 
                if($this->type == "unverifying") 
                    echo $this->translate("Please tell the user why you're unverifying this member?");
                else 
                    echo $this->translate("Please tell the user why you're denying this member?");
            ?>
        </p>
        <!--  error -->
        <?php if(isset($this->error_reason)):?>
        <ul class="form-errors">
          <?php if($this->error_reason == 1): ?>
                  <li><ul class="errors"><li><?php echo $this->translate("Please select reason");?></li></ul></li>
          <?php else:?>
                  <li><ul class="errors"><li><?php echo $this->translate("Please enter the reason");?></li></ul></li>
          <?php endif;?>
        </ul>      
        <?php endif;?>

        <?php foreach($this->reasons as $reason):?>
            <p><input type="checkbox" name="reason[]" value="<?php echo $reason->reason_id?>"><?php echo $reason->description;?></p>
        <?php endforeach;?>
        <p id="other_reason" style="margin-bottom: 5px"><input type="checkbox" id="other" name="other" value="other" onclick="checkOther()"><?php echo $this->translate("Other reason");?></p>
        <div id="other_content" style="display:none; margin-bottom: 5px">
          <p>
            <?php echo $this->translate("Please enter the reason") ?>
          </p>
          <p>
            <textarea cols="80" rows="24" class="text" id="content" name="content"></textarea>
          </p>
        </div>
        <p id="button_submit">        
          <button type='submit'><?php if($this->type == "unverifying") echo $this->translate("Unverify and send the message"); else echo $this->translate("Deny and send the message");?></button>
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

<script type="text/javascript">
    var height = 0;
    var iframe_height = 0;
    function checkOther()
    {
        <?php if(isset($this->error_reason)):?>
            if($('other').checked == true)
             {
                 $('other_content').setStyle('display','');
                 height = <?php echo 160 + 165 + 40 + (count($this->reasons)*15);?>;
                 $('popup-deny').setStyle('height', height + 'px');
                 iframe_height = parent.document.getElementById('TB_iframeContent').get('height').toInt() + 10 + <?php echo (count($this->reasons)*15);?>;
                 parent.document.getElementById('TB_iframeContent').setStyle('height', iframe_height + 'px');

             }
             else
             {
                 $('other_content').setStyle('display','none');
                 height = <?php echo 160 + 60 + (count($this->reasons)*15);?>;
                 $('popup-deny').setStyle('height', height + 'px');
                 iframe_height = parent.document.getElementById('TB_iframeContent').get('height').toInt() - 150 - <?php echo (count($this->reasons)*15);?>;
                 parent.document.getElementById('TB_iframeContent').setStyle('height', iframe_height + 'px');

             }
        <?php else:?>
            if($('other').checked == true)
            {
                $('other_content').setStyle('display','');
                height = <?php echo 160 + 165 + (count($this->reasons)*15);?>;
                $('popup-deny').setStyle('height', height + 'px');
                iframe_height = parent.document.getElementById('TB_iframeContent').get('height').toInt() + 10 + <?php echo (count($this->reasons)*15);?>;
                parent.document.getElementById('TB_iframeContent').setStyle('height', iframe_height + 'px');

            }
            else
            {
                $('other_content').setStyle('display','none');
                height = <?php echo 165 + (count($this->reasons)*15);?>;
                $('popup-deny').setStyle('height', height + 'px');
                iframe_height = parent.document.getElementById('TB_iframeContent').get('height').toInt() - 130 - <?php echo (count($this->reasons)*15);?>;
                parent.document.getElementById('TB_iframeContent').setStyle('height', iframe_height + 'px');

            }
        <?php endif;?>
    }
    
    <?php if(isset($this->error_reason) && $this->error_reason == 2):?>
        $('other').click();
    <?php endif;?>
    
    <?php if(isset($this->error_reason)):?>
       if($('other').checked == true)
        {
            $('other_content').setStyle('display','');
            height = <?php echo 150 + 165 + 40 + (count($this->reasons)*15);?>;
            $('popup-deny').setStyle('height', height + 'px');
            iframe_height = parent.document.getElementById('TB_iframeContent').get('height').toInt() + 40 + <?php echo (count($this->reasons)*15);?>;
            parent.document.getElementById('TB_iframeContent').setStyle('height', iframe_height + 'px');
            
        }
        else
        {
            $('other_content').setStyle('display','none');
            height = <?php echo 150 + 40 + (count($this->reasons)*15);?>;
            $('popup-deny').setStyle('height', height + 'px');
            iframe_height = parent.document.getElementById('TB_iframeContent').get('height').toInt() + 40 - 150 - <?php echo (count($this->reasons)*15);?>;
            parent.document.getElementById('TB_iframeContent').setStyle('height', iframe_height + 'px');
            
        }
    <?php endif;?>
</script>
<?php 
if( count($this->navigation) ): ?>
    <div class="headline">
      <h2>
        <?php echo $this->translate('Invite Your Friends');?>
      </h2>
      <div class="tabs">
        <?php
          // Render the menu
          echo $this->navigation()
            ->menu()
            ->setContainer($this->navigation)
            ->render();
        ?>
      </div>
    </div>
    <?php endif; ?>

<?php      
    if (isset($this->ers))
    {
        echo "<div><ul class='form-errors'><li><ul class='errors'><li>" . $this->translate($this->ers) . "</li></ul></li></ul></div>";
        unset($this->ers);
        ?>
        <div id="uploadcsvform3">
        <form method="post" action="<?php echo $this->url(array('module' => 'contactimporter', 'controller' => 'index','action'=>'upload'), 'default', true);?>" class="global_form" enctype="multipart/form-data" onsubmit="sending_request();">
            <?php echo $this->translate('File to upload:');?> <input type='file' name='csvfile' class='text'>
        <button type="submit" name="submit_button"><?php echo $this->translate('Upload');?></button>
        </form>
        </div>
        <?php
    }
    elseif($this->step =="add_contact")
    {
         echo $this->render('contactImports.tpl') ;
    }     
    elseif($this->step =="get_invite")
    {
         echo $this->render('invites.tpl') ;
    }
    else
    {
    ?>
        <div id="uploadcsvform3">
        <form method="post" action="<?php echo $this->url(array(), 'contactimporter_upload');?>" class="global_form" enctype="multipart/form-data" onsubmit="sending_request();">
            <?php echo $this->translate('File to upload:');?> <input type='file' name='csvfile' class='text'>
        <button type="submit" name="submit_button"><?php echo $this->translate('Upload');?></button>
        </form>
        </div>
    <?php
    }
?>
<div class="global_form_popup" >
    <form action="" method="post">
        <div>
            <h3><?php echo $this->translate("Warning")?></h3>
            <p style="margin: 10px 0"><?php echo $this->translate("UNVERIFIED_PROFILE_CHANGE_DESCRIPTION")?></p>
            <p>
                <button type="submit" name="continue"><?php echo $this->translate("Continue")?></button> <?php echo $this->translate("or")?> <button type="submit" name="cancel"><?php echo $this->translate("Cancel")?></button>
            </p>
        </div>
    </form>
</div>

<script type="text/javascript">
  //parent.window.location.href = '<?php //echo $this->url(array("module" => "slprofileverify", "controller" => "index", "action" => "profile-change", "type" => "close"), "default", true);?>';
  //Smoothbox.close(window.location.href = '<?php //echo $this->url(array("module" => "slprofileverify", "controller" => "index", "action" => "profile-change", "type" => "close"), "default", true);?>');
</script>

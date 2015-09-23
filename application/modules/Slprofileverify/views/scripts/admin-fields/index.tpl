<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>
<div class='clear'>
    <div class='settings' id="setting-custom">
        <?php echo $this->form_field->render($this); ?>
    </div>
</div>

<div class='clear'>
    <div class='settings' id="setting-custom-field">
        <form class="global_form set-custom">
            <div>
                <?php echo $this->render('_jsAdmin.tpl') ?>
                <h2 style="margin-bottom: 0px"><?php echo $this->translate("Create Profile(s) For This Verification Step") ?></h2>
                <p class="description-step"><?php echo $this->translate('PROFILE_VERIFICATION_STEP_DESCRIPTION') ?></p>

                <div class="admin_fields_type">
                    <h3><?php echo $this->translate("Configure verification fields for") ?></h3>
                    <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
                </div>
                <br />
                <div class="admin_fields_options">
                    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion">Add Question</a>
                    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading">Add Heading</a>
                    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype">Rename Profile Type</a>
                    <?php if (count($this->topLevelOptions) > 1): ?>
                        <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype">Delete Profile Type</a>
                    <?php endif; ?>
                    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype">Create New Profile Type</a>
                    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;">Save Order</a>
                </div>
                <br />
                <ul class="admin_fields">
                    <?php foreach ($this->secondLevelMaps as $map): ?>
                        <?php echo $this->adminFieldMeta($map) ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    window.addEvent("domready", function() {
        <?php foreach ($this->src_img as $key => $img): ?>
            var img = new Element('img', {src: "<?php echo $img; ?>", class: "file_id_img"});
            $('file_step-<?php echo $key ?>').grab(new Element("br"), 'after').grab(img, 'after');
            $('enable_img-<?php echo $key ?>').inject($('file_step-<?php echo $key ?>'), 'before');
        <?php endforeach; ?>
        //
        $('enable_img-wrapper').dispose();
        $('setting-custom').inject($('setting-custom-field'), 'after');
        $$('#setting_custom .form-description').set('html', <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("CUSTOM_VERIFICATION_STEP_DESCRIPTION"))); ?>);
    });
</script>
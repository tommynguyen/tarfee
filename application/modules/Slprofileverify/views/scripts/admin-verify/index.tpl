<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<div class='clear'>
    <div class='settings set-custom' id="setting-profile-verify">
        <form class="global_form set-custom">
            <?php echo $this->render('_jsAdmin.tpl') ?>
            <div class="admin_fields_type">
                <h2><?php echo $this->translate("Change profile questions for verification settings") ?></h2>
                <p class="description-select-required"><?php echo $this->translate("IDENTITY_VERIFICATION_SETTINGS_DESCRIPTION") ?></p>
                <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
            </div>
            <div id="wrapper-verify" style="margin-top:0;">
                <div class="field-left" id="field-left">
                    <h3><?php echo $this->translate("Default profile"); ?></h3>
                    <div class="content">
                        <ul>
                            <?php foreach ($this->listFieldMeta as $fieldMeta): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" id="field_id_<?php echo $fieldMeta['field_id'] ?>" name="field_id_<?php echo $fieldMeta['field_id'] ?>" value="<?php echo $fieldMeta['field_id'] ?>" class="selected"> 
                                        <span><?php echo $fieldMeta['label'] ?></span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="convert">
                    <p class="convert-top"><button type="button" id="convert-right">&DoubleRightArrow;</button></p>
                    <p class="convert-bottom"><button type="button" id="convert-left" >&DoubleLeftArrow;</button></p>
                </div>
                <div class="field-right" id="field-right">
                    <h3><?php echo $this->translate("Required verify"); ?></h3>
                    <div class="content">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='settings' id="setting-custom">
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">
    window.addEvent("domready", function() {
        var option_id = $('profileType').get('value');

        var warning_get = '<div id="field-warning">' +
                '<h3 class="notice-selected">' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("Notice"))); ?> + '</h3>' +
                '<p class="content-selected">' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("WARNING_GET_VERIFY"))); ?> + '</p>' +
                '</div>';
        var warning_remove = '<div id="field-warning">' +
                '<h3 class="notice-selected">' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("Notice"))); ?> + '</h3>' +
                '<p class="content-selected">' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("WARNING_REMOVE_VERIFY"))); ?> + '</p>' +
                '</div>';
        new Request({
            url: '<?php echo $this->url(array("module" => "slprofileverify", "controller" => "ajax", "action" => "get-verify"), "admin_default", true) ?>',
            data: {
                'option_id': option_id
            },
            onRequest: function() {
            },
            onSuccess: function(responseText) {
                $$('#field-right .content').set('html', responseText);
            },
            onFailure: function() {
            }
        }).send();

        $('convert-right').addEvent('click', function() {
            var array_field_id = [];
            $$("#field-left .selected").each(function(element) {
                if (element.checked === true)
                {
                    array_field_id.push(element.value);
                    element.getParent().getParent().remove();
                }
            });
            if (array_field_id.length > 0) {
                new Request({
                    url: '<?php echo $this->url(array("module" => "slprofileverify", "controller" => "ajax", "action" => "get-verify"), "admin_default", true) ?>',
                    data: {
                        'data': array_field_id,
                        'option_id': option_id
                    },
                    onRequest: function() {
                        $('convert-right').set('disabled', true);
                        $('convert-left').set('disabled', true);
                    },
                    onSuccess: function(responseText) {
                        $$('#field-right .content').set('html', responseText);
                        $('convert-right').set('disabled', false);
                        $('convert-left').set('disabled', false);
                    },
                    onFailure: function() {
                    }
                }).send();
            } else {
                Smoothbox.open(warning_get);
                // Edit smoothbox
                var height = $('TB_ajaxContent').getStyle('height').toInt() - 23;
                var width = $('TB_ajaxContent').getStyle('width').toInt() - 20;
                $('TB_ajaxContent').setStyle('height', height + 'px');
                $('TB_ajaxContent').setStyle('width', width + 'px');
                $('TB_ajaxContent').setStyle('padding-bottom', '12px');
                $('TB_ajaxContent').setStyle('overflow', 'hidden');
            }
        });

        $('convert-left').addEvent('click', function() {
            var array_field_id = [];
            $$("#field-right .selected").each(function(element) {
                if (element.checked === true)
                {
                    array_field_id.push(element.value);
                    element.getParent().getParent().remove();
                }
            });
            if (array_field_id.length > 0) {
                new Request({
                    url: '<?php echo $this->url(array("module" => "slprofileverify", "controller" => "ajax", "action" => "remove-verify"), "admin_default", true) ?>',
                    data: {
                        'data': array_field_id,
                        'option_id': option_id
                    },
                    onRequest: function() {
                        $('convert-right').set('disabled', true);
                        $('convert-left').set('disabled', true);
                    },
                    onSuccess: function(responseText) {
                        $$('#field-left .content').set('html', responseText);
                        $('convert-right').set('disabled', false);
                        $('convert-left').set('disabled', false);
                    },
                    onFailure: function() {
                    }
                }).send();
            } else {
                Smoothbox.open(warning_remove);
                // Edit smoothbox
                var height = $('TB_ajaxContent').getStyle('height').toInt() - 23;
                var width = $('TB_ajaxContent').getStyle('width').toInt() - 20;
                $('TB_ajaxContent').setStyle('height', height + 'px');
                $('TB_ajaxContent').setStyle('width', width + 'px');
                $('TB_ajaxContent').setStyle('padding-bottom', '12px');
                $('TB_ajaxContent').setStyle('overflow', 'hidden');
            }
        });

<?php foreach ($this->src_img as $key => $img): ?>
            var img = new Element('img', {src: "<?php echo $img; ?>", class: "file_id_img"});
            $('file_step-<?php echo $key ?>').grab(new Element("br"), 'after').grab(img, 'after');
            $('enable_img-<?php echo $key ?>').inject($('file_step-<?php echo $key ?>'), 'before');
<?php endforeach; ?>
        $('enable_img-wrapper').dispose();
    });
</script>

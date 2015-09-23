<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <?php echo $this->content()->renderWidget('user.settings-menu') ?>
    </div>
</div>
<div class="generic_layout_container layout_main">
    <div class="generic_layout_container layout_middle">
        <?php if (!$this->auth): ?>
            <div class="tip">
                <span>
                    <?php echo $this->translate("You do not have permission to send verification request!"); ?>
                </span>
            </div><br />
        <?php else: ?>
            <div class="global_form">
                <?php echo $this->form->render($this) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    window.addEvent("domready", function() {

        $$('#verify-document .form-description').set('html', '<?php echo $this->discription ?>');
        var profile_picture = new Element('div', {
            html: '<p class="img"><img src="<?php echo $this->profile_picture; ?>"></p>' +
                    '<p><a href="<?php echo $this->url(array("controller" => "edit", "action" => "photo"), "user_extended", true); ?>">' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("Changes picture"))); ?> + '</a></p>',
            id: 'profile-picture'
        });
        profile_picture.inject($('profile_picture-element'), 'top');

        var image_identity = new Element('p', {
            html: "<?php foreach ($this->imageIV as $key => $img): ?><img src='<?php echo $img; ?>' /><?php endforeach; ?>", class: "file_id_img"
        });
        image_identity.inject($('document-element'), 'top');

        var notice = new Element('p', {
            html: <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("NOTICE_VERIFIED"))); ?>, 
            class: 'notice'
        });
        notice.inject($('copy_document-element'), 'bottom');

        $('exp-document-identity').inject($$('#document-element #MAX_FILE_SIZE')[0], 'before');

        $('why-get-verify').addEvent('click', function() {
            var content =   '<div class="why-get-verify">' +
                                '<h3>' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("Why should I get verified?"))); ?> + '</h3>' +
                                '<p>' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("DESCRIPTION_WHY_GET_VERIFIED"))); ?> + '</p>' +
                            '</div>';
            Smoothbox.open(content);
            var height = $('TB_ajaxContent').get('height').toInt() - 123;
            $('TB_ajaxContent').setStyle('height', height + 'px');
            $('TB_ajaxContent').setStyle('padding-bottom', '12px');
            $('TB_ajaxContent').setStyle('overflow', 'hidden');
        });

        $('why-verify-safe').addEvent('click', function() {
            var content =   '<div class="why-verify-safe">' +
                                '<h3>' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("Why it is safe?"))); ?> + '</h3>' +
                                '<p>' + <?php echo Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $this->translate("WHY_IT_IS_SAFE_DESCRIPTION"))); ?> + '</p>' +
                            '</div>';
            Smoothbox.open(content);
            var height = $('TB_ajaxContent').get('height').toInt() - 50;
            $('TB_ajaxContent').setStyle('height', height + 'px');
            $('TB_ajaxContent').setStyle('padding-bottom', '12px');
            $('TB_ajaxContent').setStyle('overflow', 'hidden');
        });
    });
</script>
<div id="verified-document">
    <h4><span><?php echo $this->translate("Verification document"); ?></span></h4>
    <div>
        <?php echo $this->fieldValueLoop($this->user, $this->fieldUserStructure) ?>
    </div>
    <div class="verified-img">
        <?php foreach ($this->aFileId as $iFileId): ?>
            <a href="javascript:void(0)" class="verify-identity" onclick="verifyIdentity(<?php echo $iFileId; ?>, 'verify-identity-');">
                <img src="<?php echo Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($iFileId, 'thumb.normal'); ?>" class="normal"/>
                <img src="<?php echo Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($iFileId); ?>" id="verify-identity-<?php echo $iFileId; ?>" style="display: none"/>
            </a>
        <?php endforeach; ?>
    </div>
    <?php if ($this->enable_step): ?>
        <h4><span><?php echo $this->translate("Custom verification document"); ?></span></h4>
        <div>
            <?php echo $this->fieldValueLoop($this->verifyRow, $this->fieldSlverifyStructure) ?>
        </div>
        <div class="verified-img">
            <?php foreach ($this->aFileIdCus as $iFileId): ?>
                <a href="javascript:void(0)" class="verify-custom" onclick="verifyIdentity(<?php echo $iFileId; ?>, 'verify-custom-')">
                    <img src="<?php echo Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($iFileId, 'thumb.normal') ?>" class="normal"/>
                    <img src="<?php echo Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($iFileId); ?>" id="verify-custom-<?php echo $iFileId; ?>" style="display: none"/>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php if ($this->is_admin): ?>
    <div class='buttons' id="verify_deny-button">
        <?php
        $urlDeny = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'deny', 'id' => $this->user_id, 'type' => 'denied'), 'default');
        $urlUnverify = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'deny', 'id' => $this->user_id, 'type' => 'unverifying'), 'default');
        $urlVerify = $this->url(array('module' => 'slprofileverify', 'controller' => 'verify', 'action' => 'verify', 'id' => $this->user_id), 'default');
        ?>
        <?php if ($this->verifyRow->approval == 'verified'): ?>  
            <a class="buttonlink deny_a smoothbox" href="<?php echo $urlUnverify; ?>"><?php echo $this->translate("Unverify") ?></a>
        <?php elseif ($this->verifyRow->approval == 'unverified'): ?>  
            <a class="buttonlink verify_a smoothbox" href="<?php echo $urlVerify; ?>"><?php echo $this->translate("Verify") ?></a>
            <a class="buttonlink deny_a" style="text-decoration: none"><?php echo $this->translate("Denied") ?></a>
        <?php else: ?>
            <a class="buttonlink verify_a smoothbox" href="<?php echo $urlVerify; ?>"><?php echo $this->translate("Verify") ?></a>
            <a class="buttonlink deny_a smoothbox" href="<?php echo $urlDeny; ?>"><?php echo $this->translate("Deny") ?></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    function verifyIdentity(id, type) {
        var elImg = $(type + id).show();
        var height = elImg.height;
        var width = elImg.width;
        if (height < 145) {
            elImg.setStyle('height', '145px');
            width = width * 145 / height;
        }
        Smoothbox.open(elImg, {
            width: width
        });
        elImg.hide();
        $('TB_ajaxContent').setStyle('padding', '0px');
    }
</script>
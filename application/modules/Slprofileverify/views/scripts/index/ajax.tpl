<div class="description">
    <p class="file_id_img">
        <?php
        foreach ($this->src_img as $key => $img):
            if (in_array($key, $this->ena_img)):
                ?>
                <img src='<?php echo $img; ?>' />
                <?php
            endif;
        endforeach;
        ?>
    </p>
    <?php echo ($this->exp_document) ? $this->exp_document : '<p>' . $this->translate("DOCUMENT_DEFAULT_DESCRIPTION") . '</p>'; ?>
</div>
<input type="hidden" name="MAX_FILE_SIZE" value="786432000" id="MAX_FILE_SIZE">
<?php for($i = 0; $i < $this->image_number; $i++): ?>
<input type="file" name="document[]" id="document_<?php echo $i; ?>_" class="input-file-block">
<?php endfor; ?>
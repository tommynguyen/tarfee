<ul>
<?php foreach( $this->listFieldMeta as $fieldMeta ): ?>
    <li>
        <label>
            <input type="checkbox" id="field_id_<?php echo $fieldMeta['field_id']?>" name="field_id_<?php echo $fieldMeta['field_id']?>" value="<?php echo $fieldMeta['field_id']?>" class="selected"> 
            <span><?php echo $fieldMeta['label']?></span>
        </label>
    </li>
<?php endforeach; ?>
</ul>
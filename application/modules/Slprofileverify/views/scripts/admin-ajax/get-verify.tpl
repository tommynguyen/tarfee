<ul>
<?php foreach( $this->arrFieldMeta as $fieldMeta ): ?>
    <li>
        <label>
            <input type="checkbox" name="field_id_<?php echo $fieldMeta['field_id']?>" value="<?php echo $fieldMeta['field_id']?>" class="selected"> 
            <span><?php echo $fieldMeta['label']?></span>
        </label>
    </li>
<?php endforeach; ?>
</ul>
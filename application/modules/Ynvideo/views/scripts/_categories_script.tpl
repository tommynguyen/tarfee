<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<script type="text/javascript" language="javascript">
    var categories = [];
    <?php foreach ($this->categories as $category) : ?>
        <?php if (count($category->getSubCategories()) > 0): ?>
            subCategories = [];
            <?php foreach ($category->getSubCategories() as $subCat) : ?>
                subCategories.push({
                    'id' : <?php echo $subCat->getIdentity() ?>,
                    'category_name' : '<?php echo $this->string()->escapeJavascript($subCat->category_name) ?>'
                });
            <?php endforeach; ?>
            categories['<?php echo $category->getIdentity() ?>'] = subCategories;
        <?php endif; ?>
    <?php endforeach; ?>

    var updateSubCategories = window.updateSubCategories = function() {
        var subCatElement = $('subcategory_id');                
        subCatElement.empty();            
        subCatElement.adopt(new Element('option', {
            'value' : 0
        }));
        var value = $('category_id').value;
        if (!value) {
            $('subcategory_id-wrapper').style.display = 'none';
        } else {
            if (categories[value]) {
                $('subcategory_id-wrapper').style.display = 'block';            
                var cats = categories[value];
                for(var index = 0; index < cats.length; index++) {
                    var options = {
                        'value' : cats[index].id,
                        'html' : cats[index].category_name
                    }
                    if (index == 0) {
                        options.selected = 'selected';
                    }
                    subCatElement.adopt(new Element('option', options));
                }
            } else {
                $('subcategory_id-wrapper').style.display = 'none';    
            }
        }
    }
</script>
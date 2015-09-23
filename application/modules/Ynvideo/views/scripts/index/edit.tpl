<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        updateSubCategories();
    });
</script>
<?php echo $this->partial('_categories_script.tpl', array('categories' => $this->categories)) ?>

<?php
    echo $this->form->render();
?>

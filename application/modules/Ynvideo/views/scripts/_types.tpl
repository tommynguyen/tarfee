<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<?php
    $videoTypes = Ynvideo_Plugin_Factory::getAllSupportTypes();
    unset($videoTypes[Ynvideo_Plugin_Factory::getUploadedType()]);
?>
ynvideo = {};
ynvideo.types = [];

<?php foreach ($videoTypes as $key => $type) : ?>
    var type = {
        'title' : '<?php echo $this->translate($type)?>',
        'value' : <?php echo $key?>
    }
    ynvideo.types.push(type);
<?php endforeach; ?>


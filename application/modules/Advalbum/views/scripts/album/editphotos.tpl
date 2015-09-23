<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: editphotos.tpl 7249 2010-09-01 04:15:19Z john $
 * @author     Sami
 */
?>
<?php
	$this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places');
?>
<script type="text/javascript">
window.addEvent('domready', function(){
    function initialize() {
    	<?php foreach( $this->paginator as $photo ): ?>
	        var input = document.getElementById('advalbum_photo_<?php echo $photo->getIdentity() ?>-location');
	        var autocomplete = new google.maps.places.Autocomplete(input);
	    <?php endforeach; ?>
	 }
	 google.maps.event.addDomListener(window, 'load', initialize);
	 $$("select[multiple='multiple'] option").addEvent('click', function(){
			selected_items = $(this).getParent().getSelected();
			if (selected_items.length > <?php echo $this->max_color;?>)
			{
				alert("<?php echo $this->translate('Maximum main colors is only ') . $this->max_color;?>");
				$(this).selected= false;
			}
	});
})
</script>
<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>

<div class="layout_middle">
<h3 style="margin-bottom: 18px;">
    <?php echo $this->translate('Manage Photos');?>
    - <span><?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>
  (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->count()),$this->locale()->toNumber($this->album->count())) ?>)</span>  
</h3>

<?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
        array(
        'pageAsQuery' => false,
        'query' => $this->formValues
    )); ?>
<?php endif; ?>

<?php if ($this->paginator->count() > 0):?>
<form class="global_form" action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <?php echo $this->form->album_id; ?>
  <ul class='albums_editphotos'>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
         <div class="albums_editphotos_photo" style ="width: 170px">
          <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal'))  ?>
        </div>
        <div class="albums_editphotos_info">
          <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
          ?>
    <div class="albums_editphotos_cover">
            <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->album->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
    </div>
    <div class="albums_editphotos_label">
            <label><?php echo $this->translate('Album Cover');?></label>
    </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
    <?php echo $this->form->submit->render(); ?>
</form>
<?php else :?>
<div class="tip">
      <span><?php echo $this->translate("There is no photo in this album.");?></span>
</div>
<?php endif;?>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
    <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum")); ?>
  <?php endif; ?>
</div>
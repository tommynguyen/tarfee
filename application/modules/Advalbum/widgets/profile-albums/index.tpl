<?php
$css = "global_form_box";
$album_listing_id = 'advalbum_profile_albums';
$no_photos_message = $this->translate('There has been no photo profile.');
$strRand = rand(1,100).rand(1,100);
?>
<div class="ymbHomeAbumSlideshow">
<?php
echo $this->partial('_albumlist.tpl', 'advalbum', array(
    'class_mode' => $this->class_mode,
    'view_mode' => $this->view_mode,
    'mode_enabled' => $this->mode_enabled,    
    'paginator'=> $this->paginator, 
    'album_listing_id'=> $album_listing_id, 
    'no_photos_message'=>$no_photos_message, 
    'css'=>$css,
    'rand'=>$strRand,
));
?>
</div>
<?php
 $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . '/application/modules/Advalbum/externals/styles/prettyPhoto.css');
  ?>
<?php $strRand = rand(1,100).rand(1,100); ?>
<?php if ($this->is_ajax): ?>
<div id="photo_listing_random<?php echo $strRand?>"></div>
<script type="text/javascript">
 function do_onload() {
    var l = document.getElementById('photo_listing_random<?php echo $strRand?>');
    l.innerHTML = '<img src="./application/modules/Advalbum/externals/images/loading.gif"/>';
    var rand = <?php echo $strRand?>;
    var limit = <?php echo $this->limit?>;
    var makeRequest = new Request(
            {
                url: en4.core.baseUrl + "advalbum/ajax/random-photos/rand/"+rand+"/number/"+ limit,
                onComplete: function (respone){
                 l.innerHTML = respone;
                }
            }
    );
    makeRequest.send();
 }
document.onload = do_onload();
addSmoothboxEvents();
</script>
<?php else: ?>
<?php
$css = "global_form_box";
if($this->no_title)
{
	$css .= " ".$this->no_title;
}
$photo_listing_id = 'advalbum_random_photos';
$no_photos_message = $this->translate ( 'Nobody has uploaded a photo yet.' );
?>
<?php echo $this->partial('_photolist.tpl', 'advalbum', array(
		'class_mode' => $this->class_mode,
		'view_mode' => $this->view_mode,
		'mode_enabled' => $this->mode_enabled,	
		'arr_photos'=> $this->arr_photos,
		'photo_listing_id' => $photo_listing_id,
		'no_photos_message' => $no_photos_message,
		'css' => $css,
		'rand' => $strRand
		));
?>
<?php endif; ?>
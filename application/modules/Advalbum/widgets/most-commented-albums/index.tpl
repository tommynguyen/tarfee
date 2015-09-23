<?php $strRand = rand(1,100).rand(1,100); ?>
<?php if ($this->is_ajax): ?>
<div id="album_listing_most_commented<?php echo $strRand?>"></div>
<script type="text/javascript">
 function do_onload() {
    var l = document.getElementById('album_listing_most_commented<?php echo $strRand?>');
    l.innerHTML = '<img src="./application/modules/Advalbum/externals/images/loading.gif"/>';
    var limit = <?php echo $this->limit?>;
     var makeRequest = new Request(
            {
                url: "advalbum/ajax/most-commented-album/number/"+ limit,
                onComplete: function (respone){
                 l.innerHTML = respone;
                }
            }
    );
    makeRequest.send();
 }
document.onload = do_onload();
</script>
<?php else: ?>
<?php
$css = "global_form_box";
if($this->no_title)
{
	$css .= " ".$this->no_title;
}
$album_listing_id = 'advalbum_most_commented_albums';
$no_albums_message = $this->translate ( 'There has been no album in this category yet.' );
?>
<?php echo $this->partial('_albumlist.tpl', 'advalbum', array(
		'arr_albums' => $this->arr_albums,
		'album_listing_id' => $album_listing_id,
		'no_albums_message' => $no_albums_message,
		'short_title' => 1,
		'css' => $css,
		'class_mode' => $this->class_mode,
		'view_mode' => $this->view_mode,
		'mode_enabled' => $this->mode_enabled,
));
?>
<?php endif; ?>

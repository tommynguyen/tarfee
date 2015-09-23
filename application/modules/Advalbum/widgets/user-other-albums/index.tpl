<?php
$session = new Zend_Session_Namespace('mobile'); 
if(!$session -> mobile)
{
?>
<div>
	<?php if (count($this->otherAlbums)>0) { ?>
	<div class="global_form_box" style="margin-bottom: 0px; padding: 0px;">
		<div class="album_others">
			<h4>
				<?php echo $this->translate('%1$s\'s Other Albums', $this->album->getOwner()->__toString());?>
			</h4>
			<div class="album_others_list" id="div_others"></div>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="50%" style="text-align: left;"><a id="btn_prev"
						href="javascript:;" onClick="slide_prev()"><b><?php echo $this->translate('Previous');?>
						</b> </a></td>
					<td width="50%" style="text-align: right;"><a id="btn_next"
						href="javascript:;" onClick="slide_next()"><b><?php echo $this->translate('Next');?>
						</b> </a></td>
				</tr>
			</table>
		</div>
	</div>

	<?php
	$arr = array();
	$number_of_albums_per_slide = 3;
	$album_num = 0;
	$idx = -1;
	foreach ($this->otherAlbums as $album_item) {
				if ($album_num%$number_of_albums_per_slide==0) {
					$idx ++;
					$arr[$idx] = array();
				}
				$arr[$idx][] = $album_item;
				$album_num++;
			}
			$slide_total = count($arr);
			for ($idx=0; $idx < $slide_total; ++$idx) {
				echo '<div class="album_others_data" id="div_others_' . $idx . '">' . "\r\n";
				$album_listing_id = 'album_listing_others_slide_' . $idx;
				echo $this->partial('_albumlist.tpl', array(
					'arr_albums'=>$arr[$idx], 
					'album_listing_id'=> $album_listing_id, 
					'no_author_info'=>1, 
					'no_bottom_space'=>1,
					'class_mode' => 'ynalbum-grid-view',
					'view_mode' => 'grid',
				));
				echo "</div>\r\n";
			}
			?>
	<script type="text/javascript">
			var slideTotal = <?php echo $slide_total; ?>;
			var slideCurrent = 0;
			function update_buttons() {
				// show
				objOthers = document.getElementById("div_others");
				objCurrent = document.getElementById("div_others_" + slideCurrent);
				if (objOthers && objCurrent) {
					objOthers.innerHTML = objCurrent.innerHTML;
				}
				// buttons
				objButtonNext = document.getElementById("btn_next");
				if (objButtonNext) {
					if (slideCurrent>=slideTotal-1) {
						objButtonNext.style.visibility = 'hidden';
					} else {
						objButtonNext.style.visibility = 'visible';
					}
				}
				objButtonPrev = document.getElementById("btn_prev");
				if (objButtonPrev) {
					if (slideCurrent<=0) {
						objButtonPrev.style.visibility = 'hidden';
					} else {
						objButtonPrev.style.visibility = 'visible';
					}
				}
			}
			function slide_prev() {
				if (slideCurrent>0) {
					slideCurrent --;
					update_buttons();
				}
			}
			function slide_next() {
				if (slideCurrent<slideTotal-1) {
					slideCurrent ++;
					update_buttons();
				}
			}
			update_buttons();
			</script>
	<?php } ?>
</div>
<?php } ?>
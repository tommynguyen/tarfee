<?php if(count($this->tags) > 0):?>
<ul class="global_form_box" style="margin-bottom: 15px;">
	<span id="script" style="font-size: 9pt;"> 
	<?php
	$index = 0; $flag = false;
	foreach($this->tags as $tag):
		$index ++;
		if(trim($tag->text) != ""):
            if ($index > 1) echo ",";            
            if($index > 10 && $flag == false): $flag = true;?>
				<p id="showlink" style="display: block; font-weight: bold">
					[<a id='title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('show all');?></a>]
				</p>
				</span>
				<span id="hide" style="display: none; font-size: 9pt;"> 
			<?php  endif;?>
			<?php 
				$tagCounter = 0;
				if (isset($this->tagCounter[$tag->tag_id]))
				{
					$tagCounter = $this->tagCounter[$tag->tag_id];
				}
			?>
			<span style="<?php if($tagCounter > 99 && $tagCounter < 599): echo "font-size:".($tagCounter/80 + 8)."pt"; elseif($tagCounter > 599): echo "font-size: 14pt"; endif; ?>">            
				<a href="javascript:;" onclick="javascript:tagAction(<?php echo $tag->tag_id; ?>);" title="<?php echo $this->translate(array("%s event", "%s event", $tagCounter), $this->locale()->toNumber($tagCounter));?>">
					<?php echo $tag->text?>
				</a>(<?php echo $tagCounter?>) 
			</span>         
		<?php endif; ?> 
	<?php endforeach; ?>
	<?php if($flag == true):?>
		<p id="hidelink" style="display: none; font-weight: bold">
			[<a id='title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('hide');?></a>]
		</p> 
	<?php endif; ?>
	</span>
</ul>
<?php endif;?>

<script type="text/javascript">
  	var tagAction = function(tag_id)
  	{
  		<?php if(defined("YNRESPONSIVE_ACTIVE") && YNRESPONSIVE_ACTIVE == 'ynresponsive-event'):?>
  	  		var url = '<?php echo $this->url(array(),'ynresponsive_event_listtng'); ?>' + '?tag=' + tag_id;
  	  	<?php else:?>
  	  		var url = '<?php echo $this->url(array(),'event_listing'); ?>' + '?tag=' + tag_id;
  	  	<?php endif;?>
  		window.location.assign(url);
  	}

  	function showhide(id)
  	{
	    if (document.getElementById)
	    {
	        obj = document.getElementById(id);
	        if (obj.style.display == "none")
	        {
	            obj.style.display = "";
	            $('showlink').style.display = "none";
	            $('hidelink').style.display = "";
	        } 
	        else
	        {
	            obj.style.display = "none";
	             $('showlink').style.display = "";
	            $('hidelink').style.display = "none";
	        }
	}
}
</script>

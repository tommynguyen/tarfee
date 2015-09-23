<div class="ynevent_announcement_item">
	<?php if($this -> announcement):?>
		<div class="title"><?php echo $this -> announcement -> title; ?></div>
		<div class="content">
			<span id = 'show'>
			   <?php
			   	  echo $this -> string() -> truncate(strip_tags($this -> announcement->body), 800);
	              if(strlen($this -> announcement->body) > 800): $flag = true;?>
	              <p id="showlink" style="display: block; font-weight: bold">
	                [<a style="color: #FFF" id = 'title' href="javascript:void()" onclick="showhide('hide'); return(false);"><?php echo $this->translate('show all');?></a>]
	              </p>
	              <?php endif; ?>
			</span>
			 <span id="hide" style="display:none;">
		         <?php echo $this -> announcement->body; ?>
	             <?php if($flag == true):?>
	             	<p id="hidelink" style="display: none;font-weight: bold">[<a style="color: #FFF" id = 'title' href="#" onclick="showhide('hide'); return(false);"><?php echo $this->translate('hide');?></a>]</p>
	             <?php endif; ?>
			</span>
        </div>
	<?php endif; ?>
</div>

<div class='ynevent_announcement_button'>
  	<?php if($this -> announcement->isOwner($this->viewer) ):?>
    <?php
	echo $this -> htmlLink(array('route' => 'event_extended', 'controller' => 'announcement', 'action' => 'edit', 'announcement_id' => $this -> announcement -> getIdentity(), 'event_id' => $this -> event -> getIdentity(), 'reset' => true, 'back' => true), $this -> translate('Edit Entry'), array('class' => 'buttonlink icon_event_edit', ));
    ?>
   
    <?php
	echo $this -> htmlLink(array('route' => 'event_extended', 'controller' => 'announcement', 'action' => 'delete', 'announcement_id' => $this -> announcement -> getIdentity(), 'event_id' => $this -> event -> getIdentity(), 'format' => 'smoothbox'), $this -> translate('Delete Entry'), array('class' => 'buttonlink smoothbox icon_event_delete'));
    ?>
   
    <?php
	echo $this -> htmlLink(array('route' => 'event_extended', 'controller' => 'announcement', 'action' => 'highlight', 'announcement_id' => $this -> announcement -> getIdentity(), 'event_id' => $this -> event -> getIdentity(), ),  $this -> translate('Un-highlight') , array('class' => 'smoothbox buttonlink ynevent_announcement_highlight icon_ynevent_announcement_unhighlight'));
	endif;
?>
</div>
<script type="text/javascript">
  function showhide(id)
  {
    if (document.getElementById(id))
    {
        obj = document.getElementById(id);
        if (obj.style.display == "none")
        {
            obj.style.display = "";
            $('show').style.display = "none";
            $('showlink').style.display = "none";
            $('hidelink').style.display = "";
        } else
        {
            obj.style.display = "none";
            $('showlink').style.display = "";
            $('show').style.display = "";
            $('hidelink').style.display = "none";
        }
    }
}
</script>
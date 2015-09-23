  <h2>
    <?php echo $this->group->__toString();
          echo '&#187;';
          if($this->album->getTitle()!='') echo $this->album->getTitle();
          else echo 'Untitle Album';
    ?>
</h2>

<div class="group_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'album', 'action' => 'list','subject' => $this->group->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Back to Album List'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php if($this->canEdit) echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'edit', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Edit Album'), array(
    'class' => 'buttonlink icon_group_edit smoothbox'
  )) ?>
  <?php if($this->canEdit  && $this->album->getTitle() !== 'Group Profile') echo $this->htmlLink(array('route' => 'group_extended','controller' => 'album', 'action' => 'delete', 'group_id' => $this->group->getIdentity(),'album_id'=>$this->album->getIdentity()), $this->translate('Delete Album'), array(
    'class' => 'buttonlink icon_group_delete smoothbox'
  )) ?>
  <?php if($this->canEdit)
      echo $this->htmlLink(array('route' => 'group_extended','controller' => 'photo', 'action' => 'upload', 'subject' => $this->group->getGuid(),'album_id'=>$this->album->getIdentity()), $this->translate('Add More Photos'), array(
    'class' => 'buttonlink icon_group_photo_new'
  )) ?>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <p><?php echo $this->album->description?></p>
  <br/>
  <ul class="thumbs">
    <?php foreach( $this->paginator as $photo ): ?>
     <li id='thumbs_nocaptions_<?php echo $photo->getIdentity()?>'>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
        </a>
         <?php if($this->group->isOwner($this->viewer) || $this->group->isOfficer()):?>
        <p id="nocaptions_photo_<?php echo $photo->getIdentity()?>">
        	<a style="font-size: 10px" id="nocaptions_photo_slideshow_<?php echo $photo->getIdentity()?>" class="buttonlink <?php if($photo->is_featured) echo 'icon_event_delete'; else echo 'icon_event_slideshow';?>" onclick="return slideshow(<?php echo $photo->getIdentity()?>)" href="javascript:void();" ><?php if($photo->is_featured) echo $this->translate("Remove from Slideshow"); else echo $this->translate("Add to Slideshow");?></a>
        	<a style="font-size: 10px"  id="nocaptions_photo_remofile_<?php echo $photo->getIdentity()?>" class="buttonlink icon_event_delete" onclick="return removeFile(<?php echo $photo->getIdentity()?>)" href="javascript:void();" ><?php echo $this->translate("Remove from Club")?></a>
        </p>
        <?php endif;?>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
)); ?>
  <?php endif; ?>
  <br/>
  <?php 
	//echo $this->action("list", "comment", "core", array("type"=>"advgroup_album", "id"=>$this->album->getIdentity()));
	echo $this->content()->renderWidget('core.comments', array("type"=>"advgroup_album", "id"=>$this->album->getIdentity()));
  ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded in this album yet.');?>
    </span>
  </div>

<?php endif; ?>
<script type="text/javascript">
	function slideshow(photo_id)
   	{
		request = new Request.JSON({
			'format' : 'json',
            'url' :  en4.core.baseUrl + 'groups/photo/set-slideshow',
            'data': {
            	'photo_id' : photo_id,
            },
            'onSuccess' : function(responseJSON) {
            	if(responseJSON.status)
            	{
            		obj = $('nocaptions_photo_slideshow_'+responseJSON.photo_id);
					obj.set('class','buttonlink icon_event_delete');
					obj.set('html',en4.core.language.translate('Remove from Slideshow'));
            	}
            	else
            	{
            		obj = $('nocaptions_photo_slideshow_'+responseJSON.photo_id);
            		obj.set('class','buttonlink icon_event_slideshow');
					obj.set('html',en4.core.language.translate('Add to Slideshow'));
            	}
            }
		});
        request.send();
        return false;
   	};
   	
   	function removeFile(photo_id)
   	{
   		var action = confirm(en4.core.language.translate('Are you sure you want to delete this photo?'));
   		
   		if(action)
   		{
   			request = new Request.JSON({
				'format' : 'json',
	            'url' :  en4.core.baseUrl + 'groups/photo/delete-photo',
	            'data': {
	            	'photo_id' : photo_id,
	            },
	            'onSuccess' : function(responseJSON) {
	            	
	            }
			});
	        request.send();
	        
	        $('thumbs_nocaptions_'+photo_id).dispose();
   		}
		
		return false;
   	}  
	
</script>
<!-- Header -->
<h2>
    <?php echo $this->group->__toString();
          echo $this->translate('&#187; Files');
    ?>
</h2>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>
<!-- Menu Bar -->
<div class="group_discussions_options">
  <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Club'), array(
    'class' => 'buttonlink icon_back'
  )) ?>

</div>
<!-- Search Form -->
<div class="album_search_form">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<!-- Content -->
 <?php if ($this->canCreate) : ?>
	<div class="ynfs_block">
		<?php if($this->canCreate): ?>
		<?php
			echo $this->htmlLink(
				$this->url(
					array(
						'controller' => 'folder', 
						'action' => 'create',
						'parent_type' => $this->parentType,
						'parent_id' => $this->parentId,
						'subject_id' => 'group_'.$this->parentId,
						
					), 
					'ynfilesharing_general', 
					true
				), 
				$this->translate('Create a new folder'),
				array('class' => 'buttonlink ynfs_folder_add_icon')); 
		?>
			
		<?php endif;?>
			
	
	<div class="ynfs_block" style="font-weight: bold; font-size: 11px;">
		<?php 
		if($this -> maxSizeKB && $this -> maxSizeKB > 0)
			echo $this -> translate("%s MB of %s MB used",$this -> totalUploaded, $this -> maxSizeKB);
		else
			echo $this -> translate("%s MB of Unlimited",$this -> totalUploaded);
		?>
	</div>
<?php endif;?>



<?php if (!empty($this->messages)) : ?>
	<ul class="<?php echo empty($this->error)?'ynfs_notices':'ynfs_fail_notices'?>">
		<?php foreach ($this->messages as $mess) : ?>
			<li><?php echo $mess?></li>
		<?php endforeach;?>
	</ul>
<?php endif?>

<?php 
	echo $this->partial(
		'_browse_folders.tpl', 
		'advgroup', 
		array(
			'subFolders' => $this->subFolders, 
			'foldersPermissions' => $this->foldersPermissions, 
			'files' => $this->files,
			'parentType' => $this->parentType,
			'parentId' => $this->parentId,
			'canCreate' => $this->canCreate,
			'canDeleteRemove' => $this->canDeleteRemove,
			'group' => $this->group
		)
	);
?>
<?php
	$this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<?php
	 $ynvideo_enable = Engine_Api::_() -> advgroup() ->checkYouNetPlugin('ynvideo');
 ?>
<?php if($this->subject()->isOwner($this->viewer())) :?>
	<?php 
		if($ynvideo_enable)
		{
			echo $this->htmlLink(array(
				'route' => 'video_general',
				'action' => 'create',
				'parent_type' =>'group',
				'subject_id' =>  $this->group->group_id,
			), $this->translate('Add Video'), array(
			'class' => 'tf_button_action'
			)) ;
		}
		else
		{
			echo $this->htmlLink(array(
				'route' => 'video_general',
				'action' => 'create',
				'parent_type' =>'group',
				'subject_id' =>  $this->group->getGuid(),
			), $this->translate('Add Video'), array(
			'class' => 'tf_button_action'
			)) ;
		}
	?>
<?php endif; ?>

<?php
if($this->paginator -> getTotalItemCount()):?>
    <ul class="videos_browse" id="ynvideo_recent_videos">
        <?php foreach ($this->paginator as $item): ?>
        <?php
             $table = Engine_Api::_() -> getDbTable('highlights', 'advgroup');
             $select = $table -> select() -> where("group_id = ?", $this->group->group_id) -> where('item_id = ?', $item->getIdentity()) -> where("type = 'video'") -> limit(1);		
			 $row = $table -> fetchRow($select);
		?>
            <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
                <?php
	        		echo $this->partial('_players_of_week.tpl', 'ynvideo', array(
	        			'video' => $item
	        		));
	            ?>
               
               <?php if($this -> viewer() -> getIdentity() && Engine_Api::_()->user()->canTransfer($item)) :?>
				<div class="tf_btn_action">
					<?php
						echo $this->htmlLink(array(
				            'route' => 'user_general',
				            'action' => 'transfer-item',
							'subject' => $item -> getGuid(),
				        ), '<i class="fa fa-exchange fa-lg"></i>', array(
				            'class' => 'smoothbox btn-exchange', 'title' => $this -> translate('Transfer to user profile')
				        ));
					?>
				</div>
				<?php endif;?>
				<?php if($item -> isOwner($this->viewer())) :?>
				<div class="tf_btn_action">
				<?php
					echo $this->htmlLink(array(
						'route' => 'video_general',
						'action' => 'edit',
						'video_id' => $item->video_id,
						'parent_type' =>'group',
						'subject_id' =>  $this->group->getIdentity(),
				    ), '<i class="fa fa-pencil-square-o fa-lg"></i>', array('class' => 'tf_button_action'));
				?>
			    </div>
			    <div class="tf_btn_action">
				<?php
					echo $this->htmlLink(array(
				 	        'route' => 'video_general', 
				         	'action' => 'delete', 
				         	'video_id' => $item->video_id, 
				         	'format' => 'smoothbox'), 
				         	'<i class="fa fa-trash-o fa-lg"></i>', array('class' => 'tf_button_action smoothbox'
				     ));
				?>
				</div>
				<?php endif;?>
            </li>
            
        <?php endforeach; ?>
    </ul>
    <?php if($this->paginator->getTotalItemCount() > $this->itemCountPerPage):?>
	  <?php echo $this->htmlLink($this -> url(array(), 'default', true).'search?type%5B%5D=video&parent_type=group&parent_id='.$this->subject()->getIdentity(), $this -> translate('View all'), array('class' => 'icon_event_viewall')) ?>
	<?php endif;?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No videos have been added in this group yet.');?>
    </span>
  </div>
<?php endif;?>
<div id='profile_photo'>
	<?php $photoUrl = $this->subject() -> getPhotoUrl('thumb.profile');?>
	<?php if(Engine_Api::_()->authorization()->isAllowed('user', $this->subject(), 'show_badge')):?>
	<div class="options">
		<?php 
		$badge = Engine_Api::_()->authorization()->getPermission($this->subject(), 'user', 'badge');
		if($badge):
		?>
		<span style="background-size:cover; background-repeat: no-repeat;background-position:0; background-image: url('./<?php echo $badge?>');"></span>
		<?php endif;?>
	</div>
	<?php endif;?>
	<div class="avatar">
		<span>
			<a href="">
				<span alt="" class="thumb_profile_stroke" style="">
					<span alt="" class="thumb_profile_innershadow" style="">
						<span alt="" class="thumb_profile" style="background-image:url(<?php echo $photoUrl?>)"></span>
					</span>
				</span>
			</a>
		</span>
	</div>
	<div class="nickname">
		<span><?php echo $this->subject() -> getTitle()?></span>
	</div>
	<div class="user_rating">
		<span class="rating_star_generic rating_star_big"></span>
		<span class="rating_star_generic rating_star_big"></span>
		<span class="rating_star_generic rating_star_big"></span>
		<span class="rating_star_generic rating_star_big"></span>
		<span class="rating_star_generic rating_star_big_disabled"></span>
	</div>
	<div class="actions">
		<div>
		<table><tr>
		<td>
			<?php echo $this->htmlLink(array(
	            'route' => 'messages_general',
	            'action' => 'compose',
	            'to' => $this -> subject() ->getIdentity()
	        ), '<span class="actions_generic messaging"></span>', array(
	            'class' => 'smoothbox'
	        ));
    		?>
		</td>
		<td>
			<?php echo $this->htmlLink(array(
	            'route' => 'default',
	            'module' => 'activity',
	            'controller' => 'index',
				'action' => 'share',
				'type' => 'user',
				'id' => $this->subject() -> getIdentity(),
	        ), '<span class="actions_generic sharing"></span>', array(
	            'class' => 'smoothbox'
	        ));
    		?>
		</td>
		<td><a href=""><span class="actions_generic like"></span></a></td>
		</tr></table>
		</div>
	</div>
</div>
<div class="follow">
	<?php 
	$viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
	$subjectRow = $subject->membership()->getRow($viewer);
	if( null === $subjectRow ) 
	{
        // Follow
        echo $this->htmlLink(array(
	        'route' => 'user_extended',
	        'controller' => 'friends',
	        'action' => 'add',
	        'user_id' => $subject->getIdentity(),
	        'rev' => true
	    ), $this -> translate("Follow"), array(
	        'class' => 'smoothbox profile_follow'
	    ));
    }
	else if( $subjectRow->resource_approved == 0 ) {
		// Cancel Follow
        echo $this->htmlLink(array(
	        'route' => 'user_extended',
	        'controller' => 'friends',
	        'action' => 'cancel',
	        'user_id' => $subject->getIdentity(),
	        'rev' => true
	    ), $this -> translate("Unfollow"), array(
	        'class' => 'smoothbox profile_unfollow'
	    ));
	}
	else
	{
		// Unfollow
        echo $this->htmlLink(array(
	        'route' => 'user_extended',
	        'controller' => 'friends',
	        'action' => 'remove',
	        'user_id' => $subject->getIdentity(),
	        'rev' => true
	    ), $this -> translate("Unfollow"), array(
	        'class' => 'smoothbox profile_unfollow'
	    ));
	} 
	?>
</div>
<div class="leftmenu">
	<ul>
		<hr/>
		<li class="">
			<a href="<?php echo $subject -> getHref();?>"><?php echo $this -> translate('Profile')?></a>
		</li>
		<hr/>
		<li class="">
			<a href="">Media</a>
		</li>
		<hr/>
		<li class="">
			<a href="">Players</a>
		</li>
		<hr/>
		<li class="">
			<a href="">This</a>
		</li>
		<hr/>
		<li class="">
			<a href="">Section</a>
		</li>
		<hr/>
	</ul>
</div>
<?php if($this -> viewer() -> isSelf($this -> group -> getOwner())):?>

 <?php echo $this->htmlLink(
        array(
            'route' => 'group_specific',
            'action' => 'email-to-followers',
            'group_id' => $this->group->getIdentity()
        ),
        $this->translate('<span class="fa fa-envelope"></span> Email to Followers'),
        array(
            'class' => 'smoothbox'
        )
    )
?>

<?php endif;?>

<ul class='group_members'>
	<?php foreach( $this->followers as $follower ): ?>
		<?php $member = Engine_Api::_() -> getItem('user', $follower -> user_id);?>
		<?php if($member -> getIdentity()) :?>
			<li>
				<div class="content">
					<div class="photo">
						<a href="<?php echo $member->getHref() ?>">
							<?php echo $this -> itemPhoto($member);?>			
						</a>				
					</div>
					<div class='group_members_body'>
						<div class="title">
							<strong><?php echo $this->htmlLink($member->getHref(), $member->getTitle()); ?> </strong>
						</div>
					</div>
				</div>
			</li>
		<?php endif;?>
	<?php endforeach;?>
</ul>
	
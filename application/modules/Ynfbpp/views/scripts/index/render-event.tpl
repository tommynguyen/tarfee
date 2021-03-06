<?php $settings =  Engine_Api::_() -> getApi('settings', 'core');
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
?>
<div class="uiContextualDialogContent">
	<div class="uiYnfbppHovercardStage">
		<div class="uiYnfbppHovercardContent">
			<table cell-padding="0">
				<tr>
					<td rowspan="2" valign="top">
					<div class="uiYnfbppScaledImageContainer">
						<?php echo $this->htmlLink($this->subject->getHref(), $this->itemPhoto($this->subject, 'thumb.profile', $this->subject->getTitle(), array('width' => '100px', 'height' => '100px'))); ?>
					</div></td>
					<td  valign="top">
					<div class="uiYnfbppHovercardTitle">
						<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
					</div>
					<ul class="uiYnfbppHovercardInfo">
						<?php if(!$allow): ?>
							<li>
								<?php echo $this->translate('You have no permission to view this event.') ?>
							</li>	
						<?php endif; ?>
						<?php if($allow && $settings->getSetting('ynfbpp.event.description') &&  !empty($this->subject->description)): ?>
						<li>
							<?php echo $this->string()->truncate($this->string()->stripTags($this->subject->description), 100) ?>
						</li>
						<?php endif; ?>
						<li>
							<?php echo $this->timestamp($this->subject->starttime, array('class'=>'eventtime')) ?>
						</li>
						<?php if($allow && $settings->getSetting('ynfbpp.event.owner',1)): ?>
						<li class="uiYnfbppRow">
							<div>
								<?php echo $this->translate('Led by') ?>:
							</div>
							<span> <?php echo $this->subject->getOwner() ?> </span>
						</li>
						<?php endif; ?>
						<?php if($allow && $settings->getSetting('ynfbpp.event.host',1) && !empty($this->subject->host)): ?>
						<li class="uiYnfbppRow">
							<div>
								<?php echo $this->translate('Host') ?>:
							</div><span><?php 
							if(strpos($this->subject->host,'younetco_event_key_') !== FALSE)
							{
								$user_id = substr($this->subject->host, 19, strlen($this->subject->host));
								$user = Engine_Api::_() -> getItem('user', $user_id);
								echo $this->htmlLink($user->getHref(), $user->getTitle());	
							}
							else {
								echo $this->string()->truncate($this->string()->stripTags($this->subject->host), 20) ;
							}	
							?></span>
						</li>
						<?php endif; ?>
						<?php if($allow && $settings->getSetting('ynfbpp.event.location',1) && !empty($this->subject->location)): ?>
						<li class="uiYnfbppRow">
							<div>
								<?php echo $this->translate('Location') ?>:
							</div>
							<span> <?php echo $this->string()->truncate($this->string()->stripTags($this->subject->location), 20) ?> </span>
						</li>
						<?php endif; ?>
					</ul></td>
				</tr>
				<tr>
				<td  valign="bottom"> 
					    <?php if($allow && $settings->getSetting('ynfbpp.event.mutual',1)): ?>
					<?php echo $this->mutualFriends($this->subject,null, $settings->getSetting('ynfbpp.event.mutuallimit',0), '%s friend joined','%s friends joined'); ?>
					<?php endif; ?> 
                </td>
				</tr>
			</table>
		</div>
	</div>
	<div class="uiYnfbppHovercardFooter">
		<?php if(isset($this->actions) && !empty($this->actions)): ?>
		<ul class="uiYnfbppListHorizontal">
			<?php foreach($this->actions as $action): ?>
			<li class="uiYnfbppListItem">
				<?php echo $action?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?> <div class="clearfix"></div>
	</div>
</div>
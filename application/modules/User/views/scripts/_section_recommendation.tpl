<?php
    $label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
	$render = ($manage && isset($params['render'])) ? $params['render'] : 'show';
	$recommendations = $user->getShowRecommendations();
	$enable = Engine_Api::_()->user()->checkSectionEnable($user, 'recommendation');
	$canAsk = ($manage) ? $user->canAskRecommendation() : false;
	$received = ($manage) ? $user->getReceivedRecommendations() : array();
	$pendings = ($manage) ? $user->getPendingRecommendations() : array();
	$requests = ($manage) ? $user->getRequestRecommendations() : 0;
	$request = ($manage || !$viewer->getIdentity()) ? false : $user->getRecommendation($viewer->getIdentity());
	$canRecommendation = ($manage) ? false : ($viewer->getIdentity() && (!$request || $request->request) && $viewer->isFriend($user->getIdentity()));
?>
<?php if (($manage || count($recommendations) || $canRecommendation) && $enable) : ?>
<div class="icon_section_profile"><i class="fa fa-comments-o"></i></div>
<table>
  <tr>
  	<th><hr></th>  
  	<th><h3 class="section-label"><?php echo $this->translate($label);?></h3></th>
  	<th><hr></th>
  </tr>
</table>
<div class="profile-section-button">
<?php if ($canAsk) :?>
	<span class="manage-section-button">
		<?php echo $this->htmlLink(array('route'=>'user_recommendation', 'action'=>'ask'), '<i class="fa fa-plus-square"></i>', array('class' => 'recommendation-popup', 'rel'=>$render))?>
	</span>	
<?php endif;?>

<?php if ($manage) :?>
	<a class="recommendation manage-section-button right <?php if ($render == 'show') echo 'active'?>" rel="show" href="javascript:void(0)"><?php echo $this->translate('View')?></a>
		
	<a class="recommendation manage-section-button right <?php if ($render == 'received') echo 'active'?>" rel="received" href="javascript:void(0)"><?php echo $this->translate('Manage')?></a>
	
	<?php if (count($pendings)|| ($render == 'pending')) : ?>
	<a class="recommendation manage-section-button right <?php if ($render == 'pending') echo 'active'?>" rel="pending" href="javascript:void(0)"><?php echo $this->translate(array('(%s) pending', '(%s) pendings', count($pendings)), count($pendings))?></a>
	<?php endif; ?>
	
	<?php if (count($requests) || ($render == 'request')) : ?>
	<a class="recommendation manage-section-button right <?php if ($render == 'request') echo 'active'?>" rel="request" href="javascript:void(0)"><?php echo $this->translate(array('(%s) request', '(%s) requests', count($requests)), count($requests))?></a>
	<?php endif; ?>
<?php endif?>	
</div>

<?php if ($canRecommendation) : ?>
<?php $message = ($request) ? $this->translate('%s is waiting for your recommendation. Recommend now!', $user->getTitle()) : $this->translate('Recommend for %s', $user->getTitle());?>
<?php echo $this->htmlLink(array('route'=>'user_recommendation', 'action'=>'give', 'receiver_id'=>$user->getIdentity()), $message, array('class' => 'recommendation-popup notice', 'rel'=>$render))?>
<?php endif;?>

<div class="profile-section-loading" style="display: none; text-align: center">
    <img src='application/modules/User/externals/images/loading.gif'/>
</div>

<div class="profile-section-content">
	<div class="profile-section-list">
	<?php if ($render == 'show') : ?>
	<?php if (count($recommendations)) : ?>
		<ul id="recommendation-list" class="section-list">
	    <?php foreach ($recommendations as $item) :?>
	    <li class="section-item" id="recommendation-<?php echo $item->getIdentity()?>">
	    	<div class="giver-info">
	    		<?php $giver = Engine_Api::_()->user()->getUser($item->giver_id);?>
	    		<div class="photo"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'), array())?></div>
	    		<div class="title"><?php echo $giver?></div>
	    	</div>
	    	<div class="recommendation-content">
	    		<div class="content">
	    			<?php echo $this->viewMore($item->content, 255);?>
	    		</div>
	    		<div class="time">
	    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
	    		</div>
	    	</div>
	    </li>
	    <?php endforeach;?>
	   </ul>
	<?php else: ?>
		<div class="tip">
			<span><?php echo $this->translate('Don\'t have any recommendations')?></span>
		</div>
	<?php endif; ?>
	<?php endif; ?>
	
	<?php if ($render == 'received') : ?>
	<?php if (count($received)) :?>
	<form rel="recommendation" method="post" class="section-form">
		<input type="hidden" name="render" value="received" />
		<ul id="recommendation-list" class="section-list">
		<?php foreach ($received as $item):?>
			<li class="recommendation-item" id="recommendation-<?php echo $item->getIdentity()?>">
		    	<div class="giver-info">
		    		<?php $giver = Engine_Api::_()->user()->getUser($item->giver_id);?>
		    		<div class="photo"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'), array())?></div>
		    		<div class="title"><?php echo $giver?></div>
		    	</div>
		    	<div class="recommendation-content">
		    		<div class="content">
		    			<?php echo $this->viewMore($item->content, 255);?>
		    		</div>
		    		<div class="time">
		    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
		    		</div>
		    	</div>
		    	<div class="recommendation-options">
		    		<div class="checkbox-wrapper">
		    			<input type="checkbox" name="show_checkbox" id="show-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>" <?php if ($item->show) echo 'checked'?>/>
		    			<label for="show-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Show on Profile')?></label>
		    		</div>
		    		<div class="checkbox-wrapper">
		    			<input type="checkbox" name="delete_checkbox" id="delete-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
		    			<label for="delete-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Delete')?></label>
		    		</div>
		    	</div>
		    </li>
		<?php endforeach;?>
		</ul>
		<button type="submit"><?php echo $this->translate('Save change')?></button>
	</form>
	<?php else:?>
	<div class="tip">
		<span><?php echo $this->translate('No received recommendations.')?></span>
	</div>
	<?php endif;?>
	<?php endif;?>
	
	<?php if ($render == 'pending') : ?>
	<?php if (count($pendings)) :?>
	<h3><?php echo $this->translate(array('%s recommendation waiting for approve', '%s recommendations waiting for approve', count($pendings)),count($pendings))?></h3>
	<form rel="recommendation" method="post" class="section-form">
		<input type="hidden" name="render" value="pending" />
		<ul id="recommendation-list" class="section-list">
		<?php foreach ($pendings as $item):?>
			<li class="recommendation-item" id="recommendation-<?php echo $item->getIdentity()?>">
		    	<div class="giver-info">
		    		<?php $giver = Engine_Api::_()->user()->getUser($item->giver_id);?>
		    		<div class="photo"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'), array())?></div>
		    		<div class="title"><?php echo $giver?></div>
		    	</div>
		    	<div class="recommendation-content">
		    		<div class="content">
		    			<?php echo $this->viewMore($item->content, 255);?>
		    		</div>
		    		<div class="time">
		    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
		    		</div>
		    	</div>
		    	<div class="recommendation-options">
		    		<div class="checkbox-wrapper">
		    			<input type="checkbox" name="approve_checkbox" id="approve-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
    			<label for="approve-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Approve')?></label>
		    		</div>
		    		<div class="checkbox-wrapper">
		    			<input type="checkbox" name="delete_checkbox" id="delete-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
		    			<label for="delete-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Delete')?></label>
		    		</div>
		    	</div>
		    </li>
		<?php endforeach;?>
		</ul>
		<button type="submit"><?php echo $this->translate('Save change')?></button>
	</form>
	<?php else:?>
	<div class="tip">
		<span><?php echo $this->translate('No recommendations waiting for approve.')?></span>
	</div>
	<?php endif;?>
	<?php endif;?>
	
	<?php if ($render == 'request') : ?>
	<?php if (count($requests)) :?>
	<h3><?php echo $this->translate(array('%s person waiting your recommendation', '%s people waiting your recommendation', count($requests)),count($requests))?></h3>
	<form rel="recommendation" method="post" class="section-form">
		<input type="hidden" name="render" id="render" value="request" />
		<ul id="recommendation-list" class="section-list">
		<?php foreach ($requests as $item):?>
			<li class="recommendation-item" id="recommendation-<?php echo $item->getIdentity()?>">
		    	<div class="receiver-info">
		    		<?php $giver = Engine_Api::_()->user()->getUser($item->receiver_id);?>
		    		<span class="photo"><?php echo $this->htmlLink($giver->getHref(), $this->itemPhoto($giver, 'thumb.icon'), array())?></span>
		    		<span class="title"><?php echo $giver?></span>
		    	</div>
		    	<div class="recommendation-options">
		    		<div class="checkbox-wrapper">
		    			<input type="checkbox" name="ignore_checkbox" id="ignore-checkbox-<?php echo $item->getIdentity()?>" value="<?php echo $item->getIdentity()?>"/>
		    			<label for="ignore-checkbox-<?php echo $item->getIdentity()?>"><?php echo $this->translate('Ignore request')?></label>
		    		</div>
		    		<div class="button-wrapper">
		    			<?php echo $this->htmlLink(array('route'=>'user_recommendation', 'action'=>'give', 'receiver_id'=>$item->receiver_id), $this->translate('Write recommendation'), array('class' => 'recommendation-popup', 'rel'=>'request'))?>
		    		</div>
		    	</div>
		    </li>
		<?php endforeach;?>
		</ul>
		<button type="submit"><?php echo $this->translate('Save change')?></button>
	</form>
	<?php else:?>
	<div class="tip">
		<span><?php echo $this->translate('No people waiting your recommendation.')?></span>
	</div>
	<?php endif;?>
	<?php endif;?>
	
	</div>
</div>
<?php endif;?>
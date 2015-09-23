<?php
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
<div id="user-profile-recommendation">
<!-- <h3 class="section-label"><?php //echo $this->translate('Recommendations');?></h3> -->
<div class="profile-section-button">
<?php if ($canAsk) :?>
	<span class="manage-button">
		<?php echo $this->htmlLink(array('route'=>'user_recommendation', 'action'=>'ask'), $this->translate('Ask for recommendations'), array('class' => 'widget-recommendation-popup', 'rel'=>$render))?>
	</span>	
<?php endif;?>

<?php if ($manage) :?>
	<a class="recommendation manage-button right <?php if ($render == 'show') echo 'active'?>" rel="show" href="javascript:void(0)"><?php echo $this->translate('View')?></a>
		
	<a class="recommendation manage-button right <?php if ($render == 'received') echo 'active'?>" rel="received" href="javascript:void(0)"><?php echo $this->translate('Manage')?></a>
	
	<?php if (count($pendings)|| ($render == 'pending')) : ?>
	<a class="recommendation manage-button right <?php if ($render == 'pending') echo 'active'?>" rel="pending" href="javascript:void(0)"><?php echo $this->translate(array('(%s) pending', '(%s) pendings', count($pendings)), count($pendings))?></a>
	<?php endif; ?>
	
	<?php if (count($requests) || ($render == 'request')) : ?>
	<a class="recommendation manage-button right <?php if ($render == 'request') echo 'active'?>" rel="request" href="javascript:void(0)"><?php echo $this->translate(array('(%s) request', '(%s) requests', count($requests)), count($requests))?></a>
	<?php endif; ?>
<?php endif?>	
</div>

<?php if ($canRecommendation) : ?>
<?php $message = ($request) ? $this->translate('%s is waiting for your recommendation. Recommend now!', $user->getTitle()) : $this->translate('Recommend for %s', $user->getTitle());?>
<?php echo $this->htmlLink(array('route'=>'user_recommendation', 'action'=>'give', 'receiver_id'=>$user->getIdentity()), $message, array('class' => 'widget-recommendation-popup notice', 'rel'=>$render))?>
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
	    	</div>
	    	<div class="recommendation-content">
	    		<div class="content">
	    			<?php echo $this->viewMore($item->content, 255);?>
	    		</div>
	    		<div class="time">
	    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
	    		</div>
	    		<div class="title"><?php echo $giver?></div>
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
		    	</div>
		    	<div class="recommendation-content">
		    		<div class="content">
		    			<?php echo $this->viewMore($item->content, 255);?>
		    		</div>
		    		<div class="time">
		    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
		    		</div>
		    		<div class="title"><?php echo $giver?></div>

			    	<div class="recommendation-options">
			    		<div class="button-wrapper">
			    			<?php if ($item->show) :?>
			    			<button type="button" class="recommendation-hide-btn" rel="received" value="<?php echo $item->getIdentity()?>"><?php echo $this->translate('Hide')?></button>
			    			<?php else: ?>
			    			<button type="button" class="recommendation-show-btn" rel="received" value="<?php echo $item->getIdentity()?>"><?php echo $this->translate('Show')?></button>
			    			<?php endif; ?>
			    			<button type="button" class="recommendation-delete-btn" rel="received" value="<?php echo $item->getIdentity()?>"><?php echo $this->translate('Delete')?></button>
			    		</div>
			    	</div>
		    	</div>
		    </li>
		<?php endforeach;?>
		</ul>
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
		    	</div>
		    	<div class="recommendation-content">
		    		<div class="content">
		    			<?php echo $this->viewMore($item->content, 255);?>
		    		</div>
		    		<div class="time">
		    			<?php echo date('M, d, Y', $item->getGivenDate()->getTimestamp());?>
		    		</div>
		    		<div class="title"><?php echo $giver?></div>
			    	<div class="recommendation-options">
			    		<div class="button-wrapper">
			    			<button type="button" class="recommendation-approve-btn" rel="pending" value="<?php echo $item->getIdentity()?>"><?php echo $this->translate('accept')?></button>
			    			<button type="button" class="recommendation-delete-btn" rel="pending" value="<?php echo $item->getIdentity()?>"><?php echo $this->translate('decline')?></button>
			    		</div>
			    	</div>
		    	</div>
		    </li>
		<?php endforeach;?>
		</ul>
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
		    	</div>
		    	<div class="recommendation-options">
		    		<span class="title" style="text-align: left"><?php echo $giver?></span>
		    		<div class="button-wrapper" style="text-align: left">
		    			<button type="button" class="recommendation-delete-btn" rel="request" value="<?php echo $item->getIdentity()?>"><?php echo $this->translate('Ignore')?></button>
		    			<button type="button" class="widget-recommendation-popup" rel="request" href="<?php echo $this->url(array('action'=>'give', 'receiver_id'=>$item->receiver_id), 'user_recommendation', true)?>"><?php echo $this->translate('Write Recommendation')?></button>
		    		</div>
		    		</div>
		    	</div>
		    </li>
		<?php endforeach;?>
		</ul>
	</form>
	<?php else:?>
	<div class="tip">
		<span><?php echo $this->translate('No people waiting your recommendation.')?></span>
	</div>
	<?php endif;?>
	<?php endif;?>
	
	</div>
</div>
</div>
<?php endif;?>

<script type="text/javascript">
var reloadWidget = false;
var currentRender = '';

function reloadRecommendationWidget(params) {
    if ($('user-profile-recommendation')) {
        var content = $('user-profile-recommendation').getElement('.profile-section-content');
        var loading = $('user-profile-recommendation').getElement('.profile-section-loading');
        if (loading) {
            loading.show();
        }
        if (content) {
            content.hide();
        }
    }
    params.subject = '<?php echo $user->getGuid()?>'
    params.format = 'html';
    var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/name/user.profile-recommendation',
        data : params,
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            elements = Elements.from(responseHTML);
            if (elements.length > 0) {
                if ($('user-profile-recommendation')) {
                    var parent = $('user-profile-recommendation').getParent();
                    elements.replaces(parent);
                    eval(responseJavaScript);
                } 
            }
            else {
            	if ($('user-profile-recommendation')) {
            		$('user-profile-recommendation').parent().destroy();
            	}
            }
        }
    });
    request.send();
}

function addRecommendationEvent() {
	
}
window.addEvent('domready', function() {
	$$('.manage-button.recommendation').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var render = el.get('rel');
            var params = {};
            params.render = render;
            reloadRecommendationWidget(params);
        });
    });
    
    $$('.recommendation-hide-btn').each(function(el) {
    	el.removeEvents('click');
        el.addEvent('click', function(e){
            var render = el.get('rel');
            var params = {};
            params.hide = el.get('value');
            params.render = render;
            reloadRecommendationWidget(params);
        });
    });
    
    $$('.recommendation-show-btn').each(function(el) {
    	el.removeEvents('click');
        el.addEvent('click', function(e){
            var render = el.get('rel');
            var params = {};
            params.show = el.get('value');
            params.render = render;
            reloadRecommendationWidget(params);
        });
    });
    
    $$('.recommendation-delete-btn').each(function(el) {
    	el.removeEvents('click');
        el.addEvent('click', function(e){
            var render = el.get('rel');
            var params = {};
            params.delete = el.get('value');
            params.render = render;
            reloadRecommendationWidget(params);
        });
    });
    
    $$('.recommendation-approve-btn').each(function(el) {
    	el.removeEvents('click');
        el.addEvent('click', function(e){
            var render = el.get('rel');
            var params = {};
            params.approve = el.get('value');
            params.render = render;
            reloadRecommendationWidget(params);
        });
    });
    
    $$('.widget-recommendation-popup').each(function(el) {
    	el.removeEvents('click');
        el.addEvent('click', function(e){
        	e.preventDefault();
        	reloadWidget = true;
            currentRender = el.get('rel');
            var url = el.get('href');
            Smoothbox.open(url);
        });
    });
    
    Smoothbox.Modal.Iframe.prototype.onClose=function() {
		this.fireEvent('closeafter', this);
 		try{
 			if (reloadWidget == true && currentRender != '') {
 				var params = {};
 				params.render = currentRender;
 				reloadRecommendationWidget(params);
 				reloadWidget = false;
 				currentRender = '';
 			}
 		}
 		catch(ex){}
	};
});

</script>
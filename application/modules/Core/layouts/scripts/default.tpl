<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: default.tpl 10227 2014-05-16 22:43:27Z andres $
 * @author     John
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <base href="<?php echo rtrim('//' . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

  
  <?php // ALLOW HOOKS INTO META ?>
  <?php echo $this->hooks('onRenderLayoutDefault', $this) ?>


  <?php // TITLE/META ?>
  <?php
    $counter = (int) $this->layout()->counter;
    $staticBaseUrl = $this->layout()->staticBaseUrl;
    $headIncludes = $this->layout()->headIncludes;
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
      ->setSeparator(' - ');
    $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
        . '-' . $request->getControllerName();
    $pageTitle = $this->translate($pageTitleKey);
    if( $pageTitle && $pageTitle != $pageTitleKey ) {
      $this
        ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
    }
    $this
      ->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      ;
    $this->headMeta()
      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());
    
    // Make description and keywords
    $description = '';
    $keywords = '';
    
    $description .= ' ' .$this->layout()->siteinfo['description'];
    $keywords = $this->layout()->siteinfo['keywords'];

    if( $this->subject() && $this->subject()->getIdentity() ) {
      $this->headTitle($this->subject()->getTitle());
      
      $description .= ' ' .$this->subject()->getDescription();
      // Remove the white space from left and right side
      $keywords = trim($keywords);
      if ( !empty($keywords) && (strrpos($keywords, ',') !== (strlen($keywords) - 1)) ) {
        $keywords .= ',';
      }
      $keywords .= $this->subject()->getKeywords(',');
    }
    
    $keywords = trim($keywords, ',');
    
    $this->headMeta()->appendName('description', trim($description));
    $this->headMeta()->appendName('keywords', trim($keywords));

    // Get body identity
    if( isset($this->layout()->siteinfo['identity']) ) {
      $identity = $this->layout()->siteinfo['identity'];
    } else {
      $identity = $request->getModuleName() . '-' .
          $request->getControllerName() . '-' .
          $request->getActionName();
    }
  ?>
  <?php echo $this->headTitle()->toString()."\n" ?>
  <?php echo $this->headMeta()->toString()."\n" ?>


  <?php // LINK/STYLES ?>
  <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => ( isset($this->layout()->favicon)
        ? $staticBaseUrl . $this->layout()->favicon
        : '/favicon.ico' ),
      'type' => 'image/x-icon'),
      'PREPEND');
    $themes = array();
    if( !empty($this->layout()->themes) ) {
      $themes = $this->layout()->themes;
    } else {
      $themes = array('default');
    }
    foreach( $themes as $theme ) {
      if( APPLICATION_ENV != 'development' ) {
        $this->headLink()
          ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/themes/' . $theme . '/theme.css');
      } else {
        $this->headLink()
          ->prependStylesheet(rtrim($this->baseUrl(), '/') . '/application/css.php?request=application/themes/' . $theme . '/theme.css');
      }
    }
    // Process
    foreach( $this->headLink()->getContainer() as $dat ) {
      if( !empty($dat->href) ) {
        if( false === strpos($dat->href, '?') ) {
          $dat->href .= '?c=' . $counter;
        } else {
          $dat->href .= '&c=' . $counter;
        }
      }
    }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>

  <?php // TRANSLATE ?>
  <?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>

  <?php // SCRIPTS ?>
  <script type="text/javascript">if (window.location.hash == '#_=_')window.location.hash = '';</script>
  <script type="text/javascript">
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>

    Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');
    
    en4.orientation = '<?php echo $orientation ?>';
    en4.core.environment = '<?php echo APPLICATION_ENV ?>';
    en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');
    en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
    en4.core.staticBaseUrl = '<?php echo $this->escape($staticBaseUrl) ?>';
    en4.core.loader = new Element('img', {src: en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});
    
    <?php if( $this->subject() ): ?>
      en4.core.subject = {
        type : '<?php echo $this->subject()->getType(); ?>',
        id : <?php echo $this->subject()->getIdentity(); ?>,
        guid : '<?php echo $this->subject()->getGuid(); ?>'
      };
    <?php endif; ?>
    <?php if( $this->viewer()->getIdentity() ): ?>
      en4.user.viewer = {
        type : '<?php echo $this->viewer()->getType(); ?>',
        id : <?php echo $this->viewer()->getIdentity(); ?>,
        guid : '<?php echo $this->viewer()->getGuid(); ?>'
      };
    <?php endif; ?>
    if( <?php echo ( Engine_Api::_()->getDbtable('settings', 'core')->core_dloader_enabled ? 'true' : 'false' ) ?> ) {
      en4.core.runonce.add(function() {
        en4.core.dloader.attach();
      });
    }
    
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
  </script>
  <?php
    $this->headScript()
      ->prependFile($staticBaseUrl . 'externals/smoothbox/smoothbox4.js')
      ->prependFile($staticBaseUrl . 'application/modules/User/externals/scripts/core.js')
      ->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/core.js')
      ->prependFile($staticBaseUrl . 'externals/chootools/chootools.js')
      ->prependFile($staticBaseUrl . 'externals/mootools/mootools-more-1.4.0.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($staticBaseUrl . 'externals/mootools/mootools-core-1.4.5-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
    // Process
    foreach( $this->headScript()->getContainer() as $dat ) {
      if( !empty($dat->attributes['src']) ) {
        if( false === strpos($dat->attributes['src'], '?') ) {
          $dat->attributes['src'] .= '?c=' . $counter;
        } else {
          $dat->attributes['src'] .= '&c=' . $counter;
        }
      }
    }
  ?>
  <?php echo $this->headScript()->toString()."\n" ?>

  
  
  <?php echo $headIncludes ?>
  
  
  
</head>
<body id="global_page_<?php echo $identity ?>">
  <script type="javascript/text">
    if(DetectIpad()){
      $$('a.album_main_upload').setStyle('display', 'none');
      $$('a.album_quick_upload').setStyle('display', 'none');
      $$('a.icon_photos_new').setStyle('display', 'none');
    }
  </script>  
  
  <div id="global_header">
    <?php echo $this->content('header') ?>
  </div>
  <div id='global_wrapper'>
    <div id='global_content'>
    	<?php
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request -> getActionName();
		if($module != 'invite' && !in_array($pageTitleKey,array('pagetitle-user-home-index', 'pagetitle-user-index-profile', 'pagetitle-social-connect-index-signup', 'pagetitle-social-connect-identity-exists-index', 'pagetitle-social-connect-add-password-index', 'pagetitle-sladvsubscription-choose-subscription', 'pagetitle-user-confirm-trial-index', 'pagetitle-payment-gateway-subscription', 'pagetitle-social-connect-add-email-index', 'pagetitle-social-connect-verify-code-map-user-index', 'pagetitle-user-index-signup', 'pagetitle-user-login-auth', 'pagetitle-user-forgot-auth', 'pagetitle-core-notfound-error', 'pagetitle-user-confirm-signup'))):
		?>
    	<div class="layout_page_breadcrumb">
	    	<ul id="breadcrumb" class="breadcrumb">
	    		<li>
	    			<a href="<?php echo $this -> url(array(), 'default', true)?>" title="<?php echo $this -> translate("Home")?>">
	    				<img src="application/themes/ynresponsive-event/images/home.png" alt= "<?php echo $this -> translate("Home")?>" />
	    			</a>
	    		</li>
	    		<?php if($module == 'tfcampaign'):?>
		    		<li <?php if($action == 'browse') echo 'class="active"'?>>
		    			<a href="<?php echo $this -> url(array(), 'tfcampaign_general', true)?>" title = "<?php echo $this -> translate("Campaigns")?>">
		    				<?php echo $this -> translate("Campaigns")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'create'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Add new campaign")?>">
			    				<?php echo $this -> translate("Add New Campaign")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    	<?php elseif($module == 'ynvideo'):?>
		    		<li>
		    			<a href="<?php echo $this -> url(array(), 'default', true).'search?type%5B%5D=video'?>" title = "<?php echo $this -> translate("Videos")?>">
		    				<?php echo $this -> translate("Videos")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'create'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Add new video")?>">
			    				<?php echo $this -> translate("Add New Video")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    	<?php elseif($module == 'ynsocialads'):?>
		    		<?php if($this -> viewer()):
		    			$club = Engine_Api::_() -> advgroup() -> getGroupUser($this -> viewer());
		    			if($club):?>
		    			<li>
			    			<a href="<?php echo $club -> getHref()?>">
			    				<?php echo $this -> translate('Organization page');?>
			    			</a>
			    		</li>
		    			<?php endif;?>
		    		<?php endif;?>
		    		<li>
		    			<a href="<?php echo $this -> url(array(), 'ynsocialads_campaigns', true)?>" title = "<?php echo $this -> translate("Ads Management")?>">
		    				<?php echo $this -> translate("Ads Management")?>
		    			</a>
		    		</li>
		    	<?php elseif($module == 'ynblog'):?>
		    		<li <?php if($action == 'listing') echo 'class="active"'?>>
		    			<a href="<?php echo $this -> url(array(), 'blog_general', true)?>" title = "<?php echo $this -> translate("Talks")?>">
		    				<?php echo $this -> translate("Talks")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'create'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Add new talk")?>">
			    				<?php echo $this -> translate("Add New Talk")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    	<?php elseif($module == 'ynevent'):?>
		    		<li <?php if($action == 'listing') echo 'class="active"'?>>
		    			<a href="<?php echo $this -> url(array(), 'event_general', true)?>" title = "<?php echo $this -> translate("Events")?>">
		    				<?php echo $this -> translate("Events")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'create'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Add new event/tryout")?>">
			    				<?php echo $this -> translate("Add New Event/Tryout")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    	<?php elseif($module == 'user' && $controller == 'player-card'):?>
		    		<li>
		    			<a href="<?php echo $this -> url(array(), 'default', true).'search?advsearch=player'?>" title = "<?php echo $this -> translate("Player Cards")?>">
		    				<?php echo $this -> translate("Player Cards")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'create'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Add new player card")?>">
			    				<?php echo $this -> translate("Add New Player Card")?>
			    			</a>
			    		</li>
			    	<?php endif;?>
			    <?php elseif($module == 'advgroup'):?>
		    		<li>
		    			<a href="<?php echo $this -> url(array(), 'default', true).'search?advsearch=organization'?>" title = "<?php echo $this -> translate("Clubs")?>">
		    				<?php echo $this -> translate("Clubs")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'create'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Create new club")?>">
			    				<?php echo $this -> translate("Create New Club")?>
			    			</a>
			    		</li>
			    	<?php elseif($action == 'edit'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Edit club")?>">
			    				<?php echo $this -> translate("Edit Club")?>
			    			</a>
			    		</li>
			    	<?php endif;?>
			    <?php elseif($module == 'contactimporter'):?>
		    		<li class="active">
		    			<a href="#" title = "<?php echo $this -> translate("Friend inviter")?>">
		    				<?php echo $this -> translate("Friend Inviter")?>
		    			</a>
		    		</li>
		    	<?php elseif($module == 'ynresponsive1'):?>
		    		<li class="active">
		    			<a href="#" title = "<?php echo $this -> translate("Notifications")?>">
		    				<?php echo $this -> translate("Notifications")?>
		    			</a>
		    		</li>
		    	<?php elseif($module == 'ynadvsearch'):?>
		    		<?php if($request -> getParam('advsearch', '')):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate($request -> getParam('advsearch', ''))?>">
			    				<?php echo $this -> translate(ucfirst($request -> getParam('advsearch', ''))).'s';	?>
			    			</a>
			    		</li>
		    		<?php else:?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Search")?>">
			    				<?php echo $this -> translate("Search Result")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    	<?php elseif($module == 'core'):?>
		    		<?php if($action == 'contact'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Contact us")?>">
			    				<?php echo $this -> translate("Contact Us")?>
			    			</a>
			    		</li>
			    	<?php elseif($action == 'terms'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Terms of service")?>">
			    				<?php echo $this -> translate("Terms of Service")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    	<?php elseif($module == 'messages'):?>
		    		<li>
		    			<a href="<?php echo $this -> url(array('action' => 'inbox'), 'messages_general', true)?>" title = "<?php echo $this -> translate("Messages")?>">
		    				<?php echo $this -> translate("Messages")?>
		    			</a>
		    		</li>
		    		<?php if($action == 'inbox'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Inbox")?>">
			    				<?php echo $this -> translate("Inbox")?>
			    			</a>
			    		</li>
			    	<?php elseif($action == 'outbox'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Sent messages")?>">
			    				<?php echo $this -> translate("Sent Messages")?>
			    			</a>
			    		</li>
			    	<?php elseif($action == 'compose'):?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Compose messages")?>">
			    				<?php echo $this -> translate("Compose Messages")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    		<?php elseif($controller == 'settings'):?>
			    		<?php if($this -> subject()):?>
			    		<li>
			    			<a href="<?php echo $this -> subject() -> getHref()?>">
			    				<?php echo $this -> subject() -> getTitle();?>
			    			</a>
			    		</li>
		    			<?php endif;?>
		    			<?php if(in_array($action, array('general', 'password','delete','deactivate'))):?>
				    		<li class="active">
				    			<a href="#" title = "<?php echo $this -> translate("General settings")?>">
				    				<?php echo $this -> translate("General Settings")?>
				    			</a>
				    		</li>
				    	<?php elseif($action == 'privacy'):?>
				    		<li class="active">
				    			<a href="#" title = "<?php echo $this -> translate("Privacy settings")?>">
				    				<?php echo $this -> translate("Privacy Settings")?>
				    			</a>
				    		</li>
				    	<?php elseif($action == 'network'):?>
				    		<li class="active">
				    			<a href="#" title = "<?php echo $this -> translate("Networks settings")?>">
				    				<?php echo $this -> translate("Networks Settings")?>
				    			</a>
				    		</li>
				    	<?php elseif($action == 'notifications'):?>
				    		<li class="active">
				    			<a href="#" title = "<?php echo $this -> translate("Notifications settings")?>">
				    				<?php echo $this -> translate("Notifications Settings")?>
				    			</a>
				    		</li>
				    	<?php elseif($action == 'referral'):?>
				    		<li class="active">
				    			<a href="#" title = "<?php echo $this -> translate("Referral codes")?>">
				    				<?php echo $this -> translate("Referral Codes Generation")?>
				    			</a>
				    		</li>
	    				<?php elseif($module == 'sladvsubscription' && $action == 'index'): ?>
				    		<li class="active">
				    			<a href="#" title = "<?php echo $this -> translate("Membership")?>">
				    				<?php echo $this -> translate("Membership")?>
				    			</a>
				    		</li>
				    	<?php endif;?>
			    	<?php elseif($module == 'socialbridge' && $controller == 'index' && $action == 'index'):?>
		    			<li>
			    			<a href="<?php echo $this -> url(array('controller' => 'settings', 'action' => 'general'), 'user_extended', true)?>" title = "<?php echo $this -> translate("Settings")?>">
			    				<?php echo $this -> translate("Settings")?>
			    			</a>
			    		</li>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Find & invite friends")?>">
			    				<?php echo $this -> translate("Find & Invite Friends")?>
			    			</a>
			    		</li>
			    	<?php elseif($module == 'social-connect' && $controller == 'index' && $action == 'account-linking'):?>
			    		<?php if($this -> subject()):?>
			    		<li>
			    			<a href="<?php echo $this -> subject() -> getHref()?>">
			    				<?php echo $this -> subject() -> getTitle();?>
			    			</a>
			    		</li>
		    			<?php endif;?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Social networks")?>">
			    				<?php echo $this -> translate("Social Networks")?>
			    			</a>
			    		</li>
			    	<?php elseif($module == 'slprofileverify' && $controller == 'index' && $action == 'setting-verification'):?>
			    		<?php if($this -> subject()):?>
			    		<li>
			    			<a href="<?php echo $this -> subject() -> getHref()?>">
			    				<?php echo $this -> subject() -> getTitle();?>
			    			</a>
			    		</li>
		    			<?php endif;?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Verifcation")?>">
			    				<?php echo $this -> translate("Verifcation")?>
			    			</a>
			    		</li>
		    		<?php elseif($module == 'user' && $controller == 'edit'):?>
			    		<?php if($this -> subject()):?>
			    		<li>
			    			<a href="<?php echo $this -> subject() -> getHref()?>">
			    				<?php echo $this -> subject() -> getTitle();?>
			    			</a>
			    		</li>
		    			<?php endif;?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Edit my profile")?>">
			    				<?php echo $this -> translate("Edit My Profile")?>
			    			</a>
			    		</li>
			    	<?php elseif($module == 'ynmember' && $controller == 'edit'):?>
			    		<?php if($this -> subject()):?>
			    		<li>
			    			<a href="<?php echo $this -> subject() -> getHref()?>">
			    				<?php echo $this -> subject() -> getTitle();?>
			    			</a>
			    		</li>
		    			<?php endif;?>
			    		<li class="active">
			    			<a href="#" title = "<?php echo $this -> translate("Edit my profile")?>">
			    				<?php echo $this -> translate("Edit My Profile")?>
			    			</a>
			    		</li>
		    		<?php endif;?>
		    		
		    		<?php if($this -> subject() && !in_array($action, array('edit','account-linking')) && !in_array($controller, array('settings','edit'))):?>
			    		<li class="active">
			    			<a href="<?php echo $this -> subject() -> getHref()?>">
			    				<?php echo $this -> subject() -> getTitle();?>
			    			</a>
			    		</li>
	    			<?php endif;?>
			</ul>
		</div>
	  <?php endif;?>
      <?php //echo $this->content('global-user', 'before') ?>
      <?php
      $content = $this->layout()->content;
	  $return = str_replace('href=', 'target="_blank" href=', $content);
	  $return = str_replace('target="_blank" href="http://tarfee.com', 'href="//tarfee.com', $return);
	  $return = str_replace('target="_blank" href="https://tarfee.com', 'href="//tarfee.com', $return);
	  $return = str_replace('target="_blank" href="http://www.tarfee.com', 'href="//www.tarfee.com', $return);
	  $return = str_replace('target="_blank" href="https://www.tarfee.com', 'href="//www.tarfee.com', $return);
	  $return = str_replace('target="_blank" href="/', 'href="//'.$_SERVER['HTTP_HOST'].'/', $return);
	  $return = str_replace('target="_blank" href=\'/', 'href=\'//'.$_SERVER['HTTP_HOST'].'/', $return);
	  $return = str_replace('target="_blank" href="#', 'href="#', $return);
	  $return = str_replace('target="_blank" href=\'#', 'href=\'#', $return);
	  $return = str_replace('target="_blank" href="javascript:;', 'href="javascript:;', $return);
	  $return = str_replace('target="_blank" href=\'javascript:;', 'href=\'javascript:;', $return);
	  $return = str_replace('target="_blank" href="javascript:void(0);', 'href="javascript:void(0);', $return);
	  $return = str_replace('target="_blank" href=\'javascript:void(0);', 'href=\'javascript:void(0);', $return);
	  $return = str_replace('target="_blank" href="javascript:void(0)', 'href="javascript:void(0)', $return);
	  $return = str_replace('target="_blank" href=\'javascript:void(0)', 'href=\'javascript:void(0)', $return);
	  $return = str_replace('target="_blank" href=\'javascript: void(sopopup', 'href=\'javascript: void(sopopup', $return);
	  $return = str_replace('target="_blank" href="javascript: void(sopopup', 'href="javascript: void(sopopup', $return);
	  $return = str_replace(' target = "_blank">', '>', $return);
	  echo $return;
      ?>
      <?php //echo $this->content('global-user', 'after') ?>
    </div>
  </div>
  <div id="global_footer">
    <?php echo $this->content('footer') ?>
  </div>
  <div id="janrainEngageShare" style="display:none">Share</div>
</body>
</html>
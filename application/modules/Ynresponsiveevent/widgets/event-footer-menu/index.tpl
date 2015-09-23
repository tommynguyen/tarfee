<?php 
$viewer = $this -> viewer();
$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getParam('module');
$advsearch = $request->getParam('advsearch');
?>
<div class="tf_right_menu">
   
   <span id="show-hide-list-items">
   </span>
   <ul class="list-items">
   	<?php if($viewer -> getIdentity()):
	$library =  $viewer -> getMainLibrary();
   	?>
      <li class="item-action" id="item-action-add">
         <a title="<?php echo $this -> translate("add")?>" href="javascript:void;"><img src="application/themes/ynresponsive-event/images/add.png" /></a>
         <ul class="list-items-dropdown">
         	<?php $videoUrl = $this->url(array(
					'action' => 'create',
					'parent_type' =>'user_library',
					'subject_id' =>  $library->getIdentity(),
					'tab' => 1490,
				), 'video_general', true) ;
			?>
           		<li><a href="<?php echo $videoUrl?>"><i class="fa fa-video-camera"></i><?php echo $this -> translate("Add Video")?></a></li>
            <?php 
            $max_player_card = Engine_Api::_()->authorization()->getPermission($viewer, 'user_playercard', 'max_player_card', 5);
	        if($max_player_card == "")
	         {
	             $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
	             $maselect = $mtable->select()
	                ->where("type = 'user_playercard'")
	                ->where("level_id = ?", $viewer -> level_id)
	                ->where("name = 'max_player_card'");
	              $mallow_a = $mtable->fetchRow($maselect);          
	              if (!empty($mallow_a))
	                $max_player_card = $mallow_a['value'];
	              else
	                 $max_player_card = 5;
	         }
			if(Engine_Api::_() -> getDbTable('playercards', 'user') -> getPlayersPaginator($viewer -> getIdentity()) ->getTotalItemCount() < $max_player_card):
	            $Url = $this->url(array(
						'controller' => 'player-card',
	            		'action' => 'create',
						'tab' => 724,
					), 'user_extended', true) ;
				?>
            	<li><a class="tfmenu_add_player" href="<?php echo $Url?>"><i class="fa fa-user-plus"></i><?php echo $this -> translate("Add Player Card")?></a></li>
            <?php endif;?>
            <?php if(Engine_Api::_() -> authorization() -> isAllowed('event', null, 'create')):?>
            	<li><a href="<?php echo $this->url(array('action' => 'create'), 'event_general')?>"><i class="fa fa-calendar"></i><?php echo $this -> translate("Add Event/Tryout")?></a></li>
            <?php endif;?>
            <li><a class="tfmenu_add" href="<?php echo $this -> url(array('action' => 'create'), 'tfcampaign_general', true);?>"><i class="tfcampaing_add"></i><?php echo $this -> translate("Add Campaign")?></a></li>
            <li><a class="tfmenu_add" href="<?php echo $this->url(array('action' => 'create'), 'blog_general')?>"><i class="tftalk_add"></i><?php echo $this -> translate("Add Talk")?></a></li>
            <li><a class="tfmenu_add" href="contactimporter/import"><i class="tf_find_friend"></i><?php echo $this -> translate("Find/Invite Friend")?></a></li>
         </ul>
      </li>
    <?php endif;?>
      <li><a <?php if($module == 'tfcampaign') echo "class='right_menu_active'"?> title="<?php echo $this -> translate("campaigns")?>" href="<?php echo $this -> url(array(), 'tfcampaign_general', true);?>">
      	<?php if($module == 'tfcampaign'):?>
      		<img src="application/themes/ynresponsive-event/images/campaign_active.png" />
  		<?php else:?>
      		<img src="application/themes/ynresponsive-event/images/campaign.png" />
      	<?php endif;?>
  	  </a></li>
      <li><a <?php if($module == 'ynadvsearch' && $advsearch == 'organization') echo "class='right_menu_active'"?> title="<?php echo $this -> translate("clubs & organizations")?>" href="search?advsearch=organization">
      	<?php if($module == 'ynadvsearch' && $advsearch == 'organization'):?>
      		<img src="application/themes/ynresponsive-event/images/club_active.png" />
  		<?php else:?>
      		<img src="application/themes/ynresponsive-event/images/club.png" />
      	<?php endif;?>
      </a></li>
      <li><a <?php if($module == 'ynevent') echo "class='right_menu_active'"?> title="<?php echo $this -> translate("events & tryouts")?>" href="<?php echo $this -> url(array(), 'event_general', true);?>">
      	<?php if($module == 'ynevent'):?>
      		<img src="application/themes/ynresponsive-event/images/event_active.png" />
  		<?php else:?>
      		<img src="application/themes/ynresponsive-event/images/event.png" />
      	<?php endif;?>
      </a></li>
      <li><a <?php if($module == 'ynblog') echo "class='right_menu_active'"?> title="<?php echo $this -> translate("talks")?>" href="<?php echo $this -> url(array(), 'blog_general', true);?>">
      	<?php if($module == 'ynblog'):?>
      		<img src="application/themes/ynresponsive-event/images/talk_active.png" />
  		<?php else:?>	
      		<img src="application/themes/ynresponsive-event/images/talk.png" />
      	<?php endif;?>
      </a></li>
      <li><a <?php if($module == 'ynadvsearch' && $advsearch == 'professional') echo "class='right_menu_active'"?>  title="<?php echo $this -> translate("professionals")?>" href="search?advsearch=professional">
      	<?php if($module == 'ynadvsearch' && $advsearch == 'professional'):?>
      		<img src="application/themes/ynresponsive-event/images/professional_active.png" />
  		<?php else:?>
      		<img src="application/themes/ynresponsive-event/images/professional.png" />
      	<?php endif;?>
      </a></li>
      
      <li class="item-action" id="item-action-help">
         <a title="<?php echo $this -> translate("help")?>" href="javascript:void;"><img src="application/themes/ynresponsive-event/images/help.png" /></a>
         <ul class="list-items-dropdown">
            <li><a target="_blank" href="https://tarfee.zendesk.com/access/sso?return_to=https%3A%2F%2Ftarfee.zendesk.com%2Fhc%2Fen-us"><i class="fa fa-question-circle"></i><?php echo $this -> translate("Help Centre")?></a></li>
            <!--<li><a target="_blank" href="https://tarfee.zendesk.com/access/sso?return_to=https%3A%2F%2Ftarfee.zendesk.com%2Fhc%2Fen-us"><i class="fa fa-info"></i><?php echo $this -> translate("Suggest Idea or Feature")?></a></li>-->
            <li><a href="help/contact"><i class="fa fa-phone"></i><?php echo $this -> translate("Contact Us")?></a></li>
         </ul>
      </li>

   </ul>
</div>

<div class="container">
   <span class="ynresponsive_menus"> 
      <?php foreach( $this->navigation as $item ):
         $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
         'reset_params', 'route', 'module', 'controller', 'action', 'type',
         'visible', 'label', 'href'
         )));
      ?>

         &nbsp;&nbsp; <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
      <?php endforeach; ?>
      <span class="ynresponsive_languges">
	     <?php if( 1 !== count($this->languageNameList) ):?>
	        <form id="form_language" method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
	            <?php 
	            if($this -> countryLanguage)
			  	{
			  		$this->translate()->setLocale($this -> countryLanguage);
			  	}
	            $selectedLanguage = $this->translate()->getLocale() ?>
	            <div class="language-dropdown render-once" data-view="LanguageDropdown" data-hash="LanguageDropdown">
	            	<i class="fa fa-globe"></i>
	          		<span><?php echo strtoupper(substr($selectedLanguage, 0, 2))?></span>
	            	<ul>
	            		<?php foreach($this->languageNameList as $key => $language):?>
	            		<li>
	            			<a onclick="changeLanguages('<?php echo $key?>')" data-locale="<?php echo $key?>" class="locale old-app"><?php echo strtoupper(substr($key,0, 2))?></a>
	            		</li>
	            		<?php endforeach;?>
	            	</ul>
	
	            </div>
	            <?php echo $this->formHidden('language', $selectedLanguage);?>
	            <?php echo $this->formHidden('return', $this->url()) ?>
	        </form>
	        <script type="text/javascript">
	        var changeLanguages = function(lang)
	        {
	        	$('language').value = lang;
	        	$('form_language').submit();
	        }
	        </script>
	     <?php endif; ?>
		</span>
    </span>
      &copy; <?php echo $this->translate('%s tarfee', date('Y')) ?>
    <?php if( !empty($this->affiliateCode) ): ?>
      <div class="affiliate_banner">
        <?php 
          echo $this->translate('Powered by %1$s', 
            $this->htmlLink('http://www.socialengine.com/?source=v4&aff=' . urlencode($this->affiliateCode), 
            $this->translate('SocialEngine Community Software'),
            array('target' => '_blank')))
        ?>
      </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
   jQuery.noConflict();

      //Add class when click icon arrow
      jQuery('#show-hide-list-items').click(function() {
         jQuery('.list-items').fadeToggle(400);
         jQuery(this).toggleClass('list-item-show');
      });

      //Show submenu right menu
      jQuery('.item-action').click(function() {
         jQuery(this).find('.list-items-dropdown').fadeToggle(400);
      });


      jQuery(document).mouseup(function (e)
      {
          var container_add = jQuery('#item-action-add');

          if (!container_add.is(e.target) // if the target of the click isn't the container...
              && container_add.has(e.target).length === 0) // ... nor a descendant of the container
          {   
              container_add.find('.list-items-dropdown').hide();
          }


          var container_help = jQuery('#item-action-help');

          if (!container_help.is(e.target) // if the target of the click isn't the container...
              && container_help.has(e.target).length === 0) // ... nor a descendant of the container
          {   
              container_help.find('.list-items-dropdown').hide();
          }

      });


</script>
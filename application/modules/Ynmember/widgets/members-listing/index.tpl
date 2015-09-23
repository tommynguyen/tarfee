<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynmember/externals/scripts/wookmark/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynmember/externals/scripts/wookmark/jquery.wookmark.min.js"></script>


<?php if( count($this->paginator) > 0 ): ?>
<div class="ynmember-browse-listings-header clearfix">
	<span><?php echo $this-> translate("Members");?></span>

	<div id="ynmember-mode-view" class="ynmember-mode-view">
		<?php if(in_array('list', $this -> mode_enabled)):?>
			<span class="ynmember-viewmode-list" rel="ynmember-browse-viewmode-list"></span>
		<?php endif;?>
		<?php if(in_array('grid', $this -> mode_enabled)):?>
			<span class="ynmember-viewmode-grid" rel="ynmember-browse-viewmode-grid"></span>
		<?php endif;?>	
		<?php if(in_array('pin', $this -> mode_enabled)):?>
			<span class="ynmember-viewmode-pinterest" rel="ynmember-browse-viewmode-pinterest"></span>
		<?php endif;?>	
		<?php if(in_array('map', $this -> mode_enabled)):?>
			<span class="ynmember-viewmode-maps" rel="ynmember-browse-viewmode-maps"></span>
		<?php endif;?>	
	</div>
</div>

<div id="ynmember-browse-listings">
	<ul class="ynmember-browse-member-items ynmember-clearfix">
		<?php $featuredTbl = Engine_Api::_()->getItemTable('ynmember_feature'); ?>
		<?php foreach($this->paginator as $user) :?>			
			<?php
				$facebook = $twitter = $about_me = "";
			    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
				foreach( $fieldStructure as $map ) {
			       $field = $map->getChild();
			       $value = $field->getValue($user);
				   if($field->type == 'facebook')
				   {
				   	 $facebook = $value['value'];
				   }
				   if($field->type == 'twitter')
				   {
				   	 $twitter = $value['value'];
				   }
				   if($field->type == 'about_me')
				   {
				   	 $about_me = $value['value'];
				   }
				}
		    ?>
			<li>
				<div class="ynmember-browse-item ynmember-clearfix">
					<div class="ynmember-item-cover">
					<?php
						$coverPhotoUrl = "";
						if ($user->cover_id)
						{
							$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($user->cover_id)->current();
							if($coverFile)
								$coverPhotoUrl = $coverFile->map();
						}
						?>				
						<div class="ynmember-profile-cover-picture" style="background-image: url('<?php echo $coverPhotoUrl; ?>');"></div>
						<div class="ynmember-profile-cover-image">
							<?php if ($coverPhotoUrl) : ?>
							<img class="" src="<?php echo $coverPhotoUrl; ?>" />
							<?php endif; ?>
						</div>
					</div>

					<div class="ynmember-item-avatar">
						<?php $background_image = Engine_Api::_()->ynmember()->getMemberPhoto($user);?>
						<?php echo $this->htmlLink($user->getHref(), '<span alt="'.$user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$user->getTitle())) ?>
						
						<?php if ($featuredTbl->isFeatured($user)) : ?>
							<div class="ynmember-item-featured">
								<?php echo $this->translate('Featured'); ?>
							</div>
						<?php endif; ?>

						<!-- add friend button -->
						<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
						<?php if(is_array($canAdd)):?>
							<a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox ynmember-btn-addfriend">
								<i class="fa fa-plus"></i>
				            </a>
						<?php endif;?>
					</div>					

					<div class="ynmember-item-information">

						<div class="ynmember-item-title">
							<?php
					         $onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
					         $step = 900;
					         $select = $onlineTable -> select() -> where('user_id=?', (int)$user -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
					         $online = $onlineTable -> fetchRow($select);
					         if(is_object($online)): ?>
					     		<span class="ynmember-item-status online"></span>
					        <?php else:?>
					            <span class="ynmember-item-status off"></span>
					        <?php endif;?>
							<span><a href='<?php echo $user->getHref();?>'><?php echo $user->getTitle();?></a></span>					
						</div>

						<div class="ynmember-item-info">
							
							<!-- study -->
							<?php
								$studyPlacesTbl = Engine_Api::_()->getDbTable('studyplaces', 'ynmember');
								$studyplaces = $studyPlacesTbl -> getCurrentStudyPlacesByUserId($user -> getIdentity());
								$str_studyplace = "";
								if ($studyplaces && $studyplaces -> isViewable())
								{
									$str_studyplace = "<a target='_blank' href='https://www.google.com/maps?q={$studyplaces->latitude},{$studyplaces->longitude}'>{$studyplaces->name}</a>";
								}
							?>
							<div class="ynmember-item-info-studyplaces <?php if ($str_studyplace=='') echo 'ynmember-nodata'; ?>">
								<i class="fa fa-graduation-cap"></i>
								<span><?php echo $this-> translate("Studied at");?></span>
						    	<?php echo $str_studyplace;?> 			
							</div>
							
							<!-- workplace -->
							<?php
								$workPlacesTbl = Engine_Api::_()->getDbTable('workplaces', 'ynmember');
								$workplaces = $workPlacesTbl -> getCurrentWorkPlacesByUserId($user -> getIdentity());
								$str_workplace = "";
								if ($workplaces && $workplaces -> isViewable()) {
									$str_workplace = "<a target='_blank' href='https://www.google.com/maps?q={$workplaces->latitude},{$workplaces->longitude}'>{$workplaces->company}</a>";
								}
							?>
							<div class="ynmember-item-info-workplaces <?php if ($str_workplace=='') echo 'ynmember-nodata'; ?>">
								<i class="fa fa-briefcase"></i>
								<span><?php echo $this-> translate("Works at");?></span>
						    	<?php echo $str_workplace;?> 							
							</div>
							
							<!-- living places -->
							<?php
								$livePlacesTbl = Engine_Api::_()->getDbTable('liveplaces', 'ynmember');
								$liveplaces = $livePlacesTbl -> getLiveCurrentPlacesByUserId($user -> getIdentity());
								$lives = array();
								foreach ($liveplaces as $live)
								{
									if($live -> isViewable())
									{
										$lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
									}
								}
								$lives = implode(", ", $lives); 
							?>
							<div class="ynmember-item-info-live <?php if ($lives=='') echo 'ynmember-nodata'; ?>">
								<i class="fa fa-map-marker"></i>
								<span><?php echo $this-> translate("Lives in");?></span>							
								<?php if($lives) :?>
								 	<?php echo $lives; ?> 
								<?php endif;?>						
							</div>
							
							<!-- groups -->					
							<?php
							if (Engine_Api::_()->hasModuleBootstrap('group') || Engine_Api::_()->hasModuleBootstrap('advgroup'))
							{
								$groupTbl = Engine_Api::_()->getItemTable('group');
								$membership = (Engine_Api::_()->hasModuleBootstrap('advgroup'))
									? Engine_Api::_()->getDbtable('membership', 'advgroup')
									: Engine_Api::_()->getDbtable('membership', 'group');
							
								$select = $membership->getMembershipsOfSelect($user);
								$groups = $groupTbl->fetchAll($select);
							}
							?>
							<div class="ynmember-item-info-group <?php if (count($groups)==0) echo 'ynmember-nodata'; ?>">
								<i class="fa fa-users"></i>
								<span><?php echo $this-> translate("Groups:");?></span>
								<?php if (count($groups)):?>
									<?php foreach ($groups as $group) :?>
										 <a href="<?php echo $group->getHref();?>"><?php echo $group->getTitle();?></a>
										<?php break;?>
									<?php endforeach;?>
									<?php if (count($groups) > 1) : ?>
										<?php echo $this-> translate("and");?> <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'review', 'action' => 'user-group', 'id' => $user -> getIdentity()),'ynmember_extended');?>"><?php echo $this -> translate(array("%s other", "%s others" , (count($groups) - 1 )), (count($groups) - 1))?></a>
									<?php endif;?>
								<?php endif;?>
							</div>
						</div>

						<div class="ynmember-item-grid-social">
							<?php if ($facebook!='') :?>
								<?php if((strpos($facebook,'http://') === false) && (strpos($facebook,'https://') === false)) : ?>
								<?php
									$facebook = 'http://'.$facebook;
								?>
								<?php endif; ?>		
								<a href="<?php echo $facebook;?>" target="_blank" class="ynmember-icon-social-facebook"><i class="fa fa-facebook"></i></a>
							<?php endif; ?>

							<?php if ($twitter!='') :?>
								<?php if((strpos($twitter,'http://') === false) && (strpos($twitter,'https://') === false)) : ?>
								<?php
									$twitter = 'http://'.$twitter;
								?>
								<?php endif; ?>
								<a href="<?php echo $twitter;?>" target="_blank" class="ynmember-icon-social-twitter"><i class="fa fa-twitter"></i></a>
							<?php endif; ?>
						</div>

						<div class="ynmember-item-about <?php if ($about_me=='') echo 'ynmember-nodata'; ?>">
							<div class="ynmember-item-about-content">
								<i class="fa fa-quote-left"></i>
								<?php echo $about_me;?>
							</div>
						</div>

						<div class="ynmember-item-action">

							<div class="ynmember-item-more-option">
								<span class="ynmember-item-more-btn"><i class="fa fa-angle-down"></i></span>
								<div class="ynmember-item-more-option-hover">
									<?php echo $this->action('render', 'menu', 'ynmember', array('id' => $user->getIdentity()));?>
									<?php Engine_Api::_()->core()->clearSubject();?>
								</div>
							</div>

							<!-- add friend button -->
							<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
							<?php if(is_array($canAdd)):?>
								<div class="ynmember-item-add-friend">
									<a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox">
						                	<?php echo $canAdd['label']; ?>
						            </a>
					            </div>
							<?php endif;?>
							

							<?php if(!$this -> viewer -> isSelf($user)):?>
								<?php 
									$list_mutual_friends = Engine_Api::_() -> ynmember() -> getMutualFriends($user); 
									$mutual_user ="";
								?>
								<?php if($list_mutual_friends) :?>
									<div class="ynmember-item-mutual-friend">
									<?php foreach ($list_mutual_friends as $mutual_user) :?>
										 <a href="<?php echo $mutual_user->getHref();?>"><?php echo $mutual_user->getTitle();?></a>
										 <?php echo $this->translate('is a mutual friend');?>
										<?php break;?>
									<?php endforeach;?>
									<?php if (count($list_mutual_friends) > 1) : ?>
										<?php echo $this-> translate("and");?> <a class='smoothbox' href='<?php echo $this->url(array('controller' => 'index', 'action' => 'get-mutual-friends', 'except_id' => $mutual_user -> getIdentity(), 'subject_id' => $user -> getIdentity()),'ynmember_extended') ?>'><?php echo $this -> translate(array("%s other", "%s others" , (count($list_mutual_friends) - 1 )), (count($list_mutual_friends) - 1))?></a>
									<?php endif;?>
									</div>
								<?php endif;?>
							<?php endif;?>
							
							<?php if ($facebook!='') :?>
							<a href="<?php echo $facebook;?>" target="_blank" class="ynmember-icon-social-facebook"><i class="fa fa-facebook"></i></a>
							<?php endif; ?>

							<?php if ($twitter!='') :?>
							<a href="<?php echo $twitter;?>" target="_blank" class="ynmember-icon-social-twitter"><i class="fa fa-twitter"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</li>
		<?php endforeach;?>
	</ul>
	<div id="ynmember-browse-member-maps" class="ynmember-browse-member-maps">
		<iframe id='map-view-iframe'style="max-height: 500px; display: none;" > </iframe>
	</div>
</div>

<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no members found yet.') ?>
		</span>
    </div>
<?php endif; ?>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>

<script text="text/javascript">
    jQuery.noConflict();
	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	    }
	    return "";
	}
    
    function setMapMode(){
       	var html =  "<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->userIds), 'ynmember_general') ?>";
       		
       	document.getElementById('map-view-iframe').dispose();
       		
		var iframe = new IFrame({
			id : 'map-view-iframe',
			src: html,
			styles: {			       
				'height': '500px',
				'width' : '100%'
			},
		});

       	iframe.inject( $('ynmember-browse-member-maps') );
		document.getElementById('map-view-iframe').style.display = 'block';
		document.getElementById('paginator').style.display = 'none';
    }
    
	function setPinterestMode() {
		(function (jQuery){
			var handler = jQuery('#ynmember-browse-listings .ynmember-browse-member-items > li');

			handler.wookmark({
			  // Prepare layout options.
			  autoResize: true, // This will auto-update the layout when the browser window is resized.
			  container: jQuery('#ynmember-browse-listings .ynmember-browse-member-items'), // Optional, used for some extra CSS styling
			  offset: 10, // Optional, the distance between grid items
			  outerOffset: 0, // Optional, the distance to the containers border
			  itemWidth: 230, // Optional, the width of a grid item
			  flexibleWidth: '50%',
			});
		})(jQuery);
	}

	function removePinterestMode() {
		$$('#ynmember-browse-listings .ynmember-browse-member-items')[0].erase('style'); 
		$$('#ynmember-browse-listings .ynmember-browse-member-items li').each(function(el){
		    el.erase('style'); 
		});

		
		(function (jQuery){
			jQuery(window).unbind('resize.wookmark');
		})(jQuery);
	}

	// Get cookie
	var myCookieViewMode = getCookie('ynmember-viewmode-cookie');
	if ( myCookieViewMode == '') {
		//TODO get form DB
		myCookieViewMode = '<?php echo $this -> class_mode;?>';
	}
	if ( myCookieViewMode == '') {
		// Set default
		myCookieViewMode = 'ynmember-browse-viewmode-list';
	}
	$$('.ynmember-mode-view')[0].addClass( myCookieViewMode );
	$('ynmember-browse-listings').addClass( myCookieViewMode );

	// render MapView
	if ( myCookieViewMode == 'ynmember-browse-viewmode-maps') {
		setMapMode();
	} else 

	// render pinterestView
	if ( myCookieViewMode == 'ynmember-browse-viewmode-pinterest') {
		setPinterestMode();	

		// recall
		window.addEventListener("load", setPinterestMode);
	}

	// Set click viewMode
	$$('.ynmember-mode-view > span').addEvent('click', function(){
		var viewmode = this.get('rel'),
			browse_content = $('ynmember-browse-listings'),
			header_mode = $$('.ynmember-mode-view')[0];

		setCookie('ynmember-viewmode-cookie', viewmode, 1);

		header_mode
			.removeClass('ynmember-browse-viewmode-list')
			.removeClass('ynmember-browse-viewmode-grid')
			.removeClass('ynmember-browse-viewmode-pinterest')
			.removeClass('ynmember-browse-viewmode-maps');

		browse_content
			.removeClass('ynmember-browse-viewmode-list')
			.removeClass('ynmember-browse-viewmode-grid')
			.removeClass('ynmember-browse-viewmode-pinterest')
			.removeClass('ynmember-browse-viewmode-maps');

		header_mode.addClass( viewmode );
		browse_content.addClass( viewmode );

		// render MapView
		if ( viewmode == 'ynmember-browse-viewmode-maps') {
			setMapMode();
		} else {
			document.getElementById('paginator').style.display = 'block';
		}

		if (viewmode == 'ynmember-browse-viewmode-pinterest' ) {
			// set pinterest mode
			setPinterestMode(); 
		} else {
			// remove pinterest mode
			removePinterestMode();
		}		
	});

	$$('.ynmember-item-more-option > span.ynmember-item-more-btn').addEvent('click', function() {
		this.getParent('.ynmember-item-more-option').toggleClass('ynmember-item-show-option');
	});
</script>
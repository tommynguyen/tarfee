<?php
$this -> headScript() 
        -> appendFile($this->baseUrl() . '/application/modules/User/externals/scripts/jquery.min.js')
        -> appendFile($this->baseUrl() . '/application/modules/User/externals/scripts/jquery-ui-1.10.4.min.js')
        -> appendFile($this->baseUrl() . '/application/modules/User/externals/scripts/jquery.form.min.js');

$coverPhotoUrl = "";
$hasCover = false;
if ($this->user->cover_photo) {
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->user->cover_photo)->current();
	$coverPhotoUrl = $coverFile->map();
    if (!$coverPhotoUrl) {
        $coverPhotoUrl = 'application/modules/User/externals/images/user_default_cover.jpg';
    }
    else {
        $hasCover = true;
    }
    
}
else {
    $coverPhotoUrl = 'application/modules/User/externals/images/user_default_cover.jpg';
}
?>

<script type="text/javascript">
    var cover_top = <?php echo ($hasCover) ? $this->user->cover_top : 0?>;
    function repositionCover() {
        jQuery('.reposition-cover').show();
        jQuery('.cover-resize-buttons').show();
        jQuery('.edit-position-buttons').hide();
        jQuery('.view-cover').hide();
        jQuery('.reposition-cover')
        .css('cursor', 's-resize')
        .draggable({
            scroll: false,
            axis: "y",
            cursor: "s-resize",
            drag: function (event, ui) {
                y1 = jQuery('.tarfee_profile_cover_photo').height();
                y2 = jQuery('.reposition-cover').height();

                if (ui.position.top >= 0) {
                    ui.position.top = 0;
                }
                else
                if (ui.position.top <= (y1-y2)) {
                    ui.position.top = y1-y2;
                }
            },

            stop: function(event, ui) {
                jQuery('input.cover-position').val(ui.position.top);
            }
        });
    }

    function saveReposition() {
        if (jQuery('input.cover-position').length == 1) {
            posY = jQuery('input.cover-position').val();
            new Request.JSON({
                'url': '<?php echo $this->url(array('action'=>'reposition', 'controller'=>'edit'),'user_extended', true)?>',
                'method': 'post',
                'data' : {
                    'position' : posY
                },
                'onSuccess': function(responseJSON, responseText) {
                    if (responseJSON.status == true) {
                        cover_top = posY;
                        jQuery('.profile-cover-picture-span').css('top', posY+'px');
                        jQuery('.reposition-cover').hide();
                        jQuery('.cover-resize-buttons').hide();
                        jQuery('.edit-position-buttons').show();
                        jQuery('.view-cover').show();
                    }
                    else {
                    }            
                }
            }).send();
        }
    }

    function cancelReposition() {
        jQuery('.reposition-cover').hide();
        jQuery('.reposition-cover').css('top', cover_top+'px');
        jQuery('.cover-resize-buttons').hide();
        jQuery('.edit-position-buttons').show();
        jQuery('.view-cover').show();
        jQuery('input.cover-position').val(cover_top);
    }
</script>


<div class="tarfee_profile_cover_wrapper">
    <div class="tarfee_profile_cover_photo_wrapper" id="siteuser_cover_photo">
        <div class="tarfee_profile_cover_photo">
            <span class="tarfee_profile_cover_bg_gradient"></span>
            <div class="cover-reposition">
          	  	<?php if($this->user -> isSelf($this -> viewer())):?>
    		        <span id="edit-cover-btn">
    		          <?php echo $this->htmlLink(array('action'=>'cover', 'route'=>'user_extended', 'controller'=>'edit', 'id'=>$this->user->getIdentity()), $this->translate('Update Cover Photo'), array('class'=>'smoothbox'))?>
    		        </span>
                  
    	        <?php if ($hasCover) :?>
    		        <span class="edit-position-buttons">
                        <a href="javascript:void(0)" onclick="repositionCover();"><?php echo $this->translate('Reposition Cover Photo')?></a>
                    </span>
                  
    		        <div class="cover-resize-buttons" style="display: none;">
    		            <span><a href="javascript:void(0)" onclick="saveReposition();"><?php echo $this->translate('Save Position')?></a></span>
    		            <span><a href="javascript:void(0)" onclick="cancelReposition();"><?php echo $this->translate('Cancel')?></a></span>
    		            <input class="cover-position" name="pos" value="<?php echo ($hasCover) ? $this->user->cover_top : 0?>" type="hidden">
    		        </div>
                <?php endif; ?>
            </div><!-- cover reposition-->
          
            <img class="reposition-cover profile-cover-picture-span cover_photo thumb_cover item_photo_album_photo thumb_cover" src="<?php echo $coverPhotoUrl; ?>" style="display: none; <?php if ($hasCover) echo 'top: '.$this->user->cover_top.'px'?>" />
	        <?php else: ?>
	        <?php endif; ?>
                <img class="cover_photo thumb_cover profile-cover-picture-span item_photo_album_photo thumb_cover" src="<?php echo $coverPhotoUrl; ?>" style="<?php if ($hasCover) echo 'top: '.$this->user->cover_top.'px'?>" />
        </div><!--tarfee_profile_cover_photo-->


    </div>
</div><!--tarfee_profile_cover_wrapper-->

<div class="tarfee_profile_avatar_infomation">
    <div class="tarfee_profile_cover_has_tabs clearfix" id="siteuser_main_photo">
             
        <?php $profileUrl = $this -> user -> getPhotoUrl('thumb.profile');
            if(!$profileUrl){
                $profileUrl = 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
            }
        ?>
        <div class="item_photo" style="background-image:url('<?php echo $profileUrl ?>')">
            <?php if($this->user -> isSelf($this -> viewer())):?>
                <span id="edit-photo-btn">
                    <?php echo $this->htmlLink(array('action'=>'photo-popup', 'route'=>'user_extended', 'controller'=>'edit', 'id'=>$this->user->getIdentity()), $this->translate('Update Profile Photo'), array('class'=>'smoothbox'))?>
                </span>
            <?php endif; ?>

            <div class="tarfee_profile_cover_tarfee_button">
                <ul>
                     <?php if ($this->viewer()->getIdentity() && ($this->viewer()->getIdentity() != $this -> subject()->getIdentity())) :?>
	                 	<li>
	                        <?php echo $this->htmlLink(array(
	                            'route' => 'user_general',
	                            'action' => 'view-basic',
	                            'subject' => $this -> subject() ->getGuid()
	                        ), '<span class="profile_info_button"><i class="fa fa-info-circle"></i></span>', array(
	                            'class' => 'smoothbox', 'title' => $this -> translate("Basic Information")
	                        ));
	                        ?>
	                     </li>
                 	<?php endif;?>
                     <?php if (Engine_Api::_()->ynfbpp()->_allowMessage($this->viewer(), $this -> subject())) :?>
                 	 <li>
                        <?php echo $this->htmlLink(array(
                            'route' => 'messages_general',
                            'action' => 'compose',
                            'to' => $this -> subject() ->getIdentity()
                        ), '<span class="profile_inbox_button"><i class="fa fa-comments"></i></span>', array(
                            'class' => 'smoothbox', 'title' => $this -> translate("Message")
                        ));
                        ?>
                     </li>
                     <?php elseif (Engine_Api::_()->ynfbpp()->_allowMail($this->viewer(), $this -> subject())) :?>
                 	 <li>
                        <?php echo $this->htmlLink(array(
                            'route' => 'user_general',
                            'action' => 'in-mail',
                            'to' => $this -> subject() ->getIdentity()
                        ), '<span class="profile_inbox_button"><i class="fa fa-envelope"></i></span>', array(
                            'class' => 'smoothbox', 'title' => $this -> translate("Email")
                        ));
                        ?>
                     </li>
                     <?php endif;?>
                     <?php $viewer = Engine_Api::_()->user()->getViewer();
                        $subject = Engine_Api::_()->core()->getSubject();
                        if(!$viewer -> isSelf($subject)):
                     ?>
                     <li>
                        <?php 
                        $subjectRow = $subject->membership()->getRow($viewer);
                        if( null === $subjectRow ) 
                        {
                            // Follow
                            echo $this->htmlLink(array(
                                'route' => 'user_extended',
                                'controller' => 'friends',
                                'action' => 'add',
                                'user_id' => $subject->getIdentity(),
                            ), '<span class="profile_follow_button"><i class="fa fa-flag"></i></span>', array(
                                'class' => 'smoothbox profile_follow', 'title' => $this -> translate("Follow")
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
                            ), '<span class="profile_follow_button"><i class="fa fa-flag-o"></i></span>', array(
                                'class' => 'smoothbox profile_unfollow', 'title' => $this -> translate("Unfollow")
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
                            ), '<span class="profile_follow_button"><i class="fa fa-flag-o"></i></span>', array(
                                'class' => 'smoothbox profile_unfollow', 'title' => $this -> translate("Unfollow")
                            ));
                        } 
                        ?>
                     </li>
                     <?php endif;?>
                </ul>
            </div>
        </div>
         <div class="tarfee_profile_coverinfo_status">
               <h2>
                   <a href="<?php echo $this -> user -> getHref();?>">
                      <?php echo $this -> user -> getTitle()?>
                   </a>
                   <?php $pro_ids = Engine_Api::_() -> user() -> getProfessionalUsers();
				   
                   if($this -> user -> level_id == 6 && array_search($this -> user -> getIdentity(), $pro_ids) < 100):  ?>
		               <div class="founder_member">
		                    <img src="application/modules/User/externals/images/icon_founder_memeber.png" alt="">
		                    <?php echo $this->translate('founder member'); ?>
		               </div>
	               <?php endif;?>
               </h2>

 			<?php
                $about_me = "";
                $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($this -> user);
                foreach( $fieldStructure as $map ) {
                    $field = $map->getChild();
                    $value = $field->getValue($this -> user);
                    if($field->type == 'about_me') {
                        $about_me = $value['value'];
                    }
                }
                ?>
                <?php if ($about_me != "") :?>
                    <p style="display: none" ><?php echo $about_me?></p>
                <?php endif;?>

                <div class="account_type">                  
                    <?php if(Engine_Api::_()->authorization()->isAllowed('user', $this->subject(), 'show_badge')):?>
                        <?php 
                            $badge = Engine_Api::_()->authorization()->getPermission($this->subject(), 'user', 'badge');
                            if($badge && strpos($badge,'public/admin') !== false): ?>
                                <img height="26" src="<?php echo $badge?>" />
                                <?php echo $this->translate('professional account'); ?>
                            <?php endif;?>
                        <?php endif;?>
                </div>

                <div class="verified_account">
                    <?php if($this->src_img):?>
                        <img height="26" src='<?php echo $this->src_img;?>'>
                        <?php echo $this -> translate("verified account");?>
                     <?php endif;?>
                     <?php $menu = new Slprofileverify_Plugin_Menus();
                        $aVerifyButton = $menu->onMenuInitialize_UserProfileVerified();
                        ?>
                     <?php if($aVerifyButton):?>
                        <a href="<?php echo $this->url($aVerifyButton['params'], 
                            $aVerifyButton['route'], array());?>" 
                            class="<?php echo $aVerifyButton['class'];?> buttonlink"
                            title="<?php echo $aVerifyButton['label']; ?>"
                            style="background-image: url(<?php echo $aVerifyButton['icon']?>);"
                            target=""> 
                            <?php echo $aVerifyButton['label'];?>
                        </a>
                     <?php endif;?>
                </div>
                <div class='status_alt status_parent'>
                    <ul id='main_tabs'>
                       <?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction');
                            if ( $direction == 0 ): ?>
                            <li>
                               <?php if($this->followingCount):?>
                                    <a href="<?php echo $this -> url(array('controller' => 'friends', 'action' => 'list-all-following', 'user_id' => $this->user -> getIdentity()), 'user_extended')?>" class="smoothbox">
                                        <span class="number_tabs"><?php echo $this->locale()->toNumber($this->followingCount);?></span>
                                        <div><?php echo $this -> translate('following')?></div>
                                    </a>
                                <?php else:?>
                                    <a href="javascript:void(0)">
                                        <span class="number_tabs">0</span>
                                        <div><?php echo $this -> translate('following')?></div>
                                     </a>
                                <?php endif;?>
                            </li>
                            <li>
                                <?php if($this->friendCount):?>
                                    <a href="<?php echo $this -> url(array('controller' => 'friends', 'action' => 'list-all-followers', 'user_id' => $this->user -> getIdentity()), 'user_extended')?>" class="smoothbox">
                                        <span class="number_tabs"><?php echo $this->locale()->toNumber($this->friendCount);?></span>
                                        <div><?php echo $this->translate(array('follower', 'followers', $this->friendCount),
                                            $this->locale()->toNumber($this->friendCount)) ?></div>
                                    </a>
                                <?php else:?>
                                    <a href="javascript:void(0)">
                                        <span class="number_tabs">0</span>
                                        <div><?php echo $this -> translate('followers')?></div>
                                     </a>
                                <?php endif;?>
                            </li>
                            <?php else:?>
                                <li>
                                    <?php if($this->friendCount):?>
                                        <a href="<?php echo $this -> url(array('controller' => 'friends', 'action' => 'list-all-friends', 'user_id' => $this->user -> getIdentity()), 'user_extended')?>" class="smoothbox">
                                            <span class="number_tabs"><?php echo $this->locale()->toNumber($this->friendCount);?></span>
                                            <div><?php echo $this->translate(array('friend', 'friends', $this->friendCount),
                                                $this->locale()->toNumber($this->friendCount)) ?></div>
                                        </a>
                                    <?php else:?>
                                        <a href="javascript:void(0)">
                                            <span class="number_tabs">0</span>
                                            <div><?php echo $this -> translate('friends')?></div>
                                         </a>
                                    <?php endif;?>
                                </li>
                            <?php endif;?>
                            <li>
                            	<?php $url = $this->url(array('action'=>'view-eyeons', 'user_id'=>$this->user->getIdentity()),'user_general', true)?>
                               <a class="smoothbox" href="<?php echo $url?>">
                                  <span class="number_tabs"><?php echo count($this->user->getEyeOns())?></span>
                                  <div><?php echo $this -> translate("eye on")?></div>
                               </a>
                            </li>
                    </ul>
                </div>


              
         </div>
         <div class="tarfee_profile_club_item">
             <ul>               
                <?php foreach($this -> sports as $sport):?>
                 <li>
                    <a title="<?php echo $sport -> getTitle()?>" href="<?php echo $sport -> getHref();?>">
                        <?php echo $this -> itemPhoto($sport, 'thumb.icon');?>
                       <!-- <?php echo $this -> string() -> truncate($sport -> getTitle(), 10)?>-->
                     </a>
                 </li>
                 <?php endforeach;?>
                <?php foreach($this -> clubs as $club):?>
                <li>
                   <a title="<?php echo $club -> getTitle()?>" href="<?php echo $club -> getHref();?>">
                        <?php echo $this -> itemPhoto($club, 'thumb.icon');?>
                        <!--<?php echo $this -> string() -> truncate($club -> getTitle(), 10)?>-->
                   </a>
                </li>
                <?php endforeach;?>
             </ul>
         </div><!-- tafee profile club item -->
    </div><!-- tarfee profile cover has tabs -->
</div>

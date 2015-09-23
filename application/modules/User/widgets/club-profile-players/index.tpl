<div class="player_contents">
	<div class="tarfee-profile-module-header">
	    <!-- Menu Bar -->
	    <?php
	    if($this -> viewer() -> getIdentity()):
			 $max_player_card = Engine_Api::_()->authorization()->getPermission($this -> viewer(), 'user_playercard', 'max_player_card', 5);
	         if($max_player_card == "")
	         {
	            $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
	             $maselect = $mtable->select()
	                ->where("type = 'user_playercard'")
	                ->where("level_id = ?",$this -> viewer() -> level_id)
	                ->where("name = 'max_player_card'");
	              $mallow_a = $mtable->fetchRow($maselect);          
	              if (!empty($mallow_a))
	                $max_player_card = $mallow_a['value'];
	              else
	                 $max_player_card = 5;
	         }
		    
			if($this->paginator->getTotalItemCount() < $max_player_card && $this -> subject() -> isOwner($this -> viewer())):
		    ?>
		    <div class="group_album_options">
		        <?php echo $this->htmlLink(array(
		            'route' => 'user_extended',
		            'controller' => 'player-card',
		            'action' => 'create',
		            'club_parent' => $this -> subject() -> getIdentity(),
		        ), $this->translate('Add Player'), array(
		            'class' => 'tf_button_action'
		        ))
		        ?>
		    </div>    
		    <?php endif;?>  
		<?php endif;?>
	</div>
	
	<div class="tarfee_list" id="players">
	    <!-- Content -->
	    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	    <ul class="players_browse">  
	        <?php foreach ($this->paginator as $player): 
			$totalPhoto = $player -> getPhotosTotal();
			$totalVideo = $player -> getTotalVideo();
	        ?>
	        <?php if($player -> isViewable()) :?>
	        	<li id="player-item-<?php echo $player->playercard_id ?>">
	           	<div id='profile_photo'>
					<?php $photoUrl = ($player -> getPhotoUrl('thumb.main')) ? $player->getPhotoUrl('thumb.main') : "application/modules/User/externals/images/nophoto_playercard_thumb_profile.png" ?>
					<div class="avatar">
						<div class="thumb_profile" style="background-image:url(<?php echo $photoUrl?>)">
							
							<div class="avatar-box-hover">
								<ul class="actions">
									<li><a href="<?php echo $player -> getHref()?>"><i class="fa fa-external-link"></i></a></li>
									<?php if($this -> viewer() -> getIdentity() && $player -> getOwner() -> isSelf($this -> viewer())): ?>
									<!-- Button Edit Crop Delete -->
									<li class="first">
										<?php
							            	echo $this->htmlLink(array(
									            'route' => 'user_extended',
									            'controller' => 'player-card',
									            'action' => 'edit',
									            'id' => $player->playercard_id,
									        ), '<i class="fa fa-pencil"></i>', array(
									            'class' => '', 'title' => $this -> translate('Edit')
									        ));
										?>
									</li>

									<li class="second">
										<?php
							        		echo $this->htmlLink(array(
									            'route' => 'user_extended',
									            'controller' => 'player-card',
									            'action' => 'crop-photo',
									            'id' => $player->playercard_id,
									        ), '<i class="fa fa-crop"></i>', array(
									            'class' => 'smoothbox', 'title' => $this -> translate('Crop Photo')
									        ));
										?>
									</li>

									<li class="fifth">
										<?php
											echo $this->htmlLink(array(
									            'route' => 'user_extended',
									            'controller' => 'player-card',
									            'action' => 'delete',
									            'id' => $player->playercard_id,
									        ), '<i class="fa fa-times"></i>', array(
									            'class' => 'smoothbox', 'title' => $this -> translate('Delete')
									        ));
										?>
									</li>
									
									<?php if($this -> viewer() -> getIdentity() && Engine_Api::_()->user()->canTransfer($player)) :?>
									<li>
										<?php
											echo $this->htmlLink(array(
									            'route' => 'user_general',
									            'action' => 'transfer-item',
			    								'subject' => $player -> getGuid(),
									        ), '<i class="fa fa-exchange"></i>', array(
									            'class' => 'smoothbox', 'title' => $this -> translate('Transfer to user profile')
									        ));
										?>
									</li>
									<?php endif;?>	

									<li class="setting" onclick="showOptions(<?php echo $player->playercard_id ?>, this)">
										<a href="javascript:void(0)"><i class="fa fa-plus"></i></a>
									</li>
										<ul class="setting-list" style="display: none" id="setting-list_<?php echo $player->playercard_id?>">
											<li>
											<?php
							        			echo $this->htmlLink(array(
												'route' => 'video_general',
													'action' => 'create',
													'parent_type' =>'user_playercard',
													'subject_id' =>  $player->playercard_id,
												), '<i class="fa fa-video-camera"></i>&nbsp;'.$this->translate('Add Video'), array(
												'class' => '', 'title' => $this -> translate('Add Video')
												)) ;
											?>
											</li>
											<!--
											<li>
											<?php
												echo $this->htmlLink(array(
										            'route' => 'user_photo',
										            'controller' => 'upload',
										            'id' => $player->playercard_id,
										            'type' => $player->getType(),
										        ), '<i class="fa fa-camera"></i>&nbsp;'.$this->translate('Add Photos'), array(
										            'class' => 'smoothbox', 'title' => $this -> translate('Add Photos')
										        ));
											?>
											</li>
											-->
										</ul>
									
									
									<?php else: ?>

										<!-- asd sa d  -->
										<?php if ($this -> viewer() -> getIdentity()):?>
											<li title="<?php echo $this -> translate("Eye on")?>" id="user_eyeon_<?php echo $player -> getIdentity()?>">
					                    		<?php if($player->isEyeOn()): ?>              
					                        	<a class="actions_generic" href="javascript:void(0);" onclick="removeEyeOn('<?php echo $player->getIdentity() ?>')">
					                        		<span>
					                        			<i class="fa fa-eye-slash"></i>
				                        			</span>
					                    		</a>
					                    		<?php else: ?>
					                        	<a class="actions_generic" href="javascript:void(0);" onclick="addEyeOn('<?php echo $player->getIdentity() ?>')">
					                        		<span>
					                        			<i class="fa fa-eye"></i>
				                        			</span>
					                        	</a>
					                    		<?php endif; ?>
					                		</li>

											<li title="<?php echo $this -> translate('Comment')?>">
												<a class="actions_generic" href="<?php echo $player -> getHref()?>">
													<span>
														<i class="fa fa-comment"></i>
													</span>
												</a>
											</li>
										
											<?php $url = $this->url(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $player->getGuid()),'default', true);?>
											<li title="<?php echo $this -> translate('Report')?>">
												<a class="actions_generic smoothbox" href="<?php echo $url?>">
													<span>
														<i class="fa fa-flag"></i>
													</span>
												</a>
											</li>
										<?php endif;?>
									<?php endif; ?>
									
								</ul>
							</div>
							<div class="tarfee_sport_type_position">
								<?php if($player -> getSport()):?>
									<span title="<?php echo $this -> translate($player -> getSport());?>"><?php echo $this -> itemPhoto($player -> getSport(), 'thumb.icon');?></span>
									<!--<span title="<?php echo $player -> getSport() -> getTitle();?>" class="player-title"><?php echo $player -> getSport() -> getTitle();?></span>-->
								<?php endif;?>
								<?php if($player -> getPosition()):?>
									<span class="player-position" title="<?php echo $this -> translate($player -> getPosition() -> getTitle());?>">
							        	<?php 
								    		preg_match_all('/[A-Z]/', $player -> getPosition() -> getTitle(), $matches);
											echo implode($matches[0]);?>
									</span>
								<?php endif;?>
								
							</div><!--tarfee_sport_type_position-->
						</div>
					</div>
					<div class="tarfee_gender_player_name">
						<span class="gender_player">
							<?php if (($player->gender) == 1){
								echo '<i class="fa fa-mars"></i>';
							}else{
								echo '<i class="fa fa-venus"></i>';
							}

							?>
							
						</span>
						<a title="<?php echo $player -> first_name.' '.$player -> last_name;?>" href="<?php echo $player -> getHref()?>" class="player_name" ><?php echo $this -> string() -> truncate($player -> first_name.' '.$player -> last_name, 20)?></a>
					</div>

					<?php $overRallRating = $player -> rating;?>
					<div class="user_rating" title="<?php echo $overRallRating;?>">
						<?php for ($x = 1; $x <= $overRallRating; $x++): ?>
					        <span class="rating_star_generic"><i class="fa fa-star"></i></span>
					    <?php endfor; ?>
					    <?php if ((round($overRallRating) - $overRallRating) > 0): $x ++; ?>
					        <span class="rating_star_generic"><i class="fa fa-star-half-o"></i></span>
					    <?php endif; ?>
					    <?php if ($x <= 5) :?>
					        <?php for (; $x <= 5; $x++ ) : ?>
					            <span class="rating_star_generic"><i class="fa fa-star-o"></i></span>
					        <?php endfor; ?>
					    <?php endif; ?>
					</div>
					<?php
						$countryName = '';
						if($player ->country_id && $country = Engine_Api::_() -> getItem('user_location', $player ->country_id))
						{
							$countryName = $country -> getTitle();
						}
					?>

					<div class="tarfee_infomation_player">
						<p>
							<?php echo  $this->locale()->toDate($player -> birth_date);?> 
						</p>
						<p>
							<?php 
								if($countryName)
								echo $countryName
							?>
						</p>
						<p>
							<?php 
								$laguages = json_decode($player -> languages);
								$arr_tmp = array();
								if($laguages)
								{
									foreach ($laguages as $lang_id) 
									{
										$langTb =  Engine_Api::_() -> getDbTable('languages', 'user');
										$lang = $langTb -> fetchRow($langTb ->select()->where('language_id = ?', $lang_id));
										if($lang)
											$arr_tmp[] = $lang -> title;
									}
								}
								echo implode(' | ', $arr_tmp);
							?>
						</p>
					</div>
					<ul class="tarfee_count">
						<li>
							<?php $eyeons = $player->getEyeOns(); ?>
							<?php $url = $this->url(array('action'=>'view-eye-on', 'player_id'=>$player->getIdentity()), 'user_playercard' , true)?>
							<?php if(count($eyeons)):?>		
							<a href="<?php echo $url?>" class="smoothbox">
								<span class="tarfee-count-number"><?php echo count($eyeons); ?></span>
								<span><?php echo $this->translate('eye on');  ?></span>
							</a>
							<?php else:?>
								<span class="tarfee-count-number"><?php echo count($eyeons); ?></span>
								<span><?php echo $this->translate('eye on');  ?></span>
							<?php endif;?>
						</li>

						<li>
							<span class="tarfee-count-number"><?php  echo $totalVideo; ?></span>
							<span><?php echo $this->translate(array('video','videos', $totalVideo)); ?></span>
						</li>
						<!--
						<li>
							<span class="tarfee-count-number"><?php echo $totalPhoto; ?></span>
							<span><?php echo $this->translate(array('photo','photos', $totalPhoto));?></span>
						</li>
						-->
					</ul>
					
					<div class="nickname">
						<?php echo $this->htmlLink($player -> getOwner()->getHref(), $this->itemPhoto($player -> getOwner(), 'thumb.icon', $player -> getOwner()->getTitle(), array('style' => 'width: auto')), array('class' => 'members_thumb')) ?>
						<div class='members_info'>
					        <div class='members_name'>
						          <?php echo $this->htmlLink($player -> getOwner()->getHref(), $player -> getOwner() ->getTitle()) ?>
					        </div>
					        <div class='members_date'>
					          <?php echo $this->timestamp($player -> getOwner() -> creation_date) ?>
					        </div>
				      	</div>
			     	</div><!-- nickname-->

				</div>
	        </li>
			<?php endif;?>
        	<?php endforeach; ?>             
	    </ul>  
	    <?php if($this->paginator->getTotalItemCount() > $this->itemCountPerPage):?>
	  		<?php echo $this->htmlLink($this -> url(array(), 'default', true).'search?type%5B%5D=user_playercard&parent_type=group&parent_id='.$this->subject()->getIdentity(), $this -> translate('View all'), array('class' => 'icon_event_viewall')) ?>
		<?php endif;?>
	    <?php else: ?>
	    <div class="tip">
	        <span>
	             <?php echo $this->translate('No players have been created.');?>
	        </span>
	    </div>
	    <?php endif; ?>
	</div>
</div>
<script type="text/javascript">
function addEyeOn(itemId) 
{
    $('user_eyeon_'+itemId).set('html', '<a class="actions_generic" href="javascript:void(0);"><span><i class="fa fa fa-spinner fa-pulse"></i></span></a>');
    new Request.JSON({
        'url': '<?php echo $this->url(array('action'=>'add-eye-on'),'user_playercard', true)?>',
        'method': 'post',
        'data' : {
            'id' : itemId
        },
        'onSuccess': function(responseJSON, responseText) {
            if (responseJSON.status == true) {
                html = '<a class="actions_generic eye-on" href="javascript:void(0);" onclick="removeEyeOn('+itemId+')"><span><i class="fa fa-eye-slash"></i></span></a>';
                $('user_eyeon_'+itemId).set('html', html);
            }
            else {
                alert(responseJSON.message);
            }            
        }
    }).send();
}

function removeEyeOn(itemId){
	$('user_eyeon_'+itemId).set('html', '<a class="actions_generic" href="javascript:void(0);"><span><i class="fa fa fa-spinner fa-pulse"></i></span></a>');
    new Request.JSON({
        'url': '<?php echo $this->url(array('action'=>'remove-eye-on'),'user_playercard', true)?>',
        'method': 'post',
        'data' : {
            'id' : itemId
        },
        'onSuccess': function(responseJSON, responseText) {
            if (responseJSON.status == true) {
                html = '<a class="actions_generic" href="javascript:void(0);" onclick="addEyeOn('+itemId+')"><span><i class="fa fa-eye"></i></span></a>';
                $('user_eyeon_'+itemId).set('html', html);
            }
            else {
                alert(responseJSON.message);
            }            
        }
    }).send();
}
function showOptions(itemId, obj)
{
	$$('.setting-list').each(function(e)
	{
		if(e != $('setting-list_' + itemId))
			e.style.display = 'none';
	});
	$$('.setting').each(function(e)
	{
		e.removeClass('active');
	});
	if($('setting-list_' + itemId).style.display == '')
	{
		$('setting-list_' + itemId).style.display = 'none';
		obj.removeClass('active');
	}
	else
	{
		$('setting-list_' + itemId).style.display = ''
		obj.addClass('active');
	}

}
</script>

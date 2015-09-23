<?php
	$viewer = Engine_Api::_() -> user() -> getViewer();
	$tableCategory = Engine_Api::_() -> getItemTable('ynfeedback_category');
	$categories = $tableCategory -> getCategories();
    $siteTitle = $this->layout()->siteinfo['title'];
	unset($categories[0]);
	$isAllow = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynfeedback_idea', null, 'create')->checkRequire();
?>


<div class='ynfeedback-preview-overlay'></div>
<div class='ynfeedback-preview-main'>
	<span class='btn-ynfeedback-preview-popup-close'><?php echo $this->translate('<i class=\'fa fa-times\'></i>'); ?></span>
	<div class='ynfeedback-preview-main-index'>	
		<div id='popup-title'>
	        <span><?php echo $this->translate('Share your idea with ')?></span>
	       	<span><?php echo $siteTitle?></span>       
	    </div>
		<div id='tab-header' class='add-new-idea'>
	        <div id='add-new-idea' class='tab-header active' onclick='changePopupTab(this)'>
	        	<i class='fa fa-pencil'></i>
	            <div class='tab-title'><?php echo $this->translate('Add New Idea')?></div>
	            <div class='tab-description'><?php echo $this->translate('Click here to suggest improvements.')?></div>
	        </div>
	        <div id='current-ideas' class='tab-header' onclick='changePopupTab(this)'>
	        	<i class='fa fa-lightbulb-o'></i>
	            <div class='tab-title'><?php echo $this->translate('Current Ideas')?></div>
	            <div class='tab-description'><?php echo $this->translate('Read, comment and vote on ideas from the other users.')?></div>
	        </div>
	        <div id='all-feedbacks' class='tab-header' onclick='showAllFeedback()'>
	        	<i class='fa fa-comments'></i>
	            <div class='tab-title'><?php echo $this->translate('All Feedback')?></div>
	            <div class='tab-description'><?php echo $this->translate('Click to view all ideas.')?></div>
	        </div>
	    </div>
	    <div id='tab-content'>
	        <div id='add-new-idea-content' class='tab-content active'>
	        	<?php if($isAllow) :?>
		        	<div class='global_form'>
		        	<form class='global_form' onsubmit='return false;'>
		        		<div><div>
		        			<?php if($this -> isFinal) :?>
		        				<h3><?php echo $this -> translate('Awesome!!!'); ?></h3>
		        					<p class='form-description'><?php echo $this -> translate('Do you still want to send us a message?');?></p>
		        			<?php else:?>
		        				<h3><?php echo $this -> translate('Add a new Feedback'); ?></h3>
		        				<p class='form-description'><?php echo $this -> translate('We would like to get your feedbacks on how our website should be improved. Tell us about new features we should consider or how we can improve existing features.');?></p>
		        			<?php endif;?>
		        				<?php if(empty($categories)):?>
		        					<div class='tip'>
		        						<span><?php echo $this -> translate('Create feedback require at least one category. Please contact admin for more details.');?></span>
		        					</div>
		        				<?php endif;?>
	        				<div id='popup-form-valid' class='tip'></div>
		        			<div class='form-elements'>
		        				<div class='form-elements-left'>
			        				<div id='title_popup-wrapper' class='form-wrapper'>
			        					<div id='title_popup-label' class='form-label'>
			        						<label for='title_popup'><?php echo $this -> translate('*Give your feedback');?></label>
			        					</div>
			        					<div id='title_popup-element' class='form-element'>
			        						<input type='text' name='title_popup' id='title_popup' value=''>
			        					</div>
			        				</div>
			        				<div id='description_popup-wrapper' class='form-wrapper'>
			        					<div id='description_popup-label' class='form-label'>
			        						<label for='description_popup'><?php echo $this -> translate('*Give your description');?></label>
			        					</div>
			        					<div id='description_popup-element' class='form-element'>
			        						<textarea name='description_popup' id='description_popup' cols='45' rows='6'></textarea>
			        					</div>
			        				</div>
		        				</div>
		        				<div class='form-elements-right'>
			        				<div id='category_id_popup-wrapper' class='form-wrapper'>
			        					<div id='category_id_popup-label' class='form-label'>
			        						<label for='category_id_popup'><?php echo $this -> translate('*Category');?></label>
			        					</div>
			        					<div id='category_id_popup-element' class='form-element'>
			        						<select name='category_id_popup' id='category_id_popup'>
			        							<?php foreach ($categories as $item) :?>
			        						    	<option value='<?php echo $item['category_id'];?>'><?php echo str_repeat('-- ', $item['level'] - 1) . $this -> translate($item['title']);?></option>
			        							<?php endforeach;?>
			        						</select>
			        					</div>
			        				</div>
			        				<div id='severity_popup-wrapper' class='form-wrapper'>
			        					<div id='severity_popup-label' class='form-label'>
			        						<label for='severity_popup' class='required'><?php echo $this -> translate('Severity');?></label>
			        					</div>
			        					<div id='severity_popup-element' class='form-element'>
			        						<select name='severity_popup' id='severity_popup'>
			        						   <?php
													$tableSeverity = Engine_Api::_() -> getDbTable('severities', 'ynfeedback');
						 							$severityArray = $tableSeverity -> getSeverityArray();
												?>
												<?php foreach($severityArray as $key => $value) :?>
											   		<option value='<?php echo $key;?>'><?php echo $this -> translate($value);?></option>
											    <?php endforeach;?>
			        						</select>
			        					</div>
			        				</div>
			        				<?php if($viewer -> getIdentity()) :?>
			        					<?php
			        						// Privacy
			        					    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynfeedback_idea', $viewer, 'auth_view');
			        						$availableLabels = array(
			        					        'everyone' => $this -> translate('Everyone'),
			        					        'owner_network' =>  $this -> translate('Friends and Networks'),
			        					        'owner_member_member' => $this -> translate('Friends of Friends'),
			        					        'owner_member' => $this -> translate('Friends Only'),
			        					        'owner' => $this -> translate('Just Me')
			        					      );
			        					    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
			        					?>
			        					<div id='auth_view_popup-wrapper' class='form-wrapper'>
			        						<div id='auth_view_popup-label' class='form-label'>
			        							<label for='auth_view_popup' class='optional'><?php echo $this -> translate('Privacy');?></label>
			        						</div>
			        						<div id='auth_view_popup-element' class='form-element'>
			        							<select name='auth_view_popup' id='auth_view_popup'>
			        								<?php foreach ($viewOptions as $key => $value) :?>
			        							    	<option value='<?php echo $key;?>'><?php echo $value;?></option>
			        							    <?php endforeach;?>
			        							</select>
			        						</div>
			        					</div>
			        				<?php else :?>
			        					<div class='ynfeedback-clearfix'>
				        					<div id='guest_name_popup-wrapper' class='form-wrapper'>
				        						<div id='guest_name_popup-label' class='form-label'>
				        							<label for='guest_name_popup'><?php echo $this -> translate('*Your Name');?></label>
				        						</div>
				        						<div id='guest_name_popup-element' class='form-element'>
				        							<input type='text' name='guest_name_popup' id='guest_name_popup' value=''>
				        						</div>
				        					</div>
				        					<div id='guest_email_popup-wrapper' class='form-wrapper'>
				        						<div id='guest_email_popup-label' class='form-label'>
				        							<label for='guest_email_popup'><?php echo $this -> translate('*Your Email');?></label>
				        						</div>
				        						<div id='guest_email_popup-element' class='form-element'>
				        							<input type='text' name='guest_email_popup' id='guest_email_popup' value=''>
				        						</div>
				        					</div>
			        					</div>
			        				<?php endif;?>
			        				<div id='buttons-wrapper' class='form-wrapper'>
			        					<div id='buttons-element' class='form-element'>
			        						<button name='popup_addnew_button' id='popup_addnew_button'><?php echo $this -> translate('Next');?> <i class='fa fa-chevron-right'></i></button>
			        					</div>
			        				</div>
		        				</div>
		        			</div>	
		        		</div></div>
		        	</form>
		        	</div>
		        <?php else:?>
		        	<div class='tip'>
		        		<span><?php echo $this -> translate('You do not have permission to create feedback.');?></span>
		        	</div>
		        <?php endif;?>
	        </div>
	        <div id='current-ideas-content' class='tab-content'>
	            <div id='current-tab-header'>
	                <div id='most-popular-ideas' class='current-tab-header active' onclick='changeCurrentTab(this)'>
	                    <?php echo $this->translate('Most Popular Ideas')?>
	                </div>
	                <div id='newest-ideas' class='current-tab-header' onclick='changeCurrentTab(this)'>
	                    <?php echo $this->translate('Newest Ideas')?>
	                </div>
	            </div>
	            <div id='current-tab-content'>
	                <div id='most-popular-ideas-content' class='current-tab-content active'>
	                </div>
	                <div id='newest-ideas-content' class='current-tab-content'>
	                </div>
	            </div>
	        </div>
	    </div>
    </div>
</div>
<?php if($this -> isBack):?>
<script type="text/javascript">
			var params = {};
		        params['format'] = 'html';
		        params['inputTitle'] = '1';
		        var request = new Request.HTML({
		            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.most-voted-feedback-popup',
		            data : params,
		            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                $('add-new-idea-content').innerHTML = responseHTML;
		                eval(responseJavaScript);
		            }
		        });
		        request.send();
</script>
<?php else :?>
<?php echo $this -> translate('Are any of these helpful');?>

<div class="ynfeedback-popup-simple-content">
<?php foreach ($this -> ideas as $idea) :?>
	<div class="ynfeedback-idea-item">
		<i class="fa fa-files-o"></i>
		<div class="ynfeedback-idea-item-title"><?php echo $this -> htmlLink($idea -> getHref(), $idea -> getTitle());?></div>
		<div class="ynfeedback-idea-item-description"><?php echo $idea -> description;?></div>
	</div>
<?php endforeach;?>
</div>

<div class="ynfeedback-popup-simple-footer">
	<a id="post_own_helpful_back"><i class="fa fa-arrow-left"></i> <?php echo $this -> translate('Back');?></a>
	<button id="post_own_helpful_skip"><?php echo $this -> translate('Skip and post your feedback');?></button>
</div>

<script type="application/javascript">
	
	var isLogin = false;
	<?php if(Engine_Api::_() -> user() -> getViewer() -> getIdentity()) :?>
		var isLogin = true;
	<?php endif;?>
	
	window.addEvent('domready', function() {
		
		
		if($('post_own_helpful_skip'))
		{
			$('post_own_helpful_skip').addEvent('click', function(e){
				var html = "<?php echo str_replace(array("\n","\r","\r\n"),'', $this->partial('_add_popup_feedback_simple.tpl', 'ynfeedback', array()))?>";
				$('add-new-idea-content').innerHTML = html;
				
				//populate values if exist
				var category_id_popup_cockie = getCookiePopUp('ynfeedback-category_id_popup');
				var title_popup_cockie = getCookiePopUp('ynfeedback-title_popup');
				var description = getCookiePopUp('ynfeedback-description_popup');
				if(description != "")
				{
					var description_popup_cockie = description.replace(/------/g, '\n');
				}
				else
				{
					var description_popup_cockie = "";
				}
				
				var severity_popup_cockie = getCookiePopUp('ynfeedback-severity_popup');
				if(isLogin)
				{
					var auth_view_popup_cockie = getCookiePopUp('ynfeedback-auth_view_popup');
				}	
				else
				{
					var guest_name_popup_cockie = getCookiePopUp('ynfeedback-guest_name_popup');
					var guest_email_popup_cockie = getCookiePopUp('ynfeedback-guest_email_popup');
				}
				
				if ( category_id_popup_cockie != '') 
				{
					$('category_id_popup').set('value', category_id_popup_cockie);
				}
				if ( title_popup_cockie != '') 
				{
					$('title_popup').set('value', title_popup_cockie);
				}
				if ( description_popup_cockie != '') 
				{
					$('description_popup').set('value', description_popup_cockie);
				}
				if ( severity_popup_cockie != '') 
				{
					$('severity_popup').set('value', severity_popup_cockie);
				}
				if(isLogin)
				{
					if ( auth_view_popup_cockie != '') 
					{
						$('auth_view_popup').set('value', auth_view_popup_cockie);
					}
				}
				else
				{
					if ( guest_name_popup_cockie != '') 
					{
						$('guest_name_popup').set('value', guest_name_popup_cockie);
					}
					if ( guest_email_popup_cockie != '') 
					{
						$('guest_email_popup').set('value', guest_email_popup_cockie);
					}
				}
				
				
				if($('simple_create_back'))
				{
					$('simple_create_back').addEvent('click', function(e){
						
						//save data for populate
						//get values from popup
						var category_id = $('category_id_popup').getSelected().get('value')[0];
						var title = $('title_popup').value;
						var description = $('description_popup').value;
						description = description.replace(/\n/g, '------');
						var severity = $('severity_popup').getSelected().get('value');
						if(isLogin)
						{
							var auth_view = $('auth_view_popup').getSelected().get('value');
						}
						else
						{
							var guest_name = $('guest_name_popup').get('value');
							var guest_email = $('guest_email_popup').get('value');
						}
		
						//set cockie
						setCookiePopUp('ynfeedback-category_id_popup', category_id, 1);
						setCookiePopUp('ynfeedback-title_popup', title, 1);
						setCookiePopUp('ynfeedback-description_popup', description, 1);
						setCookiePopUp('ynfeedback-severity_popup', severity, 1);
						if(isLogin)
						{
							setCookiePopUp('ynfeedback-auth_view_popup', auth_view, 1);
						}
						else
						{
							setCookiePopUp('ynfeedback-guest_name_popup', guest_name, 1);
							setCookiePopUp('ynfeedback-guest_email_popup', guest_email, 1);
						}
						
						titleValue = getCookiePopUp('post_own_feedback_title');
						var params = {};
				        params['format'] = 'html';
				        params['back'] = '1';
				        params['text'] = title;
				        var request = new Request.HTML({
				            url : '<?php echo $this -> url(array('action' => 'simple-helpful'), 'ynfeedback_general', true);?>',
				            data : params,
				            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				                $('add-new-idea-content').innerHTML = responseHTML;
				                eval(responseJavaScript);
				            }
				        });
			        	request.send();
					});
				}
				if($('simple_create'))
				{
					$('simple_create').addEvent('click', function(e){
						
						//get values from popup
						var category_id = $('category_id_popup').getSelected().get('value')[0];
						var title = $('title_popup').value;
						var description = $('description_popup').value;
						var severity = $('severity_popup').getSelected().get('value');
						if(isLogin)
						{
							var auth_view = $('auth_view_popup').getSelected().get('value');
						}
						else
						{
							var guest_name = $('guest_name_popup').get('value');
							var guest_email = $('guest_email_popup').get('value');
						}
						//check validate
						if ($('popup-form-valid')) {
						    $('popup-form-valid').empty();
						}
						if ( !isNumeric(category_id)) 
						{
						    if ($('popup-form-valid')) {
						        var span = new Element('span', {
						            text: '<?php echo $this -> translate('Please select category!')?>'
						        });
						        $('popup-form-valid').grab(span);
						    }
							return;
						}
						if ( title == '') 
						{
						    if ($('popup-form-valid')) {
                                var span = new Element('span', {
                                    text: '<?php echo $this -> translate('Please enter title!')?>'
                                });
                                $('popup-form-valid').grab(span);
                            }
                            return;
						}
						else
						{
							if(title.length > 100)
							{
							    if ($('popup-form-valid')) {
                                    var span = new Element('span', {
                                        text: '<?php echo $this -> translate('The title is more than 100 characters long!')?>'
                                    });
                                    $('popup-form-valid').grab(span);
                                }
                                return;
							}
						}
						if ( description == '') 
						{
						    if ($('popup-form-valid')) {
                                var span = new Element('span', {
                                    text: '<?php echo $this -> translate('Please enter description!')?>'
                                });
                                $('popup-form-valid').grab(span);
                            }
                            return;
						}
						if ( severity == '') 
						{
						    if ($('popup-form-valid')) {
                                var span = new Element('span', {
                                    text: '<?php echo $this -> translate('Please select severity!')?>'
                                });
                                $('popup-form-valid').grab(span);
                            }
                            return;
						}
						if(isLogin)
						{
							if ( auth_view == '') 
							{
							    if ($('popup-form-valid')) {
                                    var span = new Element('span', {
                                        text: '<?php echo $this -> translate('Please select privacy!')?>'
                                    });
                                    $('popup-form-valid').grab(span);
                                }
                                return;
							}
						}
						else
						{
							if ( guest_name == '') 
							{
							    if ($('popup-form-valid')) {
                                    var span = new Element('span', {
                                        text: '<?php echo $this -> translate('Please enter your name!')?>'
                                    });
                                    $('popup-form-valid').grab(span);
                                }
                                return;
							}
							if ( guest_email == '') 
							{
							    if ($('popup-form-valid')) {
                                    var span = new Element('span', {
                                        text: '<?php echo $this -> translate('Please enter your email!')?>'
                                    });
                                    $('popup-form-valid').grab(span);
                                }
                                return;
							}
							else
							{
								if(!validateEmail(guest_email))
						    	{
						    	    if ($('popup-form-valid')) {
                                        var span = new Element('span', {
                                            text: '<?php echo $this -> translate('Email is invalid!')?>'
                                        });
                                        $('popup-form-valid').grab(span);
                                    }
                                    return;
						    	}
							}
						}
					
						//set cockie
						setCookiePopUp('ynfeedback-category_id_popup', category_id, 1);
						setCookiePopUp('ynfeedback-title_popup', title, 1);
						setCookiePopUp('ynfeedback-description_popup', description, 1);
						setCookiePopUp('ynfeedback-severity_popup', severity, 1);
						if(isLogin)
						{
							setCookiePopUp('ynfeedback-auth_view_popup', auth_view, 1);
						}
						else
						{
							setCookiePopUp('ynfeedback-guest_name_popup', guest_name, 1);
							setCookiePopUp('ynfeedback-guest_email_popup', guest_email, 1);
						}
						
						
						//create
						var url = "<?php echo $this -> url(array('action' => 'create-popup'), 'ynfeedback_general', true);?>";
						//get values
						var category_id_popup_cockie = getCookiePopUp('ynfeedback-category_id_popup');
						var title_popup_cockie = getCookiePopUp('ynfeedback-title_popup');
						var description_popup_cockie = description;
						var severity_popup_cockie = getCookiePopUp('ynfeedback-severity_popup');
						if(isLogin)
						{
							var auth_view_popup_cockie = getCookiePopUp('ynfeedback-auth_view_popup');
						}	
						else
						{
							var guest_name_popup_cockie = getCookiePopUp('ynfeedback-guest_name_popup');
							var guest_email_popup_cockie = getCookiePopUp('ynfeedback-guest_email_popup');
						}
						
						//generate data
						if(isLogin)
						{
							var data = {
								'category_id': category_id_popup_cockie,
					            'title': title_popup_cockie,
					            'description': description_popup_cockie,
					            'severity': severity_popup_cockie,
					            'auth_view': auth_view_popup_cockie,
							};
						}
						else
						{
							var data = {
								'category_id': category_id_popup_cockie,
					            'title': title_popup_cockie,
					            'description': description_popup_cockie,
					            'severity': severity_popup_cockie,
					            'guest_name': guest_name_popup_cockie,
					            'guest_email': guest_email_popup_cockie,
							};
						}					
						new Request.JSON({
					        url: url,
					        method: 'post',
					        data: data,
					        'onSuccess' : function(responseJSON, responseText)
					        {
								//clear cockie
								setCookiePopUp('ynfeedback-category_id_popup', '', 1);
								setCookiePopUp('ynfeedback-title_popup', '', 1);
								setCookiePopUp('ynfeedback-description_popup', '', 1);
								setCookiePopUp('ynfeedback-severity_popup', '', 1);
								if(isLogin)
								{
									setCookiePopUp('ynfeedback-auth_view_popup', '', 1);
								}
								else
								{
									setCookiePopUp('ynfeedback-guest_name_popup', '', 1);
									setCookiePopUp('ynfeedback-guest_email_popup', '', 1);
								}
							}
						}).send();			
						
						//clear cockie
						setCookiePopUp('ynfeedback-category_id_popup', '', 1);
						setCookiePopUp('ynfeedback-title_popup', '', 1);
						setCookiePopUp('ynfeedback-description_popup', '', 1);
						setCookiePopUp('ynfeedback-severity_popup', '', 1);
						if(isLogin)
						{
							setCookiePopUp('ynfeedback-auth_view_popup', '', 1);
						}
						else
						{
							setCookiePopUp('ynfeedback-guest_name_popup', '', 1);
							setCookiePopUp('ynfeedback-guest_email_popup', '', 1);
						}
						
						//load finish screen
						var params = {};
				        params['format'] = 'html';
				        params['inputTitle'] = '1';
				        params['isFinal'] = '1';
				        var request = new Request.HTML({
				            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.most-voted-feedback-popup',
				            data : params,
				            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
				                $('add-new-idea-content').innerHTML = responseHTML;
				                eval(responseJavaScript);
				            }
				        });
				        request.send();
									
					});
				}
			});
		}
		
		if($('post_own_helpful_back'))
		{
			$('post_own_helpful_back').addEvent('click', function(e){
				var params = {};
		        params['format'] = 'html';
		        params['inputTitle'] = '1';
		        var request = new Request.HTML({
		            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.most-voted-feedback-popup',
		            data : params,
		            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
		                $('add-new-idea-content').innerHTML = responseHTML;
		                eval(responseJavaScript);
		            }
		        });
		        request.send();
			});
		}
		
		<?php if($this -> isSkip):?>
			if($('post_own_helpful_skip'))
			{
				$('post_own_helpful_skip').click();
			}
		<?php endif;?>
		
	});
</script>
<?php endif;?>
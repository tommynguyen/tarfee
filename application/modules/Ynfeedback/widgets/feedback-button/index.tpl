<style>
    .layout_ynfeedback_feedback_button {
        position: fixed;
        z-index: 15;
        top: <?php echo $this->position?>%;

        <?php if ($this->left == 2) : ?>
        	right: 0;
        	margin-top: -32px;
        <?php else : ?>
        	left: 0;
        	margin-top: -32px;
        <?php endif; ?>
    }

    #ynfeedback-feedback-button {
        background-color: <?php echo $this->buttoncolor?>;
        <?php if ($this->type == 1) : ?>
            height: 32px;
	        transform: rotate(<?php echo ($this->left == 1) ? '' : '-';?>90deg);
	        -webkit-transform: rotate(<?php echo ($this->left == 1) ? '' : '-';?>90deg);
	        -moz-transform: rotate(<?php echo ($this->left == 1) ? '' : '-';?>90deg);
	        transform-origin: <?php echo ($this->left == 1) ? 'left 32px' : 'right 32px';?>;
	        -webkit-transform-origin: <?php echo ($this->left == 1) ? 'left 32px' : 'right 32px';?>;
	        -moz-transform-origin: <?php echo ($this->left == 1) ? 'left 32px' : 'right 32px';?>;
        <?php endif; ?>
    }

    #ynfeedback-feedback-button:hover {
        background-color: <?php echo $this->hoverbuttoncolor?>;
    }

    #ynfeedback-feedback-button:hover #button-hover-text{
        display: inline;
    }

    #button-text {
        color: <?php echo $this->textcolor?>;
    }
    
    #ynfeedback-feedback-button:hover #button-text {
    	color: <?php echo $this->hovertextcolor?>;
    }
	
    #button-hover-text {
        display: none;
        color: <?php echo $this->hovertextcolor?>;
    }
</style>
<button id="ynfeedback-feedback-button">
<?php if ($this->type == 1) : ?>
    <span id="button-text"><?php echo $this->text?></span>
<?php else: ?>
    
    <?php if ($this->left == 2) : ?>
    <span id="button-hover-text"><?php echo $this->hovertext?></span>
    <?php endif; ?>
    <?php
    $iconSrc = ''; 
    if ($this->icon) {
        $iconSrc = Engine_Api::_()->ynfeedback()->getPhotoLink($this->icon);
    }
    else {
        $iconSrc = $this->baseUrl().'/application/modules/Ynfeedback/externals/images/icon.png';    
    }
    ?>
    <span id="button-icon"><img src='<?php echo $iconSrc?>'/></span>
    <?php if ($this->left == 1) : ?>
    <span id="button-hover-text"><?php echo $this->hovertext?></span>
    <?php endif; ?>
<?php endif; ?>
</button>

<script>
	
	<?php 
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$view = new Zend_View(); 
	?>
	
	var isLogin = false;
	<?php if($viewer -> getIdentity()) :?>
		var isLogin = true;
	<?php endif;?>

	function addCloseEvent()
	{
		$$('.btn-ynfeedback-preview-popup-close').addEvent('click', function(){
			$$('.ynfeedback-preview-popup').dispose();
		});	

		window.scrollTo(0, 100);
	}
	
	function setCookiePopUp(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}

	function getCookiePopUp(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	    }
	    return "";
	}
	
	function validateEmail(email) { 
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	} 
	
	function isNumeric(n) {
  		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	//TODO loadsimple()
	function loadDefaultSimple()
	{
		var html = "<?php echo str_replace(array("\n","\r","\r\n"),'', $view->partial('_popup_feedback_simple.tpl', 'ynfeedback', array()))?>";
		var mypreview = new Element('div', {html: html});
		mypreview.addClass('ynfeedback-preview-popup');
		$$('.ynfeedback-preview-popup').dispose();
		mypreview.inject( $$('body')[0] );
		addCloseEvent();
		
		var params = {};
        params['format'] = 'html';
        var request = new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.most-voted-feedback-popup',
            data : params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('add-new-idea-content').innerHTML = responseHTML;
                eval(responseJavaScript);
            }
        });
        request.send();
		return;
		
	}
	
	function buttonBack()
	{
		var url = "<?php echo $this -> url(array('action' => 'helpful'), 'ynfeedback_general', true);?>";
		new Request.JSON({
	        url: url,
	        method: 'post',
	        data: {
	            'id': $('popup_back_button_detail').getProperty('data-id'),
	        },
	        'onSuccess' : function(responseJSON, responseText)
	        {
	        	//check if no helpful available
	        	if(responseJSON.error == 1)
	        	{
	        	}
	        	else //if has same category idea
	        	{
					var html = responseJSON.popup_helpful_view;
					var mypreview = new Element('div', {html: html});
					
					$('add-new-idea-content').empty();
					$('add-new-idea-content').grab(mypreview);
					addCloseEvent();
					
					$('popup_back_button').addEvent('click', function(e){
						loadDefault();
					});
					
					$('popup_skip_button').addEvent('click', function(e){
						
							var url = "<?php echo $this -> url(array('action' => 'create-popup'), 'ynfeedback_general', true);?>";
							
							//get values
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
									loadDefault(true);
								}
							}).send();
					});
					//action when clicking to each helpful(to detail popup)
					$$('.idea_popup').addEvent('click', function(e){
						var url = "<?php echo $this -> url(array('action' => 'detail-popup'), 'ynfeedback_general', true);?>";
						var id = this.getProperty('data-id');
						new Request.JSON({
					        url: url,
					        method: 'post',
					        data: {
					            'id': id,
					        },
					        'onSuccess' : function(responseJSON, responseText)
					        {
					        	if(responseJSON.error == 0)
					        	{
									var html = responseJSON.popup_helpful_view_detail;
									var mypreview = new Element('div', {html: html});
									
									$('add-new-idea-content').empty();
									$('add-new-idea-content').grab(mypreview);
									addCloseEvent();
									
									$('popup_back_button_detail').addEvent('click', function(e){
										buttonBack();
									});
									
									$('popup_skip_button_detail').addEvent('click', function(e){
										$$('.ynfeedback-preview-popup').dispose();
									});
								}
					        }
				        }).send();
			  		});
				}
	          }
	   	 }).send();
	}
	
	function loadDefault(isFinal)
  	{	
  		<?php
  			$isAllow = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynfeedback_idea', null, 'create')->checkRequire();
		?>
		
		<?php
			$settings = Engine_Api::_()->getApi('settings', 'core');
			$simpleLoad = $settings->getSetting('ynfeedback_popup_style', 1);
		?>
		
		<?php if($simpleLoad) :?>
			loadDefaultSimple();
			return;
		<?php endif;?>
				
  		 if(typeof isFinal === 'undefined'){
		  	 isFinal = false;
		 };
  		
  		if(isFinal)
  		{
  			var html = "<?php echo str_replace(array("\n","\r","\r\n"),'', $view->partial('_popup_feedback.tpl', 'ynfeedback', array('isFinal' => true)))?>";
  		}
  		else
  		{
  			var html = "<?php echo str_replace(array("\n","\r","\r\n"),'', $view->partial('_popup_feedback.tpl', 'ynfeedback', array()))?>";
		}
		var mypreview = new Element('div', {html: html});
		
		mypreview.addClass('ynfeedback-preview-popup');
		$$('.ynfeedback-preview-popup').dispose();
		mypreview.inject( $$('body')[0] );
		addCloseEvent();
		
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
		
		window.scrollTo(0, 0);
		
		<?php if($isAllow) :?>
		
		$('popup_addnew_button').addEvent('click', function(e){
			
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
			description = description.replace(/\n/g, '------');
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
			
			var url = "<?php echo $this -> url(array('action' => 'helpful'), 'ynfeedback_general', true);?>";
			new Request.JSON({
		        url: url,
		        method: 'post',
		        data: {
		            'id': category_id,
		        },
		        'onSuccess' : function(responseJSON, responseText)
		        {
		        	//check if no helpful available
		        	if(responseJSON.error == 1)
		        	{
		        		var url = "<?php echo $this -> url(array('action' => 'create-popup'), 'ynfeedback_general', true);?>";
								
						//get values
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
								loadDefault(true);
							}
						}).send();
		        	}
		        	else //if has same category idea
		        	{
						var html = responseJSON.popup_helpful_view;
						var mypreview = new Element('div', {html: html});
						
						$('add-new-idea-content').empty();
						$('add-new-idea-content').grab(mypreview);
						addCloseEvent();
						
						$('popup_back_button').addEvent('click', function(e){
							loadDefault();
						});
						
						$('popup_skip_button').addEvent('click', function(e){
							
								var url = "<?php echo $this -> url(array('action' => 'create-popup'), 'ynfeedback_general', true);?>";
								
								//get values
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
										loadDefault(true);
									}
								}).send();
						});
						//action when clicking to each helpful(to detail popup)
						$$('.idea_popup').addEvent('click', function(e){
							var url = "<?php echo $this -> url(array('action' => 'detail-popup'), 'ynfeedback_general', true);?>";
							var id = this.getProperty('data-id');
							new Request.JSON({
						        url: url,
						        method: 'post',
						        data: {
						            'id': id,
						        },
						        'onSuccess' : function(responseJSON, responseText)
						        {
						        	if(responseJSON.error == 0)
						        	{
										var html = responseJSON.popup_helpful_view_detail;
										var mypreview = new Element('div', {html: html});
										
										$('add-new-idea-content').empty();
										$('add-new-idea-content').grab(mypreview);
										addCloseEvent();
										
										$('popup_back_button_detail').addEvent('click', function(e){
											buttonBack();
										});
										
										$('popup_skip_button_detail').addEvent('click', function(e){
											$$('.ynfeedback-preview-popup').dispose();
										});
									}
						        }
					        }).send();
				  		});
					}
		          }
		   	 }).send();
		});
		<?php endif;?>
  	}
</script>

<script>
	// show all feedback
	function showAllFeedback() {
		window.location = '<?php echo $this->url(array('action' => 'index'), 'ynfeedback_general', true)?>';
        return;
	}

    //for switch tabs on popup
    function changePopupTab(obj) {
        $$('#tab-header .tab-header').removeClass('active');
        obj.addClass('active');

        $$('#tab-content .tab-content').removeClass('active');
        var id = obj.get('id');

        $('tab-header').erase('class');
        $('tab-header').addClass( id );

        if (id == 'current-ideas') {
            if ($('most-popular-ideas').hasClass('active')) {
                renderMostPopularIdeas();
            }
            else { 
                renderNewestIdeas();
            }
        }
        $(id+'-content').addClass('active');
    }
    
    function changeCurrentTab(obj) {
        $$('#current-tab-header .current-tab-header').removeClass('active');
        obj.addClass('active');
        $$('#current-tab-content .current-tab-content').removeClass('active');
        var id = obj.get('id');
        if (id == 'most-popular-ideas') {
            renderMostPopularIdeas();
        }
        else {
            renderNewestIdeas();
        }
        $(id+'-content').addClass('active');
    }
    
    function renderMostPopularIdeas() {
        if (!$('most-popular-ideas-content')) {
            return;
        }
        var params = {};
        params['format'] = 'html';
        var request = new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.most-popular-ideas',
            data : params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('most-popular-ideas-content').empty();
                $('most-popular-ideas-content').grab(Elements.from(responseHTML)[0]);
                $('newest-ideas-content').empty();
                eval(responseJavaScript);
            }
        });
        request.send();
    }
    
    function renderNewestIdeas() {
        if (!$('newest-ideas-content')) {
            return;
        }
        var params = {};
        params['format'] = 'html';
        var request = new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/name/ynfeedback.newest-ideas',
            data : params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('newest-ideas-content').empty();
                $('newest-ideas-content').grab(Elements.from(responseHTML)[0]);
                $('most-popular-ideas-content').empty();
                eval(responseJavaScript);
            }
        });
        request.send();
    }
</script>
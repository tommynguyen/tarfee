<?php $this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"); ?>
<?php $this -> headScript() -> appendFile($this -> layout() -> staticBaseUrl . 'application/modules/User/externals/scripts/validator.js'); ?>
<?php 
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this -> headScript() -> appendFile($staticBaseUrl . 'application/modules/User/externals/scripts/jquery.min.js')    
                      -> appendScript('jQuery.noConflict();')
                      -> appendFile($staticBaseUrl . 'application/modules/User/externals/scripts/js/vendor/jquery.ui.widget.js')  
                      -> appendFile($staticBaseUrl . 'application/modules/User/externals/scripts/js/jquery.iframe-transport.js')
                      -> appendFile($staticBaseUrl . 'application/modules/User/externals/scripts/js/jquery.fileupload.js'); ?>
<?php 
    $viewer = $this->viewer;
	$subject = $this->subject;
?>

<div id="user-profile-sections-content">
    <ul id="sections-content-items">
    <?php 
    $allSections = Engine_Api::_()->user()->getAllSections($subject);
    foreach ($allSections as $key => $section): ?>
		<?php 
			$content = Engine_Api::_()->user()->renderSection($key, $subject, array());
			if (trim($content)) : ?>
		        <li class="sections-content-item" id="sections-content-item_<?php echo $key?>">
			        <div class="profile-section">
			        	<?php echo $content; ?>
			        </div>
		        </li>
	        <?php endif;?>   
    <?php endforeach;?>
    </ul>
</div>

<script type="text/javascript">
en4.core.language.addData({'email_valid': ' <?php echo $this->translate('email_valid')?>'});
en4.core.language.addData({'require_valid': ' <?php echo $this->translate('require_valid')?>'});
en4.core.language.addData({'require-select_valid': ' <?php echo $this->translate('require-select_valid')?>'});
en4.core.language.addData({'year_valid': ' <?php echo $this->translate('year_valid')?>'});
en4.core.language.addData({'year-before_valid': ' <?php echo $this->translate('year-before_valid')?>'});
en4.core.language.addData({'month-year-before_valid': ' <?php echo $this->translate('year-before_valid')?>'});
en4.core.language.addData({'month-year-before-current_valid': ' <?php echo $this->translate('month-year-before-current_valid')?>'});
var confirm = false;
var type = '';
var item_id = 0;
var reloadRecommendation = false;
var render = '';

window.addEvent('domready', function() {
    addEventToForm();
    Smoothbox.Modal.Iframe.prototype.onClose=function() {
		this.fireEvent('closeafter', this);
 		try{
 			if (reloadRecommendation == true && render != '') {
 				var type = 'recommendation';
 				var params = {};
 				params.render = render;
 				renderSection(type, params);
 				reloadRecommendation = false;
 				render = '';
 			}
 		}
 		catch(ex){}
	};
});

function addEventToForm() {
	$$('.create-button').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var type = this.get('rel');
            var params = {};
            params.create = true;
            renderSection(type, params);
        });
    });
    
    $$('.section-form').each(function(el) {
        el.removeEvents('submit');
        el.addEvent('submit', function(e){
            e.preventDefault();
            var type = this.get('rel');
            var params = this.toQueryString().parseQueryString();
            params.save = true;
            var valid = validForm(type);
            if (valid)
                 renderSection(type, params);
        });
    });
    
    $$('.reload-cancel-btn').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var type = this.get('rel');
            var params = {};
            renderSection(type, params);
        });
    });
    
    $$('.remove-btn').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var item = this.getParent('.section-form').getElement('.item_id');
            if (item) {
            	var id = item.get('id');
            	var arr = id.split('-');
            	if (arr.length >= 2) {
            		type = arr[0];
            		item_id = arr[1];
            	}
            }
            else {
            	type = this.get('rel');
            }
            
            
            var div = new Element('div', {
               'class': 'profile-section-confirm-popup' 
            });
            var text = '<?php echo $this->translate('Do you want to remove this?')?>';
            var p = new Element('p', {
                'class': 'profile-section-confirm-message',
                text: text,
            });
            var button = new Element('button', {
                'class': 'profile-section-confirm-button',
                text: '<?php echo $this->translate('Remove')?>',
                onclick: 'parent.Smoothbox.close();confirm=true;removeItemConfirm();'
                
            });
            var span = new Element('span', {
               text: '<?php echo $this->translate(' or ')?>' 
            });
            
            var cancel = new Element('a', {
                text: '<?php echo $this->translate('Cancel')?>',
                onclick: 'parent.Smoothbox.close();',
                href: 'javascript:void(0)'
            });
            
            div.grab(p);
            div.grab(button);
            div.grab(span);
            div.grab(cancel);
            Smoothbox.open(div);
        });
    });
    
    $$('.cancel-btn').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var form = this.getParent('.profile-section-form');
            form.hide();
        });
    });
        
    $$('.edit-btn').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var item = this.getParent('.section-item');
            var id = item.get('id');
            var arr = id.split('-');
            var type = arr[0];
            var item_id = arr[1];
            var params = {};
            params.edit = true;
            params.item_id = item_id;
            renderSection(type, params);
        });
    });
    
    $$('.manage-section-button.recommendation').each(function(el) {
        el.removeEvents('click');
        el.addEvent('click', function(e){
            var type = 'recommendation';
            var render = el.get('rel');
            var params = {};
            params.render = render;
            renderSection(type, params);
        });
    });
    
    $$('.recommendation-popup').each(function(el) {
    	el.removeEvents('click');
        el.addEvent('click', function(e){
        	e.preventDefault();
        	reloadRecommendation = true;
            render = el.get('rel');
            var url = el.get('href');
            Smoothbox.open(url);
        });
    });
    
    //for upload photos
    var url = '<?php echo $this->url(array('action'=>'upload-photo'), 'user_general', true)?>';
    $$('.section-fileupload').each(function(el) {
        var div_parent = el.getParent('.profile-section-form-wrapper');
        var id = el.get('id');
        jQuery('#'+id).fileupload({
            url: url,
            dataType: 'json',
            done: function (e, data) {
                var status_div = div_parent.getElement('.upload-status');
                var photo_id = div_parent.getElement('#photo_id');
                jQuery.each(data.result.files, function (index, file) {
                	var loading = div_parent.getElement('.upload-loading');
            		loading.hide();
                    if(file.status) {
                        status_div.innerHTML = '<?php echo $this->translate('Upload successfully!')?> '+file.name;
                        photo_id.value = file.photo_id;
                    }
                    else {
                    	status_div.innerHTML = '<?php echo $this->translate('Upload fail!')?> '+file.error;
                    }
                });
            },
            progressall: function (e, data) {
            	var loading = div_parent.getElement('.upload-loading');
            	loading.show();
            	var status_div = div_parent.getElement('.upload-status');
            	status_div.innerHTML = '';
          	},
        }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');        
    });
}

function renderSection(type, params) {
        if ($('sections-content-item_'+type)) {
            var content = $('sections-content-item_'+type).getElement('.profile-section-content');
            var loading = $('sections-content-item_'+type).getElement('.profile-section-loading');
            if (loading) {
                loading.show();
            }
            if (content) {
                content.hide();
            }
        }
        var url = '<?php echo $this->url(array('action' => 'render-section', 'user_id' => $subject->getIdentity()), 'user_general', true)?>';
        var data = {};
        data.type = type;
        data.params = params;
        var request = new Request.HTML({
            url : url,
            data : data,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                elements = Elements.from(responseHTML);
                if (elements.length > 0) {
                    if ($('sections-content-item_'+type)) {
                        var content = $('sections-content-item_'+type).getElement('.profile-section');
                        if (content) {
                        	content.empty();
	                        content.adopt(elements);
	                        eval(responseJavaScript);
	                    	addEventToForm();
                        }
                    } 
                }
                else {
                	if ($('sections-content-item_'+type)) {
                		$('sections-content-item_'+type).destroy();
                	}
                }
            }
        });
        request.send();
    }
    
    function removeItemConfirm() {
        if (confirm == true && type != '') {
            var params = {};
            params.remove = true;
            if (item_id > 0) {
            	params.item_id = item_id;
            }
            renderSection(type, params);
            confirm = false;
            type = '';
            item_id = 0;
        }
    }
    
    function validForm(section) {
        var args = [];
        switch (section) {
        	case 'contact':
        		args.push(['contact-contact_num', 'require', '<?php echo $this->translate('Contact #')?>']);
        		args.push(['contact-email1', 'require', '<?php echo $this->translate('Email 1')?>']);
                args.push(['contact-email1', 'email', '<?php echo $this->translate('Email 1')?>']);
                args.push(['contact-email2', 'email', '<?php echo $this->translate('Email 2')?>']);
                break;
        	case 'bio':
        		return true;
        		break;
        	case 'offerservice':
        		args.push(['offerservice-service', 'require', '<?php echo $this->translate('Service')?>']);
        		break;
    		case 'archievement':
                args.push(['archievement-title', 'require', '<?php echo $this->translate('Title')?>']);
                args.push(['archievement-year', 'require', '<?php echo $this->translate('Year')?>']);
                args.push(['archievement-year', 'year', '<?php echo $this->translate('Year')?>']);
                break;
            case 'license':
                args.push(['license-title', 'require', '<?php echo $this->translate('Name')?>']);
                /*args.push(['license-number', 'require', '<?php echo $this->translate('Number')?>']);*/
                args.push(['license-year', 'require', '<?php echo $this->translate('Year')?>']);
                args.push(['license-year', 'year', '<?php echo $this->translate('Year')?>']);
                args.push(['license-year', 'month-year-before-current', 'license-month', '<?php echo $this->translate('Time')?>']);
                break;  
            case 'experience':
                args.push(['experience-title', 'require', '<?php echo $this->translate('Position')?>']);
                args.push(['experience-company', 'require', '<?php echo $this->translate('Company')?>']);
                args.push(['experience-start_year', 'require', '<?php echo $this->translate('Start Year')?>']);
                args.push(['experience-start_year', 'year', '<?php echo $this->translate('Start Year')?>']);
                if (!$('experience-current').checked) {
                    args.push(['experience-end_year', 'require', '<?php echo $this->translate('End Date')?>']);
                    args.push(['experience-end_year', 'year', '<?php echo $this->translate('End Date')?>']);
                    args.push(['experience-start_year', 'month-year-before', 'experience-start_month', 'experience-end_year', 'experience-end_month', '<?php echo $this->translate('Start Date')?>', '<?php echo $this->translate('End Date')?>']);
                }
                else {
                	args.push(['experience-start_year', 'month-year-before-current', 'experience-start_month', '<?php echo $this->translate('Start Date')?>']);
                }
                break; 
                
            case 'education':
                args.push(['education-degree', 'require', '<?php echo $this->translate('Degree')?>']);
                args.push(['education-institute', 'require', '<?php echo $this->translate('Institute')?>']);
                args.push(['education-attend_from', 'year-before', 'education-attend_to', '<?php echo $this->translate('Start Year')?>', '<?php echo $this->translate('End Year')?>']);
                break;
                
            case 'recommendation':
            	return true;
            	break;
                 
        }
        if ($('profile-section-form-'+section)) {
            $('profile-section-form-'+section).getElements('.error').each(function(el) {
                el.empty();    
            });
            validator.init(args);
            return validator.execute();
        }
    }
</script>

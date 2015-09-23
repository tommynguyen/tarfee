<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
    en4.core.runonce.add(function() 
    {
        var tagsUrl = '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>';
        var validationUrl = '<?php echo $this->url(array('action' => 'validation'), 'video_general', true) ?>';
        var validationErrorMessage = "<?php echo $this->translate("We could not find a video there - please check the URL and try again. If you are sure that the URL is valid, please click %s to continue.", "<a href='javascript:void(0);' onclick='javascript:ignoreValidation();'>".$this->translate("here")."</a>"); ?>";
        var checkingUrlMessage = "<?php echo $this->string()->escapeJavascript($this->translate('Checking URL...')) ?>";

        var current_code = "";

        var submitable = false;
        $('form-upload').addEvent('submit', function(obj) {            
            return submitable;
        });

        var ignoreValidation = window.ignoreValidation = function() 
        {
            $('upload-wrapper').style.display = "block";
            $('validation').style.display = "none";
            $('code').value = current_code;
            $('ignore').value = true;
            submitable = true;
        }
    
        var updateTextFields = window.updateTextFields = function() 
        {
            var video_element = document.getElementById("type");
            var url_element = document.getElementById("url-wrapper");
            var file_element = document.getElementById("file-wrapper");
            var submit_element = document.getElementById("upload-wrapper");

            // clear url if input field on change
            submit_element.style.display = "none";

            // If video source is empty
            if( video_element.value == 0 ) {
                $('url').value = "";
                file_element.style.display = "none";
                url_element.style.display = "none";
                return;
            } else if( $('code').value && $('url').value ) 
            {
                file_element.style.display = "none";
                submit_element.style.display = "none";
                $('url').value = "";
                return;
            } else if( video_element.value == <?php echo Ynvideo_Plugin_Factory::getUploadedType()?> ) {
                // If video source is from computer
                $('url').value = "";
                $('code').value = "";
                file_element.style.display = "block";
                url_element.style.display = "none";
                return;
            }
            else if(video_element.value == 6)
            {
                if($('url-label').getElements('label').length > 0)
                {
                    var label = $('url-label').getElements('label')[0];
                    label.innerHTML = '<?php echo $this -> translate('Embed Codes');?>';
                }
                if($('url-element').getElements('p').length > 0)
                {
                    var description = $('url-element').getElements('p')[0];
                    description.innerHTML = '<?php echo $this -> translate('Paste embed codes (iframe) of the video here.');?>';
                }
                $('url').value = "";
                $('code').value = "";
                file_element.style.display = "none";
                url_element.style.display = "block";
                return;
            } 
            else if(video_element.value) {
                // If video source is youtube or vimeo
                $('url').value = "";
                $('code').value = "";
                file_element.style.display = "none";
                url_element.style.display = "block";
                if($('url-label').getElements('label').length > 0)
                {
                    var label = $('url-label').getElements('label')[0];
                    label.innerHTML = '<?php echo $this -> translate('Video Link (URL)');?>';
                }
                if($('url-element').getElements('p').length > 0)
                {
                    var description = $('url-element').getElements('p')[0];
                    description.innerHTML = '<?php echo $this -> translate('Paste the web address of the video here.');?>';
                }
                return;
            } else if( $('id').value ) {
                // if there is video_id that means this form is returned from uploading 
                // because some other required field
                $('type-wrapper').style.display = "none";
                file_element.style.display = "none";
                $('upload-wrapper').style.display = "block";
                return;
            } else {
                
            }
        }

        var ynvideo = window.video = {
            active : false,
            debug : false,
            currentUrl : null,
            currentTitle : null,
            currentDescription : null,
            currentImage : 0,
            currentImageSrc : null,
            imagesLoading : 0,
            images : [],
            maxAspect : (10 / 3), //(5 / 2), //3.1,
            minAspect : (3 / 10), //(2 / 5), //(1 / 3.1),
            minSize : 50,
            maxPixels : 500000,
            monitorInterval: null,
            monitorLastActivity : false,
            monitorDelay : 500,
            maxImageLoading : 5000,
            attach : function() {
                var bind = this;
                $('url').addEvent('keyup', function() {
                    bind.monitorLastActivity = (new Date).valueOf();
                });
                var url_element = document.getElementById("url-element");
                var myElement = new Element("p");
                myElement.innerHTML = "test";
                myElement.addClass("description");
                myElement.id = "validation";
                myElement.style.display = "none";
                url_element.appendChild(myElement);

                var body = $('url');
                var lastBody = '';
                var lastMatch = '';
                var video_element = $('type');
                (function() {
                    // Ignore if no change or url matches
                    if( body.value == lastBody || bind.currentUrl ) {
                        return;
                    }
                    // Ignore if delay not met yet
                    if( (new Date).valueOf() < bind.monitorLastActivity + bind.monitorDelay ) {
                        return;
                    }
                    // Check for link
                    var m = body.value.match(/https?:\/\/([-\w\.]+)+(:\d+)?(\/([-#:\w/_\.]*(\?\S+)?)?)?/);
                    // check for embed
                    if(video_element.value == 6)
                    {
                        m = body.value.match(/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/);
                    }
                    
                    if( $type(m) && $type(m[0]) && lastMatch != m[0] ) 
                    {
                        var videoTypes = [];
                        <?php foreach (Ynvideo_Plugin_Factory::getAllSupportTypes('name') as $key => $name) : ?>
                            videoTypes[<?php echo $key?>] = '<?php echo $name?>';
                        <?php endforeach; ?>

                        if (video_element.value != '<?php echo Ynvideo_Plugin_Factory::getUploadedType()?>') {
                            var method = videoTypes[video_element.value];
                            ynvideo.validate(body.value, video_element.value, 'ynvideo_extract_code.' + method);
                        }                           
                    }
                    lastBody = body.value;
                }).periodical(250);
            },
            validate : function(url, type, methodToExtractCode)  {              
                // extract v from url
                var code = '';
                if (type == '6') 
                {
                    code = btoa(url);
                }
                else
                {
                    code = eval(methodToExtractCode + "('" + url + "')");
                }
                if (code)
                {
                    if (type == '<?php echo Ynvideo_Plugin_Factory::getVideoURLType()?>') {
                    	var ext = code.substr(code.lastIndexOf('.') + 1);
                        if (ext.toUpperCase() == 'FLV' || ext.toUpperCase() == 'MP4') {
                        	$('upload-wrapper').style.display = "block";
                        	$('code').value = url;
                        	submitable = true;
                        }
                    } 
                    else 
                    {
                        (new Request.HTML({
                            'format': 'html',
                            'url' : validationUrl,
                            'data' : {
                                'ajax' : true,
                                'code' : code,
                                'type' : type
                            },
                            'onRequest' : function(){
                                $('validation').style.display = "block";
                                $('validation').innerHTML = checkingUrlMessage;
                                $('upload-wrapper').style.display = "none";
                            },
                            'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                                if (valid) {
                                    $('upload-wrapper').style.display = "block";
                                    $('validation').style.display = "none";
                                    $('code').value = code;                                    
                                } else {
                                    $('upload-wrapper').style.display = "none";
                                    current_code = code;
                                    $('validation').innerHTML = validationErrorMessage;                                    
                                }
                                submitable = valid;
                            }
                        })).send();
                    }
                }
            }
        }
        
        // Run stuff
        updateTextFields();
        ynvideo.attach();

        var autocompleter = new Autocompleter.Request.JSON('tags', tagsUrl, {
            'postVar' : 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'customChoices' : true,
            'filterSubset' : true,
            'multiple' : true,
            'injectChoice': function(token){
                var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
                new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
    });
    var changeUploadTo = function(value)
    {
    	if(value == 'user_playercard')
    	{
    		 $('playercard_id-wrapper').show();
    	}
    	else
    	{
    		 $('playercard_id-wrapper').hide();
    	}
    }
    window.addEvent('domready', function()
    {
    	<?php if($this -> parent_type != 'user_playercard'):?>
        	$('playercard_id-wrapper').hide();
        <?php endif;?>
    });
</script>
<?php if (($this->current_count >= $this->quota) && !empty($this->quota)): ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('You have already uploaded the maximum number of videos allowed.'); ?>
            <?php echo $this->translate('If you would like to upload a new video, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'video_general')); ?>
        </span>
    </div>
    <br/>
<?php else: ?>
    <?php echo $this->form->render($this); ?>
<?php endif; ?>
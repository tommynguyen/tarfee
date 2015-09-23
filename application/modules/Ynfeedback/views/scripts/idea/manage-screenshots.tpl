<?php
    $staticBaseUrl = $this -> layout() -> staticBaseUrl;
    $this -> headLink() 
        -> prependStylesheet('//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css') 
        -> prependStylesheet($staticBaseUrl . 'application/modules/Ynfeedback/externals/styles/upload_photo/jquery.fileupload.css');

    $this -> headScript() 
        -> appendFile($staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/jquery-1.7.1.min.js') 
        -> appendScript('jQuery.noConflict();') 
        -> appendFile($staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/js/vendor/jquery.ui.widget.js') 
        -> appendFile($staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/js/jquery.iframe-transport.js') 
        -> appendFile($staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/js/jquery.fileupload.js') 
        -> appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js');
?>

<?php $widgetId = ($this->identity) ? ($this->identity) : 0;?>
<div id="ynfeedback-item-vote-action-<?php echo $this -> idea->getIdentity();?>-<?php echo $widgetId;?>">
    <?php echo $this -> partial ('_vote_action.tpl', 'ynfeedback', array('feedback' => $this -> idea, 'widget_id' => $widgetId));?>
</div>

<div class="ynfeedback-idea-manage-items">
 <div id="file-wrapper">
    <div id="file-label" class="form-label">
        <label for="header" class="optional"><?php echo $this->translate('Manage Screenshots') ?></label>
    </div>
  <div class="form-element">
    <p class="description"><?php echo $this->translate('You can upload image files.'); ?></p>
    <p class="description"><?php echo $this->translate('The file size limit is %s Kb. If you upload does not work, try uploading a smaller picture.', $this->maxSize); ?></p>
    <p class="description"><?php echo $this->translate(array('Maximum %s screenshot uploaded.', 'Maximum %s screenshots uploaded.', $this->max), $this->max); ?></p>
    <!-- The fileinput-button span is used to style the file input field as button -->
  
  <span id="ynfeedback-html5-upload-button" class="btn fileinput-button btn-success" type="button" <?php echo (count($this -> screenshots) < $this -> max) ? '' : 'style="display: none;"'; ?>>
      <i class="glyphicon glyphicon-plus"></i>
      <span><?php echo $this->translate("Add Photos")?></span>
      <!-- The file input field used as target for the file upload widget -->
      <input id="fileupload" type="file" name="files[]" multiple accept="image/*">
  </span>
  <button id="ynfeedback-html5-clear-button" type="button" class="btn btn-danger delete" onclick="clearList();" <?php echo (count($this -> screenshots) < $this -> max) ? '' : 'style="display: none;"'; ?>>
      <i class="glyphicon glyphicon-trash"></i>
      <span><?php echo $this->translate("Clear List")?></span>
  </button>
  
  
  <a href="<?php echo $this -> idea -> getHref();?>">
  <span class="btn btn-success" type="button">
      <i class="fa fa-reply"></i>
      <span><?php echo $this->translate("Back to Feedback")?></span>
  </span>
  </a>
  <!-- The global progress bar -->
  <div class="progress-contain">
      <div id="progress" class="progress" style="display: none; margin-top: 10px; width: 400px; float:left">
          <div class="progress-bar progress-bar-success"></div>
      </div>
      <span id="progress-percent" style="margin-top: 10px;"></span>
  </div>

  <!-- The container for the uploaded files -->
  <div class="files-contain <?php if (sizeof($this->screenshots)) echo 'success';?>">
    <ul id="files" class="files">
        <?php foreach ($this->screenshots as $photo) : ?>
            <li id="screenshot-item-<?php echo $photo->getIdentity()?>" class="file-success">
                <input type="checkbox" class="remove-checkbox" value=<?php echo $photo->getIdentity()?> />
                <a class="file-remove" id="screenshot-<?php echo $photo->getIdentity() ?>" onclick = "removeFileConfirm(this, <?php echo $photo->getIdentity()?>)" href="javascript:void(0);" title="<?php echo $this->translate("Click to remove this screenshot.")?>"><?php echo $this->translate("Remove")?></a>
                <?php echo $this->itemPhoto($photo, 'thumb.normal')?>
                <span class="file-name"><?php echo $photo->title?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <button id="remove-selected"><?php echo $this->translate('Remove selected')?></button>
  </div>
  
 </div>
</div>
<script>
var confirm = false;
var id = '';
var photo = 0;
var id_arr = [];
jQuery(function () {
    // Change this to the location of your server-side upload handler:
    var children = $('files').getChildren('li');
    var count = children.length;
    var url = '<?php echo $this->url(array('action' => 'add-screenshot', 'idea_id' => $this->idea->getIdentity()), 'ynfeedback_specific', true)?>';
    jQuery('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data)  {
            $('files').style.display = 'block';
            $('remove-selected').style.display = 'block';
            jQuery('.files-contain').addClass('success');
            jQuery.each(data.result.files, function (index, file) {
                var text = "";
                if(file.status) {
                    var ele = jQuery('<li/>', {
                        id: 'screenshot-item-'+file.screenshot_id,
                    });
                    text = '<input type="checkbox" class="remove-checkbox" value="'+ file.screenshot_id+'" />';
                    text += '<a class="file-remove" id="screenshot-' + file.screenshot_id + '" onclick = "removeFileConfirm(this, ' + file.screenshot_id + ')" href="javascript:void(0);" title="<?php echo $this->translate("Click to remove this screenshot.")?>"><?php echo $this->translate("Remove")?></a>';
                    text += '<img class="file-image" src="'+file.photo_url+'"/>';
                    if(file.name)
                        text += '<span class="file-name">' + file.name + '</span>';
                    ele.addClass('file-success');
                    if ($$('.file-success').length > 0)
                        ele.html(text).insertAfter('.file-success:last');
                    else
                        ele.html(text).prependTo('#files');
                }
                else
                {
                    var ele = jQuery('<li/>', {});
                    ele.addClass('file-unsuccess');
                    text = '<input type="checkbox" disabled="true"/><a class="file-remove" onclick = "removeFile(this, 0)" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
                    if(file.name)
                        text += '<span class="file-name">' + file.name + '</span>';
                    text += '<span class="file-info"><span>' + file.error +'</span></span>';
                    if ($$('.file-unsuccess').length > 0)
                        ele.html(text).insertAfter('.file-unsuccess:last');
                    else
                        ele.html(text).appendTo('#files');
                }

                if (data.result.current < data.result.max)
                {
                	$("ynfeedback-html5-upload-button").show();
                    $("ynfeedback-html5-clear-button").show();
                }
                else
                {
                	$("ynfeedback-html5-upload-button").hide();
                    $("ynfeedback-html5-clear-button").hide();
                }
            });
            count ++;
        },
        progressall: function (e, data) 
        {
             $('progress').style.display = 'block';
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
            jQuery('#progress-percent').css('display', 'inline-block').text(
                progress + '%'
            );
        }
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
});

function removeFileConfirm(obj, photo_id) {
    id = obj.get('id');
    photo = photo_id;
    var div = new Element('div', {
       'class': 'ynfeedback-confirm-popup' 
    });
    var p = new Element('p', {
        'class': 'ynfeedback-confirm-message',
        text: '<?php echo $this->translate('Do you want to delete this screenshot?')?>',
    });
    var button = new Element('button', {
        'class': 'ynfeedback-confirm-button',
        text: '<?php echo $this->translate('Remove')?>',
        onclick: 'parent.Smoothbox.close();confirm=true;removeFileAfterConfirm();'
        
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
}

function removeFileAfterConfirm() {
    if ((confirm == true) && (id != '')) {
        var obj = $(id);
        removeFile(obj, photo);
        confirm = false;
        id = '';
        photo = 0;
    }
}

function removeFile(obj, photo_id) {
    obj.getParent().destroy();
    if(photo_id) {
        new Request.JSON({
            url: '<?php echo $this->url(array('action' => 'remove-screenshots', 'idea_id' => $this->idea->getIdentity()), 'ynfeedback_specific', true)?>',
            method: 'post',
            data: {
                'photo_ids': photo_id,
            },
            'onSuccess' : function(responseJSON) {
                if (!responseJSON.status) {
                    alert(responseJSON.message);
                }
                if (responseJSON.current < responseJSON.max)
                {
                	$("ynfeedback-html5-upload-button").set('style','');
                    $("ynfeedback-html5-clear-button").set('style','');
                }
            }
        }).send();
    }
    
    if( $('files').getChildren().length == 0 ) {
        $('files').hide();
        $('progress').hide();
        $('remove-selected').hide();
        $('progress-percent').set('style', 'margin-top: 10px; display: none');
        $$('.files-contain')[0].set('class','files-contain');        
    }   
    
    
    return false;
}
function clearList() {
    var ids = [];
    $$('input.remove-checkbox').each(function(el) {
       ids.push(el.get('value')); 
    });
    if (ids.length <= 0) {
        $('files').style.display = 'none';
        $('remove-selected').style.display = 'none';
        jQuery('.files-contain').removeClass('success');
        jQuery('#files').text('');
        $('progress').style.display = 'none';
        $('progress-percent').innerHTML = '';
    }
    else {
        id_arr = ids;
        clearListConfirm();
    }
}

function clearListConfirm() {
    var div = new Element('div', {
       'class': 'ynfeedback-confirm-popup' 
    });
    var p = new Element('p', {
        'class': 'ynfeedback-confirm-message',
        text: '<?php echo $this->translate('Do you want to delete all screenshots?')?>',
    });
    var button = new Element('button', {
        'class': 'ynfeedback-confirm-button',
        text: '<?php echo $this->translate('Remove')?>',
        onclick: 'parent.Smoothbox.close();confirm=true;clearListAfterConfirm();'
        
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
}

function clearListAfterConfirm() {
    if (confirm == true) {
        ids = id_arr;
        new Request.JSON({
            url: '<?php echo $this->url(array('action' => 'remove-screenshots', 'idea_id' => $this->idea->getIdentity()), 'ynfeedback_specific', true)?>',
            method: 'post',
            data: {
                'photo_ids': ids.join(),
            },
            'onSuccess' : function(responseJSON) {
                if (!responseJSON.status) {
                    openPopupMessage(responseJSON.message);
                }
                else {
                    $('files').style.display = 'none';
                    $('remove-selected').style.display = 'none';
                    jQuery('.files-contain').removeClass('success');
                    jQuery('#files').text('');
                    $('progress').style.display = 'none';
                    $('progress-percent').innerHTML = '';
                    openPopupMessage('<?php echo $this->translate('All of screenshots have been removed.')?>')
                }
            }
        }).send();
        confirm = false;
        id_arr = [];
    }
}
</script>
<script type="text/javascript">
    window.addEvent('domready', function () {
        <?php if (sizeof($this->screenshots) > 0) :?>
            $('files').style.display = 'block';
            $('remove-selected').style.display = 'block';
        <?php else: ?>
            $('remove-selected').style.display = 'none';
        <?php endif; ?>
        
        $('remove-selected').addEvent('click', function() {
            var ids = [];
            $$('input.remove-checkbox').each(function(el) {
               if (el.checked) {
                   ids.push(el.get('value'));
               } 
            });
            if (ids.length <= 0) {
                openPopupMessage('<?php echo $this->translate('Please choose at least one screenshot for removing.')?>');
            }
            else {
                id_arr = ids;
                removeSelectedConfirm();
            }
        });
        
    });
    
    function removeSelectedConfirm() {
        var div = new Element('div', {
           'class': 'ynfeedback-confirm-popup' 
        });
        var p = new Element('p', {
            'class': 'ynfeedback-confirm-message',
            text: '<?php echo $this->translate('Do you want to delete those screenshots?')?>',
        });
        var button = new Element('button', {
            'class': 'ynfeedback-confirm-button',
            text: '<?php echo $this->translate('Remove')?>',
            onclick: 'parent.Smoothbox.close();confirm=true;removeSeclectedAfterConfirm();'
            
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
    }
    
    function removeSeclectedAfterConfirm() {
        if ((confirm == true) && id_arr.length > 0) {
            ids = id_arr;
            new Request.JSON({
                url: '<?php echo $this->url(array('action' => 'remove-screenshots', 'idea_id' => $this->idea->getIdentity()), 'ynfeedback_specific', true)?>',
                method: 'post',
                data: {
                    'photo_ids': ids.join(),
                },
                'onSuccess' : function(responseJSON) {
                    if (!responseJSON.status) {
                        openPopupMessage(responseJSON.message);
                    }
                    else {
                        for (var i=0; i<ids.length;i++) {
                            $('screenshot-item-'+ids[i]).destroy();
                        }
                        if( $('files').getChildren().length == 0 ) {
                            $('files').hide();
                            $('progress').hide();
                            $('remove-selected').hide();
                            $('progress-percent').set('style', 'margin-top: 10px; display: none');
                            $$('.files-contain')[0].set('class','files-contain');        
                        }
                        //openPopupMessage(ids.length+' <?php echo $this->translate('screenshot(s) have been removed.')?>')
                        window.location = window.location;
                    }
                }
            }).send();
            confirm = false;
            id_arr = [];
        }
    }
    
    function openPopupMessage($message) {
        var div = new Element('div', {
            'class': 'popup-message'    
        });
        var p = new Element('p', {
           text: $message
        });
        var button = new Element('button', {
           text: '<?php echo $this->translate('Close')?>',
           onclick: 'parent.Smoothbox.close();' 
        });
        div.grab(p);
        div.grab(button);
        Smoothbox.open(div);
    }
</script>
</div>
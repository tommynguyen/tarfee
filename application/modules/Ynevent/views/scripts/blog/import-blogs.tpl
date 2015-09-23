<?php
 $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/Autocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/Autocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/Autocompleter.Request.js');

?>
<?php echo $this->form->render($this) ?>
<script type="text/javascript">
	// Populate data
  var maxRecipients = 1;
  var json_blogs = <?php echo $this->json_blog?>;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
  
  function removeFromToValue(id) 
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    
    document.getElementById('ynevent_blog_'+id).destroy();
    $$('#toValues-element span')[0].destroy();
    $('to-wrapper').show();
    $('to').value = ''
    
    
    var toValues = document.getElementById('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    
      removeToValue(id, toValueArray);


    // hide the wrapper for usernames if it is empty
    if (document.getElementById('toValues').value==""){
      document.getElementById('toValues-wrapper').style.height = '0';
    }

    document.getElementById('to-wrapper').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
    document.getElementById('to').value ='';
  }
	
  en4.core.runonce.add(function() {
  	
    if( !isPopulated ) { // NOT POPULATED
      new Autocompleter2.Request.JSON('to', '<?php echo $this->url(array('action' => 'import', 'event_id' => $this->event_id), 'ynevent_blog', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){
          if(token.type == 'user'){
            var choice = new Element('li', {
              'class': 'autocompleter-choices',
              'html': token.photo,
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          else {
            var choice = new Element('li', {
              'class': 'autocompleter-choices friendlist',
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
        },
        onPush : function(){        	
        	//blogs_browse_photo
        	var liElement = new Element('li',{
        		'id' : 'ynevent_blog_'+document.getElementById('toValues').value
			});
			var divElement = new Element('div',{
				'class': 'blogs_browse_photo ynblogs_browse_photo'
			});
			var divaElement = new Element('a',{
				'href': json_blogs[document.getElementById('toValues').value]['blog_href'],
				'html': json_blogs[document.getElementById('toValues').value]['owner_photo']
			});
			divaElement.inject(divElement);
			divElement.inject(liElement);
			liElement.inject(document.getElementById('ynevent_blog_import'));
        	
        	//blogs_browse_info
			var divElement = new Element('div',{
				'class': 'blogs_browse_info ynblogs_browse_info'
			});
			var divp2Element = new Element('p',{
				'class': 'blogs_browse_info_title ynblogs_browse_info_title',
			});
			var divp2aElement = new Element('a',{
				'href': json_blogs[document.getElementById('toValues').value]['blog_href'],
				'html': json_blogs[document.getElementById('toValues').value]['blog_title']
			});
			
			var divp2a2Element = new Element('a',{
				'href': 'javascript:void(0)',
				'html': ' X',
				'style' : 'padding-left:8px;',
				'onclick' : 'removeFromToValue('+document.getElementById('toValues').value+',"toValues")',
			});
			
			divp2aElement.inject(divp2Element);
			divp2a2Element.inject(divp2Element);
			divp2Element.inject(divElement);
			
			var divp3Element = new Element('p',{
				'class': 'blogs_browse_info_date ynblogs_browse_info_date',
				'html': en4.core.language.translate("Posted ")+json_blogs[document.getElementById('toValues').value]['blog_creation_date']+en4.core.language.translate(" by ")+'<a href="'+json_blogs[document.getElementById('toValues').value]['owner_href']+'">'+json_blogs[document.getElementById('toValues').value]['owner_title']+'</a>',
 			});
            divp3Element.inject(divElement);
            
            // blogs_browse_info_blurb
            var divp3Element = new Element('p',{
				'class': 'blogs_browse_info_blurb ynblogs_browse_info_blurb',
				'html' : json_blogs[document.getElementById('toValues').value]['blog_body'],
			});
            divp3Element.inject(divElement);
            
			divElement.inject(liElement);
			liElement.inject(document.getElementById('ynevent_blog_import'));
        	
          if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
             document.getElementById('to-wrapper').style.display = 'none';
             document.getElementById('to').value = document.getElementById('toValues').value;
          }
        }
      });
      
      new Composer.OverText($document.getElementById('to'), {
        'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });

    } else { // POPULATED
      var myElement = new Element("span", {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title /* + ' <a href="javascript:void(0);" ' +
                  'onclick="this.parentNode.destroy();removeFromToValue("' + toID + '");">x</a>"' */
      });
      document.getElementById('to-element').appendChild(myElement);
      document.getElementById('to-wrapper').style.height = 'auto';

      // Hide to input?
      document.getElementById('to').style.display = 'none';
      document.getElementById('toValues-wrapper').style.display = 'none';
    }
  	});
</script>


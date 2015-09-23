<?php if ($this->error) : ?>
	
<div class="tip">
    <span><?php echo $this->message?></span>
</div>

<?php else :?>

<style>
    #to {
        width: 85%;
    }
</style>
	
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/Autocompleter.Request.js');
?>

<script type="text/javascript">
  window.addEvent('domready', function() {
  	
  	function remove_tags(html)
    {
       return html.replace(/<(?:.|\n)*?>/gm, '');
    }
  	
  	//keyup enter for autosuggest founder
  	var name = "";
  	$('to').addEvent('keyup', function(e)
  	{
  		 if(e.code === 13)
  		 {
			if(name == "")
  		 	{
  		 		return;
  		 	}
  		 	name = remove_tags(name);
  		 	 //set value to hidden field
	          var hiddenInputField = document.getElementById('toValues');
	          var previousToValues = remove_tags(hiddenInputField.value);
	          if (checkSpanExists(name, name)){
	            if (previousToValues==''){
	              document.getElementById('toValues').value = name;
	            }
	            else {
	              document.getElementById('toValues').value = previousToValues+","+name;
	            }
	          }
  		 	  if (checkSpanExists(name, name)){
	  		 	 //create block
	             var myElement = new Element("span");
	             myElement.id = "tospan_"+name+"_"+name;;
	             
	             myElement.innerHTML = "<a>"+name+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\");'>x</a>";
		         document.getElementById('toValues-wrapper').style.height= 'auto';
		
		         myElement.addClass("tag");
		
		         document.getElementById('toValues-element').appendChild(myElement);
		         this.fireEvent('push');
		         $('to').set('value', "");
		         
		         if( (maxRecipients != 0) && (document.getElementById('toValues').value.split(',').length >= maxRecipients) ){
		            document.getElementById('to').style.display = 'none';
		          }
	        }
        }
        else
        {
        	name = $('to').value; 
        }
  	});
  	
  	function addFounder(name, id , href )
	{
		  //set value to hidden field
		  var hiddenInputField = document.getElementById('toValues');
		  var previousToValues = hiddenInputField.value;
		  if(!id)
		  {
			  if (checkSpanExists(name, name)){
				if (previousToValues==''){
				  document.getElementById('toValues').value = name;
				}
				else {
				  document.getElementById('toValues').value = previousToValues+","+name;
				}
			  }
			  if (checkSpanExists(name, name)){
				 //create block
				 var myElement = new Element("span");
				 myElement.id = "tospan_"+name+"_"+name;;
				 
				 myElement.innerHTML = name+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\");'>x</a>";
				 document.getElementById('toValues-wrapper').style.height= 'auto';
		
				 myElement.addClass("tag");
		
				 document.getElementById('toValues-element').appendChild(myElement);
				 this.fireEvent('push');
				 $('to').set('value', "");
				 
				 if( (maxRecipients != 0) && (document.getElementById('toValues').value.split(',').length >= maxRecipients) ){
					document.getElementById('to').style.display = 'none';
				  }
			}
		}
		else
		{
			if (checkSpanExists(name, id)){
				if (previousToValues==''){
				  document.getElementById('toValues').value = id;
				}
				else {
				  document.getElementById('toValues').value = previousToValues+","+id;
				}
			  }
			  if (checkSpanExists(name, id)){
				 //create block
				 var myElement = new Element("span");
				 myElement.id = "tospan_"+name+"_"+id;;
				 myElement.innerHTML = "<a target='_blank' href="+href+">"+name+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+id+"\");'>x</a>";
				 document.getElementById('toValues-wrapper').style.height= 'auto';
		
				 myElement.addClass("tag");
		
				 document.getElementById('toValues-element').appendChild(myElement);
				 this.fireEvent('push');
				 $('to').set('value', "");
				 
				 if((maxRecipients != 0) && (document.getElementById('toValues').value.split(',').length >= maxRecipients) ){
					document.getElementById('to').style.display = 'none';
				  }
			}
		}
	}
  	
  	function checkSpanExists(name, toID){
	      var span_id = "tospan_"+name+"_"+toID;
	      if ($(span_id)){
	        return false;
	      }
	      else return true;
    } 
  	
  	function capitaliseFirstLetter(string)
	{
	    return string.charAt(0).toUpperCase() + string.slice(1);
	}
  	
  	
  	$('category_id').addEvent('change', function(){
      $(this).getParent('form').submit(); 
    }); 
  	
  	if($('0_0_1-wrapper'))
  	{
  		$('0_0_1-wrapper').setStyle('display','none');
  	}
  	
  		
	//TODO popylate data back
	//author
	<?php if(!empty($this -> posts['toValues'])):?>
		$('toValues').set('value', "");
		<?php $authors = explode(",", $this -> posts['toValues']); ?>
		<?php foreach($authors as $author) :?>
			var id = null;
			var href = null;
			var name = null;
			<?php $user = Engine_Api::_() -> getItem('user', $author); ?>
			<?php if($user -> getIdentity() > 0) :?>
				name =  '<?php echo $user -> getTitle();?>';
				id = '<?php echo $author;?>';
				href = '<?php echo $user -> getHref() ?>';
				addFounder(name, id, href);
			<?php else :?>
				name = '<?php echo $author;?>';
				addFounder(name, id, href);
			<?php endif;?>
		<?php endforeach;?>	
	<?php endif;?>
  
  function removeSubmit()
  {
   $('buttons-wrapper').hide();
  }
  });
</script>

<div class='global_form'>
  <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
  // Populate data
  var maxRecipients = 0;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;
  
   //founder
	<?php if(!empty($this -> posts['toValues'])):?>
		$('toValues').set('value', "");
		<?php $founders = explode(",", $this -> posts['toValues']); ?>
		<?php foreach($founders as $founder) :?>
			var id = null;
			var href = null;
			var name = null;
			<?php $user = Engine_Api::_() -> getItem('user', $founder); ?>
			<?php if($user -> getIdentity() > 0) :?>
				name =  '<?php echo $user -> getTitle();?>';
				id = '<?php echo $founder;?>';
				href = '<?php echo $user -> getHref() ?>';
				addFounder(name, id, href);
			<?php else :?>
				name = '<?php echo $founder;?>';
				addFounder(name, id, href);
			<?php endif;?>
		<?php endforeach;?>	
	<?php endif;?>
  
  function addFounder(name, id , href )
	{
		  //set value to hidden field
		  var hiddenInputField = document.getElementById('toValues');
		  var previousToValues = hiddenInputField.value;
		  if(!id)
		  {
			  if (checkSpanExists(name, name)){
				if (previousToValues==''){
				  document.getElementById('toValues').value = name;
				}
				else {
				  document.getElementById('toValues').value = previousToValues+","+name;
				}
			  }
			  if (checkSpanExists(name, name)){
				 //create block
				 var myElement = new Element("span");
				 myElement.id = "tospan_"+name+"_"+name;;
				 
				 myElement.innerHTML = name+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+name+"\");'>x</a>";
				 document.getElementById('toValues-wrapper').style.height= 'auto';
		
				 myElement.addClass("tag");
		
				 document.getElementById('toValues-element').appendChild(myElement);
				 this.fireEvent('push');
				 $('to').set('value', "");
				 
			}
		}
		else
		{
			if (checkSpanExists(name, id)){
				if (previousToValues==''){
				  document.getElementById('toValues').value = id;
				}
				else {
				  document.getElementById('toValues').value = previousToValues+","+id;
				}
			  }
			  if (checkSpanExists(name, id)){
				 //create block
				 var myElement = new Element("span");
				 myElement.id = "tospan_"+name+"_"+id;;
				 myElement.innerHTML = "<a target='_blank' href="+href+">"+name+"</a>"+" <a href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\""+id+"\");'>x</a>";
				 document.getElementById('toValues-wrapper').style.height= 'auto';
		
				 myElement.addClass("tag");
		
				 document.getElementById('toValues-element').appendChild(myElement);
				 this.fireEvent('push');
				 $('to').set('value', "");
				 
			}
		}
	}
  
   function checkSpanExists(name, toID){
	      var span_id = "tospan_"+name+"_"+toID;
	      if ($(span_id)){
	        return false;
	      }
	      else return true;
    } 
  
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
    var toValues = document.getElementById('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if (document.getElementById('toValues').value==""){
      document.getElementById('toValues-wrapper').style.height = '0';
    }
	document.getElementById('to').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    if( !isPopulated ) { // NOT POPULATED
      new Autocompleter2.Request.JSON('to', '<?php echo $this->url(array('controller' => 'index', 'action' => 'author-suggest'), 'ynfeedback_general', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType'  : 'message',
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
          if( (maxRecipients != 0) && (document.getElementById('toValues').value.split(',').length >= maxRecipients) ){
            document.getElementById('to').style.display = 'none';
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

<?php
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');
?>

<?php endif; ?>
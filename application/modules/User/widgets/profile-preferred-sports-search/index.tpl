<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/AutocompleterExtend.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Request.js');
?>
<div class="tf_club_search">
    <div id = "show_result_sport" style="display: none; color: red"><?php echo $this -> translate("Your changes have been saved.")?></div>
    <input type="text" name="sport" id="sport" value="" autocomplete="off" placeholder="<?php echo $this -> translate("Sport...") ?>">
    <div id="sport_ids-wrapper" class="form-wrapper">
    	<div id="sport_ids-element" class="form-element">
    		<input type="hidden" name="sport_ids" value="" id="sport_ids">
    	</div>
    </div>

    <button id="preferred-sports-save-btn"><?php echo $this -> translate('Save');?></button>
</div>
<script type="text/javascript">
	 function removeToValue(id, toValueArray, hideLoc){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        document.getElementById(hideLoc).value = toValueArray.join();
     }
	
	 function sportRemoveFromToValue(id) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = document.getElementById('sport_ids').value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.toString().search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(recipientsArray[i], toValueArray, 'sport_ids');
            }
        }
        else{
            removeToValue(id, toValueArray, 'sport_ids');
        }

        // hide the wrapper for usernames if it is empty
        if (document.getElementById('sport_ids').value==""){
            document.getElementById('sport_ids'+'-wrapper').style.height = '0';
            document.getElementById('sport_ids'+'-wrapper').hide();
        }

        document.getElementById('sport').style.display = 'block';
    }
    
    // Populate data
    var maxRecipients = <?php echo $this->max_sport?$this->max_sport:0?>;
    var to = {
        id : false,
        type : false,
        guid : false,
        title : false
    };
    
    window.addEvent('domready', function() {
    	//add event for submit button
    	$('preferred-sports-save-btn').addEvent('click', function (){
    		var ids = $('sport_ids').get('value');
    		(new Request.JSON({
                'format': 'json',
                'url' : '<?php echo $this->url(array('action' => 'save-preferred'), 'user_sport', true) ?>',
                'data' : {
                    'format' : 'json',
                    'ids' : ids,
                    'user_id': <?php echo $this -> subject() -> getIdentity();?>,
                },
                'onSuccess' : function(responseJSON, responseText)
                {
                	if(responseJSON[0].status == "true") 
                	{
                		$('show_result_sport').style.display = 'block';
                	}
                }
        	})).send();
    	});
    	
        //for owners autocomplete
        new Autocompleter2.Request.JSON('sport', '<?php echo $this->url(array('action' => 'suggest'), 'user_sport', true) ?>', {
            'toValues': 'sport_ids',
            'minLength': 1,
            'delay' : 250,
            'autocompleteType' : 'message',
            'multiple': true,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token)
            {
            	$('show_result_sport').style.display = 'none';
                if(token.type == 'sport')
                {
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
            },
            onPush : function(){
                if((maxRecipients != 0) && (document.getElementById('sport_ids').value.split(',').length >= maxRecipients) ){
                    document.getElementById('sport').style.display = 'none';
                }
            }
        });
        <?php foreach ($this->sports as $sport) : ?>
        var value = $('sport_ids').get('value');
        if(value.trim() == ""){
        	value += '<?php echo $sport->getIdentity()?>';
        } else {
        	value += ','+'<?php echo $sport->getIdentity()?>';
        }
        $('sport_ids').set('value', value);
        
        var myElement = new Element("span", {
            'id' : 'sport_ids_tospan_' + '<?php echo $sport->getIdentity()?>',
            'class': 'sport_tag',
            'html' :  '<?php echo $this->itemPhoto($sport, 'thumb.icon').$sport->getTitle().'<a class = "sport_preferred_remove" href="javascript:void(0);" onclick="this.parentNode.destroy();sportRemoveFromToValue('.$sport->getIdentity().');"><i class="fa fa-times"></i></a>';?>'
        });
        document.getElementById('sport_ids-element').appendChild(myElement);
        document.getElementById('sport_ids-wrapper').show();
        document.getElementById('sport_ids-wrapper').style.height = 'auto';
        <?php endforeach; ?>
        
        <?php if ($this->max_sport > 0 && count($this->sports) >= $this->max_sport) :?>
        document.getElementById('sport').style.display = 'none';
        <?php endif; ?>
    });
 </script>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/AutocompleterExtend.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Request.js');
?>
<?php echo $this->form->render($this) ?>

<div id="blockedUserList" style="display:none;">
  <ul>
  	<li>
  		<input type="text" name="user" id="user" value="" autocomplete="off" placeholder = "<?php echo $this -> translate("Type the name of a user...")?>">
  		<button id="block-users-save-btn"><?php echo $this -> translate('Block');?></button>
  		<div id="user_ids-wrapper" class="form-wrapper">
			<div id="user_ids-element" class="form-element">
				<input type="hidden" name="user_ids" value="" id="user_ids">
			</div>
		</div>
  	</li>
    <?php foreach ($this->blockedUsers as $user): ?>
      <?php if($user instanceof User_Model_User && $user->getIdentity()) :?>
        <li>[
          <?php echo $this->htmlLink(array('controller' => 'block', 'action' => 'remove', 'user_id' => $user->getIdentity()), 'Unblock', array('class'=>'smoothbox')) ?>
          ] <?php echo $user->getTitle() ?></li>
      <?php endif;?>
    <?php endforeach; ?>
  </ul>
</div>

<script type="text/javascript">
<!--
window.addEvent('load', function(){
  $$('#blockedUserList ul')[0].inject($('blockList-element'));
});
// -->
 function removeToValue(id, toValueArray, hideLoc){
    for (var i = 0; i < toValueArray.length; i++){
        if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById(hideLoc).value = toValueArray.join();
 }
	
 function removeFromToValue(id, hideLoc, elem) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = document.getElementById(hideLoc).value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
        var recipientsArray = id.split(",");
        for (var i = 0; i < recipientsArray.length; i++){
            removeToValue(recipientsArray[i], toValueArray, hideLoc);
        }
    }
    else{
        removeToValue(id, toValueArray, hideLoc);
    }

    // hide the wrapper for usernames if it is empty
    if (document.getElementById(hideLoc).value==""){
        document.getElementById(hideLoc+'-wrapper').style.height = '0';
        document.getElementById(hideLoc+'-wrapper').hide();
    }

    document.getElementById(elem).style.display = 'inline-block';
}

// Populate data
var to = {
    id : false,
    type : false,
    guid : false,
    title : false
};

window.addEvent('domready', function() {
	//add event for submit button
	$('block-users-save-btn').addEvent('click', function ()
	{
		var ids = $('user_ids').get('value');
		(new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('action' => 'block-users'), 'user_general', true) ?>',
            'data' : {
                'format' : 'json',
                'ids' : ids,
                'user_id': <?php echo $this -> subject() -> getIdentity();?>,
            },
            'onSuccess' : function(responseJSON, responseText)
            {
            	//refresh
            	location.reload();
            }
    	})).send();
	});
	
    //for owners autocomplete
    new Autocompleter2.Request.JSON('user', '<?php echo $this->url(array('action' => 'suggest-user-block'), 'user_general', true) ?>', {
        'toValues': 'user_ids',
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
            if(token.type == 'user')
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
        }
    });
});
 </script>

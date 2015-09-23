<script>
	en4.core.runonce.add(function(){
		$('btnChoose').addEvent('click', function(){		
            check = (this.value == '0')?true:false;
            if(this.value == '0'){
                this.value = 1;               
            }                
            else{
                this.value = 0;               
            }                
			list = document.getElementsByName('users[]');
			for(var i= 0; i < list.length; i++){
				list[i].checked = check;						
			}			
		});
	});
</script>
<script>
function updateList(obj,type) 
{	
	if (type == 0) 
	{				           

		var element = document.getElementById('ynevent_group_invite');
		var content = element.innerHTML;
		element.innerHTML= "<img  src='application/modules/Ynevent/externals/images/loading.gif'></img>";
        var key = document.getElementById('ynevent_group_search').value;
		new Request.JSON({
			'method': 'get',
			'url' : '<?php echo $this->url(array('action' => 'ajax-groups'), 'event_member',true) ?>',
			'data' : {
				'format' : 'json',					
                'key' : key,
                'event_id' : <?php echo $this->event_id; ?>,
			},
			'onSuccess' : function(json) {

				element.innerHTML = "";
                
                var ul = new Element('UL', {
					'class':'invite-group-wrapper'
				});
							
				element.appendChild(ul);
		
				for(var i=0;i<json.total;i++)
				{
					var item  = json.rows[i];
			
					var li = document.createElement('li');
					var li = new Element('LI');
					var input = new Element('INPUT',{
						'type': 'checkbox',
						'name': 'users[]',
						'id': 'users-'+ item.group_id,
						'value': item.group_id
					});
					/*var img = new Element('IMG',{
						'src': item.photo,
                        'class':'thumb_icon item_photo_group'							
					});*/
                    
					var label = new Element('LABEL',{
						'html': item.title,							
					});
					
					li.appendChild(input);
                    li.innerHTML = li.innerHTML + item.photo;
					li.appendChild(label);		
                    ul.appendChild(li);				
				}
			}
		}).send();
	}
}
    
function checkSelect()
{    
    var flag = false;
    list = document.getElementsByName('users[]');
    for(var i= 0; i < list.length; i++){
       if(list[i].checked == true)
            flag =  true;						
	}	
    if(flag == false){
        alert("<?php echo $this->translate('Please choose at least a group to send invite!'); ?>");    
        return false;
    }
    else
        return true;
}
</script>
<?php
$onclick = 'parent.Smoothbox.close();';
$session = new Zend_Session_Namespace('mobile');
if ($session -> mobile)
{
	$onclick = 'history.go(-1); return false;';
} 
?>
<div class="global_form_popup">
    <div>
        <h3><?php echo $this->translate('Invite your groups');?></h3>
    	<p class="form-description"><?php echo $this->translate('Please choose the following groups you would like to invite to join this event.'); ?></p>
    	<br/>
        <input type="text" id="ynevent_group_search" name="ynevent_group_search" value="" onchange="updateList(this,0);" />
    </div>
    
</div>
<form class="global_form_popup" action="" method="post">	
	<div class="form-element" id="all-element">
	<?php if ($session -> mobile):?>
		<button value="0" type="button" id="btnChoose" name="btnChoose"><?php echo $this->translate('Choose All Groups');?></button>
	<?php endif;?>
	<?php	
	if(count($this->groups) <= 0):		
	?>
	<div id="buttons-wrapper" class="form-wrapper">
		<p class="form-description"><?php echo $this->translate('You have no groups you can invite.'); ?></p>
		<br/>
		<a onclick="<?php echo $onclick?>" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel');?></a>
	</div>	
	<?php
	else:
	?>
	<div class="form-element" id="users-element">        
		<input type="hidden" value="" name="users"/>        
        <div id="ynevent_group_invite">        
    		<ul class="invite-group-wrapper">
            <?php
                foreach($this->groups as $i => $g):
            ?>
    			<li>
    				<input type="checkbox" value="<?php echo $g->group_id; ?>" id="users-<?php echo $g->group_id; ?>" name="users[]">
    				<?php echo $this->itemPhoto($g,'thumb.icon'); ?>                
    				<label for="users-<?php echo ++$i;?>"><?php echo Engine_Api::_()->ynevent()->subPhrase($g->title,20); ?></label>
    			</li>
            <?php
                endforeach;
            ?>			
    		</ul>
        </div>
	</div>
    <br />
    <div id="buttons-wrapper" class="form-wrapper">
		<fieldset id="fieldset-buttons">
			<?php if (!$session -> mobile):?>
            	<button value="0" type="button" id="btnChoose" name="btnChoose"><?php echo $this->translate('Choose All Groups');?></button>
			<?php endif;?>
			<button onclick="return checkSelect();" type="submit" id="btnSubmit" name="btnSubmit"><?php echo $this->translate('Send Invites');?></button>
 			or <a onclick="<?php echo $onclick ?>" href="javascript:void(0);" type="button" id="cancel" name="cancel"><?php echo $this->translate('cancel');?></a>
            <input type="hidden" id="max_groups" value="" />
 		</fieldset>
 	</div>
	
	<?php endif; ?>
 	</div></div>
</form>
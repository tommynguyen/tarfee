<?php 
$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
if( null !== $viewRenderer && $viewRenderer->view instanceof Zend_View_Interface ) 
{
    $myview = $viewRenderer->view;	
}
$myview->headScript()  
       ->appendFile($myview->baseUrl() . '/application/modules/Contactimporter/externals/scripts/TabContent.js');
?>
<?php 
if ($myview->step == "get_contacts" || $myview->ers)
{?>  
 	<div style="display:none; margin-left:300px; text-align:center; background:url(application/modules/Contactimporter/externals/images/loading.gif) no-repeat;width:320px;height:320px; padding-top:180px;" id="loading">    
    	<div style="text-align:center; "><?php echo $myview->translate('Sending request ...');?></div>
	</div>
  
	<div id="import_form">          
	<script type="text/javascript">
		function sending_request()
		{
			$('import_form').style.display = 'none';
			$('loading').style.display = 'block';
		}
		function skipForm()
		{
			$('skip').style.display = "none";
			$('SkipForm').submit();	
		}
	  	function finishForm()
	  	{
			document.getElementById("nextStep").value = "finish";
	  	}
	</script>
    <?php if($myview -> facebookAPI):?>
		<script type="text/javascript">
			window.fbAsyncInit = function() 
			{
				FB.init({
					appId : '<?php echo $myview -> facebookAPI?>',
					xfbml : true,
					version : 'v2.0'
				});
				open_facebook_invite_dialog();
			}; 
			
			(function(d, s, id) 
			{
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {
					return;
				}
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}
			(document, 'script', 'facebook-jssdk'));
		
			function open_facebook_invite_dialog() {
				if ( typeof FB == 'undefined')
					return;
			}
		
			function invite_facebook_open() {
				FB.ui({
					method : 'apprequests',
					message : '<?php echo $myview ->default_message?>',
					data: <?php echo $myview -> viewer() -> getIdentity()?>
				}, function(res) 
				{	
					if(res.to)
					{
						//save user friend
						Smoothbox.open(en4.core.baseUrl + 'contactimporter/fb-invite-successfull/total_send/'+ res.to.length);
						new Request.JSON({
					      'url' : en4.core.baseUrl + 'contactimporter/fb-save-invitations',
					      'data' : {
					        'format' : 'json',
					        'ids': res.to
					        
					      },
					      onSuccess : function(response) 
					      {
					      }
					    }).send();
					}
				});
		
			}
		</script>
	<?php endif;?>
	<form id="SkipForm" method="post" action="<?php echo $myview->action ?>" style="display:none" >
		<input type="hidden" name="skip" value="skipForm" />
	</form>
	<form method="post" action="<?php echo $myview->action ?>" class="global_form" enctype="">
		<div >
            <div id="import_form_homepage">         
            	<h3><?php echo $myview->translate("Import Your Contacts"); ?></h3>
			    <div class="heading"></div>
			    <?php 
                    if ($myview->ers)
                    {
                        foreach($myview->ers as $key=>$value)
                        {
                            echo "<div><ul class='form-errors'><li><ul class='errors'><li>$value</li></ul></li></ul></div>";
                        }
                    }
	            ?>   
	            <div style='display:none' id="error_mail">
	                <ul class="form-errors"><li><ul class="errors"><li id='error_mail_content'></li></ul></li></ul>
	            </div> 
			    <?php foreach($myview->providers as $provider): 
			    if($provider -> name != 'facebook'):?>
					<div class="logoContact">
						<a class="smoothbox" title="<?php echo $provider->title?>" href="<?php echo $myview->url(array('provider'=>$provider->name, 'type' => $provider->requirement, 'signup'=> true),'contactimporter_popup') ?>">
							<img src='application/modules/Contactimporter/externals/images/<?php echo $provider -> logo?>.png'>
						</a>		
					</div>
				<?php elseif($myview -> facebookAPI): ?>
					<div class="logoContact">
						<a href="javascript: invite_facebook_open()" title="<?php echo $provider->title?>">
							<img src='application/modules/Contactimporter/externals/images/<?php echo $provider -> logo?>.png'>
						</a>		
				</div>
				<?php endif; endforeach;?>
				<div style="clear:both; width:100%; display:block; padding-bottom: 10px"></div>
				<button type="button" id="skip"  name="skip" onclick="skipForm();"><?php echo $myview->translate('Skip »');?></button>
			</div>
		</div>
	</form>        
     
<?php      
}   	 
else
{?>
<script type='text/javascript'>
    var totalContacts = <?php echo count($myview->contacts) ?>;
	var total_allow_select =  <?php echo $myview->max_invitation;?>;
	var contactPerPage = <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contactimporter.contactsPerPage', 30);?>;
	var invitation_selected = <?php echo $myview -> checked?>;
	var total_checked = <?php echo $myview -> checked?>;
	var page = 1;
	var service = <?php echo count($myview->provider);?>;
	function IsNumeric(sText)
	{
	   var ValidChars = "0123456789.";
	   var IsNumber=true;
	   var Char;
	   for (i = 0; i < sText.length && IsNumber == true; i++) 
		  { 
		  Char = sText.charAt(i); 
		  if (ValidChars.indexOf(Char) == -1) 
			 {
			 IsNumber = false;
			 }
		  }
	   return IsNumber;
   
   }
	function toggleAll(element) 
	{
		for(var id = (page - 1)*contactPerPage + 1; id <= page*contactPerPage; id++)
		{
			if(document.getElementById('check_'+ id))
			{
				if(element.checked == true && document.getElementById('check_'+ id).checked == false)
					total_checked ++;
				else if(document.getElementById('check_'+ id).checked == true)
					total_checked --;
				if (element.checked == true && total_checked > total_allow_select)
				{
					total_checked --;
					if(id == (page - 1)*contactPerPage + 1)
					{
						element.checked = false;
					}
					document.getElementById('count_contacts').innerHTML = total_checked;
					alert('You have reach of limit invitation.\nYou only can send '+total_allow_select + ' invitations.\nPlease contact admin to get more information.');
					return;
				}
				document.getElementById('check_'+ id).checked = element.checked;
				if(document.getElementById('row_'+id))
	            {
	                if(element.checked)
	                {
	                    document.getElementById('row_'+id).className='thTableSelectRow';
	                }
	                else
	                {
	                     if (id % 2 ==1)
	                    {
	                        document.getElementById('row_'+id).className='thTableOddRow';
	                    }
	                    else
	                    {
	                        document.getElementById('row_'+id).className='thTableEvenRow';
	                    }
	                }
	            }
	        }
		}
		if(total_checked < invitation_selected)
		{
			document.getElementById('count_contacts').innerHTML = invitation_selected;
		}
		else
		{
			document.getElementById('count_contacts').innerHTML = total_checked;
		}
	}
    
	function check_toggle(element_id,obj,isCheckBox) 
	{
	  var check_element = document.getElementById('check_'+element_id);
		if(isCheckBox)
			check_element.checked = !check_element.checked;
		if(check_element.checked == true)
		{
			obj.className = 'thTableSelectRow';
			total_checked ++;
		}
		else
		{
			if (element_id % 2 ==1)
			{
				obj.className='thTableOddRow';
			}
			else
			{
				obj.className='thTableEvenRow';
			}
			total_checked --;
		}
		document.getElementById('count_contacts').innerHTML = total_checked;
		if (total_checked > total_allow_select && check_element.checked)
		{
			alert('You have reach of limit invitation.\nYou only can send '+total_allow_select + ' invitations.\nPlease contact admin to get more information');
			check_element.checked = false;
			total_checked --;
			document.getElementById('count_contacts').innerHTML = total_checked;
			if (element_id % 2 ==1)
			{
				obj.className='thTableOddRow';
			}
			else
			{
				obj.className='thTableEvenRow';
			}
			return false;
		} 
	}
    function check_select()
    {
        var sIds = document.getElementById('friendIds').value;
    	var sNames = document.getElementById('friendNames').value;
        error_no_contact = "<?php echo $myview->translate('No contacts were selected.'); ?>";
        var limit_select = 0;
        for(id = 1; id <= totalContacts; id++)
        {       
            if (document.getElementById('check_'+id).checked)
            {
                limit_select ++; 
                sIds += document.getElementById('email_'+id).value + ',';
            	sNames += document.getElementById('name_'+id).value + ',';
            }           
        }
        if (limit_select > 0)
        {
           if(limit_select > total_allow_select)
            {
                if(confirm("You can send "+ total_allow_select +" invitations.\nYou have selected "+limit_select+" contacts.\nAre you sure want to continue ?"))
                {
                    document.getElementById('friendIds').value = sIds;
    				document.getElementById('friendNames').value = sNames;
                    sending_request();
                    return true;                    
                }
                else
                {
                    document.getElementById('checkallBox').checked = false;
                    toggleAll(document.getElementById('checkallBox'));
                    return false;
                }
            }
            else
            {
            	document.getElementById('friendIds').value = sIds;
    			document.getElementById('friendNames').value = sNames;
                sending_request();
                return true;            
            }
        }
        
        error_notify(error_no_contact);
        return false;
    }
    function error_notify(error)
    {
        $("error_content").innerHTML = error;
        $("error").style.display = '';
    }
    function skipSendInvite()
	{
		$('submit_skip').style.display = "none";
		$('skip').submit();	
	}
	function sending_request()
    {
        $('openinviter').style.display = 'none';
        $('loading').style.display = 'block';
    }
</script>
<?php
	if ($myview->step =="add")
	{
?>	
<div>
	<div>
		
		<h3><?php echo $myview->translate('Your Contacts');?></h3>
		<p class="description"><?php echo $myview->translate("The following people are not your friends yet, please select to add connections.");?></p>
		<br />
		<div style='display:none' id="error">
			<ul class="form-errors"><li><ul class="errors"><li id='error_content'></li></ul></li></ul>
		</div>
		<div class="contactimporter_contactlist">
			<?php if(count($myview->contacts) > 0):?>
			<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style="width: 100%;border-left:2px solid #EDEDED;border-right:2px solid #EDEDED;">
				<tr style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#EDEDED none repeat scroll 0 50%;border-bottom:1px solid #C0C0C0;margin:0px auto 0;font-weight:bold;clear:both;width:80%'>
					<td style="width: 2.5%">&nbsp;</td>
					<td style="width: 9%"><input id='checkallBox' type='checkbox' onclick='toggleAll(this)' name='toggle_all' title='Select/Deselect all'></td>
					<td style="width: 50%"><?php echo $myview->translate('Name') ?></td>
					<?php if($myview->plugType == 'email'): ?>
						<td><?php echo $myview->translate('E-mail')?></td>
					<?php else: ?>
						<td></td>
					<?php endif;?>
					</tr>
			</table>
			<?php endif;?>
			<div id = 'page_1'>
				<div style="max-height: 560px; overflow-x: hidden; overflow-y: auto; float: left; width: 100%; margin-bottom: 10px">
					<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFFF none repeat scroll 0 50%;border:1px solid #C0C0C0;overflow:auto;width:100%; padding-left:5px;'>
					<?php
					$contents = "";
					$counter = 0;
					$contacts = array();
					$temps = $myview->contacts;
					if($myview->plugType == 'email')
						{
							foreach ($temps as $email => $data)
							{
								if (!is_array($data))
								{
									if($data == "")
										$data = $email;
									$contacts[$email] = $data;
								}
							}
						}
						else {
							$contacts = $temps;
						}
					$total_contacts = 0;
					
					$total_contacts = count($contacts);
					$contact_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contactimporter.contactsPerPage', 30);
					$total_pages = ceil($total_contacts/$contact_per_page);
					$page = 1;
					
					if ($total_contacts == 0)
					{
						$contents.="<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='".($myview->plugType=='email'? "3":"2")."'>".$myview->translate('You do not have any contacts in your address book.')."</td></tr>";
					}
					else
					{
						if($myview->plugType != 'email')
						{
							uasort($contacts, 'compareOrder');
						}
						else 
						{
							uasort($contacts, 'compare');
						}
						$check_first_cha = "";
						foreach ($contacts as $email => $data)
						{
							if(strpos($email,"no-cache") > 0)
							{
								continue;
							}
							$counter++;
					        if (is_array($data))
					        {
					            if ($myview->show_photo)
					                $pic = "<img height='30px' src='{$data['pic']}'>";
					            else
					                $pic = "<img height='30px' src='application/modules/User/externals/images/nophoto_user_thumb_icon.png'>";;
					            $name = trim($data['name']);
					        }
					        else
					        {
					            $name = trim($data);
								if($name == "")
									$name = $email;
					            $pic = '';
					        }
							//check and add new page
							if($counter > $page*$contact_per_page)
							{
								$contents.="</table></div><span>"
										.$myview->translate("%1s-%2s of %3s Contacts",($page -1)*$contact_per_page + 1,$page*$contact_per_page > $total_contacts?$total_contacts:$page*$contact_per_page,$total_contacts)
										."</span></div>";
								$page ++;
								$contents.= "<div id = 'page_".$page."'>";
								$contents.="<div style='max-height: 560px; overflow-x: hidden; overflow-y: auto; float: left; width: 100%;margin-bottom: 10px'>";
								$contents.= "<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFFF none repeat scroll 0 50%;border:1px solid #C0C0C0;overflow:auto;width:100%; padding-left:5px;'>";
							}
							
							if(ucfirst(mb_substr($name, 0, 1,'UTF-8')) != $check_first_cha)
							{
								$contents .= '<tr class="letter"><td class="label">&nbsp;</td>
	                    			<td colspan="3" class="label">
	                    				<div style="padding-left:2px;">'. ucfirst(mb_substr($name, 0, 1,'UTF-8')).'</div></td>
	                			</tr>';
	                			$check_first_cha = ucfirst(mb_substr($name, 0, 1, 'UTF-8'));
							}
							if ($counter % 2)
							    $class =' thTableOddRow';
							else 
							    $class ='thTableEvenRow';
						
							$contents.="<tr class='{$class}'  id='row_{$counter}'  ><td style = 'width: 2.5%'>&nbsp;</td><td style = 'width: 9%'><input id='check_{$counter}' name='check_{$counter}' onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),false);' value='{$counter}' type='checkbox' class='thCheckbox'";
							$contents.="><input type='hidden' name='email_{$counter}' id='email_{$counter}' value='{$email}'><input type='hidden' name='name_{$counter}' id='name_{$counter}' value='{$name}'></td><td style = 'width: 50%' onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),true);'>{$name}</td>".($myview->plugType == 'email' ?"<td onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),true);'>&lt;{$email}&gt;</td>":"<td class = 'contactimporter_contact_image'>{$pic}</td>")."</tr>";
						}
					}
					if($counter == 0)
						$contents = "<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='".($myview->plugType=='email'? "3":"2")."'>".$myview->translate("You do not have any contacts in your address book.")."</td></tr>";
					else
						$contents.="<script type='text/javascript'>counter={$counter}</script>";
					echo $contents	;		
					?>
					</table>
				</div>
				<span>
					<?php if($total_contacts > 0)
							echo $myview->translate("%1s-%2s of %3s Contacts",($page - 1)*$contact_per_page + 1,$page*$contact_per_page > $total_contacts?$total_contacts:$page*$contact_per_page,$total_contacts)?>
				</span>
			</div>
		</div>
		<?php if($total_contacts > 0):?>
		<div id="pagination">
			<div class="pages" style="float:right; margin-top: -20px;">
				<?php if ($total_pages > 1): ?>
				  <ul class="paginationControl" id="contactimporter_page_list">
				  		<li style="display:none">
		                    <a href="javascript:;" id="0" rel="page_0" >
		                          <?php echo $myview->translate('&#171; Previous');?>
		                    </a>
		                </li>
				  		<li class="selected">
		                    <a href="javascript:;" id="1" rel="page_1">
		                          1
		                    </a>
		                </li>
				    <?php for($i = 2; $i <= $total_pages; $i ++):?>
				    	<li <?php if($i > 10) echo "style = 'display: none'"?>>
		                    <a href="javascript:;" id="<?php echo $i?>" rel="page_<?php echo $i?>" >
		                          <?php echo $i?>
		                    </a>
		                </li>
				    <?php endfor;?>
				    	<li>
		                    <a href="javascript:;" id="<?php echo $total_pages + 1?>" rel="page_<?php echo $total_pages + 1;?>">
		                          <?php echo $myview->translate('Next &#187;');?>
		                    </a>
		           	 	</li>
				  </ul>
				<?php endif; ?>
			</div>
		</div>
		<?php endif;?>
		<br /><br />
		<form method="post" action="<?php echo $myview->action ?>" class="global_form" name='openinviter' enctype="application/x-www-form-urlencoded" onsubmit="return check_select();">
		<table>
			<tr>
				<td style="padding-top: 10px">
					<input type="hidden" value="<?php echo $myview->invite_list?>" name="invite_list" />
					<input type="hidden" value="do_add" name="task" />
					<input type="hidden" value="<?php echo $myview->plugType ?>" name="plugType" />
					<input type='hidden' name='oi_session_id' value='<?php echo $myview->oi_session_id ;?>'>
					<input type='hidden' name='provider' value='<?php echo $myview->provider ;?>'>
					<input type='hidden' name='friendIds' id = 'friendIds' value=''> 
					<input type='hidden' name='friendNames' id = 'friendNames' value= ''> 
					<button type='submit' id='submit' name='send' ><?php echo $myview->translate('Add Connection'); ?> (<span id = "count_contacts">0</span>)</button> 		
				</td>
				<td style="padding-top: 10px">
				&nbsp;&nbsp;<button type='button' id='submit_skip' name='skip' onclick="skipSendInvite();"><?php echo $myview->translate('Skip');?> &gt;&gt;</button>
				</td>
			</tr>
		</table>
	</form>
	<form  method="post" action="<?php echo $myview->action ?>" id = "skip"> 
		<input type="hidden" value="<?php echo $myview->invite_list?>" name="invite_list" />
		<input type="hidden" value="<?php echo $myview->plugType ?>" name="plugType" />
		<input type='hidden' name='oi_session_id' value='<?php echo $myview->oi_session_id ;?>'>
		<input type='hidden' name='provider' value='<?php echo $myview->provider;?>'>
		<input type="hidden" value="skip_add" name="task" />
	</form>
</div>
</div>

<?php			 
	}	 
	elseif($myview->step =="invite")
	{
?>
<div>		
	<div>
	<form method="post" id="search_contacts">
		<input type="hidden" name="openId" value ="<?php echo $myview->provider?>"/>
		<input type="hidden" value="1" name="page_id" id = 'page_id' />
		<input type='hidden' name='page_friendIds' id = 'page_friendIds' value=''>
		<input type='hidden' name='page_friendNames' id = 'page_friendNames' value=''>
		<input type='hidden' name='task' value ='get_contacts'>
	</form>
	<div style="display:none; margin-left:300px; text-align:center; background:url(application/modules/Contactimporter/externals/images/loading.gif) no-repeat;width:223px;height:30px; margin-top:50px;" id="loading">    
    	<div style="text-align:center; "><?php echo $this->translate('Sending request ...');?></div>
	</div>
			<div id="openinviter" style="width: 70%">
				<div>
					<h3><?php echo $myview->translate("Your Contacts")?></h3>
					<p class="description"><?php echo $myview->translate("The following people have not joined yet, please select to send invitations.");?></p>
					<p class="description"><?php echo $myview->translate("*You can send ");?><?php echo $myview->max_invitation;?><?php echo $myview->translate(' invitations');?></p>
					<br />
					<div style='display:none' id="error">
						<ul class="form-errors"><li><ul class="errors"><li id='error_content'></li></ul></li></ul>
					</div>
					<div class="contactimporter_contactlist">
					<?php
					if(count($myview->contacts) > 0):?>
					<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style="width: 100%;border-left:2px solid #EDEDED;border-right:2px solid #EDEDED;">
						<tr style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#EDEDED none repeat scroll 0 50%;border-bottom:1px solid #C0C0C0;margin:0px auto 0;font-weight:bold;clear:both;width:80%'>
							<td style="width: 2.5%">&nbsp;</td>
							<td style="width: 9%"><input id='checkallBox' type='checkbox' onclick='toggleAll(this)' name='toggle_all' title='Select/Deselect all'></td>
							<td style="width: 50%"><?php echo $myview->translate('Name') ?></td>
							<?php if($myview->plugType == 'email'): ?>
								<td><?php echo $myview->translate('E-mail')?></td>
							<?php else: ?>
								<td></td>
							<?php endif;?>
							</tr>
					</table>
					<?php endif;?>
					<div id = 'page_1'>
						<div style="max-height: 560px; overflow-x: hidden; overflow-y: auto; float: left; width: 100%;margin-bottom: 10px">
							<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFFF none repeat scroll 0 50%;border:1px solid #C0C0C0;overflow:auto;width:100%; padding-left:5px;'>
							<?php
							$contents = "";
							$counter = 0;
							$contacts = array();
							$arr_Friends = explode(',', $_SESSION['ynfriends_checked']['page_friendIds']);
							$temps = $myview->contacts;
							if($myview->plugType == 'email')
							{
								foreach ($temps as $email => $data)
								{
									if (!is_array($data))
									{
										if($data == "")
											$data = $email;
										$contacts[$email] = $data;
									}
								}
							}
							else {
								$contacts = $temps;
							}
							$total_contacts = count($contacts);
							
							$contact_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contactimporter.contactsPerPage', 30);
							$total_pages = ceil($total_contacts/$contact_per_page);
							$page = $myview -> page;
							
							if ($total_contacts == 0)
							{
								$contents.="<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='".($myview->plugType=='email'? "3":"2")."'>".$myview->translate('You do not have any contacts in your address book.')."</td></tr>";
							}
							else
							{
								if($myview->plugType != 'email')
								{
									uasort($contacts, 'compareOrder');
								}
								else 
								{
									uasort($contacts, 'compare');
								}
								$check_first_cha = "";
								foreach ($contacts as $email => $data)
								{
									if(strpos($email,"no-cache") > 0)
									{
										continue;
									}
									$counter++;
							        if (is_array($data))
							        {
							            if ($myview->show_photo)
							                $pic = "<img height='30px' src='{$data['pic']}'>";
							            else
							                $pic = "<img height='30px' src='application/modules/User/externals/images/nophoto_user_thumb_icon.png'>";;
							            $name = trim($data['name']);
							        }
							        else
							        {
							            $name = trim($data);
										if($name == "")
											$name = $email;
							            $pic = '';
							        }
									//check and add new page
									if($counter > $page*$contact_per_page)
									{
										$contents.="</table></div><span class='contactimporter_total_page'>"
												.$myview->translate("%1s-%2s of %3s contacts",($page -1)*$contact_per_page + 1,$page*$contact_per_page > $total_contacts?$total_contacts:$page*$contact_per_page,$total_contacts)
												."</span></div>";
										$page ++;
										$contents.= "<div id = 'page_".$page."'>";
										$contents.="<div style='max-height: 560px; overflow-x: hidden; overflow-y: auto; float: left; width: 100%;margin-bottom: 10px'>";
										$contents.= "<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#FFFFFF none repeat scroll 0 50%;border:1px solid #C0C0C0;overflow:auto;width:100%; padding-left:5px;'>";
									}
									
									if(ucfirst(mb_substr($name, 0, 1,'UTF-8')) != $check_first_cha)
									{
										$contents .= '<tr class="letter"><td class="label">&nbsp;</td>
			                    			<td colspan="3" class="label">
			                    				<div style="padding-left:2px;">'. ucfirst(mb_substr($name, 0, 1,'UTF-8')).'</div></td>
			                			</tr>';
			                			$check_first_cha = ucfirst(mb_substr($name, 0, 1, 'UTF-8'));
									}
									if (in_array($email, $arr_Friends))
									    $class='thTableSelectRow';
									elseif ($counter % 2)
									    $class =' thTableOddRow';
									else 
									    $class ='thTableEvenRow';
								
									$contents.="<tr class='{$class}'  id='row_{$counter}'  ><td style = 'width: 2.5%'>&nbsp;</td><td style = 'width: 9%'><input id='check_{$counter}' name='check_{$counter}' onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),false);' value='{$counter}' type='checkbox' class='thCheckbox'";
									if (in_array($email, $arr_Friends)) $contents.=" checked ";
									$contents.="><input type='hidden' name='email_{$counter}' id='email_{$counter}' value='{$email}'><input type='hidden' name='name_{$counter}' id='name_{$counter}' value='{$name}'></td><td style = 'width: 50%' onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),true);'>{$name}</td>".($myview->plugType == 'email' ?"<td onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),true);'>&lt;{$email}&gt;</td>":"<td class = 'contactimporter_contact_image'>{$pic}</td>")."</tr>";
								}
							}
							if($counter == 0)
								$contents = "<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='".($myview->plugType=='email'? "3":"2")."'>".$myview->translate("You do not have any contacts in your address book.")."</td></tr>";
							else
								$contents.="<script type='text/javascript'>counter={$counter}</script>";
							echo $contents	;		
							?>
							</table>
						</div> 
						<?php if($total_contacts > 0): ?>
							<span class="contactimporter_total_page">
								<?php echo $myview->translate("%1s-%2s of %3s contacts",($page - 1)*$contact_per_page + 1,$page*$contact_per_page > $total_contacts?$total_contacts:$page*$contact_per_page,$total_contacts)?>
							</span>
						<?php endif;?>
					</div>
				</div>
				<?php if($total_contacts > 0):?>
				<div id="pagination">
					<div class="pages" style="float:right; margin-top: -20px;">
						<?php if ($total_pages > 1): ?>
						  <ul class="paginationControl" id="contactimporter_page_list">
						  		<li style="display:none">
				                    <a href="javascript:;" id="0" rel="page_0" >
				                          <?php echo $myview->translate('&#171; Previous');?>
				                    </a>
				                </li>
						  		<li class="selected">
				                    <a href="javascript:;" id="1" rel="page_1">
				                          1
				                    </a>
				                </li>
						    <?php for($i = 2; $i <= $total_pages; $i ++):?>
						    	<li <?php if($i > 10) echo "style = 'display: none'"?>>
				                    <a href="javascript:;" id="<?php echo $i?>" rel="page_<?php echo $i?>" >
				                          <?php echo $i?>
				                    </a>
				                </li>
						    <?php endfor;?>
						    	<li>
				                    <a href="javascript:;" id="<?php echo $total_pages + 1?>" rel="page_<?php echo $total_pages + 1;?>">
				                          <?php echo $myview->translate('Next &#187;');?>
				                    </a>
				           	 	</li>
						  </ul>
						<?php endif; ?>
					</div>
				</div>
				<?php endif;?>
					<br /><br />
					<form method="post" action="<?php echo $myview->action ?>" class="global_form" name='openinviter' enctype="application/x-www-form-urlencoded" onsubmit="return check_select() ">
					<input type="hidden" value="do_invite" name="task" />
					<input type="hidden" value="<?php echo $myview->plugType ?>" name="plugType" />
					<input type='hidden' name='oi_session_id' value='<?php echo $myview->oi_session_id ;?>'>
					<input type='hidden' name='provider' value='<?php echo $myview->provider ;?>'>
					<input type='hidden' name='friendIds' id = 'friendIds' value='<?php echo $myview -> friendIds?>'> 
					<input type='hidden' name='friendNames' id = 'friendNames' value= '<?php echo $myview -> friendNames?>'> 
					<div class="form-wrapper" id="message-wrapper"><div class="form-label" id="message-label" style="width: 120px;text-align: left;"><label class="optional" for="message"><?php echo $myview->translate('Custom Message'); ?></label></div>
					<div class="form-element" id="message-element">
					<textarea rows="6" cols="45" id="message" name="message"><?php echo $myview->default_message; ?></textarea>
					<table>
						<tr>
							<td style="padding-top: 10px">
								<button type='submit' id='submit' name='send' ><?php echo $myview->translate('Send Invitations'); ?> (<span id = "count_contacts"><?php echo $myview -> checked?></span>)</button> 
							</td>
							<td style="padding-top: 10px">
							&nbsp;&nbsp;<button type='button' id='submit_skip' name='send' onclick="skipSendInvite();"><?php echo $myview->translate('Skip');?> &gt;&gt;</button>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form  method="post" action="<?php echo $myview->action ?>" id="skip"> 
			<input type="hidden" value="<?php echo $myview->plugType ?>" name="plugType" />
			<input type='hidden' name='oi_session_id' value='<?php echo $myview->oi_session_id ;?>'>
			<input type='hidden' name='provider' value='<?php echo $myview->provider ;?>'>
			<input type="hidden" value="skip_invite" name="task" />		
		</form>
	</div>
</div>
<?php		
	}
}     
?>
<?php
//define functions 
function compareOrder($a, $b)
{
	if($a['name'])
	{
		return ucfirst(trim($b['name'])) < ucfirst(trim($a['name']));
	}
} 
function compare($a, $b)
{
	return ucfirst(trim($a)) > ucfirst(trim($b));
}

?>
<script type="text/javascript">
	$$("#contactimporter_page_list a").addEvent("click", function(e)
	{
		var page_heads = document.getElementById('contactimporter_page_list').getElementsByTagName("a");
		var total = page_heads.length;
		page = this.id;
		if(this.id == 0)
		{
			page = page - 1;
		}
		if(this.id == total - 1)
		{
			page = page + 1;
		}
		$('checkallBox').checked = false;
	});
   var contactimporter_pages =new ddtabcontent("contactimporter_page_list");
   contactimporter_pages.setpersist(false);
   contactimporter_pages.setselectedClassTarget("link");
   contactimporter_pages.init(0);

</script>
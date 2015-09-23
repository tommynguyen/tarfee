<?php
$this->headScript()  
       ->appendFile($this->baseUrl() . '/application/modules/Contactimporter/externals/scripts/TabContent.js');
?>
<?php
$contact_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contactimporter.contactsPerPage', 30);
if (count($this->navigation)):
    ?>
    <div class="headline">
        <h2>
            <?php echo $this -> translate('Invite Your Friends'); ?>
        </h2>
        <div class="tabs">
            <?php
			// Render the menu
			echo $this -> navigation() -> menu() -> setContainer($this -> navigation) -> render();
            ?>
        </div>
    </div>
<?php endif; ?>

<script type='text/javascript'>
    var totalContacts = <?php echo count( $this->contacts) ?>;
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
   var total_allow_select =  <?php echo $this->max_invitation;?>;
	var contactPerPage = <?php echo $contact_per_page?>;
	var total_checked = 0;
	var page = 1;
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
		document.getElementById('count_contacts').innerHTML = total_checked;
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
        error_no_contact = "<?php echo $this->translate('No contacts were selected.'); ?>";
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
    function sending_request()
    {
        $('openinviter').style.display = 'none';
        $('loading').style.display = 'block';
        return false;
    }
    function skipSendInvite()
	{
		$('submit_skip').style.display = "none";
		$('skip').submit();	
	}
</script>
<div style="display:none; margin-left:300px; text-align:center; background:url(application/modules/Contactimporter/externals/images/loading.gif) no-repeat;width:223px;height:30px; margin-top:50px;" id="loading">    
    <div style="text-align:center; "><?php echo $this->translate('Sending request ...');?></div>
</div>
<div id="openinviter" style="width: 70%">
	<div>
		<h3><?php echo $this->translate('Your Contacts'); ?></h3>
		<p class="description"><?php echo $this->translate('The following people have not joined yet, please select to send invitations.');?></p>
		<p class="description"><?php echo $this->translate('*You can send ');?> <?php echo $this->max_invitation;?> <?php echo $this->translate(" invitations"); ?></p>
		<br />
		<div style='display:none' id="error">
		    <ul class="form-errors"><li><ul class="errors"><li id='error_content'></li></ul></li></ul>
		</div>
		<div class="contactimporter_contactlist">
			<?php if(count($this->contacts) > 0):?>
			<table class='thTable' align='left' cellspacing='0' cellpadding='5px' style="width: 100%;border-left:2px solid #EDEDED;border-right:2px solid #EDEDED;">
				<tr style='-moz-background-clip:border;-moz-background-inline-policy:continuous;-moz-background-origin:padding;background:#EDEDED none repeat scroll 0 50%;border-bottom:1px solid #C0C0C0;margin:0px auto 0;font-weight:bold;clear:both;width:80%'>
					<td style="width: 2.5%">&nbsp;</td>
					<td style="width: 9%"><input id='checkallBox' type='checkbox' onclick='toggleAll(this)' name='toggle_all' title='Select/Deselect all'></td>
					<td style="width: 50%"><?php echo $this->translate('Name') ?></td>
					<?php if($this->plugType == 'email'): ?>
						<td><?php echo $this->translate('E-mail')?></td>
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
					$temps = $this->contacts;
					if($this->plugType == 'email')
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
					$total_pages = ceil(count($contacts)/$contact_per_page);
					$page = 1;
					
					if ($total_contacts == 0)
					{
						$contents.="<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='".($this->plugType=='email'? "3":"2")."'>You do not have any contacts in your address book.</td></tr>";
					}
					else
					{
						if($this->plugType != 'email')
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
					            if ($this->show_photo)
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
										.$this->translate("%1s-%2s of %3s Contacts",($page -1)*$contact_per_page + 1,$page*$contact_per_page > $total_contacts?$total_contacts:$page*$contact_per_page,$total_contacts)
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
							$contents.="><input type='hidden' name='email_{$counter}' id='email_{$counter}' value='{$email}'><input type='hidden' name='name_{$counter}' id='name_{$counter}' value='{$name}'></td><td style = 'width: 50%' onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),true);'>{$name}</td>".($this->plugType == 'email' ?"<td onclick='check_toggle({$counter},document.getElementById(\"row_{$counter}\"),true);'>&lt;{$email}&gt;</td>":"<td class = 'contactimporter_contact_image'>{$pic}</td>")."</tr>";
						}
					}
					if($counter == 0)
						$contents = "<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='".($this->plugType=='email'? "3":"2")."'>".$this->translate("You do not have any contacts in your address book.")."</td></tr>";
					else
						$contents.="<script type='text/javascript'>counter={$counter}</script>";
					echo $contents	;		
					?>
					</table>
				</div>
				<span class='contactimporter_total_page'>
					<?php echo $this->translate("%1s-%2s of %3s Contacts",($page - 1)*$contact_per_page + 1,$page*$contact_per_page > $total_contacts?$total_contacts:$page*$contact_per_page,$total_contacts)?>
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
		                          <?php echo $this->translate('&#171; Previous');?>
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
		                          <?php echo $this->translate('Next &#187;');?>
		                    </a>
		           	 	</li>
				  </ul>
				<?php endif; ?>
			</div>
		</div>
		<?php endif;?>
		<br /><br />
		<form method="post" action="<?php echo $this->url(array('module' => 'contactimporter', 'controller' => 'index','action'=>'invite'), 'default', true);?>" class="global_form" name='openinviter' enctype="application/x-www-form-urlencoded" onsubmit="return check_select() ">
		<div class="form-wrapper" id="message-wrapper"><div class="form-label" id="message-label" style="width: 120px;text-align: left;"><label class="optional" for="message"><?php echo $this->translate("Custom Message"); ?></label></div>
		<div class="form-element" id="message-element">
		<textarea rows="6" cols="45" id="message" name="message"><?php echo $this->default_message; ?></textarea>
		
		<table><tr><td>
		<input type="hidden" value="do_add" name="task" />
		<input type="hidden" value="<?php echo $this->plugType ?>" name="plugType" />
		<input type='hidden' name='oi_session_id' value='<?php echo $this->oi_session_id ;?>'>
		<input type='hidden' name='provider_box' value='<?php echo $this->provider_box ;?>'>
		<input type="hidden" name="openId" value ="<?php echo $this->openId?>"/>
		<input type='hidden' name='friendIds' id = 'friendIds' value=''> 
		<input type='hidden' name='friendNames' id = 'friendNames' value= ''> 
		<button type='submit' id='submit' name='send' ><?php echo $this->translate('Send Invitations'); ?> (<span id = "count_contacts">0</span>)</button> 
		</td>
		<td> 
		&nbsp;&nbsp;<button type='button' id='submit_skip' name='send' onclick="skipSendInvite()"><?php echo $this->translate("Skip")?> &gt;&gt;</button>
		</td></tr></table>
		 </form>       
		<form  method="post" action="<?php echo $this->url(array('module' => 'contactimporter', 'controller' => 'index','action'=>'invite'), 'default', true);?>" id="skip"> 
		
		<input type="hidden" value="<?php echo $this->plugType ?>" name="plugType" />
		<input type='hidden' name='oi_session_id' value='<?php echo $this->oi_session_id ;?>'>
		<input type='hidden' name='provider_box' value='<?php echo $this->provider_box ;?>'>
		<input type="hidden" value="skip" name="task" />
		
		</form>
	</div>
</div>
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
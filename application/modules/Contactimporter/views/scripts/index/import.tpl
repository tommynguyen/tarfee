<?php 
if (($this->step != "get_invite" && $this->step != "add_contact") || (($this->ers))) 
{?>  
    <div id="loading">    
        <div>
        	<?php echo $this -> translate('Sending request ...'); ?>
        </div>
    </div>  
    <div id="import_form">
		<?php
		if ($this -> ers)
		{
			foreach ($this->ers as $key => $value)
			{
				echo "<div><ul class='form-errors'><li><ul class='errors'><li>" . $this -> translate($value) . "</li></ul></li></ul></div>";
			}
		}
		?>   
		<div class="provider_list">
			<?php foreach($this->providers as $provider): 
			if($provider -> name != 'facebook'):?>
				<div>
					<a class="usingapi" title="<?php echo $provider->title?>" href="javascript:void();" onclick="openPopup('<?php echo $this->url(array('provider'=>$provider->name, 'type' => $provider->requirement),'contactimporter_popup') ?>')">
						<img src='application/modules/Contactimporter/externals/images/<?php echo $provider -> logo?>.png'>
						<span class="title"><?php echo $provider->title?></span>
					</a>		
				</div>
			<?php elseif($this -> facebookAPI): ?>
				<div>
					<a class="usingapi" href="javascript: invite_facebook_open()" title="<?php echo $provider->title?>">
						<img src='application/modules/Contactimporter/externals/images/<?php echo $provider -> logo?>.png'>
						<span class="title"><?php echo $provider->title?></span>
					</a>		
				</div>
			<?php endif; endforeach;?>
			<?php if($this -> viewer() -> isAdmin()):?>
			<div>
				<a class="usingapi" title="<?php echo $this->translate("Upload CSV/VCF file")?>" href="<?php echo $this->url(array(),'contactimporter_upload')?>">
					<img src='application/modules/Contactimporter/externals/images/csv.png'>
					<span class="title"><?php echo $this->translate("Upload CSV/VCF file")?></span>
				</a>		
			</div>
			<?php endif;?>
			<div>
					<a class="usingapi" title="<?php echo $this->translate("Invite by manually typing emails")?>" href="<?php echo $this -> url(array('module' => 'invite'), 'default', true); ?>">
						<img src='application/modules/Contactimporter/externals/images/manual.png'>
						<span class="title"><?php echo $this->translate("Invite by manually typing emails")?></span>
					</a>		
			</div>
		</div>
		<div>
			<h4><?php echo $this->translate("Send a Custom Invitation Link")?></h4>
			<?php echo $this->translate("Send friends your custom invitation link by copy and pasting it into your own email application. When your friend joins:")?>
			<input readonly="readonly" type="text" name="null" value="<?php echo $this->invite_link?>" id="js_custom_link" size="40" style="width:65%;" onclick="this.select();" onkeypress="return false;">
		</div>
    </div>     
    <script type="text/javascript">   
    	function openPopup(url)
	    {	    	
	     	if(window.innerWidth <= 320)
	      	{
	       		Smoothbox.open(url, {autoResize : true, width: 300});
	      	}
	      	else
	      	{
	       		Smoothbox.open(url);
	      	}
	    }        
		function sending_request()
		{
			$('import_form').style.display = 'none';
			$('loading').style.display = 'block';
		}
    </script>
    <?php if($this -> facebookAPI):?>
		<script type="text/javascript">
			window.fbAsyncInit = function() 
			{
				FB.init({
					appId : '<?php echo $this -> facebookAPI?>',
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
					message : '<?php echo str_replace(array("\n","\r","\r\n"),'', $this ->default_message)?>',
					data: <?php echo $this -> viewer() -> getIdentity()?>
				}, function(res) 
				{	
					if(res.to)
					{
						//save user friend
						Smoothbox.open(en4.core.baseUrl + 'contactimporter/fb-invite-successfull/refresh/true/total_send/'+ res.to.length);
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
<?php
} 
elseif ($this->step == "add_contact") 
{
	echo $this->render('contactImports.tpl');
} 
elseif ($this->step == "get_invite")
{
	echo $this->render('invites.tpl');
}
?>
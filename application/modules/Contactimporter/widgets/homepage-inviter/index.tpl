<div id="import_form_homepage">
    <div class="heading"></div>
    
    <?php foreach($this->providers as $provider): 
	if($provider -> name != 'facebook'):
    	?>
		<div class="logoContact">
			<a class="smoothbox" title="<?php echo $provider->title?>" href="<?php echo $this->url(array('provider'=>$provider->name, 'type' => $provider->requirement),'contactimporter_popup') ?>">
				<img src='application/modules/Contactimporter/externals/images/<?php echo $provider -> logo?>.png'>
			</a>		
		</div>
	<?php elseif($this -> facebookAPI): ?>
			<div class="logoContact">
			<a href="javascript: invite_facebook_open()" title="<?php echo $provider->title?>">
				<img src='application/modules/Contactimporter/externals/images/<?php echo $provider -> logo?>.png'>
			</a>		
		</div>
	<?php endif; endforeach;?>
	<div style="clear:both;width:100%;display:block"></div>
	<span style="display:block; text-align: right; padding-right: 8px; margin-bottom: 15px;">
		<a alt="<?php echo $this->translate("View all of providers")?>" title="<?php echo $this->translate("View all of providers")?>" href="<?php echo $this->url(array(),"contactimporter")?>"><?php echo $this->translate("View More »"); ?></a>
	</span>
</div>
<div style="clear:both;width:100%;display:block"></div>
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
<h2><?php echo $this->translate("Libraries Settings") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='clear'>
    <div class='settings'>
    	<?php echo $this->form->render($this); ?>
    	
<?php 
	$settings = Engine_Api::_()->getApi('settings', 'core');
	$token = $settings->getSetting('user_youtube_token', "");
	$OAUTH2_CLIENT_ID = $settings->getSetting('user_youtube_clientid', "");
	$OAUTH2_CLIENT_SECRET = $settings->getSetting('user_youtube_secret', "");
	if(!empty($OAUTH2_CLIENT_ID) && !empty($OAUTH2_CLIENT_SECRET)):
?>    	
    	<?php
		
		// Call set_include_path() as needed to point to your client library.
		
		require_once 'Google/autoload.php';
		require_once 'Google/Client.php';
		require_once 'Google/Service/YouTube.php';
		session_start();
		
		/*
		 * You can acquire an OAuth 2.0 client ID and client secret from the
		 * Google Developers Console <https://console.developers.google.com/>
		 * For more information about using OAuth 2.0 to access Google APIs, please see:
		 * <https://developers.google.com/youtube/v3/guides/authentication>
		 * Please ensure that you have enabled the YouTube Data API for your project.
		 */
		
		$client = new Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$client->setAccessType('offline');
		$client->setScopes('https://www.googleapis.com/auth/youtube');
		$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
		    FILTER_SANITIZE_URL);
		$redirect = str_replace("index.php", "admin/user/libraries/token", $redirect);   
		$client->setRedirectUri($redirect);
		
    	if($token) {
    		$client->setAccessToken($token);
    		
    		if($client->isAccessTokenExpired()) {
	            $newToken = json_decode($client->getAccessToken());
	            $client->refreshToken($newToken->refresh_token);
	            $settings->setSetting('user_youtube_token', $client->getAccessToken());
       	 	}
    	}
    	
		if (isset($_GET['code'])) {
		  $client->authenticate($_GET['code']);
		  $settings->setSetting('user_youtube_token', $client->getAccessToken());
		  $token = $settings->getSetting('user_youtube_token', "");
		}
		
		?>
		
		
		<?php if (!$client->getAccessToken()):?>
		
			<?php 
			  // If the user hasn't authorized the app, initiate the OAuth flow
			  $state = mt_rand();
			  $client->setState($state);
			  $_SESSION['state'] = $state;
			   $authUrl = $client->createAuthUrl();
			?>
		  <h3>Authorization Required</h3>
		  <p>You need to <a href="<?php echo $authUrl;?>">authorize access</a> before proceeding.<p></p>
		  
		<?php else :?>
			<?php //echo $token;?>
			<div class="tip">
				<span>
				<?php echo $this -> translate('You are connecting to YouTube');?>
				</span>
			</div>
		<?php endif;?>
<?php endif;?>		
    </div>
</div>
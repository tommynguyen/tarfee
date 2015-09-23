<style>
.ynsc_sprite
{
	width: <?php echo $this->iconsize;?>px;
	height: <?php echo $this->iconsize;?>px;
	margin-top: <?php echo $this->margintop;?>px;
	margin-right: <?php echo $this->marginright;?>px;
	padding: 2px;
}
</style>
<?php
$background = rand(1, 3);
?>
<div class="tf_bgbody_landing" style="background-image: url(application/themes/ynresponsive-event/images/Bkgden_<?php echo $background?>.jpg)">
	<div class="tf_bgdot_landing"></div>
</div>
<div id="landing_popup" ></div>
<h1><img src="application/themes/ynresponsive-event/images/example_only.png" alt="tarfee . Spring 2015" class="brand"></h1>

<?php if( !$this->noForm ): ?>
  <?php echo $this->form->setAttrib('class', 'global_form_box')->render($this) ?>
	
  <?php if( !empty($this->fbUrl) ): ?>

    <script type="text/javascript">
      var openFbLogin = function() {
        Smoothbox.open('<?php echo $this->fbUrl ?>');
      }
      var redirectPostFbLogin = function() {
        window.location.href = window.location;
        Smoothbox.close();
      }
    </script>

  <?php endif; ?>

<?php else: ?>
  <h3 style="margin-bottom: 0px;">
    <?php echo $this->htmlLink(array('route' => 'user_login'), $this->translate('Sign In')) ?>
    <?php echo $this->translate('or') ?>
    <?php echo $this->htmlLink(array('route' => 'user_signup'), $this->translate('Join')) ?>
  </h3>
  <?php echo $this->form->setAttrib('class', 'global_form_box no_form')->render($this) ?>
<?php endif; ?>

<div class="ynadvmenu-popup">		
	<div class="ynadvmenu-overlay"></div>
	<div class="ynadvmenu-popup-content">
		<div class="ynadvmenu-popup-close"></div>

		<div class='advmenusystem_lightbox' id='user_form_default_sea_lightbox'>
		<div class="tarfee-popup-close"><i class="fa fa-times fa-lg"></i></div>
			<div id="user_register_form" class="ynadvmenu-user-signup-form">
				<?php echo $this->action("register", "signup", "user", array()) ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
	 var closeRegister = function() {
		landing_popup		= $('landing_popup'),
		main_html			= $$('html');
    	main_html.removeClass('ynadvmenu-html-fixed');
		landing_popup.removeClass('ynadvmenu-signup');
		landing_popup.getElements('.ynadvmenu-popup').destroy();
    }
	
	var openRegister = function()
	{
		var ynadvpopup_content 	= $$('.ynadvmenu-popup')[0],
		landing_popup		= $('landing_popup'),
		main_html			= $$('html');

		// reset popup
		landing_popup.removeClass('ynadvmenu-signup');
		landing_popup.getElements('.ynadvmenu-popup').destroy();
	
		// open popup
		main_html.addClass('ynadvmenu-html-fixed');
		landing_popup.addClass( 'ynadvmenu-signup');
		landing_popup.grab( ynadvpopup_content.clone() , 'top');
		$$('input[name=name]').getParent('.form-wrapper').hide();
	
		if ( window.getSize().y > landing_popup.getElement('.advmenusystem_lightbox').getSize().y ) {
			landing_popup.getElement('.advmenusystem_lightbox').setStyle('margin-top', (window.getSize().y-landing_popup.getElement('.advmenusystem_lightbox').getSize().y) / 2);
		}	
	
		// close popup
		landing_popup.getElement('.tarfee-popup-close').addEvent('click',function(){
			main_html.removeClass('ynadvmenu-html-fixed');
			landing_popup.removeClass('ynadvmenu-signup');
			landing_popup.getElements('.ynadvmenu-popup').destroy();
		});
	}
	 var toggleIt =  function(id2,id1)
	 {
			var e = document.getElementById(id2);
			var m = e.getAttribute('mode');
			function k(n){
				$$('.ld44').each(function(a,b){a.style.display=n;});
			}
			if(m =='close'){
				k('none');
				e.setAttribute('mode','open');
				e.innerHTML = '<img title = "<?php echo $this->translate('More')?>" src = "./application/modules/SocialConnect/externals/images/more.png" width = "26px" height = "26px"/>';
			}else{
				e.setAttribute('mode','close');
				e.innerHTML = '<img title = "<?php echo $this->translate('Hide')?>" src = "./application/modules/SocialConnect/externals/images/hide.png" width = "26px" height = "26px"/>';
				k('');
			}
	 }
</script>
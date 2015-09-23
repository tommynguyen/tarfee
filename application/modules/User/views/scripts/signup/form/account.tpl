<div class="tarfee-popup-close"><i class="fa fa-times fa-lg"></i></div>
<style>
#name-wrapper, h2 {
  display: none;
}

</style>

<script type="text/javascript">
//<![CDATA[
  window.addEvent('load', function() {
    if( $('username') && $('profile_address') && $('profile_address').length > 0) {
      $('profile_address').innerHTML = $('profile_address')
        .innerHTML
        .replace('<?php echo /*$this->translate(*/'yourname'/*)*/?>',
          '<span id="profile_address_text"><?php echo $this->translate('yourname') ?></span>');

      $('username').addEvent('keyup', function() {
        var text = '<?php echo $this->translate('yourname') ?>';
        if( this.value != '' ) {
          text = this.value;
        }
        
        $('profile_address_text').innerHTML = text.replace(/[^a-z0-9]/gi,'');
      });
      // trigger on page-load
      if( $('username').value.length ) {
          $('username').fireEvent('keyup');
      }
    }
  });
//]]>
</script>
<?php //echo $this->form->render($this) ?>
<script type="text/javascript">
	$$('.tarfee-popup-close').addEvent('click',function(){parent.Smoothbox.close()});	
</script>

<span id="global_content_simple">
    
<h2>
  </h2>

<script type="text/javascript">
  function skipForm() {
    document.getElementById("skip").value = "skipForm";
    $('SignupForm').submit();
  }
  function finishForm() {
    document.getElementById("nextStep").value = "finish";
  }
</script>

<div  style="padding-left:20px;">
  <div>
    <div>
		<div class="ybo_logo">
		<a href="/index.php/"><img src="public/admin/tarfee_logo.png" alt="navbar-brand"></a>	
		</div>
        <p>The invalid account. Please try again.</p>
		<div id="buttons-wrapper" class="form-wrapper">
			<div id="buttons-label" class="form-label">&nbsp;</div>
			<div id="buttons-element" class="form-element">
				<a href="/index.php/"><button>Back</button></a>
			</div>
		</div>
    </div>
  </div>
</div>
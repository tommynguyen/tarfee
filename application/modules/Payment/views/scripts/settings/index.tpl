<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('My Settings');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<?php if( $this->isAdmin ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Membership are not required for ' .
          'administrators and moderators.') ?>
    </span>
  </div>
<?php return; endif; ?>

<form method="get" action="<?php echo $this->escape($this->url(array('action' => 'confirm'))) ?>"
      class="global_form payment_form_settings" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
      <h3>
        <?php echo $this->translate('Membership') ?>
      </h3>
      <?php if( $this->currentPackage && $this->currentSubscription ): ?>
        <p class="form-description">
          <?php echo $this->translate('The plan you are currently subscribed ' .
              'to is: %1$s', '<strong>' .
              $this->translate($this->currentPackage->title) . '</strong>') ?>
          <br />
          <?php echo $this->translate('You are currently paying: %1$s',
              '<strong>' . $this->currentPackage->getPackageDescription()
              . '</strong>') ?>
        </p>
        <p style="padding-top: 15px; padding-bottom: 15px;">
          <?php echo $this->translate('If you would like to change your ' .
              'membership, please select an option below.') ?>
        </p>
      <?php else: ?>
        <p class="form-description">
          <?php echo $this->translate('You have not yet selected a ' .
              'membership plan. Please choose one now below.') ?>
        </p>
      <?php endif; ?>
      
      <!-- discount -->
      <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.referral_enable', 1)) :?>
      <div class="form-elements">
      	<div id="discount-wrapper" class="form-wrapper">
            <div id="discount-element" class="form-element">
              <div class="discount-container">
                <label class="package-label" >
                  <?php echo $this->translate("Discount Code") ?>
                </label>
                <p class="discount-description">
                  <input value="<?php echo (isset($_SESSION['ref_code']))? $_SESSION['ref_code']: " ";?>" type="text" name="discount" id="discount">
                  <i id="in_valid_code" title="<?php echo $this -> translate("Invalid Code");?>" style="float:right; color:red; display:none" class="fa fa-exclamation"></i>
                  <i id="valid_code"    title="<?php echo $this -> translate("Valid Code");?>"   style="float:right; color:green; display:none" class="fa fa-check"></i>
                </p>
              </div>
            </div>
          </div>
      </div>
      <?php endif;?>
      <!-- end discount -->
      
      <div class="form-elements">
        <?php $count = 0; ?>
        <?php foreach( $this->packages as $package ):
          $id = $package->package_id;
          $attribs = array('id' => 'package-' . $id, 'class' => 'package-select');
          if( $id == $this->currentPackage->package_id ) {
            continue;
            //$attribs['disabled'] = 'disabled';
          }
          $count++;
          ?>
          <div id="package-<?php echo $id ?>-wrapper" class="form-wrapper">
            <div id="package-<?php echo $id ?>-element" class="form-element">
              <?php echo $this->formSingleRadio('package_id', $package->package_id, $attribs) ?>
              <div class="package-container">
                <label class="package-label" for="package-<?php echo $id ?>">
                  <?php echo $this->translate($package->title) ?>
                  <?php echo $this->translate('(%1$s)', $package->getPackageDescription()) ?>
                </label>
                <p class="package-description">
                  <?php echo $this->translate($package->description) ?>
                </p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if( $count > 0 ): ?>
          <div id="execute-wrapper" class="form-wrapper">
            <div id="execute-element" class="form-element">
              <button type="submit" name="execute" onclick="var found = false; $$('input.package-select').each(function(el){ if( el.get('checked') ) { found = true; } }); return found; ">
                <?php echo $this->translate('Change Plan') ?>
              </button>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
	window.addEvent('domready', function(){
		if($('discount')) {
			$('discount').addEvent('change', function(){
				$('valid_code').setStyle("display", "none");
				$('in_valid_code').setStyle("display", "none");
				var code = this.get('value');
				var url = '<?php echo $this -> url(array('action' => 'check-code'), 'user_general', true) ?>';
				new Request.JSON({
			        url: url,
			        data: {
			            'code': code,
			        },
			        onSuccess : function(responseJSON, responseText)
			        {
			        	if(responseJSON.error == "0") {
			        		$('valid_code').setStyle("display", "block");
			        	} else {
			        		$('in_valid_code').setStyle("display", "block");
			        	}
			        }
			    }).send();
			});
		}
	});
</script>

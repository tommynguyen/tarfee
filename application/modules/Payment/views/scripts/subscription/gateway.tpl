<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: gateway.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php if( $this->status == 'pending' ): // Check for pending status ?>
  Your membership is pending payment. You will receive an email when the
  payment completes.
<?php else: ?>

  <form method="get" action="<?php echo $this->escape($this->url(array('action' => 'process'))) ?>"
        class="global_form" enctype="application/x-www-form-urlencoded">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Pay for Access') ?>
        </h3>
        <?php if( $this->package->recurrence ): ?>
        <p class="form-description">
          <?php echo $this->translate('You have selected an account type that requires ' .
            'recurring membership payments. You will be taken to a secure ' .
            'checkout area where you can setup your membership. Remember to ' .
            'continue back to our site after your purchase to sign in to your ' .
            'account.') ?>
        </p>
        <?php endif; ?>
        <p style="font-weight: bold; padding-top: 15px; padding-bottom: 15px;">
          <?php if( $this->package->recurrence ): ?>
            <?php echo $this->translate('Please setup your membership to continue:') ?>
          <?php else: ?>
            <?php echo $this->translate('Please pay a one-time fee to continue:') ?>
          <?php endif; ?>
          <?php echo $this->package->getPackageDescription() ?>
        </p>
        <div class="form-elements">
          <div id="buttons-wrapper" class="form-wrapper">
              <?php foreach( $this->gateways as $gatewayInfo ):
                $gateway = $gatewayInfo['gateway'];
                $plugin = $gatewayInfo['plugin'];
                $first = ( !isset($first) ? true : false );
                ?>
                <?php if( !$first ): ?>
                  <?php echo $this->translate('or') ?>
                <?php endif; ?>
                <button type="submit" name="execute" onclick="$('gateway_id').set('value', '<?php echo $gateway->gateway_id ?>')">
                  <?php echo $this->translate('Pay with %1$s', $this->translate($gateway->title)) ?>
                </button>
              <?php endforeach; ?>
              <?php if($this->package->trial_duration > 0):?>
              	 <?php
              	 	$trialPlanTable = Engine_Api::_() -> getDbTable('trialplans', 'user');
					$trialRow = $trialPlanTable -> getRow($this -> subscription -> user_id, $this -> subscription -> package_id);
              	 ?>
              	 <?php if(!isset($trialRow)) :?>
	               	<?php echo $this -> translate('or');?>
	                <button type="button" onclick="callTrial('<?php echo $this->subscription->subscription_id;?>')">
	                    <?php echo $this -> translate(array("Using trial with %s day", "Using trial with %s days", $this->package->trial_duration), $this->package->trial_duration);?>
	                </button>
	             <?php endif;?>   
          	  <?php endif;?>
          </div>
        </div>
      </div>
    </div>
    <input type="hidden" name="gateway_id" id="gateway_id" value="" />
  </form>

<?php endif; ?>

<script type="text/javascript">
	
	function createPopupNotice(text) {
		var div = new Element('div', {
		   'class': 'payment-confirm-popup' 
		});
		var p = new Element('p', {
			'class': 'payment-confirm-message',
			text: text,
		});
		div.grab(p);
		Smoothbox.open(div);
	}
	
	<?php if($this->package->trial_duration > 0):?>
		function callTrial(subscription_id) 
		{
			var url = '<?php echo $this -> url(array('action' => 'using-trial'), 'user_general', true) ?>';
			new Request.JSON({
		        url: url,
		        data: {
		            'subscription_id': subscription_id,
		        },
		    }).send();
		    createPopupNotice('<?php echo $this -> translate("You have chosen the trial plan. Please check your email for confirmation.");?>');
	    	setTimeout(function(){
				location.href='<?php echo $this -> url(array(), 'user_general', true);?>';
			}, 5000);
		}
	<?php endif;?>
</script>

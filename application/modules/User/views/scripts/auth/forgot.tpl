<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: forgot.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<div class="tarfee-popup-close"><i class="fa fa-times fa-lg"></i></div>
<?php if( empty($this->sent) ): ?>

  <?php echo $this->form->render($this) ?>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate("USER_VIEWS_SCRIPTS_AUTH_FORGOT_DESCRIPTION") ?>
    </span>
  </div>

<?php endif; ?>
<script type="text/javascript">
	$$('.tarfee-popup-close').addEvent('click',function(){parent.Smoothbox.close()});	
</script>
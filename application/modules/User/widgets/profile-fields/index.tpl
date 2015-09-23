<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php echo $this->fieldValueLoop($this->subject(), $this->fieldStructure) ?>

<?php $location = $this->subject()->getLocation(); ?>
<?php if (!empty($location)) :?>
<div class="profile_fields">
	<ul>
		<li>
			<span>
			<?php echo $this->translate('Location')?>		
			</span>
			<span>
			<?php echo implode(', ', $location)?>	
			</span>
		</li>
	</ul>
</div>
<?php endif;?>
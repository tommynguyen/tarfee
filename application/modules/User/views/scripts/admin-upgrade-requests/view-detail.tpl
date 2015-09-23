<h3><?php echo $this->translate('Request Detail')?></h3>
<div class="request-info">
	<div>
		<span class="label"><?php echo $this->translate('First Name')?></span>:
		<span class="value"><?php echo $this->req->first_name?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Last Name')?></span>:
		<span class="value"><?php echo $this->req->last_name?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Email')?></span>:
		<span class="value"><?php echo $this->req->email?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Country')?></span>:
		<?php if($this->req ->country_id && $country = Engine_Api::_() -> getItem('user_location', $this->req ->country_id)):?>
			<span class="value"><?php echo $country -> getTitle();?></span>
		<?php endif; ?>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Province/State')?></span>:
		<?php if($this->req ->province_id && $province = Engine_Api::_() -> getItem('user_location', $this->req ->province_id)):?>
			<span class="value"><?php echo $province -> getTitle();?></span>
		<?php endif; ?>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('City')?></span>:
		<?php if($this->req ->city_id && $city = Engine_Api::_() -> getItem('user_location', $this->req ->city_id)):?>
			<span class="value"><?php echo $city -> getTitle();?></span>
		<?php endif; ?>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Phone')?></span>:
		<span class="value"><?php echo $this->req->phone?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Membership')?></span>:
		<span class="value">
			<?php 
				$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
				$package = $packagesTable->fetchRow(array(
			      'enabled = ?' => 1,
			      'package_id = ?' => (int) $this->req -> package_id,
			    ));
			echo $package->getPackageDescription()?>
		</span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('About')?></span>:
		<span class="value"><?php echo $this->req->about?></span>
	</div>
</div>

<ul class="action">
	<li>
		<a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'upgrade-requests', 'action' => 'approve', 'id' => $this->req->getIdentity()), 'admin_default', true)?>"><?php echo $this->translate('Approve')?></a>
	</li>
	
	<li>
		<a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'upgrade-requests', 'action' => 'reject', 'id' => $this->req->getIdentity()), 'admin_default', true)?>"><?php echo $this->translate('Reject')?></a>
	</li>
	
	<li>
		<a href="javascript:void(0)" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></a>
	</li>
</ul>

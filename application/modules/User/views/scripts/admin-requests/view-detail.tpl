<h3><?php echo $this->translate('Request Detail')?></h3>
<div class="request-info">
	<div>
		<span class="label"><?php echo $this->translate('Email')?></span>
		<span class="value"><?php echo $this->req->email?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Name')?></span>
		<span class="value"><?php echo $this->req->name?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Phone')?></span>
		<span class="value"><?php echo $this->req->phone?></span>
	</div>
	
	<div>
		<span class="label"><?php echo $this->translate('Message')?></span>
		<span class="value"><?php echo $this->req->message?></span>
	</div>
</div>

<ul class="action">
	<li>
		<a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'requests', 'action' => 'approve', 'id' => $this->req->getIdentity()), 'admin_default', true)?>"><?php echo $this->translate('Approve')?></a>
	</li>
	
	<li>
		<a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'requests', 'action' => 'reject', 'id' => $this->req->getIdentity()), 'admin_default', true)?>"><?php echo $this->translate('Reject')?></a>
	</li>
	
	<li>
		<a href="javascript:void(0)" onclick="parent.Smoothbox.close()"><?php echo $this->translate('Close')?></a>
	</li>
</ul>

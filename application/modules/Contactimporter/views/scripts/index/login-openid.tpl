<form method="post" id="login_form" action="<?php echo $this->info['url']?>">
	<input type="hidden" name="provider" value="<?php echo $this->info['provider']?>"/>
	<input type="hidden" name = "email" value="<?php echo $this->info['email']?>"/>
	<input type="hidden" name = "password" value="<?php echo $this->info['password']?>"/>
	<input type="hidden" name="task" value="get_contacts" />
</form>
<script type="text/javascript">document.getElementById('login_form').submit();</script>
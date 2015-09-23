<h2>
  <?php echo $this->translate("Welcome to Tarfee, world's sport network.") ?>
</h2>

<p>
  <?php
  if( !($this->verified || $this->approved) ) {
    echo $this->translate("Welcome! A verification message has been sent to your email address with instructions on how to activate your account. Once you have clicked the link provided in the email and we have approved your account, you will be able to sign in.");
  } else if( !$this->verified ) {
    echo $this->translate("Welcome! A verification message has been sent to your email address with instructions for activating your account. Once you have activated your account, you will be able to sign in.");
  } else if( !$this->approved ) {
    echo $this->translate("Welcome! Once we have approved your account, you will be able to sign in.");
  }
  ?>
</p>

<h5>
  <a href="<?php echo $this -> url(array('controller' => 'edit', 'action' => 'profile', 'id' => $this -> viewer_id ), 'user_extended', true);?>"><?php echo $this->translate("Take me to my profile settings.") ?></a>
</h5>

<h5>
  <a href="<?php echo $this -> url(array(), 'default', true);?>"><?php echo $this->translate("Take me to Main Page, I will update my profile later.") ?></a>
</h5>
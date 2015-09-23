<h3>
    <?php echo $this->translate('Extra Stats');?>
  </h3>
<ul>

    <li>
        <?php  echo $this->translate('Notices:'); echo $this->notices;?>
    </li>
	<li>
        <?php echo $this->translate('Warnings: '); echo $this->warnings;?>
    </li>
	<li>
        <?php echo $this->translate('Infractions: '); echo $this->infractions;?>
    </li>
	<li>
        <?php echo $this->translate('Banned Usernames: '); echo $this->bannedUsernames;?>
    </li>
	<li>
        <?php echo $this->translate('Banned Emails: '); echo $this->bannedEmails;?>
    </li>
	<li>
        <?php echo $this->translate('Banned Ips: '); echo $this->bannedIps;?>
    </li>
</ul>
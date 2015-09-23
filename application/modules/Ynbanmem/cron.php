<?php

/**
 * @link
 * http://worcesterwideweb.com/2008/03/17/php-5-and-imagecreatefromjpeg-recoverable-error-premature-end-of-jpeg-file/
 */
ini_set('gd.jpeg_ignore_warning', 1);

ini_set('display_startup_errors', 0);
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL);

$application->getBootstrap()->bootstrap('translate');
$application->getBootstrap()->bootstrap('locale');
$view = Zend_Registry::get('Zend_View');

/**
 * following step to speed up & beat performance
 * 1. check album limit
 * 2. check quota limit
 * 3. get nodes of this schedulers
 * 4. get all items of current schedulers.
 * 5. process each node
 * 5.1 check required quota
 * 5.2 fetch data to pubic file
 * 5.3 store to file model
 * 6. check status of schedulers, if scheduler is completed == (remaining == 0)
 * 6.1 udpate feed and message.
 */
/**
 * Unlimited time.
 */
set_time_limit(0);

/**
 * default 20
 * @var int
 */
// Get data
$bannedUsernameTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
$bannedEmailTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
$bannedIpTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
//$extraInfoTable = Engine_Api::_()->getDbTable('extrainfo', 'ynbanmem');

$bannedUsernames = $bannedUsernameTable->getAllBannedUsers();
$bannedIps = $bannedIpTable->getAddresses();
$bannedEmails = $bannedEmailTable->getAllBannedEmails();
$now = strtotime(date('Y-m-d H:i:s'));
$iUsername = 0;
$iEmail = 0;
$iIp = 0;
if(count($bannedUsernames) != 0)
{
    foreach ($bannedUsernames as $bannedUsername)
    {
        if(count($bannedUsername['extra_info'][0]) != 0)
            if(strtotime($bannedUsername['extra_info'][0]['expiry_date']) <= $now)
			{
				$bannedUsernameTable->unbanUsername($bannedUsername['banned_id']);
				 $iUsername++;
			}
    }
}

if(count($bannedEmails) != 0)
{
    foreach ($bannedEmails as $bannedEmail)
    {
        if(count($bannedEmail['extra_info'][0]) != 0)
        {
		
            if(strtotime($bannedEmail['extra_info'][0]['expiry_date']) <= $now)
			{
			
				$bannedEmailTable->unbanEmail($bannedEmail['banned_id']);
				$iEmail++;
			}
			
                
        }
    }
}

if(count($bannedIps) != 0)
{
    foreach ($bannedIps as $bannedIp)
    {
        if(count($bannedIp['extra_info']) != 0)
            if(strtotime($bannedIp['extra_info'][0]['expiry_date']) >= $now)    
            {
				$bannedIpTable->unbanIp($bannedIp['banned_id']);
				$iIp++;
			}
    }
}
echo 'success. <br/>';
echo $iUsername. " user(s) have been unbanned! <br/>";
echo $iEmail. " Email(s) have been unbanned!<br/> ";
echo $iIp. " Ip(s) have been unbanned!<br/> ";
exit(0);

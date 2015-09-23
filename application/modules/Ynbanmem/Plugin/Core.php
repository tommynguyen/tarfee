<?php

class Ynbanmem_Plugin_Core {

	public function onUserLoginAfter($event) {

		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Save Ip
		if($viewer->getIdentity() > 0)
		{
			if (is_string($viewer -> lastlogin_ip)) {
				$ip = Engine_IP::normalizeAddress($viewer -> lastlogin_ip);
	
				$prefix = Engine_Api::_() -> getDbTable('ips', 'ynbanmem') -> getTablePrefix();
				$q = "			
				SELECT *
	  			FROM `{$prefix}ynbanmem_ips` WHERE user_id = $viewer->user_id and ip = '$ip';	
				";
	
				$results = Engine_Db_Table::getDefaultAdapter() -> fetchRow($q);
	
				if (!$results) 
				{	$date = date('Y-m-d H:i:s');
					$q = "			
					INSERT INTO `{$prefix}ynbanmem_ips` (user_id , ip, creation_date)
					VALUES ( $viewer->user_id , '$ip', '$date')	  			
					";
					Engine_Db_Table::getDefaultAdapter() -> query($q);
				}
			}
		}
		
	}

	public function onRenderLayoutDefault($event) {
		//echo 'banmer';die;
		 // Check if visitor is banned by IP
	    $addressObject = new Engine_IP();
	    $addressBinary = $addressObject->toBinary();
		// Load banned IPs
		$bannedIpTable = Engine_Api::_() -> getDbtable('bannedips', 'ynbanmem');
		$bannedIps = $bannedIpTable -> select() -> query() -> fetchAll();
		$bannedId;
		$isBanned = false;
		if(count($bannedIps) > 0)
		{						
			foreach ($bannedIps as $bannedIp) {
				// @todo ipv4->ipv6 transformations
				if (strlen($addressBinary) == strlen($bannedIp['start'])) {
					if (strcmp($addressBinary, $bannedIp['start']) >= 0 && strcmp($addressBinary, $bannedIp['stop']) <= 0) {
						$isBanned = true;
						$bannedId = $bannedIp['banedip_id'];
						break;
					}
				}
			}
	
			// tell them they're banned
			if ($isBanned) {
				$extraInfoTable = Engine_Api::_() -> getDbTable('extrainfo', 'ynbanmem');
				//Get extra info
				$extraInfo = $extraInfoTable -> getExtraInfo($bannedId, 1);
	
				//@todo give appropriate forbidden page
				if (!headers_sent()) {
					header('HTTP/1.0 403 Forbidden');
				}
				if (count($extraInfo) != 0)
					die($extraInfo[0]['reason']);
				die('banned');
			}
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if ($viewer->getIdentity() > 0 && $viewer -> username  != null && !$viewer->level_id != 1) 
		{
			
			// Load banned Usernames
			$bannedUsernameTable = Engine_Api::_() -> getDbtable('bannedusernames', 'ynbanmem');
			$bannedUsername = $bannedUsernameTable -> select() -> where('username = ?', $viewer -> username) -> query() -> fetchAll();
				

			// tell them they're banned
			if (count($bannedUsername) != 0) {
				
				$extraInfoTable = Engine_Api::_() -> getDbTable('extrainfo', 'ynbanmem');
				//Get extra info
				$extraInfo = $extraInfoTable -> getExtraInfo($bannedUsername[0]['bannedusername_id'], 0);

				//@todo give appropriate forbidden page
				if (!headers_sent()) {
					header('HTTP/1.0 403 Forbidden');
				}
				if (count($extraInfo) != 0)
					die('banned <br/>'.$extraInfo[0]['reason']);
				die('banned');
			}

			// Load banned emails
			$bannedEmailTable = Engine_Api::_() -> getDbtable('bannedemails', 'ynbanmem');
			$bannedEmail = $bannedEmailTable -> select() -> where('email = ?', $viewer -> email) -> query() -> fetchAll();
			//echo $viewer -> email;die;
			// tell them they're banned
			if (count($bannedEmail) != 0) {
				$extraInfoTable = Engine_Api::_() -> getDbTable('extrainfo', 'ynbanmem');
				//Get extra info
				$extraInfo = $extraInfoTable -> getExtraInfo($bannedEmail[0]['bannedemail_id'], 2);

				//@todo give appropriate forbidden page
				if (!headers_sent()) {
					header('HTTP/1.0 403 Forbidden');
				}
				if (count($extraInfo) != 0)
					die('banned <br/>'.$extraInfo[0]['reason']);
				//die('banned');
			}

		}

	}

}

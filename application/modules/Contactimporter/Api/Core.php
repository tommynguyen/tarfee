<?php
/**
 * Contact Importer Core API
 * YouNet company - 2010
 */
class Contactimporter_Api_Core extends Core_Api_Abstract
{
	/**
	 * Check Social Bridge Plugin
	 */
	public function checkSocialBridgePlugin()
	{
		$module = 'socialbridge';
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
		$module_result = $modulesTable -> fetchRow($mselect);
		if (count($module_result) > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Used to send invitation email
	 * Reference: SocialEngine Team: User/Form/Signup/Invite.php
	 *
	 */
	public function sendInvitationEmail(User_Model_User $user, $inviting_emails, $inviting_message)
	{
		$success = 1;
		$error_message = 'No error happenned';

		$settings = Engine_Api::_() -> getApi('settings', 'core');

		// Get the already invited contacts
		$invites_table = Engine_Api::_() -> getDbTable('invites', 'invite');

		$select = $invites_table -> select() -> from($invites_table -> info('name'), array('recipient AS email')) -> where('user_id = ?', $user -> getIdentity());

		$rows = $invites_table -> fetchAll($select) -> toArray();
		$invited = array();

		foreach ($rows as $row)
		{
			$invited[] = $row['email'];
		}

		$existing_members = $this -> getUserByEmail($inviting_emails);
		$mailIds = array();
		foreach ($inviting_emails as $recipient => $name)
		{
			// Only send invitation if the recipient wasn't invited
			if (!in_array($recipient, $invited))
			{
				// Preprocess recipient email address
				$recipient = trim($recipient);

				// Omit recipients who is a current member / invalid email address
				if (!$this -> validateEmail($recipient) || array_key_exists($recipient, $existing_members))
				{
					continue;
				}

				// Generate invitation code and make sure that it's unique
				do
				{
					$invitation_code = substr(md5(rand(0, 999) . $recipient), 10, 7);
					$select = $invites_table -> select() -> where('code = ?', $invitation_code);
					$check = $invites_table -> fetchRow($select);
				}
				while (null !== $check);

				// insert the invitation into database
				$db = Engine_Db_Table::getDefaultAdapter();
				$db -> beginTransaction();
				try
				{
					$row = $invites_table -> createRow();
					$row -> user_id = $user -> getIdentity();
					$row -> recipient = $recipient;
					$row -> code = $invitation_code;
					$row -> timestamp = date('Y-m-d H:i:s');
					$row -> message = $inviting_message;
					$row -> save();

					/*
					$inviteUrl = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
						'module' => 'invite',
						'controller' => 'signup',
					), 'default', true) . '?' . http_build_query(array(
						'code' => $invitation_code,
						'email' => $recipient
					));
					*/
					
					// Genreate invitation URL
					$inviteUrl = $link = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $user->getIdentity()), 'contactimporter_ref');
					
					$mail_settings = array(
						'host' => $_SERVER['HTTP_HOST'],
						'date' => time(),
						'sender_email' => $user -> email,
						'sender_title' => $user -> getTitle(),
						'sender_link' => $user -> getHref(),
						'sender_photo' => $user -> getPhotoUrl('thumb.icon'),
						'displayname' => $user -> getTitle(),
						'email' => $recipient,
						'message' => $inviting_message,
						'code' => $invitation_code,
						'object_link' => $inviteUrl
					);

					// send email
					$mailType = $settings -> getSetting('user.signup.inviteonly');
					if ($mailType == 2)
					{
						$mail_settings['code'] = $invitation_code;
						$mail_id = Engine_Api::_() -> getApi('mail', 'contactimporter') -> sendSystem($recipient, 'invite_code', $mail_settings);
					}
					else
					{
						$mail_settings['code'] = $row['code'];
						$mail_id = Engine_Api::_() -> getApi('mail', 'contactimporter') -> sendSystem($recipient, 'invite', $mail_settings);
					}
					
					$mailIds[$recipient] = $mail_id;
					$db -> commit();
				}
				catch ( Zend_Mail_Transport_Exception $e)
				{
					$db -> rollBack();
					$success = 0;
					$error_message = 'Fail to create records in database.';
					$result_message['success'] = 0;
					$result_message['error_message'] = $error_message;

					return $result_message;
				}

			} // end if in_array($recipient,$invited)
		}// end foreach ($recipients as $recipient)

		$success = 0;
		$error_message = '';
		$result_message['success'] = $success;
		$result_message['error_message'] = $error_message;
		return $mailIds;
	}

	/**
	 * Resend invitation emails
	 */
	public function resendInvitationEmail(User_Model_User $user, $recipient, $inviting_message, $invitation_code)
	{
		$success = 1;
		$error_message = 'No error happenned';

		$settings = Engine_Api::_() -> getApi('settings', 'core');

		// Get the already invited contacts
		$invites_table = Engine_Api::_() -> getDbTable('invites', 'invite');

		$existing_members = $this -> getUserByEmail($recipient);

		// Preprocess recipient email address
		$recipient = trim($recipient);

		// Omit recipients who is a current member
		if (!$this -> validateEmail($recipient) || array_key_exists($recipient, $existing_members))
		{
			$success = 0;
			$error_message = 'Invalid email or already registered email';
			$result_message['success'] = $success;
			$result_message['error_message'] = $error_message;
			return $result_message;
		}

		// Genreate invitation URL
		$inviteUrl = $link = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $user->getIdentity()), 'contactimporter_ref');
		
		if ($invitation_code)
		{
			$inviteUrl = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'module' => 'invite',
				'controller' => 'signup',
			), 'default', true) . '?' . http_build_query(array(
				'code' => $invitation_code,
				'email' => $recipient
			));
		}
		
		// insert the invitation into database
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try
		{
			$mail_settings = array(
				'host' => $_SERVER['HTTP_HOST'],
				'date' => time(),
				'sender_email' => $user -> email,
				'sender_title' => $user -> getTitle(),
				'sender_link' => $user -> getHref(),
				'sender_photo' => $user -> getPhotoUrl('thumb.icon'),
				'displayname' => $user -> getTitle(),
				'email' => $recipient,
				'message' => $inviting_message,
				'object_link' => $inviteUrl
			);

			if ($invitation_code)
			{
				$mail_settings['code'] = $invitation_code;
			}

			// send email
			$mailType = $settings -> getSetting('user.signup.inviteonly');
			if ($mailType == 2)
			{
				$mail_settings['code'] = $invitation_code;
				Engine_Api::_() -> getApi('mail', 'contactimporter') -> sendSystem($recipient, 'invite_code', $mail_settings);
			}
			else
			{
				$mail_settings['code'] = $invitation_code;
				Engine_Api::_() -> getApi('mail', 'contactimporter') -> sendSystem($recipient, 'invite', $mail_settings);
			}

			$db -> commit();
		}
		catch ( Zend_Mail_Transport_Exception $e)
		{
			$db -> rollBack();
			$success = 0;
			$error_message = 'Fail to create records in database.';
			$result_message['success'] = 0;
			$result_message['error_message'] = $error_message;
			return $result_message;
		}

		$success = 1;
		$error_message = '';
		$result_message['success'] = $success;
		$result_message['error_message'] = $error_message;
		return $result_message;
	}

	/**
	 * Used to distribute a list of item on many pages
	 *
	 */
	public function layout($total_item, $item_per_page = 1, $current_page)
	{
		if ($total_item == 0)
		{
			$page_count = 1;
		}
		else
		{
			$page_count = ceil($total_item * 1.0 / $item_per_page);
		}

		// validate the current page
		$current_page = ($current_page < 1) ? 1 : $current_page;
		$current_page = ($current_page > $page_count) ? $page_count : $current_page;

		// identify the first item in page.
		// Item number begins at 0. Page number begins at 1.
		$first_item = ($current_page - 1) * $item_per_page;

		return array(
			'first_item' => $first_item,
			'current_page' => $current_page,
			'page_count' => $page_count
		);
	}

	function parse_vcards(&$lines)
	{
		$cards = array();
		$card = new VCard();
		while ($card -> parse($lines))
		{
			$property = $card -> getProperty('N');
			if (!$property)
			{
				return "";
			}
			$n = $property -> getComponents();
			$tmp = array();
			if ($n[3])
				$tmp[] = $n[3];
			// Mr.
			if ($n[1])
				$tmp[] = $n[1];
			// John
			if ($n[2])
				$tmp[] = $n[2];
			// Quinlan
			if ($n[4])
				$tmp[] = $n[4];
			// Esq.
			$ret = array();
			if ($n[0])
				$ret[] = $n[0];
			$tmp = join(" ", $tmp);
			if ($tmp)
				$ret[] = $tmp;
			$key = join(", ", $ret);
			$cards[$key] = $card;
			// MDH: Create new VCard to prevent overwriting previous one (PHP5)
			$card = new VCard();
		}
		ksort($cards);
		return $cards;
	}

	function get_vcard_categories(&$cards)
	{
		$unfiled = false;
		// set if there is at least one unfiled card
		$result = array();
		foreach ($cards as $card_name => $card)
		{
			$properties = $card -> getProperties('CATEGORIES');
			if ($properties)
			{
				foreach ($properties as $property)
				{
					$categories = $property -> getComponents(',');
					foreach ($categories as $category)
					{
						if (!in_array($category, $result))
						{
							$result[] = $category;
						}
					}
				}
			}
			else
			{
				$unfiled = true;
			}
		}
		if ($unfiled && !in_array('Unfiled', $result))
		{
			$result[] = 'Unfiled';
		}
		return $result;
	}

	/**
	 * Used to upload CSV/VCF file
	 *
	 * @param mixed $user
	 */
	function uploadContactFile($user)
	{
		// Get the library file
		include_once 'VcardReader.php';
		include_once 'vcard.php';
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$contacts = array();
		$friends = array();

		$is_error = 0;
		$message = '';
		$ci_contacts = array();

		// list the permitted file type
		$permit_file_types = array(
			'text/csv' => 'csv',
			'text/comma-separated-values' => 'csv',
			'application/csv' => 'csv',
			'application/excel' => 'csv',
			'application/vnd.ms-excel' => 'csv',
			'application/vnd.msexcel' => 'csv',
			'text/anytext' => 'csv',
			'text/x-vcard' => 'vcf',
			'application/vcard' => 'vcf',
			'text/anytext' => 'vcf',
			'text/directory' => 'vcf',
			'text/x-vcalendar' => 'vcf',
			'application/x-versit' => 'vcf',
			'text/x-versit' => 'vcf',
			'application/octet-stream' => 'ldif',
		);

		for (; ; )
		{
			$uploaded_file = $_FILES['csvfile']['tmp_name'];
			$filetype = $_FILES['csvfile']["type"];
			$filename = $_FILES['csvfile']['name'];
			// Check file types
			$v = strpos($filename, '.ldif');

			if (!array_key_exists($filetype, $permit_file_types) && $v < 0)
			{
				$is_error = 1;
				$message = "Invalid file type!";
				break;
			}

			if (is_uploaded_file($uploaded_file))
			{
				$fh = fopen($uploaded_file, "r");
				//die('0');
				if ($this -> EndsWith(mb_strtolower($filename), 'csv'))
				{

					// Process CSV file type
					//die('1');
					$i = 0;
					$row = fgetcsv($fh, 1024, ',');

					$first_name_pos = -1;
					$email_pos = -1;
					$first_display_name = -1;
					$count = count($row);

					for ($i = 0; $i < $count; $i = $i + 1)
					{

						if ($row[$i] == "E-mail Display Name" || $row[$i] == "First" || $row[$i] == "First Name" || $row[$i] == "Name")
						{
							$first_name_pos = $i;
						}
						elseif ($row[$i] == "E-mail Address" || $row[$i] == "Email" || $row[$i] == "E-mail 1 - Value")
						{
							$email_pos = $i;
						}
						elseif ($row[$i] == "First Name" || $row[$i] == "First")//yahoo format oulook
						{
							$first_display_name = $i;
						}
						else
						{
							// do nothing
						}
					}

					if (($email_pos == -1) || ($first_name_pos == -1))
					{
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}
					else
					{
						if ($first_display_name == -1)
							$first_display_name = $first_name_pos;
					}

					while (($row = fgetcsv($fh, 1024, ',')) != false)
					{
						if (isset($row[$email_pos]) && $row[$email_pos] != "")
							$contacts[] = array(
								'email' => $row[$email_pos],
								'name' => empty($row[$first_name_pos]) ? @$row[$first_display_name] : @$row[$first_name_pos]
							);
					}

					fclose($fh);

				}
				elseif ($this -> EndsWith(mb_strtolower($filename), 'vcf'))
				{
					// Process VCF file type
					//die('2');
					$file_size = filesize($uploaded_file);

					if ($file_size == 0)
					{
						$is_error = 1;
						$message = 'Empty file!';
						break;
					}
					$lines = file($uploaded_file);
					$cards = @$this -> parse_vcards($lines);
					$all_categories = @$this -> get_vcard_categories($cards);
					//$names = array('FN', 'TITLE', 'ORG', 'TEL', 'EMAIL', 'URL', 'ADR', 'BDAY', 'NOTE');
					$names = array('EMAIL');
					foreach ($cards as $card_name => $card)
					{

						//echo $card_name;
						$contact['first_name'] = $card_name;
						$contact['name'] = $contact['first_name'];

						$properties = $card -> getProperties('EMAIL');
						if ($properties)
						{
							//echo "<pre>".print_r($properties,true)."</pre>";
							$contact['email'] = $properties[0] -> value;
							$contacts[] = array(
								'email' => $contact['email'],
								'name' => $contact['name']
							);
							//echo ;
						}
					}
					//die();
					/*$vcf = fread($fh, filesize($uploaded_file));
					 fclose($fh);
					 $vCard = new VCardTokenizer($vcf);

					 $contacts = array();
					 $result = $vCard->next();
					 //print_r($result);die();
					 $contact = array();

					 while($result)
					 {

					 if(mb_strtolower($result->name) == 'email')
					 {
					 $contact['email'] = $result->getStringValue();
					 }
					 else if(mb_strtolower($result->name) == 'n')
					 {

					 $name = $result->getStringValue();
					 $parts = explode(";", $name, 2);
					 if($parts[1] == '')
					 {
					 //print_r($result->getStringValue());
					 //die('0.0');
					 $contact['first_name'] = $parts[0];
					 $contact['name'] = $contact['first_name'];
					 }
					 else
					 {

					 //die('0.1');
					 $contact['last_name'] = $parts[0];
					 $contact['first_name'] = $parts[1];

					 $contact['name'] = $contact['first_name'] . ' ' . $contact['last_name'];
					 //print_r($parts.'0.1<br/>');
					 }
					 }
					 else if(mb_strtolower($result->name) == 'org')
					 {
					 //print_r($result->name);
					 //die('1');
					 $contact['company'] = $result->getStringValue();
					 }
					 else if(mb_strtolower($result->name) == 'title')
					 {
					 //die('2');
					 $contact['position'] = $result->getStringValue();
					 }

					 $result = $vCard->next();
					 }
					 */
					if ((!isset($contact['email'])) || (!isset($contact['name'])))
					{
						//die('3');
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}

					if (isset($contact['email']))
					{
						//die('4');
						if ($this -> validateEmail($contact['email']))
						{
							$contacts[] = array(
								'email' => $contact['email'],
								'name' => $contact['name']
							);
						}
						else
						{
							// error 1 contact, but omit it
							$is_error = 0;
							$message = "There's some error in your contact file";
						}
					}
				}
				elseif ($this -> EndsWith(mb_strtolower($filename), 'ldif'))//thunderbirth
				{
					//die('1');
					$thunder_data = fread($fh, filesize($uploaded_file));
					$rows = explode(PHP_EOL, $thunder_data);
					$name = "";
					$email = "";
					$contacts = array();

					foreach ($rows as $index => $row)
					{
						try
						{
							@list($key, $data) = @explode(':', $row);
							if ($key == 'cn')
								$name = $data;
							if ($key == 'mail')
								$email = trim($data);

							if ($name != "" && $email != "")
							{

								$contacts[] = array(
									'email' => $email,
									'name' => $name
								);

								$name = "";
								$email = "";
							}

						}
						catch(Exception $ex)
						{

						}

						//echo $key.'--'.$data."<br/>";
					}
				}
				else
				{

					// not support format
					$is_error = 1;
					$message = "Unknown file type!";

				}
			}

			if (empty($contacts))
			{

				$is_error = 1;
				$message = "There is no contact in your address book";
				break;
			}

			foreach ($contacts as $value)
			{

				$ci_contacts["{$value["email"]}"] = $value["name"];
			}
			break;
		}

		$returns['contacts'] = $ci_contacts;
		$returns['is_error'] = $is_error;
		$returns['error_message'] = $message;

		return $returns;
	}

	/**
	 * Validate an email address
	 *
	 * @param mixed $email
	 * @return mixed
	 */
	function validateEmail($email)
	{
		$pattern = "/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/";
		return (bool) preg_match($pattern, $email);
	}

	/**
	 * Check if a string ends with a specified substring
	 *
	 * @param mixed $FullStr
	 * @param mixed $EndStr
	 */
	function endsWith($FullStr, $EndStr)
	{
		// Get the length of the end string
		$StrLen = strlen($EndStr);

		// Look at the end of FullStr for the substring the size of EndStr

		$FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
		// If it matches, it does end with EndStr
		return $FullStrEnd == $EndStr;
	}

	/**
	 * Used to get user id by a list of emails
	 */
	public function getUserByEmail($emails)
	{
		$all_users = Engine_Api::_() -> getDbTable('users', 'user');

		$select = $all_users -> select() -> where('email IN (?)', $emails);

		$selected_contacts = $all_users -> fetchAll($select);

		$results = array();

		foreach ($selected_contacts as $row)
		{
			$results[$row -> email] = $row -> user_id;
		}

		return $results;
	}
	public function getUser($email)
	{
		$users = Engine_Api::_() -> getDbTable('users', 'user');
		$select = $users -> select() -> where('email = ?', $email);
		$user = $users -> fetchRow($select);
		return $user;
	}

}
?>
<?php

class Ynbanmem_Widget_ExtraInfoController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    
      // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer = $viewer;
		
        if (!$viewer->isAdmin() || !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'view_extra')) {
            return $this->setNoRender();
        }

//       ??? // Get subject and check auth
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
		 //print_r($subject->getIdentity());die;
        if ($subject->getIdentity() > 0) {
            $id = $subject->user_id;
        // Check auth
        //Load data
        $user = Engine_Api::_()->getItem('user', $id);

        $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
        $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');

		if($user->username != null)
			$isBannedUsername = $bannedUsernamesTable->isUsernameBanned($user->username);
		else
			$isBannedUsername = false;
        $isBannedEmail = $bannedEmailsTable->isEmailBanned($user->email);
		
//        // Build Ban/Unban URL
        $typeURL;
        $banText;
        $bannedEmail;
        $bannedUsername;
        $banned_id;
        if (!$isBannedUsername && !$isBannedEmail) {
		
            $banText = 2; // Ban 
            //$bannedUsername = $bannedUsernamesTable->getBannedUsernameByUsername($user->username);
            $typeURL = 1; // Ban username
        } else {
            if ($isBannedUsername && $isBannedEmail) {

                $banText = 1; // Unban 
                $bannedUsername = $bannedUsernamesTable->getBannedUsernameByUsername($user->username);
                $this->view->bannedUser_id = $bannedUsername['banned_id'];
                $bannedEmail = $bannedEmailsTable->getBannedEmailByEmail($user->email);
                $this->view->bannedEmail_id = $bannedEmail['banned_id'];
                $typeURL = 2; // Unban Username
            } else {
                if ($isBannedUsername) {

                    $banText = 1; // Uban 
                    $bannedUsername = $bannedUsernamesTable->getBannedUsernameByUsername($user->username);
                    $this->view->bannedid = $banned_id = $bannedUsername['banned_id'];
                    $typeURL = 3; // Unban Username
                } else {
                    if ($isBannedEmail) {
					
                        $banText = 1; //Unabn 
                        $bannedEmail = $bannedEmailsTable->getBannedEmailByEmail($user->email);
						
                        $this->view->bannedid = $banned_id = $bannedEmail['banned_id'];
                        $typeURL = 4; // Unban Email
                    }
                }
            }
        }


        $this->view->user = $user;
        $this->view->typeURL = $typeURL;
        $this->view->banText = $banText;
		$this->view->viewer = $viewer;
        
		}
		else
		{
			return $this->setNoRender();
		}
			
		
       
       
// Get user profile
       

        
  }
 
}


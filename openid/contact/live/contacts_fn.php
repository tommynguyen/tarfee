<?php

//A class to create an object for contact info attributes
//You may extend this if you want more information, but you will then need to
//"fill in" the info when parsing the return array and expand the consctructor
class Person
{
    public $first_name, $last_name, $email_address;

    public function __construct($first, $last, $em)
    {
        $this -> first_name = $first;
        $this -> last_name = $last;
        $this -> email_address = $em;
    }

    public function __toString()
    {
        return "$this->first_name 		$this->$last_name   	:	$this->$email_address <BR>";
    }

}

function get_people_array()
{
    include 'settings.php';
    include 'windowslivelogin.php';

    //initialize Windows Live Libraries
    $wll = WindowsLiveLogin::initFromXml($KEYFILE);
    $consenturl = $wll -> getConsentUrl($OFFERS);

    // If the raw consent token has been cached in a site cookie, attempt to
    // process it and extract the consent token.
    $token = null;
    $cookie = @$_COOKIE[$COOKIE];
    if ($cookie)
    {
        $token = $wll -> processConsentToken($cookie);
    }

    //Check if there's consent and, if not, redirect to the login page
    if ($token && !$token -> isValid())
    {
        $token = null;
    }
    if ($token == null)
    {
        header('Location:' . $consenturl);
    }
    if ($token)
    {
        // Convert Unix epoch time stamp to user-friendly format.
        $expiry = $token -> getExpiry();
        $expiry = date(DATE_RFC2822, $expiry);
		

        //*******************CONVERT HEX TO DOUBLE LONG INT
        // ***************************************
        $hexIn = $token -> getLocationID();
        include "hex.php";
        $longint = $output;
        //here's the magic long integer to be sent to the Windows Live service

        //*******************CURL THE REQUEST
        // ***************************************
        $uri = "https://livecontacts.services.live.com/users/@C@" . $output . "/LiveContacts";
        $session = curl_init($uri);

        curl_setopt($session, CURLOPT_HEADER, 0);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_USERAGENT, "Windows Live Data Interactive SDK");
        curl_setopt($session, CURLOPT_HTTPHEADER, array("Authorization: DelegatedToken dt=\"" . $token -> getDelegationToken() . "\""));
        curl_setopt($session, CURLOPT_VERBOSE, 1);
        curl_setopt($session, CURLOPT_HTTPPROXYTUNNEL, TRUE);
        curl_setopt($session, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($session, CURLOPT_PROXY, $PROXY_SVR);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($session, CURLOPT_TIMEOUT, 120);
        $response = curl_exec($session);
		
		
        curl_close($session);

        if (!$response)
        {
            echo $uri;
            die ;
        }

        $data = json_decode(json_encode(simplexml_load_string($response)), 1);
		
		$contacts = array();

        if (isset($data['Contacts']['Contact']))
        {
			$arrContacts = $data['Contacts']['Contact'];
			
			if(isset($arrContacts['ID'])){
				$arrContacts = array($arrContacts);
			}

            foreach ($arrContacts as $contact)
            {
				
                $name = $email = null;
                $name =  @$contact['Profiles']['Personal']['DisplayName'];
                $email = @$contact['Emails']['Email']['Address'];
				
                if($email  == '')
                {
                    continue;    
                }
                
                if(null == $name or $name ==  ' ' or $name  ==  '  '){
                    $name =  $email;
                }

                $contacts[] = array(
                    'name' => $name,
                    'email' => $email
                );
            }
        }
    }

	setcookie($COOKIE, null,time()-86400);
    return $contacts;
}
?>
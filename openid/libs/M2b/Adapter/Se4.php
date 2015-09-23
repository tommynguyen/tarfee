<?php
/**
 * @package          Social Connect
 * @subpackage       Social Connect For phpfox
 * @author           namnv
 * @since            Oct, 2010
 */

class M2b_Adapter_Se4{
	protected $_service;
  
	function getProfile($service, $user){
		$method_name =  "{$service}Profile";
		if(method_exists($this, $method_name)){
			return $this->{$method_name}($user);
		}else{
			return $this->openidProfile($user);
		}
	}
  function mhealthProfile($user){
      return $user;
  }
  function openidProfile($user){
    
    if(DEBUG == 2){
      var_dump($user);
    }
    
    $bx = array();
    $bx['username'] =  @$user['nickname'];
    $bx['email'] = @$user['email'];
    $bx['displayname'] =  @$user['fullname'];
    $bx['birthday'] =  @$user['dob'];
    $bx['gender'] =  @$user['gender'];
    $bx['PostCode'] =  @$user['postcode'];
    $bx['country_iso'] =  @$user['country'];
    $bx['language_id'] =  @$user['language'];
    $bx['time_zone'] =  @$user['timezone'];    
        
    if(DEBUG == 1){
      print_r($bx);
      exit;
    }
    return $bx;
  }   
  
	function facebookProfile($user){
		$bx =  array();
		$bx['id'] = $user['id'];
		$bx['identity'] = $user['id'];
		$bx['username'] =  @$user['name'];
		$bx['FirstName']= @$user['first_name'];
		$bx['MiddleName']= @$user['middle_name'];
		$bx['LastName'] = @$user['LirstName'];
		$bx['displayname'] = $bx['FirstName'].' '.$bx['MiddleName'].' '.$bx['LastName'];
		$sex = strtolower(@$user['gender']);
		if($sex != 'male'){
			$sex = 2;
		}
		else {
			$sex =1;
		}
		$bx['gender'] = $sex;
		//$bx['LookingFor'] =  ('male'==$sex)?'female':'male';
		$bx['email'] = @$user['email'];
		$link = @$user['link'] or '';
		$nick = str_replace('http://www.facebook.com/','',$link);
		$bx['username'] =  $nick;
		
		if(strstr($nick, 'profile')){
		 $bx['username'] =  str_replace(' ','',$bx['Name']);
		}		
		
		$bx['FacebookProfileUrl'] = $user['link'];
		$bx['FacebookProfile']     =  $nick;
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}

	function twitterProfile($user){
		if(DEBUG == 2){
			var_dump($user);
		}

		$bx =  array();
		$name = $bx['Name'] =  @$user->{"name"};
		list($fname, $lname) = self::splitName($name);
		$bx['FirstName']= $fname;
		$bx['LastName'] = $lname;
		$bx['displayname'] = $bx['FirstName'].' '.$bx['LastName'];
		$sex = strtolower(@$user->{"gender"});
		if($sex != 'male'){
			$sex = 2;
		}
		else {
			$sex =1;
		}
		$bx['gender'] = $sex;
		//$bx['LookingFor'] =  ('male'==$sex)?'female':'male';
		$bx['email'] = @$user->{"email"};
		$link = @$user->{"link"} or '';
		$bx['ProfileImageUrl'] =  @$user->profile_image_url;
		$bx['TwitterFollwing'] = @$user->following;
		$bx['username'] =  @$user->screen_name;
		$bx['time_zone'] = @$user->time_zone;
		$bx['DescriptionMe'] = @$user->description;
		$bx['country_iso'] = @$user->location;
		$bx['language_id'] = @$user->lang;
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}

	function myspaceProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx =  array();
		$name = $bx['username'] =  @$user->basicprofile->{"name"};
		$bx[''] = @$user->aboutme;
		list($fname, $lname) = self::splitName($name);
		$bx['FirstName']= $fname;
		$bx['LastName'] = $lname;
		$bx['displayname'] = $bx['FirstName'].' '.$bx['LastName'];
		$sex = strtolower(@$user->{"gender"});
		if($sex != 'male'){
			$sex = 2;
		}
		else {
			$sex =1;
		}
		$bx['gender'] = $sex;
		$bx['Age'] = @$user->age;
		$bx['City'] = @$user->city;
		//$bx['LookingFor'] =  ('male'==$sex)?'female':'male';
		$bx['email'] = @$user->{"email"};
		$link = @$user->{"link"} or '';
		$bx['ProfileImageUrl'] =  @$user->basicprofile->image;
		$bx['username'] =  @$user->name;
		$bx['DescriptionMe'] = @$user->aboutme;
		$bx['Country'] = @$user->country;
		$bx['Hometown'] = @$user->hometown;
		$bx['RelationshipStatus'] =  self::checkRelationship(@$user->maritalstatus);
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;

	}
	function yahooProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		$bx['email'] =  @$user['email'];
		$name = $bx['Name'] = @$user['fullname'];
		list($fname, $lname)  = self::splitName($name);
		$bx['FirstName'] = $fname;
		$bx['LastName']  = $lname;
		$bx['displayname'] = $bx['FirstName'].' '.$bx['LastName'];
		
		$bx['language_id'] = self::checkLanguage(@$user['language']);
		$bx['username'] = self::checkNickName(@$user['nickname']);
		$bx['gender'] = self::checkSex(@$user['gender']);
		$bx['time_zone'] = self::checkTimezone(@$user['timezone']);
		$bx['language_id'] = self::checkLanguage(@$user['language']);
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}

	function googleProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		$bx['identity'] =  @$user['id'];
		$bx['email'] =  @$user['email'];
		$name = $bx['Name'] = @$user['fullname'];
		list($fname, $lname)  = self::splitName($name);
		$bx['FirstName'] = @$user['given_name'];
		$bx['LastName']  = @$user['family_name'];
		$bx['displayname'] = @$user['name'];
		$bx['language_id'] = self::checkLanguage(@$user['locale']);
		$bx['username'] = self::checkNickName(@$user['email']);
		$bx['gender'] = self::checkSex(@$user['gender']);
		$bx['time_zone'] = self::checkTimezone(@$user['timezone']);
		$bx['Language_id'] = self::checkLanguage(@$user['language']);
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}
	
	function carepassProfile($user)
	{
		if(DEBUG == 2)
		{
			var_dump($user);
			exit;
		}
		
		$bx = array();
		$bx['id'] = $bx['identity']  = $user['id'];
		$bx['email'] =  $user['email'];
		$bx['displayname'] =  $user['firstName'] . ' '. $user['lastName'];
		$bx['gender'] = self::checkSex($user['gender']);
		$bx['language_id'] = self::checkLanguage($user['language']);
		$bx['birthday'] = self::checkDoB(@$user['dateOfBirth']);
		$bx['prefix'] = @$user['prefix'];
		return $bx;
	}

	function hyvesProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();

		$bx['displayname'] =  @$user['fullname'];
		$bx['FirstName'] =  @$user['first_name'];
		$bx['LastName'] =  @$user['last_name'];
		$bx['gender'] =  self::checkSex(@$user['gender']);
		$bx['username'] =  @$user['nickname'];
		$bx['Age'] =  @$user['age'];
		$bx['birthday'] =  self::checkDoB(@$user['dob']);
		$bx['HyvesProfileUrl'] =  @$user['hyves_url'];
		$bx['Locale'] =  @$user['locale'];
		if(DEBUG == 1){
			var_dump($user);
			print_r($bx);
			exit;
		}
		return $bx;
	}

	function linkedinProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		$bx['FirstName'] =  @$user['first_name'];
		$bx['LastName'] = @$user['last_name'];
		$bx['displayname'] = $bx['FirstName'].' '.$bx['LastName'];
		$bx['Headline'] = @$user['headline'];
		$bx['Description'] = @$user['summary'];
		$bx['country_iso'] = @$user['location'];
		$bx['Interest'] = @$user['interests'];
		$bx['Asociation'] = @$user['associations'];
		$bx['birthday'] =  self::checkDoB(@$user['dob']);
		$bx['username']  = $bx['FirstName'] . $bx['LastName'];
		

		if(DEBUG == 1){
			print_r($bx);
			exit;
		}

		return $bx;
	}

	function youtubeProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		$bx['Age'] =  @$user['age'];
		$bx['books'] = $user['books'];
		$bx['Company'] = $user['company'];
		$bx['firstName'] = $user['firstName'];
		$bx['gender'] = self::checkSex(@$user['gender']);
		$bx['hobbies'] =  @$user['hobbies'];
		$bx['Hometown'] = @$user['hometown'];
		$bx['lastName'] = @$user['lastName'];
		$bx['location'] = @$user['location'];
		$bx['music'] = @$user['music'];
		$bx['occupation'] = @$user['occupation'];
		$bx['RelationshipStatus'] = self::checkRelationship(@$user['relationship']);
		$bx['School'] = @$user['school'];
		$bx['Statistics'] = @$user['statistics'];
		$bx['username'] = @$user['nickname'];
		$bx['YouTubeProfileUrl'] = @$user['profile_url'];
		$bx['displayname'] = @$user['firstName'] . ' '. @$user['lastName'];
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}
	function checkRelationship($relatiotionship = 'Single'){
		$relatiotionship = strtolower($relatiotionship);
		$rels = array(
				'single'=>'Single',
				'in a relationship'=>'In a Relationship',
				'engaged'=>'Engaged',
				'married'=>'Married',
				'it\'s complicated'=>'It\'s Complicated',
				'in an open relationship'=>'In an Open Relationship',
		);
		if(!array_key_exists($relatiotionship,$rels)){
			$rel = 'In a Relationship';
		}
		return $rels[$relatiotionship];
	}
	function picasaProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		$bx['email'] = @$user['email'];
		$bx['displayname'] = @$user['fullname'];
		list($fname, $lname) = self::splitName($bx['Name']);
		$bx['FirstName'] = $fname;
		$bx['LastName'] = $lname;
		$bx['username'] =  self::checkNickName(@$user['nickname']);
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}

	function flickrProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}
	
	function flickr2Profile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
		$bx['username'] =  @$user['username'];
		$name = $bx['displayname'] =  @$user['realname'];
		list($fname, $lname) = self::splitName($name);
		$bx['City'] =  @$user['location'];
		$bx['FlickProfileUrl'] =  @$user['profileurl'];
		$bx['FirstName'] =  $fname;
		$bx['LastName'] =  $lname;
		
		if(DEBUG == 1){
		//	var_dump($user);
			print_r($bx);
			exit;
		}
		return $bx;
	}
	function liveProfile($user){
		if(DEBUG == 2){
			var_dump($user);
			exit();
		}
		$bx = array();
        
        $bx['id'] = $bx['identity'] = $user->id;
        $bx['displayname'] =  $user->name;
        $bx['FirstName'] =  $user->first_name;
        $bx['LastName'] =  $user->last_name;
        $bx['email'] = $user->emails->preferred;
        $bx['username'] = $bx['profile'] =  substr($bx['email'], 0, strpos($bx['email'],'@'));
        
		if(DEBUG == 1){
			print_r($bx);
			exit;
		}
		return $bx;
	}
	static function checkNickName($nickname = ''){
		return $nickname;
	}

	static function checkLanguage($lang){
		return $lang;
	}
	static function checkTimezone($timezone = 'en_US'){
		return $timezone;
	}
	static function checkDoB($dob){
		return $dob;
	}
	static function checkSex($sex = ''){
		$sex = strtolower($sex);
		switch($sex){
			case 'M':
			case '0':
			case 'male':
			case 'nam':
				return 1;
			default:
				return 2;
		}
	}
	static function splitName($name){
		$ar = explode(' ', $name, 2);
		$fname =  $ar[0];
		$lname =  @$ar[1]?$ar[1]:'';
		return array($fname, $lname);
	}
}

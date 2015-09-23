<?php

class M2b_Restore_Base{
	/**
	 *
	 * name of current service
	 * @var string
	 */
	public $service_name;
	public $logged = 0;
	public $data;
	public $loginTime;

	public function __construct($service_name, $json, $logged, $loginTime){

		$this->service_name = $service_name;
		$this->logged = $logged;
		$this->loginTime = $loginTime;
		$data = json_decode($json);

		if(is_array($data)){
			$this->data = $data;
		}else if($data instanceof stdClass){
			$this->data =  get_object_vars($data);
		}

	}
	
	public function get_profile_url(){
		return '#';
	}
	public function get_login_time(){
		return $this->loginTime;
	}
	public function get($name){

		$method_name =  'get_'.$name;

		if(method_exists($this, $method_name)){
			return $this->{$method_name}();
		}
		if(isset($this->data[$name])){
			return $this->data[$name];
		}
		return "";
	}

	public function get_service_name(){
		return $this->service_name;
	}

	public function get_name(){
		return $this->data['fullname'];
	}
	
	public function get_connect_url(){
		return "sopopup('/quick-signin/{$this->service_name}/')";
	}
	public function get_disconnect_url(){
		return "sopopup('/quick-signout/{$this->service_name}/')";
	}
	protected function _getLoggedPanel(){
		return "
			<table cellspacing='3' class='table-connect'>
				<tr>
					<th>
						{$this->get('service_name')} 
					</th>					
				</tr>
				<tr>
					<td>Connected as {$this->get('name')}</td>
				</tr>
				<tr>
					<td><a href=\"{$this->get('profile_url')}\" target='_default'>{$this->get('service_name')} profile</td>
				</tr>
			</table>
		";
	}

	protected function _getLoggedOutPanel(){
		return "
			<table cellspacing='3' class='table-disconnect'>
				<tr>
					<th>
						{$this->get('service_name')} 
					</th>					
				</tr>
				<tr>
					<td><a href=\"javascript: void(0)\" onclick=\"{$this->get('connect_url')}\">Disconnected</a></td>
				</tr>
			</table>
		";
	}

	public function getPanelHtml(){
		if($this->logged){
			return $this->_getLoggedPanel();
		}else{
			return $this->_getLoggedOutPanel();
		}
	}

}

class M2b_Restore_Facebook extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Facebook';
	}
	public function get_name(){
		return $this->data['name'];
	}
	public function get_profile_url(){
		return $this->data['link'];
	}
}

class M2b_Restore_Twitter extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Twitter';
	}
	public function get_profile_url(){
		return 'http://www.twitter.com/'.$this->data['screen_name'];
	}
	public function get_name(){
		return $this->data['name'];
	}
}
class M2b_Restore_Google extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Google';
	}
	public function get_profile_url(){
		return 'http://www.google.com/profiles/'. str_replace('@gmail.com', '', $this->data['email']);
	}
	public function get_name(){
		return $this->data['fullname'];
	}
}
class M2b_Restore_Myspace extends M2b_Restore_Base{
	public function get_service_name(){
		return 'MySpace';
	}
	public function get_profile_url(){
		return $this->data['basicprofile_webUri'];
	}
}
class M2b_Restore_Yahoo extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Yahoo!';
	}
	public function get_profile_url(){
		return 'http://pulse.yahoo.com/';
	}
}
class M2b_Restore_Hyves extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Hyves';
	}
}

class M2b_Restore_Blogger extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Blogger';
	}
	public function get_profile_url(){
		return 'http://www.blogger.com/home';
	}
}

class M2b_Restore_Youtube extends M2b_Restore_Base{
	public function get_service_name(){
		return 'YouTube';
	}
public function get_profile_url(){
		return 'http://www.youtube.com/account';
	}
}

class M2b_Restore_Linkedin extends M2b_Restore_Base{
	public function get_service_name(){
		return 'LinkedIn';
	}
}

class M2b_Restore_Picasa extends M2b_Restore_Base{
	public function get_service_name(){
		return 'Picasa';
	}
	public function get_profile_url(){
		return 'http://picasaweb.google.com/home';
	}
}
class M2b_Adapter_Restore{

	public static function factory($agent){

		$class_name = 'M2b_Restore_'.ucfirst($agent['name']);

		if(!class_exists($class_name,false)){
			$class_name  = 'M2b_Restore_Base';
		}

		return new $class_name($agent['name'], $agent['data'], $agent['login'], $agent['login_time']);
	}
}
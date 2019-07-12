<?php
/**
 * ����� LoginzaUserProfile ������������ ��� ��������� ��������� ����� ������� ������������ �����, 
 * �� ������ ����������� ������� �� Loginza API (http://loginza.ru/api-overview).
 * 
 * ��� ��������� ������������ ��������� ����� ������, ��� ��������� ������������� ������������ 
 * ������ �������, �� ������ ���������.
 * 
 * ��������: ���� � ������� ������������ �� �������� �������� nickname, �� ��� �������� ����� ����
 * �������������� �� ������ email ��� full_name �����.
 * 
 * ������ ����� - ��� ������� ������, ������� ����� ������������ ��� ����, 
 * � ��� �� ������������ � ����������� ���� ��� ��������� ������� ������ ��� ���� ������.
 * 
 * @link http://loginza.ru/api-overview
 * @author Sergey Arsenichev, PRO-Technologies Ltd.
 * @version 1.0
 */
class LoginzaUserProfile {
	/**
	 * �������
	 *
	 * @var unknown_type
	 */
	private $profile;
	
	/**
	 * ������ ��� ���������
	 *
	 * @var unknown_type
	 */
	private $translate = array(
	'�'=>'a', '�'=>'b', '�'=>'v', '�'=>'g', '�'=>'d', '�'=>'e', '�'=>'g', '�'=>'z',
	'�'=>'i', '�'=>'y', '�'=>'k', '�'=>'l', '�'=>'m', '�'=>'n', '�'=>'o', '�'=>'p',
	'�'=>'r', '�'=>'s', '�'=>'t', '�'=>'u', '�'=>'f', '�'=>'i', '�'=>'e', '�'=>'A',
	'�'=>'B', '�'=>'V', '�'=>'G', '�'=>'D', '�'=>'E', '�'=>'G', '�'=>'Z', '�'=>'I',
	'�'=>'Y', '�'=>'K', '�'=>'L', '�'=>'M', '�'=>'N', '�'=>'O', '�'=>'P', '�'=>'R',
	'�'=>'S', '�'=>'T', '�'=>'U', '�'=>'F', '�'=>'I', '�'=>'E', '�'=>"yo", '�'=>"h",
	'�'=>"ts", '�'=>"ch", '�'=>"sh", '�'=>"shch", '�'=>"", '�'=>"", '�'=>"yu", '�'=>"ya",
	'�'=>"YO", '�'=>"H", '�'=>"TS", '�'=>"CH", '�'=>"SH", '�'=>"SHCH", '�'=>"", '�'=>"",
	'�'=>"YU", '�'=>"YA"
	);
	
	function __construct($profile) {
		$this->profile = $profile;
	}
	
	public function genNickname () {
		if ($this->profile->nickname) {
			return $this->profile->nickname;
		} elseif (!empty($this->profile->email) && preg_match('/^(.+)\@/i', $this->profile->email, $nickname)) {
			return $nickname[1];
		} elseif ( ($fullname = $this->genFullName()) ) {
			return $this->normalize(iconv('utf-8', 'windows-1251',$fullname), '_');
		}
		// ������� �� ������� ��������� ��� �� identity
		$patterns = array(
			'([^\.]+)\.ya\.ru',
			'openid\.mail\.ru\/[^\/]+\/([^\/?]+)',
			'openid\.yandex\.ru\/([^\/?]+)',
			'([^\.]+)\.myopenid\.com'
		);
		foreach ($patterns as $pattern) {
			if (preg_match('/^https?\:\/\/'.$pattern.'/i', $this->profile->identity, $result)) {
				return $result[1];
			}
		}
		
		return false;
	}
	
	public function genUserSite () {
		if (!empty($this->profile->web->blog)) {
			return $this->profile->web->blog;
		} elseif (!empty($this->profile->web->default)) {
			return $this->profile->web->default;
		}
		
		return $this->profile->identity;
	}
	
	public function genDisplayName () {
	 	if ( ($fullname = $this->genFullName()) ) {
			return $fullname;
		} elseif ( ($nickname = $this->genNickname()) ) {
			return $nickname;
		}
		
		$identity_component = parse_url($this->profile->identity);
		
		$result = $identity_component['host'];
		if ($identity_component['path'] != '/') {
			$result .= $identity_component['path'];
		}
		
		return $result.$identity_component['query'];
		
	}
	
	public function genFullName () {
		if ($this->profile->name->full_name) {
			return $this->profile->name->full_name;
		} elseif ( $this->profile->name->first_name || $this->profile->name->last_name ) {
			return trim($this->profile->name->first_name.' '.$this->profile->name->last_name);
		}
		return false;
	}
	/**
	 * ��������� ��������� �������
	 *
	 * @param unknown_type $len ����� ������
	 * @param unknown_type $char_list ������ ������� ��������, ������������ ��� ���������, ����� �������. ��������: a-z,0-9,~
	 * @return unknown
	 */
	public function genRandomPassword ($len=6, $char_list='a-z,0-9') {
		$chars = array();
		// ����������������� ������ ��������
		$chars['a-z'] = 'qwertyuiopasdfghjklzxcvbnm';
		$chars['A-Z'] = strtoupper($chars['a-z']);
		$chars['0-9'] = '0123456789';
		$chars['~'] = '~!@#$%^&*()_+=-:";\'/\\?><,.|{}[]';
		
		// ����� �������� ��� ���������
		$charset = '';
		// ������
		$password = '';
		
		if (!empty($char_list)) {
			$char_types = explode(',', $char_list);
			
			foreach ($char_types as $type) {
				if (array_key_exists($type, $chars)) {
					$charset .= $chars[$type];
				} else {
					$charset .= $type;
				}
			}
		}
		
		for ($i=0; $i<$len; $i++) {
			$password .= $charset[ rand(0, strlen($charset)-1) ];
		}
		
		return $password;
	}
	
	/**
	 * �������� + ������� ��� ������ ������� ������� �� ������ $delimer
	 *
	 * @param unknown_type $string
	 * @param unknown_type $delimer
	 * @return unknown
	 */
	private function normalize ($string, $delimer='-') {
		$string = strtr($string, $this->translate);
	    return trim(preg_replace('/[^\w]+/i', $delimer, $string), $delimer);
	}
	
	public function genEmail () { // ������� email
	 return $this->profile->email;
	 }
	
	public function genProvider () { // ������� ����� ����������, �������� <a href="http://vkontakte.ru">vkontakte.ru</a>
	 return $this->profile->provider;
	 }
	
	public function genIdentity () { // ������� ����� ������������ , �������� <a href="http://vkontakte.ru/login">vkontakte.ru/login</a>
	 return $this->profile->identity;
	 }
	
	public function genDob () { // ������� ���� �������� ������������
	 return $this->profile->dob;
	 }
	
	public function genUID () { // ������� ���������� id ������������
	 return $this->profile->uid;
	 }
	 
	 public function genGender () { // ������� ��� ������������
	 return $this->profile->gender;
	 }
}
?>
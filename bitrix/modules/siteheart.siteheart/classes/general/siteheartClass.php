<?

IncludeModuleLangFile(__FILE__);

class siteheartClass{
	
    public function addScriptTag(){
		
	global $USER;
	
	$PROTOCOL = strstr($_SERVER['SERVER_PROTOCOL'], 'HTTPS') ? 'https' : 'http';
	
	if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true){
	    
	    global $APPLICATION;

	    $settings = array();

	    $settings['widget_id'] = COption::GetOptionString("siteheart.siteheart", "widget_id");
		$settings['secret_key'] = COption::GetOptionString("siteheart.siteheart", "secret_key");
		
		if(!is_object($USER)){
			$USER = new CUser();
		}
		   
		if($settings['secret_key'] && $USER->IsAuthorized()){

			 $user_data = $USER->GetByID($USER->GetID())->arResult[0];


			$name = $user_data['LAST_NAME'].' '.$user_data['NAME'];

			if (!preg_match('//u', $name)) {

				$name = iconv('WINDOWS-1251', 'UTF-8', $user_data['LAST_NAME'].' '.$user_data['NAME']);

			}

			 $user = array(
				 'nick' => $name,
				 'avatar' => strtolower( preg_replace('/\/[^\/]*$/', '', $PROTOCOL) ).'://'.$_SERVER["HTTP_HOST"].CFile::GetPath($user_data["PERSONAL_PHOTO"]),
				 'id' => $USER->GetID(),
				 'email' => $USER->GetEmail(),
			 );
			 
			 $time = time();
			 $secret = $settings['secret_key'];
			 $user_base64 = base64_encode( json_encode($user) );
			 $sign = md5($secret . $user_base64 . $time);
			 $auth = $user_base64 . "_" . $time . "_" . $sign;

		}
		
		$url = $PROTOCOL == 'https' ? 'https://static.siteheart.com/apps/plugin/siteheart.js' : 'http://mediacdn.siteheart.com/apps/plugin/siteheart.js';
		
	    $APPLICATION->AddHeadString('<script src="'.$url.'" data-plugin="bitrix" data-widget_id="'.$settings['widget_id'].'" '.($auth ? 'data-auth="'.$auth.'"' : '').' type="text/javascript" id="siteheart_widget_script"></script>', TRUE);

	}

	    
    }

}

?>
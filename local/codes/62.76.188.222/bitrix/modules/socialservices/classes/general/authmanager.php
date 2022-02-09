<?
IncludeModuleLangFile(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/socialservices/classes/general/descriptions.php");

//manager to operate with services
class CSocServAuthManager
{
	protected static $arAuthServices = false;

	public function __construct()
	{
		if(!is_array(self::$arAuthServices))
		{
			self::$arAuthServices = array();

			$db_events = GetModuleEvents("socialservices", "OnAuthServicesBuildList");
			while($arEvent = $db_events->Fetch())
			{
				$res = ExecuteModuleEventEx($arEvent);
				if(is_array($res))
				{
					if(!is_array($res[0]))
						$res = array($res);
					foreach($res as $serv)
						self::$arAuthServices[$serv["ID"]] = $serv;
				}
			}

			//services depend on current site
			$suffix = CSocServAuth::OptionsSuffix();
			self::$arAuthServices = self::AppyUserSettings($suffix);
		}
	}
	
	protected function AppyUserSettings($suffix)
	{
		$arAuthServices = self::$arAuthServices;

		//user settings: sorting, active
		$arServices = unserialize(COption::GetOptionString("socialservices", "auth_services".$suffix, ""));
		if(is_array($arServices))
		{
			$i = 0;
			foreach($arServices as $serv=>$active)
			{
				if(isset($arAuthServices[$serv]))
				{
					$arAuthServices[$serv]["__sort"] = $i++;
					$arAuthServices[$serv]["__active"] = ($active == "Y");
				}
			}
			uasort($arAuthServices, array('CSocServAuthManager', 'Cmp'));
		}
		return $arAuthServices;
	}

	public function Cmp($a, $b)
	{
		if($a["__sort"] == $b["__sort"])
			return 0;
		return ($a["__sort"] < $b["__sort"])? -1 : 1;
	}
	
	public function GetAuthServices($suffix)
	{
		//$suffix indicates site specific or common options
		return self::AppyUserSettings($suffix);
	}

	public function GetActiveAuthServices($arParams)
	{
		$aServ = array();
		self::SetUniqueKey();
		foreach(self::$arAuthServices as $key=>$service)
		{
			if($service["__active"] === true && $service["DISABLED"] !== true)
			{
				$cl = new $service["CLASS"];
				if(is_callable(array($cl, "CheckSettings")))
					if(!call_user_func_array(array($cl, "CheckSettings"), array()))
						continue;

				if(is_callable(array($cl, "GetFormHtml")))
					$service["FORM_HTML"] = call_user_func_array(array($cl, "GetFormHtml"), array($arParams));

				$aServ[$key] = $service;
			}
		}
		return $aServ;
	}
	
	public function GetSettings()
	{
		$arOptions = array();
		foreach(self::$arAuthServices as $key=>$service)
		{
			if(is_callable(array($service["CLASS"], "GetSettings")))
			{
				$arOptions[] = htmlspecialcharsbx($service["NAME"]);
				$options = call_user_func_array(array($service["CLASS"], "GetSettings"), array());
				if(is_array($options))
					foreach($options as $opt)
						$arOptions[] = $opt;
			}
		}
		return $arOptions;
	}
	
	public function Authorize($service_id)
	{
		if(isset(self::$arAuthServices[$service_id]))
		{
			$service = self::$arAuthServices[$service_id];
			if($service["__active"] === true && $service["DISABLED"] !== true)
			{
				$cl = new $service["CLASS"];
				if(is_callable(array($cl, "Authorize")))
					return call_user_func_array(array($cl, "Authorize"), array());
			}
		}

		return false;
	}
	
	public function GetError($service_id, $error_code)
	{
		if(isset(self::$arAuthServices[$service_id]))
		{
			$service = self::$arAuthServices[$service_id];
			if(is_callable(array($service["CLASS"], "GetError")))
				return call_user_func_array(array($service["CLASS"], "GetError"), array($error_code));
			return GetMessage("socserv_controller_error", array("#SERVICE_NAME#"=>$service["NAME"]));
		}
		return '';
	}

	public function SetUniqueKey()
	{
		if(!isset($_SESSION["UNIQUE_KEY"]))
			$_SESSION["UNIQUE_KEY"] = md5(bitrix_sessid_get().uniqid(rand(), true));
	}

	public function CheckUniqueKey()
	{
		if(isset($_REQUEST["state"]))
		{
			$arState = array();
			parse_str($_REQUEST["state"], $arState);
			if(isset($arState['backurl']))
				InitURLParam($arState['backurl']);
		}
		if(!isset($_REQUEST['check_key']) && isset($_REQUEST['backurl']))
			InitURLParam($_REQUEST['backurl']);
		if($_SESSION["UNIQUE_KEY"] <> '' && ($_REQUEST['check_key'] === $_SESSION["UNIQUE_KEY"]))
		{
			unset($_SESSION["UNIQUE_KEY"]);
			return true;
		}
		return false;
	}

	function CleanParam()
	{
		$redirect_url = $GLOBALS['APPLICATION']->GetCurPageParam('', array("auth_service_id", "check_key"), false);
		LocalRedirect($redirect_url);
	}
}

//base class for auth services
class CSocServAuth
{
	protected static $settingsSuffix = false;

	public function GetSettings()
	{
		return false;
	}

	protected function CheckFields($action, &$arFields)
	{
		if (isset($arFields["EXTERNAL_AUTH_ID"]) && strlen($arFields["EXTERNAL_AUTH_ID"])<=0)
		{
			return false;
		}
		if (!isset($arFields["USER_ID"]) && $action == "ADD")
			$arFields["USER_ID"]=$GLOBALS["USER"]->GetID();
		if(is_set($arFields, "PERSONAL_PHOTO"))
		{
			$res = CFile::CheckImageFile($arFields["PERSONAL_PHOTO"]);
			if(strlen($res)>0)
				unset($arFields["PERSONAL_PHOTO"]);
			else
			{
				$arFields["PERSONAL_PHOTO"]["MODULE_ID"] = "socialservices";
				CFile::SaveForDB($arFields, "PERSONAL_PHOTO", "socialservices");
			}
		}

		return true;
	}

	function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_socialservices_user WHERE ID = ".$id." ", true);
			return true;
		}
		return false;
	}

	function OnUserDelete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_socialservices_user WHERE USER_ID = ".$id." ", true);
			return true;
		}
		return false;
	}

	public function CheckSettings()
	{
		$arSettings = $this->GetSettings();
		if(is_array($arSettings))
		{
			foreach($arSettings as $sett)
				if(is_array($sett) && !array_key_exists("note", $sett))
					if(self::GetOption($sett[0]) == '')
						return false;
		}
		return true;
	}

	public function CheckPhotoURI($photoURI)
	{
		if(preg_match("|^http[s]?://|i", $photoURI))
			return true;
		return false;
	}

	public static function OptionsSuffix()
	{
		//settings depend on current site
		$arUseOnSites = unserialize(COption::GetOptionString("socialservices", "use_on_sites", ""));
		return ($arUseOnSites[SITE_ID] == "Y"? '_bx_site_'.SITE_ID : '');
	}
	
	public static function GetOption($opt)
	{
		if(self::$settingsSuffix === false)
			self::$settingsSuffix = self::OptionsSuffix();

		return COption::GetOptionString("socialservices", $opt.self::$settingsSuffix);
	}
	
	public function AuthorizeUser($arFields)
	{
		if(!isset($arFields['XML_ID']) || $arFields['XML_ID'] == '')
			return false;
		if(!isset($arFields['EXTERNAL_AUTH_ID']) || $arFields['EXTERNAL_AUTH_ID'] == '')
			return false;

		if($GLOBALS["USER"]->IsAuthorized() && $GLOBALS["USER"]->GetID())
		{
			CSocServAuthDB::Add($arFields);
		}
		else
		{
			$dbSocUser = CSocServAuthDB::GetList(array(),array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>$arFields['EXTERNAL_AUTH_ID']),false,false,array("USER_ID"));
			$dbUsersOld = $GLOBALS["USER"]->GetList($by, $ord, array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>$arFields['EXTERNAL_AUTH_ID'], 'ACTIVE'=>'Y'), array('NAV_PARAMS'=>array("nTopCount"=>"1")));
			$dbUsersNew = $GLOBALS["USER"]->GetList($by, $ord, array('XML_ID'=>$arFields['XML_ID'], 'EXTERNAL_AUTH_ID'=>'socservices', 'ACTIVE'=>'Y'),  array('NAV_PARAMS'=>array("nTopCount"=>"1")));

			if($arUser = $dbSocUser->Fetch())
			{
				$USER_ID = $arUser["USER_ID"];
			}
			elseif($arUser = $dbUsersOld->Fetch())
			{
				$USER_ID = $arUser["ID"];
			}
			elseif($arUser = $dbUsersNew->Fetch())
			{
				$USER_ID = $arUser["ID"];
			}
			elseif(COption::GetOptionString("main", "new_user_registration", "N") == "Y")
			{

				$arFields['PASSWORD'] = randString(30); //not necessary but...
				$arFields['LID'] = SITE_ID;

				$def_group = COption::GetOptionString('main', 'new_user_registration_def_group', '');
				if($def_group <> '')
					$arFields['GROUP_ID'] = explode(',', $def_group);

				$arFieldsUser = $arFields;
				$arFieldsUser["EXTERNAL_AUTH_ID"] = "socservices";
				if(!($USER_ID = $GLOBALS["USER"]->Add($arFieldsUser)))
					return false;
				$arFields['CAN_DELETE'] = 'N';
				$arFields['USER_ID'] = $USER_ID;
				CSocServAuthDB::Add($arFields);
				unset($arFields['CAN_DELETE']);
			}
			if(isset($USER_ID) && $USER_ID > 0)
				$GLOBALS["USER"]->Authorize($USER_ID);
			else
				return false;

			//it can be redirect after authorization, so no spreading. Store cookies in the session for next hit
			$GLOBALS['APPLICATION']->StoreCookies();
		}

		return true;
	}
}

//some repetitive functionality
class CSocServUtil
{
	public static function GetCurUrl($addParam="", $removeParam=false)
	{
		$arRemove = array("logout", "auth_service_error", "auth_service_id", "MUL_MODE");
		if($removeParam !== false)
			$arRemove = array_merge($arRemove, $removeParam);

		return self::ServerName().$GLOBALS['APPLICATION']->GetCurPageParam($addParam, $arRemove);
	}
	
	public static function ServerName()
	{
		$protocol = (CMain::IsHTTPS() ? "https" : "http");
		$port = ($_SERVER['SERVER_PORT'] > 0 && $_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443? ':'.$_SERVER['SERVER_PORT']:'');

		return $protocol.'://'.$_SERVER['SERVER_NAME'].$port;
	}
}
?>
<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class krayt_okshop extends CModule
{
const MODULE_ID = 'krayt.okshop';
	var $MODULE_ID = "krayt.okshop";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function krayt_okshop()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME = GetMessage("krayt_okshop_SCOM_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("krayt_okshop_SCOM_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME = GetMessage("krayt_okshop_SPER_PARTNER");
		$this->PARTNER_URI = GetMessage("krayt_okshop_PARTNER_URI");
	}


	function InstallDB($install_wizard = true)
	{
		global $DB, $DBType, $APPLICATION;

	
		RegisterModuleDependences("main", "OnBeforeProlog", "krayt.okshop", "COkshop", "ShowPanel");
        RegisterModuleDependences("main", "OnBuildGlobalMenu", "krayt.okshop", "COkshop", "OnBuildGlobalMenu");
        RegisterModuleDependences("main", "OnEndBufferContent", "krayt.okshop", "COkshop", "OnEndBufferContent");




		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		global $DB, $DBType, $APPLICATION;

		UnRegisterModule("krayt.okshop");
		UnRegisterModuleDependences("main", "OnBeforeProlog", "krayt.okshop", "COkshop", "ShowPanel");
        UnRegisterModuleDependences("main", "OnBuildGlobalMenu", "krayt.okshop", "COkshop", "OnBuildGlobalMenu");
        UnRegisterModuleDependences("main", "OnEndBufferContent", "krayt.okshop", "COkshop", "OnEndBufferContent");
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/krayt.okshop/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/krayt.okshop/install/wizards/krayt/kmarket", $_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/krayt/kmarket", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/krayt.okshop/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default", true, true);
		
        $MODULE_ID = "krayt.okshop";
        if (is_dir($admin = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$MODULE_ID.'/admin'))
        {
            if ($dir = opendir($admin))
            {
                while (false !== $item = readdir($dir))
                {
                    if ($item == '..' || $item == '.' || $item == 'menu.php')
                        continue;
                    file_put_contents($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/k_'.$item,
                        '<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');
                }
                closedir($dir);
            }
        }


		return true;
	}

	function InstallPublic()
	{
	}

    function UnInstallFiles()
    {
        $MODULE_ID = "krayt.okshop";
        if (is_dir($admin = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$MODULE_ID.'/admin'))
        {
            if ($dir = opendir($admin))
            {
                while (false !== $item = readdir($dir))
                {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/k_'.$item);
                }
                closedir($dir);
            }
        }
        return true;
    }

	function DoInstall()
	{
		global $DB, $APPLICATION, $step;
		
		if(true)
        {
            if($_POST['step'] == 1 && $_POST['Name'] && $_POST['Phone'] && $_POST['Email'])
			{
				$this->sendData();
				$this->InstallFiles();
				$this->InstallDB(false);
				$this->InstallEvents();
				$this->InstallPublic();
				RegisterModule(self::MODULE_ID);

			}else{

				$APPLICATION->IncludeAdminFile(GetMessage("krayt_install_title"),
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/step.php");
			}
		
        }else{
            $this->InstallFiles();
            $this->InstallDB(false);
			$this->InstallEvents();
			$this->InstallPublic();
            RegisterModule(self::MODULE_ID);
        }
		
		return true;
	}
	function sendData()
	{
        function _is_curl_installed() {
            if  (in_array  ('curl', get_loaded_extensions())) {
                return true;
            }
            else {


                return false;
            }
        }

        if(_is_curl_installed())
		{

            $url = "https://krayt.ru/send_data_free/index.php";

            $fields = array(
                'name'=>$_POST['Name'],
                'phone'=>$_POST['Phone'],
                'email'=>$_POST['Email'],
				'module_id' => self::MODULE_ID,
				'is_partner' => isset($_POST['IS_PARTNER'])?true:false
            );

            $postvars='';
            $sep='';
            foreach($fields as $key=>$value)
            {
                $postvars.= $sep.urlencode($key).'='.urlencode($value);
                $sep='&';
            }
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

            $result = curl_exec($ch);
            curl_close($ch);

		}else{

            $queryUrl = "https://krayt.ru/send_data_free/index.php";

            $params = array(
                'name'=>$_POST['Name'],
                'phone'=>$_POST['Phone'],
                'email'=>$_POST['Email'],
                'module_id' => self::MODULE_ID,
                'is_partner' => isset($_POST['IS_PARTNER'])?true:false
            );
            $postdata = http_build_query(
                $params
            );

            $opts = array('http' =>
                array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );
            $context  = stream_context_create($opts);
            $result = file_get_contents($queryUrl, false, $context);

        }
	}


	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();
		return true;
	}
}
?>
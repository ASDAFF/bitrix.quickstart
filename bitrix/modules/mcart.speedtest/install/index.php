<?
IncludeModuleLangFile( __FILE__);


if(class_exists("mcart_speedtest")) 
	return;

Class mcart_speedtest extends CModule
{
	var $MODULE_ID = "mcart.speedtest";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	
	
	function mcart_speedtest() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=TASKFROMEMAIL_MODULE_VERSION;
            $this->MODULE_VERSION_DATE=TASKFROMEMAIL_MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("SPEEDTEST_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("SPEEDTEST_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.speedtest"))
		{
			$this->InstallDB();
			$this->InstallEvents();
			$this->InstallFiles();
			
		}
		return true;
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallEvents();
		$this->UnInstallFiles();
		
		return true;
	}

// IBLOCK_CODE = converttickettocrm	
	function InstallDB() {
		
		$this->AddIBType(GetMessage('SPEEDTEST_IB_TYPE_NAME'), GetMessage('SPEEDTEST_IB_TYPE_NAME_RU'));
		$iblock_id	= 	$this->AddIB(GetMessage('SPEEDTEST_IB_NAME_RU'), GetMessage('SPEEDTEST_IB_NAME'), GetMessage('SPEEDTEST_IB_TYPE_NAME')); 	
		$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.speedtest/prolog.php", "w");
		fwrite($handle, '<?define("SPEEDTEST_IBLOCK_ID", '.$iblock_id.');?>');
		fclose($handle);
		RegisterModule("mcart.speedtest");
		return true;
	}
	function UnInstallDB() {
		
		UnRegisterModule("mcart.speedtest");
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
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.speedtest/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		
		return true;
	}
	
	function UnInstallFiles()
	{	
		DeleteDirFilesEx("/bitrix/components/mcart/test.speed/");
		
		return true;
	}
	


	function FillIBlocks($iblock_id)
	{
	
// ID пользователя, Дата создания, Дата начала, Дата окончания, Статус, ИНН, XML сверки	
	$arFields=array(
		/*"0" => Array(	
		'IBLOCK_ID' => $iblock_id,
		'NAME' => GetMessage('SPEEDTEST_IP_ADDRESS'),
		'ACTIVE' => 'Y',
		'CODE' => 'IP_ADDRESS',
		'PROPERTY_TYPE' => 'S',
		
		'ROW_COUNT' => 1,
		'COL_COUNT' => 30,
		'LIST_TYPE' => 'L',
		'MULTIPLE' => 'N'
		),	*/
		"0" => Array(	
		'IBLOCK_ID' => $iblock_id,
		'NAME' => GetMessage('SPEEDTEST_DATE_TEST'),
		'ACTIVE' => 'Y',
		'CODE' => 'DATE_TEST',
		'PROPERTY_TYPE' => 'S',
		'USER_TYPE' => 'DateTime',
		'ROW_COUNT' => 1,
		'COL_COUNT' => 30,
		'LIST_TYPE' => 'L',
		'MULTIPLE' => 'N'
		),

		"1" => array(
		'IBLOCK_ID' => $iblock_id,
		'NAME' => GetMessage("SPEEDTEST_SPEED_UP"),
		'ACTIVE' => 'Y',
		'CODE' => 'SPEED_UP',
		'PROPERTY_TYPE' => 'N',
		"MULTIPLE"=>"N"
		),
		"2" => array(
		'IBLOCK_ID' => $iblock_id,
		'NAME' => GetMessage("SPEEDTEST_SPEED_DOWN"),
		'ACTIVE' => 'Y',
		'CODE' => 'SPEED_DOWN',
		'PROPERTY_TYPE' => 'N',
		"MULTIPLE"=>"N"
		)
	
	
		
	);
	$ibp = new CIBlockProperty;
	for ($key = 0, $size = count($arFields); $key < $size; $key++){
	$PropID = $ibp->Add($arFields[$key]);
	}
	}
	
	function AddIB($name, $code, $type){
		$ib = new CIBlock;
		$arFields = Array(
		"ACTIVE" => "Y",
		"NAME" => $name,
		"CODE" => $code,
		"IBLOCK_TYPE_ID" => $type,
		"SITE_ID" => Array("s1")  
		);
		
		

		$ID = $ib->Add($arFields);
		$this-> FillIBlocks($ID);
		return $ID;
		}
	function AddIBType($name, $name_ru)	{
		$result = false;
		$arFields = Array(
				'ID'=>  $name,                                      //GetMessage('IB_PRODUCE_TYPE_NAME'),
				'SECTIONS'=>'Y',
				'IN_RSS'=>'N',
				'SORT'=>100,
					'LANG'=>Array(
						'en'=>Array(
							'NAME'=>$name
									),
						'ru'=>Array(
							'NAME'=>$name_ru                                //GetMessage('IB_PRODUCE_TYPE_NAME_RU')
									),		
								)
						);
	if (CModule::IncludeModule('iblock'))
	$obBlocktype = new CIBlockType;
	$res = CIBlockType::GetByID($name);
	if (!$arres=$res->Fetch())
		{
		$obBlocktype->Add($arFields);
		
		}
	$result = true;
	return $result;
	}
	
	
} //end class
	?>
<?
IncludeModuleLangFile( __FILE__);
if(class_exists("mcart_vacancy")) return;

Class mcart_vacancy extends CModule
{
	var $MODULE_ID = "mcart.vacancy";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_GROUP_RIGHTS = "Y";

	
	function mcart_vacancy() 
	{
		$arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }else{
            $this->MODULE_VERSION=MODULE_VERSION;
            $this->MODULE_VERSION_DATE=MODULE_VERSION_DATE;
        }

        $this->MODULE_NAME = GetMessage("VACANCY_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("VACANCY_MODULE_DESCRIPTION");
        
        $this->PARTNER_NAME = GetMessage("VACANCY_PARTNER_NAME");
        $this->PARTNER_URI  = "http://mcart.ru/";
	}

	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("mcart.vacancy"))
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

	
	function InstallDB() {
		
	global $APPLICATION, $step;
		if ($step==5)		
				return false;
	
	if (CModule::IncludeModule('iblock'))
		{	
			
			$isnewiblock = IntVal($_REQUEST["isnewiblock"]);
			if($step < 2)
					{	
						if (phpversion()<"5.2.6")
						{$APPLICATION->IncludeAdminFile(GetMessage("VACANCY_INSTALL_QUESTION"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/install/step_escape.php");
						}
						else
							$APPLICATION->IncludeAdminFile(GetMessage("VACANCY_INSTALL_QUESTION"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/install/step1.php");	
					}
			elseif($step==2)
					{
					if ($isnewiblock==1)
							{	
							$iblock_id	= 	$this->AddIB(GetMessage('VACANCY'), 'vacancy'); 	
							if ($iblock_id)
							
								$step = 4;
							}
					else {$APPLICATION->IncludeAdminFile(GetMessage("VACANCY_STEP2_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/install/step2.php");
						}
					}
			if ($step==4)
			{
				if(!isset($iblock_id))
				$iblock_id = IntVal($_REQUEST["id_iblock"]);
						
				$ib = new CIBlock;
				$ib->Update($iblock_id, array('EDIT_FILE_BEFORE' => '/bitrix/modules/mcart.vacancy/include.php'));		
							
				$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/prolog.php", "w");
				fwrite($handle, '<?define("VACANCY_IBLOCK_ID", '.$iblock_id.');?>');
				fclose($handle);
				
				
				$txt = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/admin/export_vacancies_back.php");
				 $export_vacancies_text = str_replace ( "VACANCY_DOCUMENT_ROOT" , '"'.$_SERVER["DOCUMENT_ROOT"].'"', $txt);
				$handle = fopen($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/admin/export_vacancies.php", "w");
				fwrite($handle, $export_vacancies_text);
				fclose($handle);
				
				
				
						
	
			}
		//return true;
		}
		
		
		RegisterModule("mcart.vacancy");	
		return true;
	
			
	}
	
	function UnInstallDB()
	{
		
		UnRegisterModule("mcart.vacancy");
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
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
	return true;
	}
	
	function UnInstallFiles()
	{	
		DeleteDirFilesEx("/bitrix/components/mcart/vacansii");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		return true;
	}
	
	
	

	
	function AddIB($name, $code, $prod_iblock_id){
	
		$str_type = GetMessage("VACANCY_IBLOCK_TYPE_EN");
		$str_type_ru =GetMessage("VACANCY_IBLOCK_TYPE_RU");
		$db_iblock_type =CIBlockType::GetByID(GetMessage("VACANCY_IBLOCK_TYPE_ID"));
		if (!$db_iblock_type->Fetch())
			{$arFields = Array(
				'ID'=>GetMessage("VACANCY_IBLOCK_TYPE_ID"),
				'SECTIONS'=>'Y',
				'IN_RSS'=>'N',
				'SORT'=>100,
				'LANG'=>Array(
				'en'=>Array(
					'NAME'=>$str_type),
				'ru'=>Array(
					'NAME'=>$str_type_ru)
				)
			);

			$obBlocktype = new CIBlockType;
			if (!$res = $obBlocktype->Add($arFields))
				{
					echo "error:".$obBlocktype->LAST_ERROR;
					return false;
				}	
		}
		
		$ib = new CIBlock;
		$arFields = Array(
		"ACTIVE" => "Y",
		"NAME" => $name,
		"CODE" => $code,
		"IBLOCK_TYPE_ID" => GetMessage("VACANCY_IBLOCK_TYPE_ID"),
		"SITE_ID" => Array("s1") ,
		'WORKFLOW' => 'N',
		//'EDIT_FILE_BEFORE' => '/bitrix/modules/mcart.vacancy/include.php'
		);
		
		if (!($ID = $ib->Add($arFields)))
			{	
				return false;
			}
		else
			{
				$this-> FillIBlocks($ID);
				return $ID;
			}
		}

		
	function FillIBlocks($iblock_id)
	{
	
	$arFields=array(
			"0" =>array(
					"IBLOCK_ID" => $iblock_id,
					"NAME" => GetMessage("VACANCY_FIELD_EMPLOYMENT"),
					"ACTIVE" => "Y",
					"SORT" => 40,
					"CODE" => "employment",
					"PROPERTY_TYPE" => "L",
					"ROW_COUNT" => 4,
					"COL_COUNT" => 30,
					"LIST_TYPE" => "L",
			
						"VALUES" => array(
										"0" => array("VALUE" => GetMessage("VACANCY_FIELD_EMPLOYMENT_VALUE_FULL"),
													  "DEF" => "N",
													  "SORT" => 10),
										"1" => array("VALUE" => GetMessage("VACANCY_FIELD_EMPLOYMENT_VALUE_SOME"),
													  "DEF" => "N",
													  "SORT" => 20),	
										"2" => array("VALUE" => GetMessage("VACANCY_FIELD_EMPLOYMENT_VALUE_TEMPORARLY"),
													  "DEF" => "N",
													  "SORT" => 30),
										"3" => array("VALUE" => GetMessage("VACANCY_FIELD_EMPLOYMENT_VALUE_STAGE"),
													  "DEF" => "N",
													  "SORT" => 40),				  
																
											)
		
						),
			"1" => array(
					"IBLOCK_ID" => $iblock_id,
					"NAME" => GetMessage("VACANCY_FIELD_STATUS"),
					"ACTIVE" => "Y",
					"SORT" => 20,
					"CODE" => "CML2_STATUS",
					"PROPERTY_TYPE" => "L",
					"LIST_TYPE" => "C",
						"VALUES" => array(
										"0" => array("VALUE" => GetMessage("VACANCY_FIELD_STATUS_VALUE_ACTIVE"),
													  "DEF" => "Y",
													  "SORT" => 10),
										"1" => array("VALUE" => GetMessage("VACANCY_FIELD_STATUS_VALUE_ARCHIVE"),
													  "DEF" => "N",
													  "SORT" => 20),			  
																
											)
						),
			"2" => array(
						"IBLOCK_ID" => $iblock_id,
						"NAME" => GetMessage("VACANCY_FIELD_TOP"),
						"ACTIVE" => "Y",
						"SORT" => 30,
						"CODE" => "CML2_TOP",
						"PROPERTY_TYPE" => "L",
						"LIST_TYPE" => "C",
								"VALUES" => array(
										"0" => array("VALUE" => "da",
													  "DEF" => "N",
													  "SORT" => 10),
										
											)
						
						),
			"3" => array(
						"IBLOCK_ID" => $iblock_id,
						"NAME" => GetMessage("VACANCY_FIELD_CONTACT"),
						"ACTIVE" => "Y",
						"SORT" => 10,
						"CODE" => "contact",
						"PROPERTY_TYPE" => "S",
						"MULTIPLE" => "Y",
						"USER_TYPE" => "UserID"
						),
			"4" => array(
						"IBLOCK_ID" => $iblock_id,
						"NAME" => GetMessage("VACANCY_FIELD_SALARY"),
						"ACTIVE" => "Y",
						"SORT" => 15,
						"CODE" => "salary",
						"PROPERTY_TYPE" => "S"
						),
			"5"=> array (
						"IBLOCK_ID" => $iblock_id,
						"NAME" => GetMessage("VACANCY_FIELD_SHEDULE"),
						"ACTIVE" => "Y",
						"SORT" => 45,
						"CODE" => "shedule",
						"PROPERTY_TYPE" => "L",
						"LIST_TYPE" => "L",
						"VALUES" => array(
										"0" => array("VALUE" => GetMessage("VACANCY_FIELD_SHEDULE_VALUE_1"),
													  "DEF" => "N",
													  "SORT" => 10),
										"1" => array("VALUE" => GetMessage("VACANCY_FIELD_SHEDULE_VALUE_2"),
													  "DEF" => "N",
													  "SORT" => 20),
										"2" => array("VALUE" => GetMessage("VACANCY_FIELD_SHEDULE_VALUE_3"),
													  "DEF" => "N",
													  "SORT" => 30),
											)
						
						)
		
	
	);
	
	$ibp = new CIBlockProperty;
	for ($key = 0, $size = count($arFields); $key < $size; $key++){
		$PropID = $ibp->Add($arFields[$key]);}
	}
		
} //end class
	?>	
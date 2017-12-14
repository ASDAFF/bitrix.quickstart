<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$requiredModules = array('highloadblock','iblock');

foreach ($requiredModules as $requiredModule)
{
	if (!CModule::IncludeModule($requiredModule))
	{
		ShowError(GetMessage("F_NO_MODULE"));
		return 0;
	}
}

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

GLOBAL $USER;

error_reporting(1);
header('Content-Type: text/html; charset=utf-8');
 
if(
	isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) {

	function isJson($string) 
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	
	$comm_id = NULL;
	$comm_update_key = NULL;
	
	$ajaxParams = array();
	$ajaxParams['ajax_type'] = htmlspecialchars(strip_tags($_REQUEST['ajax_type']));
	
	$ajaxParams['ID'] = intval($_REQUEST['ec_this_id']);
	$ajaxParams['IBLOCK_ID'] = intval($_REQUEST['ec_this_iblock']);
	$ajaxParams['HLBLOCK_PROP_CODE'] = htmlspecialchars(strip_tags($_REQUEST['ec_this_hlblock_pc']));

	if($ajaxParams['ID'] > 0)
	{
		
		$rsProps = CIBlockElement::GetProperty(
			$ajaxParams['IBLOCK_ID'],
			$ajaxParams['ID'],
			"sort",
			"asc",
			array(
				'CODE' => $ajaxParams['HLBLOCK_PROP_CODE'],
				'ACTIVE' => 'Y'
			)
		);

		$hlblocks = array();
		$reqParams = array();

		while($arProp = $rsProps->Fetch())
		{
			if(!isset($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']) || empty($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
				continue;

			if(!isset($hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']]))
			{
				$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
					array("filter" => array('TABLE_NAME' => $arProp['USER_TYPE_SETTINGS']['TABLE_NAME']))
				)->fetch();

				$hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']] = $hlblock;
			}
			else
			{
				$hlblock = $hlblocks[$arProp['USER_TYPE_SETTINGS']['TABLE_NAME']];
			}

			if (isset($hlblock['ID']))
			{
				if(!isset($reqParams[$hlblock['ID']]))
				{
					$reqParams[$hlblock['ID']] = array();
					$reqParams[$hlblock['ID']]['HLB'] = $hlblock;
				}
				$reqParams[$hlblock['ID']]['VALUES'][] = $arProp['VALUE'];
			}
		}
	
		$arFilter = array();
		
		foreach ($reqParams as $params)
		{
			$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($params['HLB']);
			$entityDataClass = $entity->getDataClass();
			$fieldsList = $entityDataClass::getMap();
			
			if (count($fieldsList) === 1 && isset($fieldsList['ID']))
				$fieldsList = $entityDataClass::getEntity()->getFields();
				
			$arFilter['filter'] = array('UF_XML_ID' => $params['VALUES']);
		}
	
		$arData = array();
		
		foreach($fieldsList as $code=>$arType)
		{
		  
			$temp = NULL;
			$dataType = "string";
			if($code == 'UF_RATING_LIST')
			{
				$temp = strip_tags($_REQUEST[$code]);
				if(isJson($temp) && $temp)
					$arData[$code] = $temp;
					
				continue;
			}
          
            if(method_exists($arType, 'getDataType'))
            {
                $dataType = $arType->getDataType();
            }else
            {
                $dataType = $arType['data_type'];
            }
            			            
			switch($dataType)
			{
				case 'integer':
					$temp = intval($_REQUEST[$code]);
					if($temp)
						$arData[$code] = $temp;
				break;
				case 'string':
					$temp = htmlspecialchars(strip_tags($_REQUEST[$code]));
					if($temp)
						$arData[$code] = $temp;
				break;
				case 'float':
					$temp = floatval($_REQUEST[$code]);
					if($temp)
						$arData[$code] = $temp;
				break;
			}
		}
		
		$comm_id = $arData['ID'];
		unset($arData['ID']);
		
		switch($ajaxParams['ajax_type'])
		{
			case 'update':
				if($comm_id > 0)
				{
					$entityDataClass = $entity->getDataClass();
					$arFilter['filter'] = array('ID' => $comm_id);
					
					$rsPropEnums = $entityDataClass::getList($arFilter);

					while ($arEnum = $rsPropEnums->fetch())
					{	
						foreach($arData as $key => $value) 
						{						
							if (array_key_exists($key, $arEnum)) 
							{
								$arData[$key] = $arEnum[$key];
								$arData[$key]++;
								$comm_update_key = $key;
								break 1;
							}
						}
					}
					
					if($comm_update_key == 'UF_SOCIAL_COMPLAINT')
					{
						if( intval($APPLICATION->get_cookie('ec_'.$comm_update_key.'_'.$comm_id)) ||
							intval($_SESSION['ec_'.$comm_update_key.'_'.$comm_id]) 	)
						{
							echo 'ERROR_REPEAT';
							die();
						}
					}
					else
					{
						if( intval($APPLICATION->get_cookie('ec_'.$comm_id)) ||
							intval($_SESSION['ec_'.$comm_id]) 	)
						{
							echo 'ERROR_REPEAT';
							die();
						}
					}

					
					$res = $entityDataClass::update($comm_id, $arData);
					if(!$res->isSuccess())
					{
						print_r($res->getErrorMessages());
					}
					else
					{
						if($comm_update_key == 'UF_SOCIAL_COMPLAINT')
						{
							$APPLICATION->set_cookie('ec_'.$comm_update_key.'_'.$comm_id, 1, time()+60*60*24*7);
							$_SESSION['ec_'.$comm_update_key.'_'.$comm_id] = 1;
						}
						else
						{
							$APPLICATION->set_cookie('ec_'.$comm_id, 1, time()+60*60*24*7);
							$_SESSION['ec_'.$comm_id] = 1;
						}
						echo 'OK';
					}
				}
			break;
			default:
				$ajaxParams['captcha_word'] = htmlspecialchars(strip_tags($_REQUEST['captcha_word']));
				$ajaxParams['captcha_code'] = htmlspecialchars(strip_tags($_REQUEST['captcha_code']));
				if(!$APPLICATION->CaptchaCheckCode($ajaxParams["captcha_word"], $ajaxParams["captcha_code"]))
				{
					echo 'ERROR_CAPTCHA';
					die();
				}
			
			
				$arData['UF_XML_ID'] = 'comm_'.time().'_'.$USER->GetId();
				$arData['UF_PRODUCT_ID'] = $ajaxParams['ID'];
				$arData['UF_SORT'] = 500;
				
				
				$entityDataClass = $entity->getDataClass();
				$res = $entityDataClass::add($arData);
				if(!$res->isSuccess())
				{
					print_r($res->getErrorMessages());
				}
				else
				{
					$arrFields = array();
					$cur_rating = 0;
					$cur_commCount = 1;
					
					//create array with comments xml_id
					foreach($reqParams as $reqItem)
					{
						foreach($reqItem['VALUES'] as $val)
						{
							$arrFields[] = $val;
						}
					}
					$arrFields[] = $arData['UF_XML_ID'];
					
					//get rating & count comments
					$rsPropEnums = $entityDataClass::getList($arFilter);
					while ($arEnum = $rsPropEnums->fetch())
					{
						$cur_commCount++;
						if($arEnum['UF_RATING'] > 0)
							$cur_rating += $arEnum['UF_RATING'];
					}
					
					$cur_rating = $cur_rating/$cur_commCount;

					//update Property
					CIBlockElement::SetPropertyValuesEx(
						$ajaxParams['ID'], 
						$ajaxParams['IBLOCK_ID'], 
						array($ajaxParams['HLBLOCK_PROP_CODE']=>$arrFields));
					CIBlockElement::SetPropertyValuesEx(
						$ajaxParams['ID'], 
						$ajaxParams['IBLOCK_ID'], 
						array('EMARKET_RATING'=>$cur_rating));
					CIBlockElement::SetPropertyValuesEx(
						$ajaxParams['ID'], 
						$ajaxParams['IBLOCK_ID'], 
						array('EMARKET_COMMENTS_COUNT'=>$cur_commCount));		
			
					echo 'OK';
				}
			break;
		}
	}
}
?>
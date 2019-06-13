<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//connect file with functions
require_once "functions.php";

if (isset($_REQUEST['ajaxRequest']) && $_REQUEST['ajaxRequest'] == 'Y')
{
    $APPLICATION->RestartBuffer();
    if (check_bitrix_sessid())
    	include($_SERVER['DOCUMENT_ROOT'].$this->GetPath()."/eventHandler.php");
    else
    	echo '<!--BX_EC_DUBLICATE_ACTION_REQUEST'.bitrix_sessid().'-->'; 
    die();
}

//for ajax initialize
$arResult['PATH_FOR_ASYNC_REQUESTS'] = htmlspecialcharsback(POST_FORM_ACTION_URI);;

//checking if we need to set title
if ($arParams["SET_TITLE"] == "Y")
{
    $APPLICATION->SetTitle(GetMessage('TITLE'));
}

//can send everybody or only to personal
$arResult["CAN_SEND_ALL"]  = $arParams["ALLOW_SEND_ANY_NUM"];

//connect modules 
if (!CModule::IncludeModule('rarus.sms4b') || !CModule::IncludeModule('iblock'))
{
	ShowError(GetMessage('MODULE_INSTALLATION'));
	return;
}
else
{
	
	if (!$_REQUEST['TO_OFFICER']  && !$_REQUEST['TO_DEPARTMENT'] && !$_REQUEST['SIMPLE_SEND'])
	{
	    unset($_SESSION["checking_f5"]);
	}
	
	if (isset($_REQUEST["checking_f5"]) && $_REQUEST["checking_f5"] == $_SESSION["checking_f5"])
	{
   		ShowError(GetMessage("RESEND_ABORTED"));
   		echo "<a href = \"".$APPLICATION->GetCurPage()."\">".GetMessage('BACK_TO_FORM')."</a>";
   		return;
	}
	
	if ($_REQUEST['TO_OFFICER'] || $_REQUEST['TO_DEPARTMENT'] || $_REQUEST['SIMPLE_SEND'])
	{
		$_SESSION["checking_f5"] = $_REQUEST["checking_f5"];
	}
	
	global $SMS4B;
	
	$arResult['ADDRESSES'] = $SMS4B->GetSender();
	$arResult['ADDRESSES_BY_DEFAULT'] = COption::GetOptionString('rarus.sms4b', 'defsenderPublic');
	
	//выбираем все 
	$usersInStructure = array();
	$arFilter = array('ACTIVE' => 'Y');
	$obUser = new CUser();
	$dbUsers = $obUser->GetList(($sort_by = 'last_name'), ($sort_dir = 'asc'), $arFilter, array('SELECT' => array('UF_*'))); 
	while ($arUser = $dbUsers->GetNext())
	{			
			$telephones = array();
			
			if ($arUser["PERSONAL_MOBILE"] != "")
			{
				$telephones[] = $arUser["PERSONAL_MOBILE"];
			}
	}
	
	//making selection for departments, that have depth level 1
	//others will be loaded by AJAX
	$arFilter = array("IBLOCK_CODE" => "departments", "ACTIVE"=>"Y", "CNT_ACTIVE"=>"Y"); 
	$cdbDepartments = CIBlockSection::GetList(Array("left_margin"=>"asc","SORT"=>"ASC"), $arFilter, true);
	
	while($section = $cdbDepartments->NavNext(true, "f_"))
	{
		$sections[] = $section;
	}
	
	$arResult["SECTIONS"] = $sections;
	
	if (strlen($_REQUEST["TO_OFFICER"]) > 0 || strlen($_REQUEST["TO_DEPARTMENT"]) > 0 || strlen($_REQUEST["SIMPLE_SEND"]))
	{
		$errors = array();
		
		if (strlen($_REQUEST["destination"]) > 0)
		{
			$destination = array();
			$destination = $_REQUEST["destination"];
			$destination = preg_replace('/<.+>/', '', $destination);
			
			//здесь у нас уже нормальный массив с номерами
			$formPhones = $SMS4B->parse_numbers($destination);
			
			if (count($formPhones) > 3000)
			{
				$errors[] = GetMessage('TOO_MANY_PHONE_NUMBERS');
			}
			else
			{
				$arResult['allNumbersCount'] = 0;
				//сначала получим сколько у нас всего номеров
				if (!is_array($destination))
				{	
					$destination_numbers = str_replace(array(",","\n"), ";", $destination);
					$arrayNumbers = explode(';',$destination_numbers);
					TrimArr($arrayNumbers);
					$arResult['allNumbersCount'] = count($arrayNumbers);
				}
				else
				{
					TrimArr($destination);
					$arResult['allNumbersCount'] = count($destination); 		    	
				}
							
				$arResult['DOUBLED_NUMBERS'] = intval($arResult['allNumbersCount']) - count($formPhones);
			}
		}
		else
		{
			$errors[] = GetMessage('NO_NUMBERS_FOR_SEND');
		}
		
		
		if (strlen($_REQUEST["message"]) < 1)
		{
			if (strlen($_REQUEST["message"]) > $SMS4B->max_mess_len)
			{
				$errors[] = GetMessage('TOO_LONG_MESSAGE');
			}
			
			$errors[] = GetMessage('NO_MESSAGE_TEXT');
		}
		else
		{
		    $message = $_REQUEST["message"];
		}
		
		//checking begin of the send
		if (!isset($_REQUEST["BEGIN_SEND_AT"]) || $_REQUEST["BEGIN_SEND_AT"] == '' )
		{
			$startUp = "";
		}
		else
		{
			$startUp = $SMS4B->GetFormatDateForSmsForm($_REQUEST["BEGIN_SEND_AT"]);
			
			//checking date
			if ($startUp == -1)
			{
				$errors[] = GetMessage('BAD_DATE_FORMAT');
			}
			
			$timestampStartUp = MakeTimeStamp($_REQUEST["BEGIN_SEND_AT"]);
			$currTimeStamp = time();
			
			if ($timestampStartUp < $currTimeStamp)
			{
				$timestampStartUp = $currTimeStamp;
				$startUp = date("Ymd H:i:s",time()+1);
			}
			
			//chosen date couldn't be better for 10 days
			$timeX = $timestampStartUp - (86400*10);
			if ($timeX > $currTimeStamp)
			{
				$errors[] = GetMessage('DATE_IS_TO_BIG');	
			}
							
		}
			
		//checking actual date for send
		if (!isset($_REQUEST["DATE_ACTUAL"]) || $_REQUEST["DATE_ACTUAL"] == '')
		{
			$dateActual = "";
		}
		else
		{
			$dateActual = $SMS4B->GetFormatDateForSmsForm($_REQUEST["DATE_ACTUAL"]);
			
			if ($dateActual == -1)
			{
				$errors[] = GetMessage('BAD_DATE_FORMAT_FOR_ACTUAL_DATE');
			}
			
			$timestampDateActual = MakeTimeStamp($_REQUEST["DATE_ACTUAL"]);
			
			//getting current time
			$currTimeStamp = time();
			
			if ($timestampDateActual < $currTimeStamp)
			{
				$errors[] = GetMessage('ERROR_IN_DATE');
			}
			
			if ($startUp != "")
			{
				$timeX = $timestampDateActual - 900;
				if ($timeX < $timestampStartUp)
				{
					$errors[] = GetMessage('ERROR_IN_DATE_1');	
				}
			}
			
			$timeX = $timestampDateActual-(86400*14);
			if ($timeX > $currTimeStamp)
			{
				$errors[] = GetMessage('ERROR_IN_DATE_2');	
			} 	
		}
		
		//checking period
		if (!isset($_REQUEST["DATE_FROM_NS"]) || !isset($_REQUEST["DATE_TO_NS"]) || $_REQUEST["DATE_FROM_NS"] == "" ||  $_REQUEST["DATE_TO_NS"] == "" )
		{
			$period = ""; 
		}
		else
		{   
			$formedLeftPart = '';
			$formedRightPart = '';
			
			$dateFromNS = htmlspecialchars($_REQUEST["DATE_FROM_NS"]);
			$dateToNS 	= htmlspecialchars($_REQUEST["DATE_TO_NS"]);
			
			if (ord($dateFromNS) >= 65 && ord($dateFromNS) <= 88 && ord($dateToNS) >= 65 && ord($dateToNS) <= 88)
			{
				//this is left part
				if ($dateToNS == 'X')
				{
					$formedLeftPart = 'A';	
				}
				else
				{
					$formedLeftPart = chr(ord($dateToNS)+1);
				}
				//this is right part
				if ($dateFromNS == 'A')
				{
					$formedRightPart = 'X';	
				}
				else
				{
					$formedRightPart = chr(ord($dateFromNS)-1);
				}
				
				$period = $formedLeftPart.$formedRightPart;
			}
			else
			{
				$errors[] = GetMessage('BAD_FORMAT_FOR_INTERVAL');
			}
		}
		
		if (count($errors) == 0)
		{
			//processing component parameter
			if ($arResult["CAN_SEND_ALL"] == "Y")
			{
				$usersPhones = $SMS4B->parse_numbers($telephones);
				
				$destination = array();
				$trig = false;
				
				foreach ($formPhones as $arIndex)
				{
					if (!in_array($arIndex, $usersPhones))
					{
						if (!$trig)
						{
							$errors[] = GetMessage("SENDING_DENIED");
							$trig = true;
						}
					}
					else
					{
						$destination[] = $arIndex;
					}
				}
			}
			else
			{
				$destination = $formPhones;
			}
			
			if ($_REQUEST['SENDER'] && !empty($_REQUEST['SENDER']))
			{
				$sender = htmlspecialchars($_REQUEST['SENDER']);
			}
			else
			{
				$sender = '';
			}
					
			$result = $SMS4B->SendSmsPack($message, $destination, $sender, $startUp, $dateActual, $period);
			
			$arResult["GOOD_SEND"] = "Y";
			$arResult["WAS_SEND"] = (empty($result["WAS_SEND"]) ? "0" : $result["WAS_SEND"]);
			$arResult["NOT_SEND"] = (empty($result["NOT_SEND"]) ? "0" : $result["NOT_SEND"]);
		}
	
		$arResult["ERRORS"] = $errors;			
	}	
}

$this->IncludeComponentTemplate();
?>
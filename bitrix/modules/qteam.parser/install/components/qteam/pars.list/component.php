<?
/*
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
*/

if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();






$copyDOC_ROOT=$_SERVER["DOCUMENT_ROOT"]; if($_SERVER["DOCUMENT_ROOT"][strlen($_SERVER["DOCUMENT_ROOT"])-1]!='/') $_SERVER["DOCUMENT_ROOT"].='/';
// echo $_SERVER["DOCUMENT_ROOT"];





//-------------- Если включен режим автоматичексого добавления в БД сбрасываем все параметры ----------
if($_GET['auto']>0)
{
 $_SESSION['PARSKEY']='';
 $_SESSION['PARSPAGE']='';
 }





if($_SERVER['REQUEST_METHOD']=="POST") 
{
 if($_POST['PARSKEYTEMPLATE']!='') 
   { 
     $_SESSION['PARSPAGE']=$_POST['PARSPAGETEMPLATE']; 
	 if($_SESSION['PARSKEY']!=$_POST['PARSKEYTEMPLATE']) $_SESSION['PARSPAGE']=1;
	 $_SESSION['PARSKEY']=$_POST['PARSKEYTEMPLATE']; $_SERVER['REQUEST_METHOD']=''; $arParams["KEYPARS"]=trim($_SESSION['PARSKEY']); $PARSERKEY=trim($arParams["KEYPARS"]); 
	 }
 }

// echo $_SESSION['PARSKEY'].'*****************'.$PARSERKEY;

// echo'----<pre>';  print_r($_SESSION['PARSDATA']); echo'</pre>----';


//echo $_SERVER['REQUEST_METHOD'].'------------'; die('************');


if($_SERVER['REQUEST_METHOD']=="POST") 
{
  foreach($_POST as $key=>$vl)
  {
   $snurec=explode('_', $key);
  
   if($snurec[0]!='PARS') continue;
   
   $_SESSION['PARSDATA'][$snurec[2]][$snurec[1]]= $vl;  
   
   } 
 }









/*
$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");
$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if(strlen($arParams["EVENT_NAME"]) <= 0)
	$arParams["EVENT_NAME"] = "FEEDBACK_FORM";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if(strlen($arParams["EMAIL_TO"]) <= 0)
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if(strlen($arParams["OK_TEXT"]) <= 0)
	$arParams["OK_TEXT"] = GetMessage("MF_OK_MESSAGE");



if($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_POST["submit"]) > 0)
{
	if(check_bitrix_sessid())
	{
		if(empty($arParams["REQUIRED_FIELDS"]) || !in_array("NONE", $arParams["REQUIRED_FIELDS"]))
		{
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["user_name"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_NAME");		
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["user_email"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_EMAIL");
			if((empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])) && strlen($_POST["MESSAGE"]) <= 3)
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_REQ_MESSAGE");
		}
		if(strlen($_POST["user_email"]) > 1 && !check_email($_POST["user_email"]))
			$arResult["ERROR_MESSAGE"][] = GetMessage("MF_EMAIL_NOT_VALID");
		if($arParams["USE_CAPTCHA"] == "Y")
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTCHA_WRONG");
			}
			else
				$arResult["ERROR_MESSAGE"][] = GetMessage("MF_CAPTHCA_EMPTY");

		}			
		if(empty($arResult))
		{
			$arFields = Array(
				"AUTHOR" => $_POST["user_name"],
				"AUTHOR_EMAIL" => $_POST["user_email"],
				"EMAIL_TO" => $arParams["EMAIL_TO"],
				"TEXT" => $_POST["MESSAGE"],
			);
			if(!empty($arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($arParams["EVENT_MESSAGE_ID"] as $v)
					if(IntVal($v) > 0)
						CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
			}
			else
				CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);
			$_SESSION["MF_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
			$_SESSION["MF_EMAIL"] = htmlspecialcharsEx($_POST["user_email"]);
			LocalRedirect($APPLICATION->GetCurPageParam("success=Y", Array("success")));
		}
		
		$arResult["MESSAGE"] = htmlspecialcharsEx($_POST["MESSAGE"]);
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_POST["user_name"]);
		$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($_POST["user_email"]);
	}
	else
		$arResult["ERROR_MESSAGE"][] = GetMessage("MF_SESS_EXP");
		
}
elseif($_REQUEST["success"] == "Y")
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}







if(empty($arResult["ERROR_MESSAGE"]))
{
	if($USER->IsAuthorized())
	{
		$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($USER->GetFullName());
		$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($USER->GetEmail());
	}
	else
	{
		if(strlen($_SESSION["MF_NAME"]) > 0)
			$arResult["AUTHOR_NAME"] = htmlspecialcharsEx($_SESSION["MF_NAME"]);
		if(strlen($_SESSION["MF_EMAIL"]) > 0)
			$arResult["AUTHOR_EMAIL"] = htmlspecialcharsEx($_SESSION["MF_EMAIL"]);
	}
}



if($arParams["USE_CAPTCHA"] == "Y") $arResult["capCode"] =  htmlspecialchars($APPLICATION->CaptchaGetCode());


*/





//$PARSERTYPEIB="mxs";
//$PARSERCODEIB="avmdl";

if($_GET['prsgetlisttmpl']=='y') $_SESSION['PARSKEY']='';

$PARSERKEY=trim($arParams["KEYPARS"]);
if($_SESSION['PARSKEY']!='') { $PARSERKEY=$_SESSION['PARSKEY']; $arParams["KEYPARS"]=trim($_SESSION['PARSKEY']); }
$_SESSION['PARSKEY']=$PARSERKEY;

if($_SESSION['PARSPAGE']>0) { } else $_SESSION['PARSPAGE']=1;
$getcntpg=10; if($arParams["GET_COUNT_NEWS"]>0) $getcntpg=$arParams["GET_COUNT_NEWS"]; if($getcntpg>50) $getcntpg=10;


$PARSERIB=$arParams["IBLOCK_ID"];

$PARSSECTION=(($arParams["PARENT_SECTION"]>0)? $arParams["PARENT_SECTION"] : 0);



$PARSER_IMGANONSADD=(($arParams["IMG_ANONS_ADD"]=='Y')? 'Y' : '');

$PARSER_IMGDETAILADD=(($arParams["IMG_DETAIL_ADD"]=='Y')? 'Y' : '');
$PARSER_IMGDETAILCREATEFROMANONS=(($arParams["IMG_DETAIL_CREATE_FROM_ANONS"]=='Y')? 'Y' : '');
$PARSER_IMGANONSCREATEFROMDETAIL=(($arParams["IMG_ANONS_CREATE_FROM_DETAIL"]=='Y')? 'Y' : '');

//$PARSER_SOURCEADD=(($arParams["SOURCE_ADD"]=='Y')? 'Y' : '');
$PARSER_SOURCEADD=((strlen($arParams["SOURCE_ADD"])>0)? $arParams["SOURCE_ADD"] : '');

$PARSER_DELHREF=(($arParams["DEL_HREF"]=='Y')? 'Y' : '');

if($arParams["CONVERT_TEXT"]>0) $_SESSION['CONVERT_TEXT']=$arParams["CONVERT_TEXT"];
$PARSER_CONVERT_TEXT=$arParams["CONVERT_TEXT"];

$PARSER_TXTANONS_FROMTXTDETAIL=(($arParams["TXTANONS_FROMTXTDETAIL"]=='Y')? 'Y' : '');


$PARSER_IMG_ANONS_FROM_DETAILTEXT=(($arParams["IMG_ANONS_CREATE_FROM_DETAILTEXT"]=='Y')? 'Y' : '');



$AddInDB=true;


// echo'<pre>'; print_r($arParams); echo'</pre>';










function PARS_win_utf8($s){
$s= strtr ($s, array (GetMessage("QTEAM_PARSER_A")=>"xD0xB0", GetMessage("QTEAM_PARSER_A1")=>"xD0x90",GetMessage("QTEAM_PARSER_B")=>"xD0xB1", GetMessage("QTEAM_PARSER_B1")=>"xD0x91", GetMessage("QTEAM_PARSER_V")=>"xD0xB2", GetMessage("QTEAM_PARSER_V1")=>"xD0x92", GetMessage("QTEAM_PARSER_G")=>"xD0xB3", GetMessage("QTEAM_PARSER_G1")=>"xD0x93", GetMessage("QTEAM_PARSER_D")=>"xD0xB4", GetMessage("QTEAM_PARSER_D1")=>"xD0x94", GetMessage("QTEAM_PARSER_E")=>"xD0xB5", GetMessage("QTEAM_PARSER_E1")=>"xD0x95", GetMessage("QTEAM_PARSER_E2")=>"xD1x91", GetMessage("QTEAM_PARSER_E3")=>"xD0x81", GetMessage("QTEAM_PARSER_J")=>"xD0xB6", GetMessage("QTEAM_PARSER_J1")=>"xD0x96", GetMessage("QTEAM_PARSER_Z")=>"xD0xB7", GetMessage("QTEAM_PARSER_Z1")=>"xD0x97", GetMessage("QTEAM_PARSER_I")=>"xD0xB8", GetMessage("QTEAM_PARSER_I1")=>"xD0x98", GetMessage("QTEAM_PARSER_Y")=>"xD0xB9", GetMessage("QTEAM_PARSER_Y1")=>"xD0x99", GetMessage("QTEAM_PARSER_K")=>"xD0xBA", GetMessage("QTEAM_PARSER_K1")=>"xD0x9A", GetMessage("QTEAM_PARSER_L")=>"xD0xBB", GetMessage("QTEAM_PARSER_L1")=>"xD0x9B", GetMessage("QTEAM_PARSER_M")=>"xD0xBC", GetMessage("QTEAM_PARSER_M1")=>"xD0x9C", GetMessage("QTEAM_PARSER_N")=>"xD0xBD", GetMessage("QTEAM_PARSER_N1")=>"xD0x9D", GetMessage("QTEAM_PARSER_O")=>"xD0xBE", GetMessage("QTEAM_PARSER_O1")=>"xD0x9E", GetMessage("QTEAM_PARSER_P")=>"xD0xBF", GetMessage("QTEAM_PARSER_P1")=>"xD0x9F", GetMessage("QTEAM_PARSER_R")=>"xD1x80", GetMessage("QTEAM_PARSER_R1")=>"xD0xA0", GetMessage("QTEAM_PARSER_S")=>"xD1x81", GetMessage("QTEAM_PARSER_S1")=>"xD0xA1", GetMessage("QTEAM_PARSER_T")=>"xD1x82", GetMessage("QTEAM_PARSER_T1")=>"xD0xA2", GetMessage("QTEAM_PARSER_U")=>"xD1x83", GetMessage("QTEAM_PARSER_U1")=>"xD0xA3", GetMessage("QTEAM_PARSER_F")=>"xD1x84", GetMessage("QTEAM_PARSER_F1")=>"xD0xA4", GetMessage("QTEAM_PARSER_H")=>"xD1x85", GetMessage("QTEAM_PARSER_H1")=>"xD0xA5", GetMessage("QTEAM_PARSER_C")=>"xD1x86", GetMessage("QTEAM_PARSER_C1")=>"xD0xA6", GetMessage("QTEAM_PARSER_C2")=>"xD1x87", GetMessage("QTEAM_PARSER_C3")=>"xD0xA7", GetMessage("QTEAM_PARSER_S2")=>"xD1x88", GetMessage("QTEAM_PARSER_S3")=>"xD0xA8", GetMessage("QTEAM_PARSER_S4")=>"xD1x89", GetMessage("QTEAM_PARSER_S5")=>"xD0xA9", GetMessage("QTEAM_PARSER_Q")=>"xD1x8A", GetMessage("QTEAM_PARSER_Q1")=>"xD0xAA", GetMessage("QTEAM_PARSER_Y2")=>"xD1x8B", GetMessage("QTEAM_PARSER_Y3")=>"xD0xAB", GetMessage("QTEAM_PARSER_Q2")=>"xD1x8C", GetMessage("QTEAM_PARSER_Q3")=>"xD0xAC", GetMessage("QTEAM_PARSER_E4")=>"xD1x8D", GetMessage("QTEAM_PARSER_E5")=>"xD0xAD", GetMessage("QTEAM_PARSER_U2")=>"xD1x8E", GetMessage("QTEAM_PARSER_U3")=>"xD0xAE", GetMessage("QTEAM_PARSER_A2")=>"xD1x8F", GetMessage("QTEAM_PARSER_A3")=>"xD0xAF"));
return $s;
}




function PARS_utf8_win($s){
$s= strtr ($s, array ("xD0xB0"=>GetMessage("QTEAM_PARSER_A"), "xD0x90"=>GetMessage("QTEAM_PARSER_A1"), "xD0xB1"=>GetMessage("QTEAM_PARSER_B"), "xD0x91"=>GetMessage("QTEAM_PARSER_B1"), "xD0xB2"=>GetMessage("QTEAM_PARSER_V"), "xD0x92"=>GetMessage("QTEAM_PARSER_V1"), "xD0xB3"=>GetMessage("QTEAM_PARSER_G"), "xD0x93"=>GetMessage("QTEAM_PARSER_G1"), "xD0xB4"=>GetMessage("QTEAM_PARSER_D"), "xD0x94"=>GetMessage("QTEAM_PARSER_D1"), "xD0xB5"=>GetMessage("QTEAM_PARSER_E"), "xD0x95"=>GetMessage("QTEAM_PARSER_E1"), "xD1x91"=>GetMessage("QTEAM_PARSER_E2"), "xD0x81"=>GetMessage("QTEAM_PARSER_E3"), "xD0xB6"=>GetMessage("QTEAM_PARSER_J"), "xD0x96"=>GetMessage("QTEAM_PARSER_J1"), "xD0xB7"=>GetMessage("QTEAM_PARSER_Z"), "xD0x97"=>GetMessage("QTEAM_PARSER_Z1"), "xD0xB8"=>GetMessage("QTEAM_PARSER_I"), "xD0x98"=>GetMessage("QTEAM_PARSER_I1"), "xD0xB9"=>GetMessage("QTEAM_PARSER_Y"), "xD0x99"=>GetMessage("QTEAM_PARSER_Y1"), "xD0xBA"=>GetMessage("QTEAM_PARSER_K"), "xD0x9A"=>GetMessage("QTEAM_PARSER_K1"), "xD0xBB"=>GetMessage("QTEAM_PARSER_L"), "xD0x9B"=>GetMessage("QTEAM_PARSER_L1"), "xD0xBC"=>GetMessage("QTEAM_PARSER_M"), "xD0x9C"=>GetMessage("QTEAM_PARSER_M1"), "xD0xBD"=>GetMessage("QTEAM_PARSER_N"), "xD0x9D"=>GetMessage("QTEAM_PARSER_N1"), "xD0xBE"=>GetMessage("QTEAM_PARSER_O"), "xD0x9E"=>GetMessage("QTEAM_PARSER_O1"), "xD0xBF"=>GetMessage("QTEAM_PARSER_P"), "xD0x9F"=>GetMessage("QTEAM_PARSER_P1"), "xD1x80"=>GetMessage("QTEAM_PARSER_R"), "xD0xA0"=>GetMessage("QTEAM_PARSER_R1"), "xD1x81"=>GetMessage("QTEAM_PARSER_S"), "xD0xA1"=>GetMessage("QTEAM_PARSER_S1"), "xD1x82"=>GetMessage("QTEAM_PARSER_T"), "xD0xA2"=>GetMessage("QTEAM_PARSER_T1"), "xD1x83"=>GetMessage("QTEAM_PARSER_U"), "xD0xA3"=>GetMessage("QTEAM_PARSER_U1"), "xD1x84"=>GetMessage("QTEAM_PARSER_F"), "xD0xA4"=>GetMessage("QTEAM_PARSER_F1"), "xD1x85"=>GetMessage("QTEAM_PARSER_H"), "xD0xA5"=>GetMessage("QTEAM_PARSER_H1"), "xD1x86"=>GetMessage("QTEAM_PARSER_C"), "xD0xA6"=>GetMessage("QTEAM_PARSER_C1"), "xD1x87"=>GetMessage("QTEAM_PARSER_C2"), "xD0xA7"=>GetMessage("QTEAM_PARSER_C3"), "xD1x88"=>GetMessage("QTEAM_PARSER_S2"), "xD0xA8"=>GetMessage("QTEAM_PARSER_S3"), "xD1x89"=>GetMessage("QTEAM_PARSER_S4"), "xD0xA9"=>GetMessage("QTEAM_PARSER_S5"), "xD1x8A"=>GetMessage("QTEAM_PARSER_Q"), "xD0xAA"=>GetMessage("QTEAM_PARSER_Q1"), "xD1x8B"=>GetMessage("QTEAM_PARSER_Y2"), "xD0xAB"=>GetMessage("QTEAM_PARSER_Y3"), "xD1x8C"=>GetMessage("QTEAM_PARSER_Q2"), "xD0xAC"=>GetMessage("QTEAM_PARSER_Q3"), "xD1x8D"=>GetMessage("QTEAM_PARSER_E4"), "xD0xAD"=>GetMessage("QTEAM_PARSER_E5"), "xD1x8E"=>GetMessage("QTEAM_PARSER_U2"), "xD0xAE"=>GetMessage("QTEAM_PARSER_U3"), "xD1x8F"=>GetMessage("QTEAM_PARSER_A2"), "xD0xAF"=>GetMessage("QTEAM_PARSER_A3")));
return $s;
}






function PARS_GetDateFromStr($date)
{

/*
   $tdate='<div>13 <b>марта</b> 2014, 14:42 (мск)</div>'; 
   $tdate=strip_tags($tdate);
//  $date = preg_match('/^([1-31])\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{4})$/', $date);
  preg_match('/^([1-31])\s(Января|января|Февраля|февраля|Марта|марта|Апреля|апреля|Мая|мая|Июня|июня|Июля|июля|Августа|августа|Сентября|сентября|Октября|октября|Ноября|ноября|Декабря|декабря)\s(\d{4})$/', $tdate, $date);

echo 'dt=|'.$tdate.'|'.$date[0].$date[1].'<br />';
*/

$rdt='';

// $date= "<b>23 января 2014, 23:58 </b>"; //гггг.мм.дд

$date=trim(strip_tags($date));

if (preg_match("/[0-9]{4}\.[0-9]{2}\.[0-9]{2}/i",$date, $rdate))
{
 $rdt=$rdate[0];
//echo'1. ----<pre>';  print_r($rdate); echo'</pre>----';
 }

if (preg_match("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/i",$date, $rdate))
{
 $rdt=$rdate[0];
//echo'2. ----<pre>';  print_r($rdate); echo'</pre>----';
 }


if (preg_match("/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/i",$date, $rdate))
{
 $rdt=$rdate[0];
// echo'2.5 ----<pre>';  print_r($rdate); echo'</pre>----';
 }


if (preg_match("/[0-9]{2}\s[0-9]{2}\s[0-9]{4}/i",$date, $rdate))
{
 $rdt=$rdate[0];
// echo'3. ----<pre>';  print_r($rdate); echo'</pre>----';
 }


if (preg_match("/[0-9]{2}\s(".GetMessage("QTEAM_PARSER_ANVARA_ANVARA_FEVRAL"),$date, $rdate))
{
 $rdt=$rdate[0];
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_ANVARA"), '.01.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_ANVARA1"), '.01.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_FEVRALA"), '.02.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_FEVRALA1"), '.02.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_MARTA"), '.03.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_MARTA1"), '.03.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_APRELA"), '.04.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_APRELA1"), '.04.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_MAA"), '.05.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_MAA1"), '.05.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_IUNA"), '.06.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_IUNA1"), '.06.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_IULA"), '.07.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_IULA1"), '.07.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_AVGUSTA"), '.08.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_AVGUSTA1"), '.08.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_SENTABRA"), '.09.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_SENTABRA1"), '.09.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_OKTABRA"), '.10.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_OKTABRA1"), '.10.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_NOABRA"), '.11.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_NOABRA1"), '.11.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_DEKABRA"), '.12.', $rdt);
 $rdt=str_replace(' '.GetMessage("QTEAM_PARSER_DEKABRA1"), '.12.', $rdt);


// echo'4. ----<pre>';  print_r($rdt); echo'</pre>----';
 }



$rtm='';
if (preg_match("/[0-9]{2}\:[0-9]{2}/i",$date, $rdate))
{
 $rtm=$rdate[0];
// echo'5. ----<pre>';  print_r($rdate); echo'</pre>----';
 }

$dttm='';
if($rdt!='') $dttm=$rdt;
if(($dttm!='')&&($rtm!='')) $dttm.=' '.$rtm;
if($dttm=='') $dttm=date("d.m.Y H:i:s");

return $dttm;
}








$hstnm=$_SERVER["HTTP_HOST"]; $ahstnm=explode(':', $hstnm); $hstnm=$ahstnm[0]; $hstnm=str_replace('www.', '', $hstnm);
function PARS_ConvrtStr($tstr)
{

// global $PARSER_CONVERT_TEXT;


// echo $_SESSION['CONVERT_TEXT'].'|'; 



 if($_SESSION['CONVERT_TEXT']==1) 
   $tstr=iconv('UTF-8', 'windows-1251', $tstr);
 else  
   if($_SESSION['CONVERT_TEXT']==2) 
     $tstr=iconv('windows-1251', 'UTF-8', $tstr);
	 
	 
	 
	 
	 
 return	$tstr; 


/*
   $rstr= iconv('UTF-8','windows-1251', $tstr);
   $drstr=$rstr;

   $drstr=substr($drstr, 0, 50);
 

   $engal='1234567890ABCDEFGHIJGKLMNOPQRSTYVWXZabcdefghijklmnopqrstyvwxz:-+';
   for($is=0; $is<(strlen($engal)-1); $is++) $drstr=str_replace($engal[$is], '', $drstr);
   if(strlen($drstr)>1) { return $rstr; } else return $tstr;
*/
 }




function PARS_AutoConvrtStr($tstr)
{
  if(ord(GetMessage("QTEAM_PARSER_F1"))==208) return $tstr;

   $rstr= iconv('UTF-8','windows-1251', $tstr);
   $drstr=$rstr;
   $drstr=strip_tags($drstr);
   $drstr=substr($drstr, 0, 70);
 

//   $engal='1234567890ABCDEFGHIJGKLMNOPQRSTYVWXZabcdefghijklmnopqrstyvwxz:-./+*;?[] ';
   $engal='1234567890ABCDEFGHIJGKLMNOPQRSTYVWXZabcdefghijklmnopqrstyvwxz'.GetMessage("QTEAM_PARSER_").'<\'"=_>[] ';
   for($is=0; $is<(strlen($engal)-1); $is++) $drstr=str_replace($engal[$is], '', $drstr);
   if(strlen($drstr)>3) { return $rstr; } else return $tstr;

 }






// echo '----------- 15 марта 2014, 16:06 (мск) --------------'.PARS_GetDateFromStr('15 марта 2014, 16:06 (мск) ').'---------------<br>';




function PARS_remove_dir($current_dir, $delfolder=true) {
    
        if($dir = opendir($current_dir)) {
            while (($f = readdir($dir)) !== false) {
                if($f > '0' and filetype($current_dir.$f) == "file") {
                    unlink($current_dir.$f);
                } elseif($f > '0' and filetype($current_dir.$f) == "dir") {
                    PARS_remove_dir($current_dir.$f."/");
                }
            }
            closedir($dir);
			
            if($delfolder) rmdir($current_dir);
        }
    }





function PARS_RplTegHTML($txt)

{
 for($i=0; $i<4; $i++) $txt=str_replace("  ", " ", $txt);

 $txt=str_replace(chr(13),"", $txt);
 $txt=str_replace(chr(10),"", $txt);




 $txt=str_replace("&amp;quot;","&quot;", $txt);

 $txt=str_replace("&amp;lt;","&lt;", $txt);

 $txt=str_replace("&amp;qt;","&qt;", $txt);

 $txt=str_replace("&amp;laquo;","&laquo;", $txt);

 $txt=str_replace("&amp;raquo;","&raquo;", $txt);

 $txt=str_replace("&lt;br&gt;","<br>", $txt);







 

 return $txt;

 }







function PARS_striptags($string,$tag,$intag = FALSE)
{
 $s = preg_quote($tag,'~');
 return preg_replace('~<\s*'.$s.'(\s.*?)?>'.($intag?'.*?':'|').'</'.$s.'>~si','',$string);
} 





$PARSERKEY.='&hst='.$hstnm;
function PARS_ClearTags($data)
{

  $data=PARS_striptags($data,'style',TRUE);
  $data=PARS_striptags($data,'STYLE',TRUE);
  $data=PARS_striptags($data,'script',TRUE);
  $data=PARS_striptags($data,'SCRIPT',TRUE);

/*
    // Убираем из текста все теги кроме <td <tr </tr  </td оставляем только текст
    preg_match_all('#<[^>]*>#', $data, $tags); 
	foreach($tags[0] as $i) 
	 {

//echo 'i='.strpos($i, '<td').'='.$i;

   
//                if( (strpos($i, '<br')!==false) || (strpos($i, '<BR')!==false) || (strpos($i, '<em')!==false) || (strpos($i, '</em')!==false)|| (strpos($i, '<EM')!==false) || (strpos($i, '</EM')!==false)||(strpos($i, '<strong')!==false) || (strpos($i, '</strong')!==false)|| (strpos($i, '<b')!==false) || (strpos($i, '</b')!==false)|| (strpos($i, '<B')!==false) || (strpos($i, '</B')!==false)||(strpos($i, '<i')!==false) || (strpos($i, '</i')!==false)||(strpos($i, '<I')!==false) || (strpos($i, '</I')!==false)|| (strpos($i, '<td')!==false) || (strpos($i, '</td')!==false)|| (strpos($i, '</tr')!==false)  || (strpos($i, '<TD')!==false) || (strpos($i, '</TD')!==false)|| (strpos($i, '</TR')!==false)) 
//                if((strpos($i, '<img')!==false) || (strpos($i, '<IMG')!==false) || (strpos($i, '<p')!==false)  || (strpos($i, '<P')!==false) || (strpos($i, '</p')!==false)|| (strpos($i, '</P')!==false) || (strpos($i, '<object')!==false)|| (strpos($i, '<OBJECT>')!==false)|| (strpos($i, '</object>')!==false)|| (strpos($i, '</OBJECT>')!==false) ) 
                if((strpos($i, '<style')!==false) || (strpos($i, '<STYLE')!==false) || (strpos($i, '</style')!==false)  || (strpos($i, '</STYLE')!==false) || (strpos($i, '<script')!==false)|| (strpos($i, '<SCRIPT')!==false) || (strpos($i, '</script')!==false)|| (strpos($i, '</SCRIPT')!==false) ) 
                 {  $data=str_replace($i, '', $data); }
				 else
				 {  }
	   }
*/

 return $data;
} 
 













function PARS_delteg($vl)
{
	// Убираем из текста все теги в массив $tagList
	$data=$vl;
	preg_match_all('#<[^>]*>#', $data, $tags); 
	array_unique($tags);
	$tagList=array(); 
	$k=0;
	foreach($tags[0] as $i) 
	{
		$k++;
		$tagList[$k]=$i;
		$data=str_replace($i, '', $data);
//		echo'i='.htmlspecialchars($i).'<br> tg='.htmlspecialchars($tags[0]).'<br>';

	}
	
  $data= strip_tags($data);
  $data=str_replace(chr(10), '', $data);
  $data=str_replace(chr(13), '', $data);
  $data=str_replace(chr(160), ' ', $data);
  for($i=0; $i<6; $i++ ) $data=str_replace('  ', ' ', $data);
  $data=trim($data);	
  return $data;	
 }



function PARS_GetLnk($cnt, $smb, $sme, $extcmp="", $extcmp2="", $extcmpNO="", $offs=1)
{
 $res=array();

/*
 $FlagRec=false;
 $lnk="";
 for($i=0; $i<strlen($cnt); $i++)
  {
   if(($cnt[$i]==$smb)&&($FlagRec)) 
    {
     $res[]=$lnk;
     $lnk="";
     $FlagRec=false;
     $i++; 
     }

   if($FlagRec) $lnk.=$cnt[$i]; 

   if(($cnt[$i]==$smb)&&(!$FlagRec)) $FlagRec=true;
   }
*/






 $ends=$offs;
 while($ends>0)
 { 
    if(($ps=strpos($cnt, $smb, $ends))>1)
     {
      $begs=$ps+strlen($smb);      
      $ends=$ps+strlen($smb);
      if(($ps=strpos($cnt, $sme, $begs))>1)
       {
        $ends=$ps+strlen($sme);

        $st=substr($cnt, $begs, $ends-strlen($sme)-$begs);

        if(($extcmp=="") || ( ($extcmp!="")&&(strpos($st, $extcmp)!==false) )) 
//         if(($extcmp2=="") || ( ($extcmp2!="")&&(strpos($st, $extcmp2)!==false) )) 
//          if(($extcmpNO=="") || ( ($extcmpNO!="")&&(strpos($st, $extcmpNO)===false) )) 
		    $res[]=$st; 
        }
      else
        $ends=0;
      }
     else
      $ends=0;
  }



 return $res;
 }










function PARS_rus2translit($string) {

    $converter = array(

        GetMessage("QTEAM_PARSER_A") => 'a',   GetMessage("QTEAM_PARSER_B") => 'b',   GetMessage("QTEAM_PARSER_V") => 'v',

        GetMessage("QTEAM_PARSER_G") => 'g',   GetMessage("QTEAM_PARSER_D") => 'd',   GetMessage("QTEAM_PARSER_E") => 'e',

        GetMessage("QTEAM_PARSER_E2") => 'e',   GetMessage("QTEAM_PARSER_J") => 'zh',  GetMessage("QTEAM_PARSER_Z") => 'z',

        GetMessage("QTEAM_PARSER_I") => 'i',   GetMessage("QTEAM_PARSER_Y") => 'y',   GetMessage("QTEAM_PARSER_K") => 'k',

        GetMessage("QTEAM_PARSER_L") => 'l',   GetMessage("QTEAM_PARSER_M") => 'm',   GetMessage("QTEAM_PARSER_N") => 'n',

        GetMessage("QTEAM_PARSER_O") => 'o',   GetMessage("QTEAM_PARSER_P") => 'p',   GetMessage("QTEAM_PARSER_R") => 'r',

        GetMessage("QTEAM_PARSER_S") => 's',   GetMessage("QTEAM_PARSER_T") => 't',   GetMessage("QTEAM_PARSER_U") => 'u',

        GetMessage("QTEAM_PARSER_F") => 'f',   GetMessage("QTEAM_PARSER_H") => 'h',   GetMessage("QTEAM_PARSER_C") => 'c',

        GetMessage("QTEAM_PARSER_C2") => 'ch',  GetMessage("QTEAM_PARSER_S2") => 'sh',  GetMessage("QTEAM_PARSER_S4") => 'sch',

        GetMessage("QTEAM_PARSER_Q2") => '\'',  GetMessage("QTEAM_PARSER_Y2") => 'y',   GetMessage("QTEAM_PARSER_Q") => '\'',

        GetMessage("QTEAM_PARSER_E4") => 'e',   GetMessage("QTEAM_PARSER_U2") => 'yu',  GetMessage("QTEAM_PARSER_A2") => 'ya',

        

        GetMessage("QTEAM_PARSER_A1") => 'A',   GetMessage("QTEAM_PARSER_B1") => 'B',   GetMessage("QTEAM_PARSER_V1") => 'V',

        GetMessage("QTEAM_PARSER_G1") => 'G',   GetMessage("QTEAM_PARSER_D1") => 'D',   GetMessage("QTEAM_PARSER_E1") => 'E',

        GetMessage("QTEAM_PARSER_E3") => 'E',   GetMessage("QTEAM_PARSER_J1") => 'Zh',  GetMessage("QTEAM_PARSER_Z1") => 'Z',

        GetMessage("QTEAM_PARSER_I1") => 'I',   GetMessage("QTEAM_PARSER_Y1") => 'Y',   GetMessage("QTEAM_PARSER_K1") => 'K',

        GetMessage("QTEAM_PARSER_L1") => 'L',   GetMessage("QTEAM_PARSER_M1") => 'M',   GetMessage("QTEAM_PARSER_N1") => 'N',

        GetMessage("QTEAM_PARSER_O1") => 'O',   GetMessage("QTEAM_PARSER_P1") => 'P',   GetMessage("QTEAM_PARSER_R1") => 'R',

        GetMessage("QTEAM_PARSER_S1") => 'S',   GetMessage("QTEAM_PARSER_T1") => 'T',   GetMessage("QTEAM_PARSER_U1") => 'U',

        GetMessage("QTEAM_PARSER_F1") => 'F',   GetMessage("QTEAM_PARSER_H1") => 'H',   GetMessage("QTEAM_PARSER_C1") => 'C',

        GetMessage("QTEAM_PARSER_C3") => 'Ch',  GetMessage("QTEAM_PARSER_S3") => 'Sh',  GetMessage("QTEAM_PARSER_S5") => 'Sch',

        GetMessage("QTEAM_PARSER_Q3") => '\'',  GetMessage("QTEAM_PARSER_Y3") => 'Y',   GetMessage("QTEAM_PARSER_Q1") => '\'',

        GetMessage("QTEAM_PARSER_E5") => 'E',   GetMessage("QTEAM_PARSER_U3") => 'Yu',  GetMessage("QTEAM_PARSER_A3") => 'Ya',

    );

    return strtr($string, $converter);

}



function PARS_str2url($str) {

    // переводим в транслит

    $str = PARS_rus2translit($str);

    // в нижний регистр

    $str = strtolower($str);

    // заменям все ненужное нам на "-"

//////    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);

    // удаляем начальные и конечные '-'

    $str = trim($str, "-");

    return $str;

}






if($_SERVER['REQUEST_METHOD']=="POST") 
{
 if($_POST['PARSKEYTEMPLATE']!='') 
   { 
     $_SESSION['PARSPAGE']=$_POST['PARSPAGETEMPLATE']; 
	 if($_SESSION['PARSKEY']!=$_POST['PARSKEYTEMPLATE']) $_SESSION['PARSPAGE']=1;
	 $_SESSION['PARSKEY']=$_POST['PARSKEYTEMPLATE']; $_SERVER['REQUEST_METHOD']=''; $arParams["KEYPARS"]=trim($_SESSION['PARSKEY']); $PARSERKEY=trim($arParams["KEYPARS"]); 
	 }
 }

// echo $_SESSION['PARSKEY'].'*****************'.$PARSERKEY;

// echo'----<pre>';  print_r($_SESSION['PARSDATA']); echo'</pre>----';


//echo $_SERVER['REQUEST_METHOD'].'------------'; die('************');


if($_SERVER['REQUEST_METHOD']=="POST") 
{



 if(CModule::IncludeModule("iblock"))
  {
/*  
     //----------- выбираем все информационные блоки типа "shop" ---------
     $iblocks = GetIBlockList($PARSERTYPEIB);
     while($arIBlock = $iblocks->GetNext()) //цикл по всем блокам
      {
        if($arIBlock["CODE"]==$PARSERCODEIB)  {  $PARSERIB=$arIBlock["ID"];    }
       }  
*/	   

// echo '----'.$PARSERIB.'<br />';







    $INFO=array();


foreach($_POST as $key=>$vl)
  {
   $nurec=explode('_', $key);
  
   if($nurec[0]!='PIADD') continue;
  



    $TMPArr=array();

   

   

   //------------------ Создаем директорию для хранения изображений ----------------


     // удаление файлов из папки /images/temp
    PARS_remove_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp", false); $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_PAPKA_S_IZOBRAJENIAM"); 




//	 $path = '/opt/lampp/htdocs/'; // - путь до создаваемой папки.



//     $folder = 'my-documents';     // - имя создаваемой папки.
//     $mode = '0777';               // - права на создаваемую папку.
//     $recursive = true;            // - несуществующие папки будут воссозданы
	 
	 
/*	 
    if(mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parsimgtmp/", $mode, $recursive)==false)
      {
	   $INFO['ERROR'][]='Не удалось создать директорию "'.$_SERVER["DOCUMENT_ROOT"].'upload/parsimgtmp/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]='Директория создана "'.$_SERVER["DOCUMENT_ROOT"].'upload/parsimgtmp/"';
	   }
*/	   




	   	 
	 
//     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser", $mode, $recursive)==false)
     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/", 0777)==false)
      {
	   $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SOZDATQ_D").$_SERVER["DOCUMENT_ROOT"].'upload/parser/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/", 0777);
	   }




     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/", 0777)==false)
      {
	   $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SOZDATQ_D").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/", 0777);
	   }



     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp/", 0777)==false)
      {
	   $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SOZDATQ_D").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp/", 0777);
	   }







//   echo '<br>-----------<br><pre>';  print_r($_SESSION['PARSDATA'][$nurec[1]]); echo'</pre><br>------------<br>';
  
  
  
  
   //-------------------------------------------- Добавляем изображение АНОНСА  -------------------------------
   if( ($PARSER_IMGANONSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']) )
    {
  	  preg_match("#[^/]*$#i", trim($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']), $timatch);
      $tmnim=explode('?', $timatch[0]);
      $tmnim=explode('&', $tmnim[0]);
      $mnim=explode('/', $tmnim[0]);
      $mnim[count($mnim)-1]=PARS_AutoConvrtStr($mnim[count($mnim)-1]);
      $tmnim[0]=trim($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']);



	  


      $file = str_replace(' ', '%20',  $tmnim[0]);
      //$file = $param[0];
      $newfile = $_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1]));

      if(strlen($file)>5)
       {
         if (!copy($file, $newfile)) 
          {  
            $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SKOPIROVA")."<br /> |$file|$newfile|"; 
            $TMPArr["SMALLFILE"]=""; 
            }
         else   
          {
            $TMPArr["SMALLFILE"]='upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1])); 
            $TMPArr["SMALLFILEALT"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME']; 	 
	        }
         }
       else 
        {
          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_NAYDEN_FAYL_IZOBR")."<br>"; 
          $TMPArr["SMALLFILE"]=""; 
          }

	
	
	
	 } // if( ($PARSER_IMGANONSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']) )
  


// if (!copy($file, $newfile))  echo 'error1'; else echo 'true1';
// if (!copy('http://rs.mail.ru/b26986827.jpg', '/home/bitrix/www/upload/parser/images/temp/b26986827.jpg')) echo 'error2'; else echo 'true2';
// die('<br>'.$file.'|'.$newfile.'<br />+++++++++++++++++++++++++++++');




  
  
   //-------------------------------------------- Добавляем изображение Детального описания  -------------------------------
//   if( ($PARSER_IMGDETAILADD)&&($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) )
   if($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) 
    {
	
  	  preg_match("#[^/]*$#i", trim($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']), $timatch);
      $tmnim=explode('?', $timatch[0]);
      $tmnim=explode('&', $tmnim[0]);
      $mnim=explode('/', $tmnim[0]);
      $mnim[count($mnim)-1]=PARS_AutoConvrtStr($mnim[count($mnim)-1]);
      $tmnim[0]=trim($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']);
	
	
/*	
      $tmnim=explode('?', trim($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']));
      $mnim=explode('/', $tmnim[0]);
*/
	

      $file = str_replace(' ', '%20',  $tmnim[0]);
      //$file = $param[0];
      $newfile = $_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1]));

      if(strlen($file)>5)
       {
         if (!copy($file, $newfile)) 
          {  
            $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SKOPIROVA1"); 
            $TMPArr["BIGFILE"]=""; 
            }
         else   
          {
            $TMPArr["BIGFILE"]='upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1])); 
            $TMPArr["BIGFILEALT"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME']; 	 
	        }
         }
       else 
        {
          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_NAYDEN_FAYL_IZOBR1")."<br>"; 
          $TMPArr["BIGFILE"]=""; 
          }

	 } // if( ($PARSER_IMGDETAILSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) )
   else
    {
	  if($PARSER_IMGDETAILCREATEFROMANONS)
	    if($TMPArr["SMALLFILE"])
		 {
           $TMPArr["BIGFILE"]=$TMPArr["SMALLFILE"];
           $TMPArr["BIGFILEALT"]=$TMPArr["SMALLFILEALT"];
		  }
		  
	  } // if( ($PARSER_IMGDETAILSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) )
	  
	  
	if( ($TMPArr["BIGFILE"]!='')  && ($TMPArr["SMALLFILE"]=='') && ($PARSER_IMGANONSCREATEFROMDETAIL) )
	 {
           $TMPArr["SMALLFILE"]=$TMPArr["BIGFILE"];
           $TMPArr["SMALLFILEALT"]=$TMPArr["BIGFILEALT"];
	  }
	  
	  
	  


//    echo '#############'.$_SESSION['PARSDATA'][$nurec[1]]['DATE'].'<br />';

    //-------------------------------------------- Добавляем Дату  -------------------------------
    if($_SESSION['PARSDATA'][$nurec[1]]['DATE']) $TMPArr["DATE"]=PARS_GetDateFromStr($_SESSION['PARSDATA'][$nurec[1]]['DATE']); else $TMPArr["DATE"]=PARS_GetDateFromStr('');

    //-------------------------------------------- Добавляем Название  -------------------------------
    if(strlen(trim($_SESSION['PARSDATA'][$nurec[1]]['HEADPAGE']))>2) $TMPArr["HEAD"]=$_SESSION['PARSDATA'][$nurec[1]]['HEADPAGE']; else $TMPArr["HEAD"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME'];


    //-------------------------------------------- Добавляем АНОНС  -------------------------------
    if($_SESSION['PARSDATA'][$nurec[1]]['ANONS']) $TMPArr["TEXT"]=PARS_ClearTags($_SESSION['PARSDATA'][$nurec[1]]['ANONS']);
    if($_SESSION['PARSDATA'][$nurec[1]]['FPANONS'])
	 { 
	   if($_SESSION['PARSDATA'][$nurec[1]]['ANONS'])
	    {
	      if(strlen($_SESSION['PARSDATA'][$nurec[1]]['FPANONS'])>strlen($_SESSION['PARSDATA'][$nurec[1]]['ANONS'])) $TMPArr["TEXT"]=PARS_ClearTags($_SESSION['PARSDATA'][$nurec[1]]['FPANONS']);   
		  }
		else
		 $TMPArr["TEXT"]=PARS_ClearTags($_SESSION['PARSDATA'][$nurec[1]]['FPANONS']);  
	   }


  if($PARSER_DELHREF)
   {
    $TMPArr["TEXT"]=PARS_striptags($TMPArr["TEXT"],'a');
    $TMPArr["TEXT"]=PARS_striptags($TMPArr["TEXT"],'A');
    }







//---------------------------- Добавление сообщения ---------------------------
$rsarv=$TMPArr;

 
       // Проверка существования записи 
       $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
//       $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "SECTION_ID"=>$_GET["tid"], "INCLUDE_SUBSECTIONS"=>"Y");
       $arFilter = Array("IBLOCK_ID"=>$PARSERIB, "SECTION_ID"=>$PARSSECTION);
       $arFilter["NAME"]=$rsarv["HEAD"];
//       $arFilter["DATE_ACTIVE_FROM"]=$rsarv["DATE"];
	   if(($PARSER_SOURCEADD)&&($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']!=''))  $arFilter['PROPERTY_'.$PARSER_SOURCEADD]=trim($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']);
//     echo '|'.$PARSER_SOURCEADD.'|========|'.$_SESSION['PARSDATA'][$nurec[1]]['SOURCE'].'|<br>';
       $res = CIBlockElement::GetList(Array("ID" => "asc"), $arFilter, false, false, $arSelect);

       $FlagFindRec=0;
       if (intval($res->SelectedRowsCount())>0) 
	    {
              $ar=$res->GetNext();  
              $FlagFindRec=$ar["ID"];
	          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_ZAPISQ_NAYDENA").$rsarv["HEAD"].')!  ID='.$ar["ID"].'  DATE='.$ar["DATE_ACTIVE_FROM"].''; 
		  } 
	   else // теперь если не существует Проверка существования записи 
	    {
	        if(!$AddInDB) $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DOBAVLAEM_ZAPISQ").$rsarv["HEAD"].')!  ID='.$ar["ID"].'  DATE='.$ar["DATE_ACTIVE_FROM"].''; 
		
		






//--------- теги

      $ftitle=str_replace('"', " ", $rsarv["HEAD"]);
      $tegi=str_replace('"', " ", $rsarv["HEAD"]);


      global $APPLICATION;
 
//      $APPLICATION->SetTitle('В черном списке: '.$ftitle);

      $keywrd=PARS_RplTegHTML(str_replace("<br>", "", str_replace(chr(13).chr(10), "<br>", str_replace("&lt;br&gt;","<br>", $tegi))));
      $dscr=$keywrd;
//      $keywrd=substr($keywrd, 0, 150);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_CTO"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NA"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_V2"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_K2"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_ILI"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_JE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_S6"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_ESLI"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_TO"), " ", $keywrd);

      $keywrd=str_replace(" - ", " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_I2"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_VSE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_BY"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NO"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_A4"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_MNE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_TOCNEE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_DLA"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_SEBE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_SEBA"), " ", $keywrd);


      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_BUDET"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_U4"), " ", $keywrd);
      $keywrd=str_replace(" , ", " ", $keywrd);

/*
      $keywrd=str_replace(" ", ", ", $keywrd);

      $keywrd=str_replace(",,", ", ", $keywrd);
      $keywrd=str_replace(", ,", ", ", $keywrd);
*/


      for($tmi=1; $tmi<4; $tmi++) $keywrd=str_replace("  ", " ", $keywrd);

      $takw=explode(' ', $keywrd); 
	  $rskwr=''; foreach($takw as $tkwvl)  {  if(strlen($tkwvl)>1) { if($rskwr!='') $rskwr.=', '; $rskwr.=$tkwvl; }   }  $keywrd= $rskwr;
      
//      $rskwr=''; $flgfirstsmb=false; for($tmi=0; $tmi<strlen($keywrd); $tmi++) { if(($keywrd[$tmi]!='')&&($keywrd[$tmi]!=',')&&($keywrd[$tmi]!=',')) $flgfirstsmb=true;   }


//      $APPLICATION->SetPageProperty("keywords", $keywrd);

      $i=0;

      $FlagEnd=false;
      $resdesc="";
      while(!$FlagEnd)
       {
        $resdesc=$resdesc.$dscr[$i];
        if(($dscr[$i]==" ")&&($i>100)) $FlagEnd=true;

        if(strlen($dscr)<=$i) $FlagEnd=true;
        $i++;
        }
//      $APPLICATION->SetPageProperty("description", 'В черном списке: '.$resdesc);
//end------ теги




          $PROP = array();
//          $PROP["DESCRIPTION"] = $rsarv["TEXT"];  
//          $PROP["source"] = $newssource; 
//          $PROP["THEMES"] = $newsthemes; 
          $PROP["KEYWORDS"] = $keywrd;  

          


          $tmpCODE=str_replace("'", " ", $rsarv["HEAD"]);
          $tmpCODE=str_replace('"', ' ', $tmpCODE);
		  $tmpCODE=PARS_str2url($tmpCODE);
		  
	
          $tmPARSSECTION=$PARSSECTION;
          if($_POST["PARENTSECT_".$nurec[1]]>0) $PARSSECTION=$_POST["PARENTSECT_".$nurec[1]];
	

          if($PARSER_DELHREF=='Y') preg_replace ("!<a.*?href=\"?'?([^ \"'>]+)\"?'?.*?>(.*?)</a>!is", "\\2", $rsarv["EXTTEXT"]); 
          if($PARSER_DELHREF=='Y') preg_replace ("!<a.*?href=\"?'?([^ \"'>]+)\"?'?.*?>(.*?)</a>!is", "\\2", $rsarv["TEXT"]); 
  
          $arLoadProductArray = Array(

//           "MODIFIED_BY"    => $arUser["ID"],         // элемент изменен текущим пользователем

           "TAGS"=> $newstags,
           "IBLOCK_SECTION" => $PARSSECTION,          // элемент лежит в указанном разделе
           "IBLOCK_ID"      => $PARSERIB,
		   
           "PROPERTY_VALUES"=> $PROP,

		   
           "NAME"           => $rsarv["HEAD"],
		   
           "PREVIEW_TEXT"   => $rsarv["TEXT"],
		   "PREVIEW_TEXT_TYPE"=> 'html',
		   
//           "DETAIL_TEXT"    => PARS_ClearTags($rsarv["EXTTEXT"]),
		   "DETAIL_TEXT_TYPE"=> 'html',
		   "CODE"=> substr(str_replace('"', '', str_replace(':', '', str_replace("'", '', str_replace(' ', '_', trim($tmpCODE))))),0, 60),
		   
		   
///           "DETAIL_PICTURE" => (CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["BIGFILE"])),
///           "PREVIEW_PICTURE" => (CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["SMALLFILE"])),

           //  "uid" => $USER->GetID();

           );
		   


      $PARSSECTION=$tmPARSSECTION;



	      $arLoadProductArray["ACTIVE"]= "Y";
 
	      $arLoadProductArray["DATE_ACTIVE_FROM"] = $rsarv["DATE"];
		   

//    if($rsarv["BIGFILE"]!='') $arLoadProductArray["DETAIL_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["BIGFILE"]);
//    if($rsarv["SMALLFILE"]!='') $arLoadProductArray["PREVIEW_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["SMALLFILE"]);




   if($rsarv["HEAD"]=='') { $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_OSIBKA_NE_ZAPOLNENO"); continue; }
    


    if(!$AddInDB)
     { 
//       echo"<br>===NEWS====<pre>"; print_r($arLoadProductArray); echo"</pre>===ENDNEWS====<br>"; 
      }





    if($AddInDB)
     {
	   

         $el = new CIBlockElement;
         if($PRODUCT_ID = $el->Add($arLoadProductArray))
          { 
           $INFO['MESSAGE'][]=$PRODUCT_ID.' '.GetMessage("QTEAM_PARSER_SOOBSENIE_DOBAVLENO").'<br>'; 
           }
        else

          { 
           $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_OSIBKA_PRI_DOBAVLENI").$el->LAST_ERROR.'<br>'; 
           }
		   
		   
     } //  if($AddInDB)
		
		
		
		
		 } // end теперь если не существует Проверка существования записи 
 
 
 
	   



















  // $html - некий html-код некой страницы, \n - это переход на новую строку (верстальщики иногда это делают) 
/// $html = 'Текст <img src="http://rs.mail.ru/b26972665.jpg" style="width:0;height:0;position:absolute;" alt=""/> и снова  конец <img src="//limg.imgsmail.ru/s/images/logo/logo.v3.png" width="195" height="46" class="logo__link__img logo__link__img_medium" alt=""/><img src="//limg.imgsmail.ru/s/images/logo/logo_wide.v3.png" width="211" height="53" class="logo__link__img logo__link__img_wide" alt=""/> qwerty dfgdfgdgf <img src="http://rs.mail.ru/b26986220.jpg" style="width:0;height:0;position:absolute;" alt=""/> dfgdfgdgdgdg';

   $TMPArr["EXTTEXT"]=$_SESSION['PARSDATA'][$nurec[1]]['DETAIL'];
  
   $html =$_SESSION['PARSDATA'][$nurec[1]]['DETAIL'];

  // Вызываем функцию, которая все совпадения помещает в массив $matches 
//  preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $html, $matches);

  preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"]*([^\"'>#]+)[^>]*>/", $html, $matches);

//  preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc]=([^>]+\s#)*>/", $html, $matches);
  $urls = $matches[1]; // Берём то место, где сама ссылка (благодаря группирующим скобкам в регулярном выражении)
  // Выводим все ссылки 
  $IMAGEPARS=array();
  for ($i = 0; $i < count($urls); $i++)
   {
//    echo $urls[$i]."<br />";
	$turl=$urls[$i];
	$turl=trim($turl);
    $turl=str_replace('http://qteam.ru/parser/glypeproxy/browse.php?u=', '', $turl); 
    $turl=str_replace('&amp;b=4', '', $turl); 
	$turl=urldecode($turl);
	
	
	
// echo 'Ссылка на изображение к ссылки первой страницы: ';
	if((substr($turl,0,4)=='http')||(substr($turl,0,2)=='//')) 
	 {  
//echo'1. '.$turl.'<br>';

          } 
	else 
	 {  
//echo'2. '.$turl.'<br>';
	   $taurl=explode('?', $turl); $turl=$taurl[0]; 
	   
       $ittxtv=PARS_ConvrtStr($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']);
	   $ittxtv=str_replace('http://', '', $ittxtv); 
	   $idmn=explode('/', $ittxtv); 
	   $turl=$idmn[0].'/'.$turl;
	   $turl=str_replace('//', '/', $turl); 
	   $turl='http://'.trim($turl);
//echo'2.1 '.$turl.'<br>';

	   }	
	
	
	
	
////	if(substr($turl,0,4)=='http') { } else
////	if(substr($turl,0,2)=='//') { $turl='http:'.$turl; } else {  $taurl=explode('?', $turl); $turl=$taurl[0]; }
//	if($turl) echo '<img src="'.$turl.'" border="0" />'; 
	
	
	
	
	$IMAGEPARS[]=array("SOURCE"=>$urls[$i], "REPLACE"=>$turl, "UPLOAD"=>$upld, "UPLOADFDI"=>'');
	} // for ($i = 0; $i < count($urls); $i++)



     if(count($IMAGEPARS)>0) 
     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/".$PRODUCT_ID."/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/".$PRODUCT_ID."/", 0777))
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/'.$PRODUCT_ID.'/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/".$PRODUCT_ID."/", 0777);
	   }


//echo'--------<pre>'; print_r($IMAGEPARS); echo'</pre>--------------';


    foreach($IMAGEPARS as $key=>$vl)
	 {
	 
      $tmnim=explode('?', trim($vl['REPLACE']));


      $mnim=explode('/', $tmnim[0]);

	

      $file = str_replace(' ', '%20',  $tmnim[0]);

      //$file = $param[0];
      $newfile = $_SERVER["DOCUMENT_ROOT"].'upload/parser/images/'.$PRODUCT_ID.'/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1]));

      if(strlen($file)>5)
       {
         if (!copy($file, $newfile)) 
          {  
            $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SKOPIROVA2"); 
            $IMAGEPARS[$key]["UPLOAD"]=""; 
            }
         else   
          {
            $IMAGEPARS[$key]["UPLOADFDI"]=$newfile; 
            $IMAGEPARS[$key]["UPLOAD"]='/upload/parser/images/'.$PRODUCT_ID.'/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1])); 
//            $TMPArr["BIGFILEALT"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME']; 	 
	        }
         }
       else 
        {
          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_NAYDEN_FAYL_IZOBR2")."<br>"; 
          $IMAGEPARS[$key]["UPLOAD"]=""; 
          }



      //-------------------------------------------- Изменяем ссылки в Детальном описании  -------------------------------

	  $TMPArr["EXTTEXT"]=str_replace($IMAGEPARS[$key]["SOURCE"], $IMAGEPARS[$key]["UPLOAD"], $TMPArr["EXTTEXT"]);
	  } // foreach($IMAGEPARS as $key=>$vl)



    //-------------------------------------------- Добавляем Источник в Детальное описание  -------------------------------

//	if($PARSER_SOURCEADD) $TMPArr["EXTTEXT"].='<p><br /><em>Источник: '.$_SESSION['PARSDATA'][$nurec[1]]['SOURCE'].'</em></p>';
	if($PARSER_SOURCEADD) $arLoadProductArray["PROPERTY_VALUES"][$PARSER_SOURCEADD]=trim($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']);





    if( ($PARSER_IMG_ANONS_FROM_DETAILTEXT=='Y') && (count($IMAGEPARS)>0) && ($rsarv["SMALLFILE"]=='') ) 
	 {
	  foreach($IMAGEPARS as $val_uifd) {  if($val_uifd!='') {  $arLoadProductArray["PREVIEW_PICTURE"]=CFile::MakeFileArray($val_uifd["UPLOADFDI"]); break; }  }
	  } 

    if(($PARSER_IMGDETAILADD)&&($rsarv["BIGFILE"]!='')) $arLoadProductArray["DETAIL_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["BIGFILE"]);
    if($rsarv["SMALLFILE"]!='') $arLoadProductArray["PREVIEW_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["SMALLFILE"]);



//          $TMPArr["EXTTEXT"]=str_replace("width:0;", "", $TMPArr["EXTTEXT"]);
//          $TMPArr["EXTTEXT"]=str_replace("height:0;", "", $TMPArr["EXTTEXT"]);
//          $TMPArr["EXTTEXT"]=str_replace("position:absolute;", "", $TMPArr["EXTTEXT"]);

  $TMPArr["EXTTEXT"]=str_replace('iframe', '**##**', $TMPArr["EXTTEXT"]);
  if($PARSER_DELHREF)
   {
    
    $TMPArr["EXTTEXT"]=PARS_striptags($TMPArr["EXTTEXT"],'a');
    $TMPArr["EXTTEXT"]=PARS_striptags($TMPArr["EXTTEXT"],'A');
    }


	$arLoadProductArray["DETAIL_TEXT"]=PARS_ClearTags($TMPArr["EXTTEXT"]);
    $arLoadProductArray["DETAIL_TEXT"]=str_replace('**##**', 'iframe', $arLoadProductArray["DETAIL_TEXT"]);
//	$arLoadProductArray["DETAIL_TEXT"]=$TMPArr["EXTTEXT"];







    if($AddInDB)
     {

         $el = new CIBlockElement;
         if($el->Update($PRODUCT_ID, $arLoadProductArray))
          { 
           $INFO['MESSAGE'][]=$PRODUCT_ID.' '.GetMessage("QTEAM_PARSER_SOOBSENIE_IZMENENO"); 
           }
        else

          { 
           $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_OSIBKA_PRI_IZMENENII").$el->LAST_ERROR.''; 
           }
		   
		   
     } //  if($AddInDB)





//       echo"<br>===NEWS====<pre>"; print_r($arLoadProductArray); echo"</pre>===ENDNEWS====<br>"; 

//   echo '<br>-----------<br><pre>';  print_r($TMPArr); echo'</pre><br>------------<br>';
//   echo '<br>+++++++++++<br><pre>';  print_r($INFO); echo'</pre><br>++++++++++++++<br>';

//   die('-------------------');



   sleep(1);

   } // foreach($_POST as $key=>$vl)


		$arResult["MESSAGE"] = $INFO['MESSAGE'];
		$arResult["TYPE"] = "SUBMIT";
		$arResult["ERROR"] = $INFO['ERROR'];


  } // if(CModule::IncludeModule("iblock"))
} 
else //  if($REQUEST_METHOD=="POST") 
{

  $_SESSION['PARSDATA']='';
  $INFO=array();


// echo'333333333333---'."http://qteam.ru/parser/getdatafromextsite.php?pkey=".$PARSERKEY.'&numlnk='.$_SESSION['PARSPAGE'].'&countlnk='.$getcntpg;	
	


/*
if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, "http://qteam.ru/parser/getdatafromextsite.php?pkey=".$PARSERKEY.'&numlnk='.$_SESSION['PARSPAGE'].'&countlnk='.$getcntpg);
    $user_agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
    curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $POSTDATA);
    $out = curl_exec($curl);
//echo $out;
   $xml1=simplexml_load_string($out, "SimpleXMLElement", LIBXML_NOCDATA);
   echo'+++++++';
   }
*/

/*подключаем xml файл*/
 $xml1= simplexml_load_file("http://qteam.ru/parser/getdatafromextsite.php?vr=230&pkey=".$PARSERKEY.'&numlnk='.$_SESSION['PARSPAGE'].'&countlnk='.$getcntpg, "SimpleXMLElement", LIBXML_NOCDATA);

/////$xml1= simplexml_load_file("http://qteam.ru/parser/getdatafromextsite.php?pkey=-89-00H-D6-5E-A3-C8-EB", "SimpleXMLElement", LIBXML_NOCDATA);
//$xml1= simplexml_load_file("http://qteam.ru/parser/xmlfile.php", "SimpleXMLElement", LIBXML_NOCDATA);
//$xml1= file_get_contents("http://www.languagelink.ru/data.xml");
if(!$xml1) $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_POLUCITQ");
//die($xml1.'<br>------------<br>');

// echo $xml1.'<br>------------<br>'."|http://qteam.ru/parser/getdatafromextsite.php?pkey=".$PARSERKEY.'&numlnk='.$_SESSION['PARSPAGE'].'&countlnk='.$getcntpg.'|';

if($xml1->ERROR)
{ 
 $arResult["ERROR"][]=PARS_ConvrtStr($xml1->ERROR);
 
 $arResult["PARSTEMPLATES"] = array();
 foreach ($xml1->PARSTEMPLATES->PARSITEM as $it)
 {
   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>PARS_AutoConvrtStr($it->NAME), "KEY"=>$it->KEY, );
/////   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>$it->NAME, "KEY"=>$it->KEY, );
  }
 
 }
else 
{

/*
// функция перекодировки
function utf_win($str, $type){
static $co = '';
if (!is_array($co))
{
$co = array();
for ($x=128; $x <= 143; $x++){
$co['utf'][] = chr(209) . chr($x);
$co['win'][] = chr($x + 112);
}
for ($x=144; $x<= 191; $x++){
$co['utf'][] = chr(208) . chr($x);
$co['win'][] = chr($x + 48);
}
$co['utf'][] = chr(208) . chr(129);
$co['win'][] = chr(168);
$co['utf'][] = chr(209) . chr(145);
$co['win'][] = chr(184);
}
if ($type == 'w'){
return str_replace($co['utf'], $co['win'], $str);
}
elseif ($type == 'u'){
return str_replace($co['win'], $co['utf'], $str);
}
else
{
return $str;
}
}
*/



/*проходим циклом по xml документу*/

//echo'<pre>'; print_r($xml1); echo'</pre>';


$numblock=0;
$XMLData=array();
foreach ($xml1->ITEM as $it)
 {
 
   if(strpos($it["ERROR"], '[-001-]')===false) { } else { echo '<p style="color:#ff0000;">'.GetMessage("QTEAM_PARSER_NE_UDALOSQ_OBNARUJIT").'</p>'; break; }


$POSTDATA=array();

// echo'<br>=========================================<br>';

$numblock++;


// echo'<br /><br />';




///////if($it->ERROR) echo '<input name="PIADD_'.$numblock.'" type="checkbox" />'; else   echo '<input name="PIADD_'.$numblock.'" type="checkbox" checked="checked" />';
 




if($it->FPDATE) 
{
// echo 'Дата ссылки первой страницы: ';
// echo iconv('UTF-8', 'windows-1251', $it->FPDATE);
// echo '<br />';
 
 $ttxtv=PARS_ConvrtStr($it->FPDATE);
 $POSTDATA['FPDATE']=$ttxtv;
// echo '<input name="FPDATE" type="hidden" value="'.$ttxtv.'" />';
 }
 
 

if($it->FPIMAGE) 
{
// echo 'Ссылка на изображение к ссылки первой страницы: ';
///////    $turl=PARS_ConvrtStr($it->FPIMAGE);
        $turl=$it->FPIMAGE; 
	$turl=trim($turl);
    $turl=str_replace('http://qteam.ru/parser/glypeproxy/browse.php?u=', '', $turl); 
    $turl=str_replace('&amp;b=4', '', $turl); 
	$turl=urldecode($turl);
	if((substr($turl,0,4)=='http')||(substr($turl,0,2)=='//')) 
	 {  
//echo'1. '.$turl.'<br>';

          } 
	else 
	 {  
//echo'2. '.$turl.'<br>';
	   $taurl=explode('?', $turl); $turl=$taurl[0]; 
	   
       $ittxtv=PARS_ConvrtStr($it->SOURCE);
	   $ittxtv=str_replace('http://', '', $ittxtv); 
	   $idmn=explode('/', $ittxtv); 
	   $turl=$idmn[0].'/'.$turl;
	   $turl=str_replace('//', '/', $turl); 
	   $turl='http://'.trim($turl);
//echo'2.1 '.$turl.'<br>';

	   }
//	if($turl) echo '<img src="'.$turl.'" border="0" />';  echo '<br />';

  $ttxtv=PARS_ConvrtStr($turl);
  $POSTDATA['FPIMAGE']=$ttxtv;
//  echo '<input name="FPIMAGE" type="hidden" value="'.$ttxtv.'" />';
 }



if($it->FPNAME)
{
// echo 'Текст ссылки первой страницы: <br> <strong>'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</strong><br />';

// echo '<a id="lnk'.$numblock.'" href="javascript: void(0); return false;">'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</a>';
// echo '<strong><span class="lnk'.$numblock.'">'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</span></strong>';


  $ttxtv=PARS_ConvrtStr($it->FPNAME);
  $POSTDATA['FPNAME']=$ttxtv;
//  echo '<input name="FPNAME" type="hidden" value="'.$ttxtv.'" />';
}




if($it->FPHREF) 
{
// echo 'Cсылка первой страницы: ';
// echo iconv('UTF-8', 'windows-1251', $it->FPHREF);
// echo '<br />';

  $ttxtv=PARS_ConvrtStr($it->FPHREF);
  $POSTDATA['FPHREF']=$ttxtv;
//  echo '<input name="FPHREF" type="hidden" value="'.$ttxtv.'" />';
}





if($it->FPANONS) 
{
// echo 'Анонс к ссылки первой страницы: ';
// echo iconv('UTF-8', 'windows-1251', $it->FPANONS);
// echo '<br />';

  $ttxtv=strip_tags(PARS_ConvrtStr($it->FPANONS));
  $POSTDATA['FPANONS']=$ttxtv;
//  echo '<input name="FPANONS" type="hidden" value="'.$ttxtv.'" />';
 }








if($it->ERROR) 
{
// echo '<span style="color:#ff0000;"><b>Ошибка загрузки страницы</b><br />';
// echo iconv('UTF-8', 'windows-1251', $it->ERROR);
// echo '</span><br />';

  $ttxtv=PARS_ConvrtStr($it->ERROR);
  $POSTDATA['ERROR']=$ttxtv;
//  echo '<input name="ERROR" type="hidden" value="'.$ttxtv.'" />';
}



///// echo '<a id="lnk'.$numblock.'"  class="lnk">Развернуть текст</a><br />';
 
///// echo'<div id="blk'.$numblock.'" class="blk">';


if($it->DATE) 
{
/// echo 'Дата второй страницы: ';
 $ttxtv='';
 foreach ($it->DATE->DATA as $dt){ $ttxtv.=PARS_ConvrtStr($dt);  }

  $POSTDATA['DATE']=$ttxtv;
// echo '<input name="DATE" type="hidden" value="'.$ttxtv.'" />';
 }


 
 
if($it->HEADPAGE) 
{
//// echo 'Заголовок второй страницы: <br>';
 $ttxtv='';
 foreach ($it->HEADPAGE->DATA as $dt){  $ttxtv.=PARS_ConvrtStr(strip_tags($dt));  }


  $POSTDATA['HEADPAGE']=$ttxtv;
//  echo '<input name="HEADPAGE" type="hidden" value="'.$ttxtv.'" />';
 }






  // название ссылки может быть значительно длиннее настоящего заголовка, поэтому мы стараемся определить что является настоящим заголовком страницы
  if(($POSTDATA["HEADPAGE"]) && ($POSTDATA['FPNAME']))
   {
     if(strlen(trim($POSTDATA["FPNAME"]))>strlen(trim($POSTDATA["HEADPAGE"])))
	 if(strlen(trim($POSTDATA["HEADPAGE"]))>4) $POSTDATA["FPNAME"]=$POSTDATA["HEADPAGE"];
    }








if($it->ANONS) 
{
/// echo 'Анонс второй страницы: <br>';
 $ttxtv='';
 foreach ($it->ANONS->DATA as $dt){  $ttxtv.=strip_tags(trim(PARS_ConvrtStr($dt))).'. ';   }


 $ttxtv=str_replace('..', '.', $ttxtv);


  $POSTDATA['ANONS']=$ttxtv;
//  echo '<input name="ANONS" type="hidden" value="'.$ttxtv.'" />';
 }







if($it->IMAGE) 
{
//// echo 'Ссылка на изображение второй страницы: <br>';
 $ttxtv='';
 foreach ($it->IMAGE as $dt)
  {  
    $turl=PARS_ConvrtStr($dt->DATA);
	$turl=trim($turl);
    $turl=str_replace('http://qteam.ru/parser/glypeproxy/browse.php?u=', '', $turl); 
    $turl=str_replace('&amp;b=4', '', $turl); 
	$turl=urldecode($turl);
	if((substr($turl,0,4)=='http')||(substr($turl,0,2)=='//')) 
	 { } 
	else 
	 {  
	   $taurl=explode('?', $turl); $turl=$taurl[0]; 
	   
           $ittxtv=PARS_ConvrtStr($it->SOURCE);
	   $ittxtv=str_replace('http://', '', $ittxtv); 
	   $idmn=explode('/', $ittxtv); 
	   $turl=$idmn[0].'/'.$turl;
	   $turl=str_replace('//', '/', $turl); 
	   $turl='http://'.trim($turl);

	   }
////	if($turl) echo '<img src="'.$turl.'" border="0" />'; echo '<br />'; 

  $POSTDATA['IMAGE']=$turl;
 //   echo '<input name="IMAGE" type="hidden" value="'.$turl.'" />';
	}
 
 }






if($it->DETAIL) 
{
// echo 'Подробное описание второй страницы: <br />';
 $ttxtv='';
 foreach ($it->DETAIL->DATA as $dt) { $ttxtv.=PARS_ConvrtStr($dt); }


  $ttxtv=str_replace('&lt;/p&gt;', '', $ttxtv);
  $POSTDATA['DETAIL']=$ttxtv;
//  echo '<input name="DETAIL" type="hidden" value="'.$ttxtv.'" />';
 }



  if( (empty($POSTDATA['FPANONS'])) && (empty($POSTDATA['ANONS'])) && ($PARSER_TXTANONS_FROMTXTDETAIL=='Y') )
   {
    $tmptxtANS=strip_tags($POSTDATA['DETAIL']);
	$artmptxtANS=explode('.', $tmptxtANS);
	for($is=0; $is<3; $is++) $POSTDATA['FPANONS'].=$artmptxtANS[$is].'. '; 
//	$artmptxtANS=explode(' ', $tmptxtANS);
//	for($is=0; $is<50; $is++) $POSTDATA['FPANONS'].=' '.$artmptxtANS[$is];
    } 



if($it->SOURCE) 
{
// echo 'Источник: ';
// echo iconv('UTF-8', 'windows-1251', $it->SOURCE);
// echo '<br />';

  $ttxtv=PARS_ConvrtStr($it->SOURCE);
  $POSTDATA['SOURCE']=$ttxtv;
//  echo '<input name="SOURCE" type="hidden" value="'.$ttxtv.'" />';
 }





/// echo'</div>';






//echo'<pre>'; print_r(iconv('UTF-8', 'windows-1251', $it->FPNAME)); echo'</pre>';

//echo'UID:'.$sort->userid.' Language:'.$sort->language.'<BR>';
//echo'UID:'.$sort->userid.' Language:'.utf_win($sort->language, "w").'<BR>';

$POSTDATA["NUMART"]=$numblock;

$_SESSION['PARSDATA'][$numblock]=$POSTDATA;
$XMLData[]=$POSTDATA;
 }


 $arResult["ITEMS"] = $XMLData;



$arResult["PARSTEMPLATES"] = array();
foreach ($xml1->PARSTEMPLATES->PARSITEM as $it)
 {
  $t=GetMessage("QTEAM_PARSER_ABVGDEJZIKL");
   $cdrv=mb_detect_encoding($t, array('utf-8', 'cp1251')); // Windows-1251 UTF-8


//   echo PARS_utf8_win($it->NAME).'===='.$it->NAME.'<br />'; 
   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>PARS_AutoConvrtStr($it->NAME), "KEY"=>$it->KEY, );
///////   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>$it->NAME, "KEY"=>$it->KEY, );
//   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>$it->NAME, "KEY"=>$it->KEY, );
//   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>PARS_AutoConvrtStr($it->NAME), "KEY"=>$it->KEY, );
//   $arResult["PARSTEMPLATES"][] = array("ID"=>$it->ID, "NAME"=>$it->NAME, "KEY"=>$it->KEY, );
  }




 } // else if($xml1->ERROR)



} // else  if($REQUEST_METHOD=="POST")








// echo'<pre>'; print_r($arResult["PARSTEMPLATES"]); echo'</pre>';





















//========================================== Автоматическое добавление записей в БД =================

if($_GET['auto']>0)
{








if($arResult["ERROR"])
 {
 
 if(count($arResult["ERROR"])>0)
  {
//   echo'<p><strong>Ошибки при загрузке страницы:</strong></p>';
//   foreach($arResult["ERROR"] as $vl) echo'<p><font class="errortext">'.$vl.'</font></p>';
   } 


// if($cnttmplt>0) { } else echo'<p align="center">&nbsp;<br />Что-то пошло не так... <a href="?prsgetlisttmpl=y">Обновить данные</a><br />&nbsp;</p>';

 
  }
else
 {   
 
// if($cnttmplt>0) { } else echo'<p align="center">&nbsp;<br />Что-то пошло не так... <a href="?prsgetlisttmpl=y">Обновить данные</a><br />&nbsp;</p>';


//$homepage = file_get_contents('http://qteam.ru/parser/xmlfile.php');
//echo $homepage;
//die('----------------');


	

//echo'<pre>'; print_r($xml1); echo'</pre>';

$RealAddItem=0;
$numblock=0;
//foreach ($xml1->ITEM as $it){
foreach($arResult["ITEMS"] as $it){

//echo'<table style="width:98%;"><tr><td>';


$POSTDATA=array();

// echo'<br>=========================================<br>';

$numblock++;


// echo'<br /><br />';







if($it["DETAIL"]) 
{
//// echo'<table style="width:98%;"><tr><td>';

	   $ittxtv=str_replace('http://', '', $it["SOURCE"]); 
	   $idmn=explode('/', $ittxtv); 


 $it["DETAIL"]=str_replace(' src="//', ' src="#', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="//', ' SRC="#', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src="/', ' src="http://'.trim($idmn[0]).'/', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="/', ' SRC="http://'.trim($idmn[0]).'/', $it["DETAIL"]);

 $it["DETAIL"]=str_replace(' src="#', ' src="//', $it["DETAIL"]);
 $it["DETAIL"]=str_replace(' SRC="#', ' SRC="//', $it["DETAIL"]);

 $it["DETAIL"]=str_replace('&lt;/p&gt;', '', $it["DETAIL"]);

 
///// echo '<p>'.$it["DETAIL"].'</p>';
///// echo '<div style="display:none;"><textarea id="PARS_DETAIL_'.$it["NUMART"].'" name="PARS_DETAIL_'.$it["NUMART"].'">'.$it["DETAIL"].'</textarea></div>';

//  echo '<input name="DETAIL" type="hidden" value="'.$ttxtv.'" />';
///// echo'</td></tr></table>';

  if(strlen($it["DETAIL"])>20) $_POST['PARS_ANONS_'.$it["NUMART"]]=$it["DETAIL"]; else continue;
 }







//// echo'<div class="parsbox parseffect8">';

if($it["ERROR"]) 
  continue;   // $_POST["PIADD_".$numblock]=''; //  echo '<p><input name="PIADD_'.$numblock.'" type="checkbox" /> сохранить статью</p>';
else
  $_POST["PIADD_".$numblock]='checked'; // echo '<p><input name="PIADD_'.$numblock.'" type="checkbox" checked="checked" /> сохранить статью</p>';
 


$RealAddItem++;




if((!$it["FPDATE"])&&($it["DATE"])) $it["FPDATE"]=$it["DATE"];
if($it["FPDATE"]) 
{
 $_POST["PARS_FPDATE_".$it["NUMART"]]=$it["FPDATE"];
 
 
/*
 echo '<p style="text-align:right; font-size:11px;">';
 echo $it["FPDATE"];
 echo '</p>';
 echo '<div style="display:none;"><textarea id="PARS_FPDATE_'.$it["NUMART"].'" name="PARS_FPDATE_'.$it["NUMART"].'">'.$it["FPDATE"].'</textarea></div>';
*/ 
 }
 
 
// echo'<p>';


if($it["FPNAME"])
{
 $_POST['PARS_FPNAME_'.$it["NUMART"]]=$it["FPNAME"];



// echo 'Текст ссылки первой страницы: <br> <strong>'.$it["FPNAME"].'</strong><br />';
///// echo '<strong>'.$it["FPNAME"].'</strong>';

// echo '<a id="parslnk'.$numblock.'" href="javascript: void(0); return false;">'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</a>';
// echo '<strong><span class="parslnk'.$numblock.'">'.iconv('UTF-8', 'windows-1251', $it->FPNAME).'</span></strong>';
//  echo '<input name="FPNAME" type="hidden" value="'.$ttxtv.'" />';
//// echo '<div style="display:none;"><textarea id="PARS_FPNAME_'.$it["NUMART"].'" name="PARS_FPNAME_'.$it["NUMART"].'">'.$it["FPNAME"].'</textarea></div>';

}

if((!$it["FPIMAGE"])&&($it["IMAGE"])) $it["FPIMAGE"]=$it["IMAGE"];
if(($it["FPIMAGE"]) && ($arParams["IMG_ANONS_ADD"]=='Y'))
{
// echo 'Ссылка на изображение к ссылки первой страницы: ';
////	echo '<img src="'.$it["FPIMAGE"].'" border="0" align="left" height="80" style="margin-right:7px;" />'; 
////    echo '<br />';
//  echo '<input name="FPIMAGE" type="hidden" value="'.$ttxtv.'" />';
///// echo '<div style="display:none;"><textarea id="PARS_FPIMAGE_'.$it["NUMART"].'" name="PARS_FPIMAGE_'.$it["NUMART"].'">'.$it["FPIMAGE"].'</textarea></div>';
 
 
 $_POST['PARS_FPIMAGE_'.$it["NUMART"]]=$it["FPIMAGE"];
 }



//echo'</p>';



if((!$it["FPANONS"])&&($it["ANONS"])) $it["FPANONS"]=$it["ANONS"];
if($it["FPANONS"]) 
{
// echo '<p>';
//  echo $it["FPANONS"];
// echo '</p>';
// echo '<div style="display:none;"><textarea id="PARS_FPANONS_'.$it["NUMART"].'" name="PARS_FPANONS_'.$it["NUMART"].'">'.$it["FPANONS"].'</textarea></div>';

//  echo '<input name="FPANONS" type="hidden" value="'.$ttxtv.'" />';

  $_POST['PARS_FPANONS_'.$it["NUMART"]]=$it["FPANONS"];
 }


/*
if($it["ERROR"]) 
{
echo '<p><span style="color:#ff0000;"><b>Ошибка загрузки страницы</b><br />';
echo $it["ERROR"];
echo '</span></p>';

//  echo '<input name="ERROR" type="hidden" value="'.$ttxtv.'" />';
}
*/



//echo '<p align="right"><a id="parslnk'.$numblock.'"  class="parslnk" style="cursor:pointer;"><em>Подробное описание &gt;&gt;</em></a></p>';


  $_POST['PARENTSECT_'.$it["NUMART"]]=$arParams["PARENT_SECTION"];


/*
echo'<p align="center">Родительский раздел:<br /><select id="PARENTSECT_'.$it["NUMART"].'" name="PARENTSECT_'.$it["NUMART"].'" >';

                echo'<option  value="0" '.(($arParams["PARENT_SECTION"]=="0")? 'selected="selected"' : '').'>Верхний уровень</option>';




   $sarFilter = array('IBLOCK_ID'=>$arParams["IBLOCK_ID"]);
	    $sdb_list = CIBlockSection::GetList(array('NAME'=>'ASC'), $sarFilter, true, array("ID", "NAME", "CODE", "DEPTH_LEVEL"));
	    while($sar_result = $sdb_list->GetNext())
	    {

//              $SECTarResult[$sar_result['ID']]=str_repeat(".", ($sar_result['DEPTH_LEVEL'])*3).$sar_result['NAME'];


                echo'<option  value="'.$sar_result['ID'].'" '.(($sar_result['ID']==$arParams["PARENT_SECTION"])? 'selected="selected"' : '').'>'.str_repeat(".", ($sar_result['DEPTH_LEVEL'])*3).$sar_result['NAME'].'</option>';


	    }
////	 echo '<pre>'; print_r($SECTarResult); echo '</pre>';




echo '</select>';
*/





if($it["FPHREF"]) 
{

// echo 'Cсылка первой страницы: ';
// echo '<br /><span style="color:#999999;font-size:11px;">'.$it["FPHREF"].'</span>';
// echo '<div style="display:none;"><textarea id="PARS_FPHREF_'.$it["NUMART"].'" name="PARS_FPHREF_'.$it["NUMART"].'">'.$it["FPHREF"].'</textarea></div>';

// echo '<br />';

  $_POST['PARS_FPHREF_'.$it["NUMART"]]=$it["FPHREF"];
}

//echo'</p></div>';



 
//echo'<div id="parsblk'.$numblock.'" class="parsblk"><table style="width:98%;"><tr><td>';

if(($it["FPDATE"])&&(!$it["DATE"])) $it["DATE"]=$it["FPDATE"];
if($it["DATE"]) 
{
/// echo '<p style="text-align:right; font-size:11px;">'.$it["DATE"].'</p>';
/// echo '<div style="display:none;"><textarea id="PARS_DATE_'.$it["NUMART"].'" name="PARS_DATE_'.$it["NUMART"].'">'.$it["DATE"].'</textarea></div>';

// echo '<input name="DATE" type="hidden" value="'.$ttxtv.'" />';

  $_POST['PARS_DATE_'.$it["NUMART"]]=$it["DATE"];
 }


 
if(!$it["HEADPAGE"]) $it["HEADPAGE"]=$it["FPNAME"];  
if($it["HEADPAGE"]) 
{
//// echo '<h1>'.$it["HEADPAGE"].'</h1>';
// echo '<input type="hidden" id="PARS_HEADPAGE_'.$it["NUMART"].'" name="PARS_HEADPAGE_'.$it["NUMART"].'" value="'.$it["HEADPAGE"].'" />';
//// echo '<div style="display:none;"><textarea id="PARS_HEADPAGE_'.$it["NUMART"].'" name="PARS_HEADPAGE_'.$it["NUMART"].'">'.$it["HEADPAGE"].'</textarea></div>';

//  echo '<input name="HEADPAGE" type="hidden" value="'.$ttxtv.'" />';
  $_POST['PARS_HEADPAGE_'.$it["NUMART"]]=$it["HEADPAGE"];
 }


////echo'<p>';

if(($it["FPIMAGE"])&&(!$it["IMAGE"])) $it["IMAGE"]=$it["FPIMAGE"];
if(($it["IMAGE"]) && ($arParams["IMG_DETAIL_ADD"]=='Y'))
{
// echo 'Ссылка на изображение второй страницы: <br>';
/////	echo '<table><tr><td><img src="'.$it["IMAGE"].'" border="0" align="center" /></td></tr><tr><td align="center"><small>изображение детального описания</small></td></tr></table>';
////	echo '<br />'; 
///    echo '<div style="display:none;"><textarea id="PARS_IMAGE_'.$it["NUMART"].'" name="PARS_IMAGE_'.$it["NUMART"].'">'.$it["IMAGE"].'</textarea></div>';

  $_POST['PARS_IMAGE_'.$it["NUMART"]]=$it["IMAGE"];
 }



if($it["ANONS"]) 
{
// echo $it["ANONS"];
//// echo '<div style="display:none;"><textarea id="PARS_ANONS_'.$it["NUMART"].'" name="PARS_ANONS_'.$it["NUMART"].'">'.$it["ANONS"].'</textarea></div>';
//  echo '<input name="ANONS" type="hidden" value="'.$ttxtv.'" />';

  $_POST['PARS_ANONS_'.$it["NUMART"]]=$it["ANONS"];
 }

////// echo'</p><hr />';







if($it["SOURCE"]) 
{
//// echo '<p style="color:#999999;font-size:11px;">Источник: '.$it["SOURCE"].'</p>';
//  echo '<input name="SOURCE" type="hidden" value="'.$ttxtv.'" />';
//// echo '<div style="display:none;"><textarea id="PARS_SOURCE_'.$it["NUMART"].'" name="PARS_SOURCE_'.$it["NUMART"].'">'.$it["SOURCE"].'</textarea></div>';
 
   $_POST['PARS_ANONS_'.$it["NUMART"]]=$it["SOURCE"];
 }





//// echo'</td></tr></table></div>';

//echo'<p><hr  style="background-color: #DDDDDD; border: medium none; color: #DDDDDD; height: 1px;">  </p>';
//// echo'<p>&nbsp;</p>';


/*
if($it->HEADPAGE) 
{
 echo 'Заголовок второй страницы: ';
 echo iconv('UTF-8', 'windows-1251', $it->HEADPAGE);
 echo '<br />';
 }
*/








//echo'<pre>'; print_r(iconv('UTF-8', 'windows-1251', $it->FPNAME)); echo'</pre>';

//echo'UID:'.$sort->userid.' Language:'.$sort->language.'<BR>';
//echo'UID:'.$sort->userid.' Language:'.utf_win($sort->language, "w").'<BR>';


///// echo'</td></tr></table>';
  }



 } // if($arResult["ERROR"])












// echo'<pre>'; print_r($_POST); echo'</pre>';
// return;


//---------------- Данные подготовлены, теперь добавляем их в БД ----------------


if($RealAddItem>0)
//if($_SERVER['REQUEST_METHOD']=="POST") 
{



 if(CModule::IncludeModule("iblock"))
  {
/*  
     //----------- выбираем все информационные блоки типа "shop" ---------
     $iblocks = GetIBlockList($PARSERTYPEIB);
     while($arIBlock = $iblocks->GetNext()) //цикл по всем блокам
      {
        if($arIBlock["CODE"]==$PARSERCODEIB)  {  $PARSERIB=$arIBlock["ID"];    }
       }  
*/	   

// echo '----'.$PARSERIB.'<br />';







    $INFO=array();


foreach($_POST as $key=>$vl)
  {
   $nurec=explode('_', $key);
  
   if($nurec[0]!='PIADD') continue;
  



    $TMPArr=array();

   

   

   //------------------ Создаем директорию для хранения изображений ----------------


     // удаление файлов из папки /images/temp
    PARS_remove_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp", false); $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_PAPKA_S_IZOBRAJENIAM"); 




//	 $path = '/opt/lampp/htdocs/'; // - путь до создаваемой папки.



//     $folder = 'my-documents';     // - имя создаваемой папки.
//     $mode = '0777';               // - права на создаваемую папку.
//     $recursive = true;            // - несуществующие папки будут воссозданы
	 
	 
/*	 
    if(mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parsimgtmp/", $mode, $recursive)==false)
      {
	   $INFO['ERROR'][]='Не удалось создать директорию "'.$_SERVER["DOCUMENT_ROOT"].'upload/parsimgtmp/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]='Директория создана "'.$_SERVER["DOCUMENT_ROOT"].'upload/parsimgtmp/"';
	   }
*/	   




	   	 
	 
//     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser", $mode, $recursive)==false)
     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/", 0777)==false)
      {
	   $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SOZDATQ_D").$_SERVER["DOCUMENT_ROOT"].'upload/parser/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/", 0777);
	   }




     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/", 0777)==false)
      {
	   $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SOZDATQ_D").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/", 0777);
	   }



     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp/", 0777)==false)
      {
	   $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SOZDATQ_D").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/"';
	   }
     else
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/temp/", 0777);
	   }







//   echo '<br>-----------<br><pre>';  print_r($_SESSION['PARSDATA'][$nurec[1]]); echo'</pre><br>------------<br>';
  
  
  
  
   //-------------------------------------------- Добавляем изображение АНОНСА  -------------------------------
   if( ($PARSER_IMGANONSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']) )
    {
  	  preg_match("#[^/]*$#i", trim($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']), $timatch);
      $tmnim=explode('?', $timatch[0]);
      $tmnim=explode('&', $tmnim[0]);
      $mnim=explode('/', $tmnim[0]);
      $mnim[count($mnim)-1]=PARS_AutoConvrtStr($mnim[count($mnim)-1]);
      $tmnim[0]=trim($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']);


	  


      $file = str_replace(' ', '%20',  $tmnim[0]);
      //$file = $param[0];
      $newfile = $_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1]));

      if(strlen($file)>5)
       {
         if (!copy($file, $newfile)) 
          {  
            $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SKOPIROVA")."<br /> |$file|$newfile|"; 
            $TMPArr["SMALLFILE"]=""; 
            }
         else   
          {
            $TMPArr["SMALLFILE"]='upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1])); 
            $TMPArr["SMALLFILEALT"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME']; 	 
	        }
         }
       else 
        {
          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_NAYDEN_FAYL_IZOBR")."<br>"; 
          $TMPArr["SMALLFILE"]=""; 
          }

	
	
	
	 } // if( ($PARSER_IMGANONSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['FPIMAGE']) )
  


// if (!copy($file, $newfile))  echo 'error1'; else echo 'true1';
// if (!copy('http://rs.mail.ru/b26986827.jpg', '/home/bitrix/www/upload/parser/images/temp/b26986827.jpg')) echo 'error2'; else echo 'true2';
// die('<br>'.$file.'|'.$newfile.'<br />+++++++++++++++++++++++++++++');




  
  
   //-------------------------------------------- Добавляем изображение Детального описания  -------------------------------
//   if( ($PARSER_IMGDETAILADD)&&($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) )
   if($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) 
    {
	
  	  preg_match("#[^/]*$#i", trim($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']), $timatch);
      $tmnim=explode('?', $timatch[0]);
      $tmnim=explode('&', $tmnim[0]);
      $mnim=explode('/', $tmnim[0]);
      $mnim[count($mnim)-1]=PARS_AutoConvrtStr($mnim[count($mnim)-1]);
      $tmnim[0]=trim($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']);
	
	
/*	
      $tmnim=explode('?', trim($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']));
      $mnim=explode('/', $tmnim[0]);
*/
	

      $file = str_replace(' ', '%20',  $tmnim[0]);
      //$file = $param[0];
      $newfile = $_SERVER["DOCUMENT_ROOT"].'upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1]));

      if(strlen($file)>5)
       {
         if (!copy($file, $newfile)) 
          {  
            $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SKOPIROVA1"); 
            $TMPArr["BIGFILE"]=""; 
            }
         else   
          {
            $TMPArr["BIGFILE"]='upload/parser/images/temp/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1])); 
            $TMPArr["BIGFILEALT"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME']; 	 
	        }
         }
       else 
        {
          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_NAYDEN_FAYL_IZOBR1")."<br>"; 
          $TMPArr["BIGFILE"]=""; 
          }

	 } // if( ($PARSER_IMGDETAILSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) )
   else
    {
	  if($PARSER_IMGDETAILCREATEFROMANONS)
	    if($TMPArr["SMALLFILE"])
		 {
           $TMPArr["BIGFILE"]=$TMPArr["SMALLFILE"];
           $TMPArr["BIGFILEALT"]=$TMPArr["SMALLFILEALT"];
		  }
		  
	  } // if( ($PARSER_IMGDETAILSADD)&&($_SESSION['PARSDATA'][$nurec[1]]['IMAGE']) )
	  
	  
	if( ($TMPArr["BIGFILE"]!='')  && ($TMPArr["SMALLFILE"]=='') && ($PARSER_IMGANONSCREATEFROMDETAIL) )
	 {
           $TMPArr["SMALLFILE"]=$TMPArr["BIGFILE"];
           $TMPArr["SMALLFILEALT"]=$TMPArr["BIGFILEALT"];
	  }
	  
	  
	  


//    echo '#############'.$_SESSION['PARSDATA'][$nurec[1]]['DATE'].'<br />';

    //-------------------------------------------- Добавляем Дату  -------------------------------
    if($_SESSION['PARSDATA'][$nurec[1]]['DATE']) $TMPArr["DATE"]=PARS_GetDateFromStr($_SESSION['PARSDATA'][$nurec[1]]['DATE']); else $TMPArr["DATE"]=PARS_GetDateFromStr('');

    //-------------------------------------------- Добавляем Название  -------------------------------
    if(strlen(trim($_SESSION['PARSDATA'][$nurec[1]]['HEADPAGE']))>2) $TMPArr["HEAD"]=$_SESSION['PARSDATA'][$nurec[1]]['HEADPAGE']; else $TMPArr["HEAD"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME'];


    //-------------------------------------------- Добавляем АНОНС  -------------------------------
    if($_SESSION['PARSDATA'][$nurec[1]]['ANONS']) $TMPArr["TEXT"]=PARS_ClearTags($_SESSION['PARSDATA'][$nurec[1]]['ANONS']);
    if($_SESSION['PARSDATA'][$nurec[1]]['FPANONS'])
	 { 
	   if($_SESSION['PARSDATA'][$nurec[1]]['ANONS'])
	    {
	      if(strlen($_SESSION['PARSDATA'][$nurec[1]]['FPANONS'])>strlen($_SESSION['PARSDATA'][$nurec[1]]['ANONS'])) $TMPArr["TEXT"]=PARS_ClearTags($_SESSION['PARSDATA'][$nurec[1]]['FPANONS']);   
		  }
		else
		 $TMPArr["TEXT"]=PARS_ClearTags($_SESSION['PARSDATA'][$nurec[1]]['FPANONS']);  
	   }


  if($PARSER_DELHREF)
   {
    $TMPArr["TEXT"]=PARS_striptags($TMPArr["TEXT"],'a');
    $TMPArr["TEXT"]=PARS_striptags($TMPArr["TEXT"],'A');
    }







//---------------------------- Добавление сообщения ---------------------------
$rsarv=$TMPArr;

 
       // Проверка существования записи 
       $arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM");
//       $arFilter = Array("IBLOCK_ID"=>$IBLOCK_ID, "SECTION_ID"=>$_GET["tid"], "INCLUDE_SUBSECTIONS"=>"Y");
       $arFilter = Array("IBLOCK_ID"=>$PARSERIB, "SECTION_ID"=>$PARSSECTION);
       $arFilter["NAME"]=$rsarv["HEAD"];
//       $arFilter["DATE_ACTIVE_FROM"]=$rsarv["DATE"];
	   if(($PARSER_SOURCEADD)&&($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']!=''))  $arFilter['PROPERTY_'.$PARSER_SOURCEADD]=trim($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']);

       $res = CIBlockElement::GetList(Array("ID" => "asc"), $arFilter, false, false, $arSelect);

       $FlagFindRec=0;
       if (intval($res->SelectedRowsCount())>0) 
	    {
              $ar=$res->GetNext();  
              $FlagFindRec=$ar["ID"];
	          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_ZAPISQ_NAYDENA").$rsarv["HEAD"].')!  ID='.$ar["ID"].'  DATE='.$ar["DATE_ACTIVE_FROM"].'';  
			  continue;
		  } 
	   else // теперь если не существует Проверка существования записи 
	    {
	        if(!$AddInDB) $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DOBAVLAEM_ZAPISQ").$rsarv["HEAD"].')!  ID='.$ar["ID"].'  DATE='.$ar["DATE_ACTIVE_FROM"].''; 
		
		






//--------- теги

      $ftitle=str_replace('"', " ", $rsarv["HEAD"]);
      $tegi=str_replace('"', " ", $rsarv["HEAD"]);


      global $APPLICATION;
 
//      $APPLICATION->SetTitle('В черном списке: '.$ftitle);

      $keywrd=PARS_RplTegHTML(str_replace("<br>", "", str_replace(chr(13).chr(10), "<br>", str_replace("&lt;br&gt;","<br>", $tegi))));
      $dscr=$keywrd;
//      $keywrd=substr($keywrd, 0, 150);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_CTO"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NA"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_V2"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_K2"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_ILI"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_JE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_S6"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_ESLI"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_TO"), " ", $keywrd);

      $keywrd=str_replace(" - ", " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_I2"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_VSE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_BY"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NO"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_A4"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_MNE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_TOCNEE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_NE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_DLA"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_SEBE"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_SEBA"), " ", $keywrd);


      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_BUDET"), " ", $keywrd);
      $keywrd=str_replace(" ".GetMessage("QTEAM_PARSER_U4"), " ", $keywrd);
      $keywrd=str_replace(" , ", " ", $keywrd);

/*
      $keywrd=str_replace(" ", ", ", $keywrd);

      $keywrd=str_replace(",,", ", ", $keywrd);
      $keywrd=str_replace(", ,", ", ", $keywrd);
*/


      for($tmi=1; $tmi<4; $tmi++) $keywrd=str_replace("  ", " ", $keywrd);

      $takw=explode(' ', $keywrd); 
	  $rskwr=''; foreach($takw as $tkwvl)  {  if(strlen($tkwvl)>1) { if($rskwr!='') $rskwr.=', '; $rskwr.=$tkwvl; }   }  $keywrd= $rskwr;
      
//      $rskwr=''; $flgfirstsmb=false; for($tmi=0; $tmi<strlen($keywrd); $tmi++) { if(($keywrd[$tmi]!='')&&($keywrd[$tmi]!=',')&&($keywrd[$tmi]!=',')) $flgfirstsmb=true;   }


//      $APPLICATION->SetPageProperty("keywords", $keywrd);

      $i=0;

      $FlagEnd=false;
      $resdesc="";
      while(!$FlagEnd)
       {
        $resdesc=$resdesc.$dscr[$i];
        if(($dscr[$i]==" ")&&($i>100)) $FlagEnd=true;

        if(strlen($dscr)<=$i) $FlagEnd=true;
        $i++;
        }
//      $APPLICATION->SetPageProperty("description", 'В черном списке: '.$resdesc);
//end------ теги




          $PROP = array();
//          $PROP["DESCRIPTION"] = $rsarv["TEXT"];  
//          $PROP["source"] = $newssource; 
//          $PROP["THEMES"] = $newsthemes; 
          $PROP["KEYWORDS"] = $keywrd;  

          


          $tmpCODE=str_replace("'", " ", $rsarv["HEAD"]);
          $tmpCODE=str_replace('"', ' ', $tmpCODE);
		  $tmpCODE=PARS_str2url($tmpCODE);
		  
	
          $tmPARSSECTION=$PARSSECTION;
          if($_POST["PARENTSECT_".$nurec[1]]>0) $PARSSECTION=$_POST["PARENTSECT_".$nurec[1]];
	

          if($PARSER_DELHREF=='Y') preg_replace ("!<a.*?href=\"?'?([^ \"'>]+)\"?'?.*?>(.*?)</a>!is", "\\2", $rsarv["EXTTEXT"]); 
          if($PARSER_DELHREF=='Y') preg_replace ("!<a.*?href=\"?'?([^ \"'>]+)\"?'?.*?>(.*?)</a>!is", "\\2", $rsarv["TEXT"]); 
  
          $arLoadProductArray = Array(

//           "MODIFIED_BY"    => $arUser["ID"],         // элемент изменен текущим пользователем

           "TAGS"=> $newstags,
           "IBLOCK_SECTION" => $PARSSECTION,          // элемент лежит в указанном разделе
           "IBLOCK_ID"      => $PARSERIB,
		   
           "PROPERTY_VALUES"=> $PROP,

		   
           "NAME"           => $rsarv["HEAD"],
		   
           "PREVIEW_TEXT"   => $rsarv["TEXT"],
		   "PREVIEW_TEXT_TYPE"=> 'html',
		   
//           "DETAIL_TEXT"    => PARS_ClearTags($rsarv["EXTTEXT"]),
		   "DETAIL_TEXT_TYPE"=> 'html',
		   "CODE"=> substr(str_replace('"', '', str_replace(':', '', str_replace("'", '', str_replace(' ', '_', trim($tmpCODE))))),0, 60),
		   
		   
///           "DETAIL_PICTURE" => (CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["BIGFILE"])),
///           "PREVIEW_PICTURE" => (CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["SMALLFILE"])),

           //  "uid" => $USER->GetID();

           );
		   


      $PARSSECTION=$tmPARSSECTION;



	      $arLoadProductArray["ACTIVE"]= "Y";
 
	      $arLoadProductArray["DATE_ACTIVE_FROM"] = $rsarv["DATE"];
		   

//    if($rsarv["BIGFILE"]!='') $arLoadProductArray["DETAIL_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["BIGFILE"]);
//    if($rsarv["SMALLFILE"]!='') $arLoadProductArray["PREVIEW_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["SMALLFILE"]);




   if($rsarv["HEAD"]=='') { $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_OSIBKA_NE_ZAPOLNENO"); continue; }
    


    if(!$AddInDB)
     { 
//       echo"<br>===NEWS====<pre>"; print_r($arLoadProductArray); echo"</pre>===ENDNEWS====<br>"; 
      }





    if($AddInDB)
     {
	   

         $el = new CIBlockElement;
         if($PRODUCT_ID = $el->Add($arLoadProductArray))
          { 
           $INFO['MESSAGE'][]=$PRODUCT_ID.' '.GetMessage("QTEAM_PARSER_SOOBSENIE_DOBAVLENO").'<br>'; 
           }
        else

          { 
           $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_OSIBKA_PRI_DOBAVLENI").$el->LAST_ERROR.'<br>'; 
           }
		   
		   
     } //  if($AddInDB)
		
		
		
		
		 } // end теперь если не существует Проверка существования записи 
 
 
 
	   



















  // $html - некий html-код некой страницы, \n - это переход на новую строку (верстальщики иногда это делают) 
/// $html = 'Текст <img src="http://rs.mail.ru/b26972665.jpg" style="width:0;height:0;position:absolute;" alt=""/> и снова  конец <img src="//limg.imgsmail.ru/s/images/logo/logo.v3.png" width="195" height="46" class="logo__link__img logo__link__img_medium" alt=""/><img src="//limg.imgsmail.ru/s/images/logo/logo_wide.v3.png" width="211" height="53" class="logo__link__img logo__link__img_wide" alt=""/> qwerty dfgdfgdgf <img src="http://rs.mail.ru/b26986220.jpg" style="width:0;height:0;position:absolute;" alt=""/> dfgdfgdgdgdg';

   $TMPArr["EXTTEXT"]=$_SESSION['PARSDATA'][$nurec[1]]['DETAIL'];
  
   $html =$_SESSION['PARSDATA'][$nurec[1]]['DETAIL'];

  // Вызываем функцию, которая все совпадения помещает в массив $matches 
//  preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/", $html, $matches);

  preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc][^=]*=[ '\"]*([^\"'>#]+)[^>]*>/", $html, $matches);

//  preg_match_all("/<[Ii][Mm][Gg][\s]{1}[^>]*[Ss][Rr][Cc]=([^>]+\s#)*>/", $html, $matches);
  $urls = $matches[1]; // Берём то место, где сама ссылка (благодаря группирующим скобкам в регулярном выражении)
  // Выводим все ссылки 
  $IMAGEPARS=array();
  for ($i = 0; $i < count($urls); $i++)
   {
//    echo $urls[$i]."<br />";
	$turl=$urls[$i];
	$turl=trim($turl);
    $turl=str_replace('http://qteam.ru/parser/glypeproxy/browse.php?u=', '', $turl); 
    $turl=str_replace('&amp;b=4', '', $turl); 
	$turl=urldecode($turl);
	
	
	
// echo 'Ссылка на изображение к ссылки первой страницы: ';
	if((substr($turl,0,4)=='http')||(substr($turl,0,2)=='//')) 
	 {  
//echo'1. '.$turl.'<br>';

          } 
	else 
	 {  
//echo'2. '.$turl.'<br>';
	   $taurl=explode('?', $turl); $turl=$taurl[0]; 
	   
       $ittxtv=PARS_ConvrtStr($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']);
	   $ittxtv=str_replace('http://', '', $ittxtv); 
	   $idmn=explode('/', $ittxtv); 
	   $turl=$idmn[0].'/'.$turl;
	   $turl=str_replace('//', '/', $turl); 
	   $turl='http://'.trim($turl);
//echo'2.1 '.$turl.'<br>';

	   }	
	
	
	
	
////	if(substr($turl,0,4)=='http') { } else
////	if(substr($turl,0,2)=='//') { $turl='http:'.$turl; } else {  $taurl=explode('?', $turl); $turl=$taurl[0]; }
//	if($turl) echo '<img src="'.$turl.'" border="0" />'; 
	
	
	
	
	$IMAGEPARS[]=array("SOURCE"=>$urls[$i], "REPLACE"=>$turl, "UPLOAD"=>$upld, "UPLOADFDI"=>'');
	} // for ($i = 0; $i < count($urls); $i++)



     if(count($IMAGEPARS)>0) 
     if(!is_dir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/".$PRODUCT_ID."/"))
     if (mkdir($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/".$PRODUCT_ID."/", 0777))
	  {
       $INFO['MESSAGE'][]=GetMessage("QTEAM_PARSER_DIREKTORIA_SOZDANA").$_SERVER["DOCUMENT_ROOT"].'upload/parser/images/'.$PRODUCT_ID.'/"';
	   chmod($_SERVER["DOCUMENT_ROOT"]."upload/parser/images/".$PRODUCT_ID."/", 0777);
	   }


//echo'--------<pre>'; print_r($IMAGEPARS); echo'</pre>--------------';


    foreach($IMAGEPARS as $key=>$vl)
	 {
	 
      $tmnim=explode('?', trim($vl['REPLACE']));


      $mnim=explode('/', $tmnim[0]);

	

      $file = str_replace(' ', '%20',  $tmnim[0]);

      //$file = $param[0];
      $newfile = $_SERVER["DOCUMENT_ROOT"].'upload/parser/images/'.$PRODUCT_ID.'/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1]));

      if(strlen($file)>5)
       {
         if (!copy($file, $newfile)) 
          {  
            $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_UDALOSQ_SKOPIROVA2"); 
            $IMAGEPARS[$key]["UPLOAD"]=""; 
            }
         else   
          {
            $IMAGEPARS[$key]["UPLOADFDI"]=$newfile; 
            $IMAGEPARS[$key]["UPLOAD"]='/upload/parser/images/'.$PRODUCT_ID.'/'.str_replace(' ', '_',  PARS_str2url($mnim[count($mnim)-1])); 
//            $TMPArr["BIGFILEALT"]=$_SESSION['PARSDATA'][$nurec[1]]['FPNAME']; 	 
	        }
         }
       else 
        {
          $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_NE_NAYDEN_FAYL_IZOBR2")."<br>"; 
          $IMAGEPARS[$key]["UPLOAD"]=""; 
          }



      //-------------------------------------------- Изменяем ссылки в Детальном описании  -------------------------------

	  $TMPArr["EXTTEXT"]=str_replace($IMAGEPARS[$key]["SOURCE"], $IMAGEPARS[$key]["UPLOAD"], $TMPArr["EXTTEXT"]);
	  } // foreach($IMAGEPARS as $key=>$vl)



    //-------------------------------------------- Добавляем Источник в Детальное описание  -------------------------------

//	if($PARSER_SOURCEADD) $TMPArr["EXTTEXT"].='<p><br /><em>Источник: '.$_SESSION['PARSDATA'][$nurec[1]]['SOURCE'].'</em></p>';
	if($PARSER_SOURCEADD) $arLoadProductArray["PROPERTY_VALUES"][$PARSER_SOURCEADD]=trim($_SESSION['PARSDATA'][$nurec[1]]['SOURCE']);





    if( ($PARSER_IMG_ANONS_FROM_DETAILTEXT=='Y') && (count($IMAGEPARS)>0) && ($rsarv["SMALLFILE"]=='') ) 
	 {
	  foreach($IMAGEPARS as $val_uifd) {  if($val_uifd!='') {  $arLoadProductArray["PREVIEW_PICTURE"]=CFile::MakeFileArray($val_uifd["UPLOADFDI"]); break; }  }
	  } 

    if(($PARSER_IMGDETAILADD)&&($rsarv["BIGFILE"]!='')) $arLoadProductArray["DETAIL_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["BIGFILE"]);
    if($rsarv["SMALLFILE"]!='') $arLoadProductArray["PREVIEW_PICTURE"]=CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$rsarv["SMALLFILE"]);



//          $TMPArr["EXTTEXT"]=str_replace("width:0;", "", $TMPArr["EXTTEXT"]);
//          $TMPArr["EXTTEXT"]=str_replace("height:0;", "", $TMPArr["EXTTEXT"]);
//          $TMPArr["EXTTEXT"]=str_replace("position:absolute;", "", $TMPArr["EXTTEXT"]);

  $TMPArr["EXTTEXT"]=str_replace('iframe', '**##**', $TMPArr["EXTTEXT"]);
  if($PARSER_DELHREF)
   {
    
    $TMPArr["EXTTEXT"]=PARS_striptags($TMPArr["EXTTEXT"],'a');
    $TMPArr["EXTTEXT"]=PARS_striptags($TMPArr["EXTTEXT"],'A');
    }


	$arLoadProductArray["DETAIL_TEXT"]=PARS_ClearTags($TMPArr["EXTTEXT"]);
    $arLoadProductArray["DETAIL_TEXT"]=str_replace('**##**', 'iframe', $arLoadProductArray["DETAIL_TEXT"]);
//	$arLoadProductArray["DETAIL_TEXT"]=$TMPArr["EXTTEXT"];







    if($AddInDB)
     {

         $el = new CIBlockElement;
         if($el->Update($PRODUCT_ID, $arLoadProductArray))
          { 
           $INFO['MESSAGE'][]=$PRODUCT_ID.' '.GetMessage("QTEAM_PARSER_SOOBSENIE_IZMENENO"); 
           }
        else

          { 
           $INFO['ERROR'][]=GetMessage("QTEAM_PARSER_OSIBKA_PRI_IZMENENII").$el->LAST_ERROR.''; 
           }
		   
		   
     } //  if($AddInDB)





//       echo"<br>===NEWS====<pre>"; print_r($arLoadProductArray); echo"</pre>===ENDNEWS====<br>"; 

//   echo '<br>-----------<br><pre>';  print_r($TMPArr); echo'</pre><br>------------<br>';
//   echo '<br>+++++++++++<br><pre>';  print_r($INFO); echo'</pre><br>++++++++++++++<br>';

//   die('-------------------');



   sleep(1);

   } // foreach($_POST as $key=>$vl)


		$arResult["MESSAGE"] = $INFO['MESSAGE'];
		$arResult["TYPE"] = "SUBMIT";
		$arResult["ERROR"] = $INFO['ERROR'];


  } // if(CModule::IncludeModule("iblock"))
  
  
  
  
  
 /* 
   $cnti=0; 
   foreach($arResult["MESSAGE"] as $infvl) 
    {
     $cnti++; if($cnti==1) echo'<p>&nbsp;<br /><strong>Сообщения:</strong></p>';
     echo'<p>'.$infvl.'</p>'; 
     }


   $cnti=0; 
   foreach($arResult["ERROR"] as $infvl) 
    {
     $cnti++; if($cnti==1) echo'<p>&nbsp;<br /><strong>Ошибки:</strong></p>';
     echo'<p style="color:#ff0000;">'.$infvl.'</p>'; 
     }


   echo'<p>&nbsp;<br /><a href="?">&lt;&lt; Вернуться</a></p>';  
  */
  
  
} // if POST 







 }
else // else if($_GET['auto']>0)
 $this->IncludeComponentTemplate();
?>
<?if ( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ] ) ) 
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!function_exists("sergeland_convert_charset_array"))
{
	function sergeland_convert_charset_array(&$arItem, $LANG_CHARSET_BEGIN, $LANG_CHARSET_END)
	{
		foreach($arItem as &$value){ 
			 if(is_array($value))
				 sergeland_convert_charset_array($value, $LANG_CHARSET_BEGIN, $LANG_CHARSET_END);
			else $value = iconv($LANG_CHARSET_BEGIN, $LANG_CHARSET_END, urldecode($value));
		}
	}
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "CALLBACK" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["CALLBACK"]["SITE_ID"];
	$_POST["CALLBACK"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);
	
	if(!array_key_exists( "COMMENT", $_POST["CALLBACK"]))
		 $_POST["CALLBACK"]["COMMENT"] = "-";
	 
	// save to infoblock contacts information
	//\Bitrix\Main\Loader::includeModule("iblock");
	//$arLoadParams = array( 
	//		"IBLOCK_ID" => $_POST["CALLBACK"]["IBLOCK_ID"],
	//		"IBLOCK_SECTION_ID" => false,
	//		"ACTIVE" => "Y",
	//		"DATE_ACTIVE_FROM" => $_POST["CALLBACK"]["DATE_ACTIVE_FROM"],
	//		"NAME"	=> $_POST["CALLBACK"]["PHONE"],
	//		"PROPERTY_VALUES" => array("NAME" => $_POST["CALLBACK"]["NAME"], "COMMENT" => $_POST["CALLBACK"]["COMMENT"]),
	//	);

	//$el = new CIBlockElement;
	//if($el->add($arLoadParams))
		 CEvent::SendImmediate("CALLBACK_FORM_EFFORTLESS", $SITE_ID, $_POST["CALLBACK"]);
	//else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "CALLBACK_MODAL" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["CALLBACK_MODAL"]["SITE_ID"];
	$_POST["CALLBACK_MODAL"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);
	
	if(!array_key_exists( "COMMENT", $_POST["CALLBACK_MODAL"]))
		 $_POST["CALLBACK_MODAL"]["COMMENT"] = "-";
	 
	// save to infoblock contacts information
	//\Bitrix\Main\Loader::includeModule("iblock");
	//$arLoadParams = array( 
	//		"IBLOCK_ID" => $_POST["CALLBACK_MODAL"]["IBLOCK_ID"],
	//		"IBLOCK_SECTION_ID" => false,
	//		"ACTIVE" => "Y",
	//		"DATE_ACTIVE_FROM" => $_POST["CALLBACK_MODAL"]["DATE_ACTIVE_FROM"],
	//		"NAME"	=> $_POST["CALLBACK_MODAL"]["PHONE"],
	//		"PROPERTY_VALUES" => array("NAME" => $_POST["CALLBACK_MODAL"]["NAME"], "COMMENT" => $_POST["CALLBACK_MODAL"]["COMMENT"]),
	//	);

	//$el = new CIBlockElement;
	//if($el->add($arLoadParams))
		 CEvent::SendImmediate("CALLBACK_FORM_EFFORTLESS", $SITE_ID, $_POST["CALLBACK_MODAL"]);
	//else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "FEEDBACK" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["FEEDBACK"]["SITE_ID"];
	$_POST["FEEDBACK"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);
	CEvent::SendImmediate("FEEDBACK_FORM_EFFORTLESS", $SITE_ID, $_POST["FEEDBACK"]);

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "FEEDBACK_MODAL" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["FEEDBACK_MODAL"]["SITE_ID"];
	$_POST["FEEDBACK_MODAL"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);
	CEvent::SendImmediate("FEEDBACK_FORM_EFFORTLESS", $SITE_ID, $_POST["FEEDBACK_MODAL"]);

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "VACANCIES" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$_POST["VACANCIES"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$_POST["VACANCIES"]["FILE"] = "-";

	$SITE_ID = $_POST["VACANCIES"]["SITE_ID"];
	$arr["MESSAGE"]["ERROR"] = 0;

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
	{
		$LANG_CHARSET = $arSite["CHARSET"];
		$SITE_DIR = $arSite["DIR"];	
	}

	if(!empty($_FILES['FILE']['tmp_name']))
	{
		sergeland_convert_charset_array($_FILES, "UTF-8", $LANG_CHARSET);

		//создаем папку загрузки файла	
		$uploaddir = $SITE_DIR.'images/'.md5(time()).'/';
		mkdir($_SERVER["DOCUMENT_ROOT"].$uploaddir);

		//адрес расположения нового файла
		$uploadfile = $uploaddir.$_FILES['FILE']['name'];

		// Копируем файл из каталога для временного хранения файлов
		if(!copy($_FILES['FILE']['tmp_name'], $_SERVER["DOCUMENT_ROOT"].$uploadfile))
			$arr["MESSAGE"]["ERROR"] = 1;
		else $_POST["VACANCIES"]["FILE"] = "http://".$_SERVER["SERVER_NAME"].$uploadfile;
	}

	if($arr["MESSAGE"]["ERROR"]<1)
		CEvent::SendImmediate("VACANCIES_FORM_EFFORTLESS", $SITE_ID, $_POST["VACANCIES"]);

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "COMMENTS" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["COMMENTS"]["SITE_ID"];
	$_POST["COMMENTS"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);

	if(!array_key_exists( "STARS", $_POST["COMMENTS"]))
		 $_POST["COMMENTS"]["STARS"] = "";
	
	// save to infoblock information
	\Bitrix\Main\Loader::includeModule("iblock");
	$arLoadParams = array( 
			"IBLOCK_ID" => $_POST["COMMENTS"]["IBLOCK_ID"],
			"IBLOCK_SECTION_ID" => false,
			"ACTIVE" => "N",
			"DATE_ACTIVE_FROM" => $_POST["COMMENTS"]["DATE_ACTIVE_FROM"],
			"NAME"	=> $_POST["COMMENTS"]["NAME"],
			"PREVIEW_TEXT" => $_POST["COMMENTS"]["COMMENT"],
			"PREVIEW_TEXT_TYPE" => "text",
			"PROPERTY_VALUES" => array("ID" => $_POST["COMMENTS"]["ID"], "EMAIL" => $_POST["COMMENTS"]["EMAIL"], "STARS" => $_POST["COMMENTS"]["STARS"]),
		);

	$el = new CIBlockElement;
	if($el->add($arLoadParams))
		 CEvent::SendImmediate("COMMENTS_FORM_EFFORTLESS", $SITE_ID, $_POST["COMMENTS"]);
	else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "ORDER" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["ORDER"]["SITE_ID"];
	$_POST["ORDER"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);
	 
	// save to infoblock information
	\Bitrix\Main\Loader::includeModule("iblock");
	$arLoadParams = array( 
			"IBLOCK_ID" => $_POST["ORDER"]["IBLOCK_ID"],
			"IBLOCK_SECTION_ID" => false,
			"ACTIVE" => "Y",
			"DATE_ACTIVE_FROM" => $_POST["ORDER"]["DATE_ACTIVE_FROM"],
			"NAME"	=> $_POST["ORDER"]["PRODUCT_NAME"],
			"PROPERTY_VALUES" => array(
				"ID" => $_POST["ORDER"]["ID"],
				"NAME" => $_POST["ORDER"]["NAME"],
				"PHONE" => $_POST["ORDER"]["PHONE"],
				"EMAIL" => $_POST["ORDER"]["EMAIL"], 
				"COMMENT" => $_POST["ORDER"]["COMMENT"]
			),
		);

	$el = new CIBlockElement;
	if($el->add($arLoadParams))
		 CEvent::SendImmediate("ORDER_FORM_EFFORTLESS", $SITE_ID, $_POST["ORDER"]);
	else $arr["MESSAGE"]["ERROR"] = $el->LAST_ERROR;

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
if( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ]) && $_SERVER[ "REQUEST_METHOD" ]=="POST" && is_array($_POST[ "CONTACTS" ]) )
{
	header("Cache-Control: no-store, no-cache, must-revalidate");

	$SITE_ID = $_POST["CONTACTS"]["SITE_ID"];
	$_POST["CONTACTS"]["DATE_ACTIVE_FROM"] = ConvertTimeStamp(time(), "FULL");
	$arr["MESSAGE"]["ERROR"] = 0;

	$dbSite = CSite::GetByID($SITE_ID);
	if($arSite = $dbSite -> Fetch())
		$LANG_CHARSET = $arSite["CHARSET"];

	sergeland_convert_charset_array($_POST, "UTF-8", $LANG_CHARSET);
	CEvent::SendImmediate("FEEDBACK_FORM_EFFORTLESS", $SITE_ID, $_POST["CONTACTS"]);

	sergeland_convert_charset_array($arr, $LANG_CHARSET, "UTF-8");
	echo json_encode($arr);

	return;
}
?>
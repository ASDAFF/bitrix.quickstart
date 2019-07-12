<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");


__IncludeLang($_SERVER["DOCUMENT_ROOT"].NOVAGR_JSWSHOP_COMPONENT_DIR."catalog.element/templates/.default/lang/ru/ajax.php");

$rsSites = CSite::GetByID(SITE_ID);
$arSite = $rsSites->Fetch();

$arResult = array();
$arResult['result'] = 'ERROR';
$arResult['message'] = '';
global $USER;
$userId = $USER->GetID();
global $APPLICATION;
//deb($_REQUEST);

if ($_REQUEST["action"] == 'comment' &&
		!empty($_REQUEST["productId"]) &&
		!empty($_REQUEST["REVIEW_TEXT"]) /*&&
		!empty($_REQUEST["productCode"])*/
		) {
	
	if (strtolower($arSite["CHARSET"]) == "windows-1251") {
		//$siteUTF8 = false;
		// конвертим реквест чтоб не было кракоз€бр
		foreach ($_REQUEST as $key => $item) {
	
			if (!empty($_REQUEST[$key])) $_REQUEST[$key] = iconv('UTF-8', 'windows-1251', $_REQUEST[$key]);
	
		}
	}
	
	$res = CIBlock::GetList(
			Array(),
			Array( "CODE"=>'comments'), true
	);
	if ($ar_res = $res->Fetch())
	{
		$iblockId = $ar_res['ID'];
	} else {
		return;
	}
	
	$filt = Array("IBLOCK_ID"=> $iblockId, "NAME" => $_REQUEST["productId"]);

	// создаем комметарий
	$secRes =CIBlockSection::GetList(Array("SORT"=>""), $filt,false, Array("ID", "NAME"));
	if ($res=$secRes->Fetch()){

		$IBLOCK_SECTION = $res["ID"];
			
	} else {
			
		$bs = new CIBlockSection;
		$arFields = Array(
				"ACTIVE" => "Y",
				"IBLOCK_SECTION_ID" => false,
				"IBLOCK_ID" => $iblockId,
				"NAME" => $_REQUEST["productId"]
		);
		$IBLOCK_SECTION = $bs->Add($arFields);
	}
	$el = new CIBlockElement;
	$arLoad = array();
		


	if ($userId>0) {

		$arLoad ["NAME"] =$USER->GetFullName();
		$email = $USER->GetEmail();;

	} else {
		$arLoad ["NAME"] = $_REQUEST["REVIEW_AUTHOR"];
		$email = $_REQUEST["REVIEW_EMAIL"];
	}
	//!empty($_REQUEST["REVIEW_AUTHOR"]) &&



	$PROP = array(
			"PRODUCT_CODE" => array($_REQUEST["productCode"]),
			"EMAIL" => array($email),
			"USER" => array($userId)
	);

	$arLoad ["ACTIVE"] = "Y";
	$arLoad ["IBLOCK_ID"] = $iblockId;
	$arLoad ["MODIFIED_BY"] = $userId;
	$arLoad ["DETAIL_TEXT"] = $_REQUEST["REVIEW_TEXT"];
	$arLoad ["ACTIVE_FROM"] = date("d.m.Y H:i:s");
	$arLoad ["IBLOCK_SECTION"] = $IBLOCK_SECTION;
	$arLoad ["PROPERTY_VALUES"] = $PROP;

	if ($newElemId = $el->Add($arLoad)) {
		//echo "New ID: ".$newElemId;
		$arResult['message'] = GetMessage("ADDED_LABEL");
		$arResult['date'] = $arLoad ["ACTIVE_FROM"];
		$arResult['name'] = $arLoad ["NAME"];
		$arResult['text'] = $arLoad ["DETAIL_TEXT"];
		$arResult['result'] = 'OK';
	}
	else {
		$arResult['result'] = 'ERROR';
		$arResult['message'] =  $el->LAST_ERROR;
		//deb( $el->LAST_ERROR);
		//deb($arLoad);
	}

	if (empty($arLoad ["NAME"])) {
		$arResult['message'] = GetMessage("LABEL_NAME_AUTHOR");
	}

}

if ($_REQUEST['ELEMENT_ID'] > 0 && $_REQUEST['AJAX_REFRESH'] == 1) {

	$filt = Array("IBLOCK_CODE"=> 'comments', "NAME" => $_REQUEST['ELEMENT_ID']);

	// пол€ им€ и имэйл
	$arResult['fields'] = '';
	// авторизован ли пользователь - возвращаем эту информацию дл€ js в дет. карточки
	$arResult['userAutorized'] = '0';
	$arResult['userEmail'] = '';

	$secRes = CIBlockSection::GetList(Array("SORT"=>""), $filt,false, Array("ID", "NAME"));
	
	$arResult["count"] = 0;
	if ($res=$secRes->Fetch()){

		$IBLOCK_SECTION = $res["ID"];

		// получаем  комметраии
		$arFComments = array("IBLOCK_ID" => $iblockId, "SECTION_ID" => $IBLOCK_SECTION );
		$arSelect = array(
				'ID',
				'NAME',
				'DETAIL_TEXT',
				'PROPERTY_EMAIL',
				'PROPERTY_USER'
		);

		if (!empty($_REQUEST["iNumPage"])) {
				
			$pageNumCom = $_REQUEST["iNumPage"];
		}
		else {
			$pageNumCom = 1;
		}

		//$pageNumCom = ( $_REQUEST["PAGEN_2"] ? $_REQUEST["PAGEN_2"] : 1);
		//$pageNumCom = ( $_REQUEST["PAGEN_1"] ? $_REQUEST["PAGEN_1"] : 1);
		$arNavStartParamsCom = array(
				'iNumPage' => $pageNumCom,
				'nPageSize' => 10,
				'bShowAll' => false
		);
		$rsElements = CIBlockElement::GetList(
				array('ACTIVE_FROM' => "DESC"),$arFComments, false, $arNavStartParamsCom, $arSelect
		);
		$arResult["count"] = $count = $rsElements->SelectedRowsCount();
		 
		//deb($count);
		$arResult['NAV_STRING_COMMENTS'] = $rsElements -> GetPageNavStringEx($navComponentObject, "", "bootstrap");
		//deb(htmlspecialchars($arResult['NAV_STRING_COMMENTS']));
		$smileArr = array(
				':D',
				':lol:',
				':-)',
				';-)',
				'8)',
				':-|',
				':oops:',
				':sad:',
				':roll:',
				
		);
		$smileArr2 = array(
				'<img src="'.SITE_DIR.'include/images/smiles/laugh.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/lol.gif" >',
				'<img src="'.SITE_DIR.'include/images/smiles/smile.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/wink.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/cool.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/normal.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/redface.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/sad.gif">',
				'<img src="'.SITE_DIR.'include/images/smiles/rolleyes.gif">',
		);
		
		$commentsText = '';
		while ($comment = $rsElements -> Fetch())
		{
				
			$text = $comment["DETAIL_TEXT"];
			$text = str_replace($smileArr, $smileArr2, $text);
				
			$commentsText .= '
			<div class="even">
			<div class="rbox">
			<div class="comment-box usertype-guest">
			<a href="#" class="comment-anchor"></a>
			<span class="comment-author">' . $comment["NAME"] . '</span>
			<span class="comment-date">' . $comment["ACTIVE_FROM"] . '</span>
			<div class="comment-body">' . $text . '</div>
			</div>
			<div class="clear"></div>
			</div>
			</div>
			';
		}

		//echo $commentsText;
		$commentsText .= $arResult["NAV_STRING_COMMENTS"];
		$arResult['commentsText'] = $commentsText;
			
	}
	
	if ($userId > 0) {
		$arResult['userAutorized'] = 1;
		$arResult['userEmail'] = $USER->GetEmail();
			
	} else {
		$arResult['fields'] = '
		<label><p>'.GetMessage("LABEL_NAME").'<!--<span class="starrequired">*</span>--></p>

		<input name="REVIEW_AUTHOR" id="REVIEW_AUTHOR" type="text" value="" >
		</label>
		';
		$arResult['fieldsEmail'] = '<label><p>'.GetMessage("LABEL_EMAIL").'</p>

		<input type="text" name="REVIEW_EMAIL" id="REVIEW_EMAIL" maxlength="50" value="" >
		</label>';
	}
	//deb("vvv".$arResult['fields'] );
	$arResult['result'] = 'OK';

	$arResult['userEmail'] = $USER->GetEmail();
	if (empty($arResult['userEmail'])) $arResult['userEmail'] = '';
		
	$arResult['result'] = 'OK';
	//$arResult['fields'] = 'LAST_ERROR';
	//$arResult['message'] =  $el->LAST_ERROR;

}



if (strtolower($arSite["CHARSET"]) == "windows-1251") {
	//$siteUTF8 = false;
	
	foreach ($arResult as $key => $item) {

		if (!empty($arResult[$key])) $arResult[$key] = iconv( 'windows-1251', 'UTF-8', $arResult[$key]);

	}
}

$arResultJson = json_encode($arResult);
die($arResultJson);
?>
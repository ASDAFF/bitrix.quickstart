<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

global $DB, $DBType, $APPLICATION;

if(!function_exists('RSMonopoly_addEV')) {
	function RSMonopoly_addEV($arEventFields=array()) {
		global $DB;
		$EventTypeID = 0;
		$et = new CEventType;
		$EventTypeID = $et->Add($arEventFields);
		return $EventTypeID;
	}
}

$arData = array(
	'RS_MONOPOLY_VACANCIES',
	'RS_MONOPOLY_REVIEWS',
	'RS_MONOPOLY_RECALL',
	'RS_MONOPOLY_PRODUCT_ASK',
	'RS_MONOPOLY_PARTNER',
	'RS_MONOPOLY_FAQ',
	'RS_MONOPOLY_EMPLOEYE',
	'RS_MONOPOLY_CONTACTS',
	'RS_MONOPOLY_BUY',
);

$arSites = array();
$rsSites = CSite::GetList($by="sort", $order="desc", array());
while ($arSite = $rsSites->Fetch()) {
	$arSites[] = $arSite['LID'];
}

if( is_array($arData) && count($arData)>0 ) {

	$ev = new CEventMessage;

	foreach($arData as $EVENT_TYPE) {
		$EventTypeID = 0;
		$arEventFields = array(
			'LID'           => 'ru',
			'EVENT_NAME'    => $EVENT_TYPE,
			'NAME'          => GetMessage('RS.MONOPOLY.EVENT_NAME_.'.$EVENT_TYPE),
			'DESCRIPTION'   => GetMessage('RS.MONOPOLY.EVENT_DESCRIPTION_.'.$EVENT_TYPE),
		);
		$EventTypeID = RSMonopoly_addEV($arEventFields);
		if($EventTypeID>0) {
			$arTemplate = array(
				'ACTIVE' 		=> 'Y',
				'EVENT_NAME' 	=> $EVENT_TYPE,
				'LID'			=> $arSites,
				'EMAIL_FROM'	=> '#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'		=> '#EMAIL_TO#',
				'BCC'			=> '',
				'SUBJECT'		=> GetMessage('RS.MONOPOLY.TEMPLATE_SUBJECT_.'.$EVENT_TYPE),
				'BODY_TYPE'		=> 'text',
				'MESSAGE'		=> GetMessage('RS.MONOPOLY.TEMPLATE_MESSAGE_.'.$EVENT_TYPE),
			);
			$EventTemplateID = $ev->Add($arTemplate);
		}
	}

}
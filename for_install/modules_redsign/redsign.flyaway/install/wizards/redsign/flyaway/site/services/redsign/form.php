<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

global $DB, $DBType, $APPLICATION;

if(!function_exists('RSMshop_addEV')) {
	function RSMshop_addEV($arEventFields=array()) {
		global $DB;
		$EventTypeID = 0;
		$et = new CEventType;
		$EventTypeID = $et->Add($arEventFields);
		return $EventTypeID;
	}
}

$arData = array(
	'RS_FLYAWAY_VACANCIES',
	'RS_FLYAWAY_REVIEWS',
	'RS_FLYAWAY_RECALL',
	'RS_FLYAWAY_PARTNER',
	'RS_FLYAWAY_FAQ',
	'RS_FLYAWAY_EMPLOEYE',
	'RS_FLYAWAY_CONTACTS',
	'RS_FLYAWAY_BUY_1_CLICK',
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
			'NAME'          => GetMessage('RS.FLYAWAY.EVENT_NAME_.'.$EVENT_TYPE),
			'DESCRIPTION'   => GetMessage('RS.FLYAWAY.EVENT_DESCRIPTION_.'.$EVENT_TYPE),
		);
		$EventTypeID = RSMshop_addEV($arEventFields);
		if($EventTypeID>0) {
			$arTemplate = array(
				'ACTIVE' 		=> 'Y',
				'EVENT_NAME' 	=> $EVENT_TYPE,
				'LID'			=> $arSites,
				'EMAIL_FROM'	=> '#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'		=> '#EMAIL_TO#',
				'BCC'			=> '',
				'SUBJECT'		=> GetMessage('RS.FLYAWAY.TEMPLATE_SUBJECT_.'.$EVENT_TYPE),
				'BODY_TYPE'		=> 'text',
				'MESSAGE'		=> GetMessage('RS.FLYAWAY.TEMPLATE_MESSAGE_.'.$EVENT_TYPE),
			);
			$EventTemplateID = $ev->Add($arTemplate);
		}
	}

}
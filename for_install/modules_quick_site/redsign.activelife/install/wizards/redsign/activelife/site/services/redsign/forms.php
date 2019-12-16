<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

WizardServices::IncludeServiceLang("forms.php", $lang);

global $DB, $DBType, $APPLICATION;

if(!function_exists('Redsign_addEV')) {
	function Redsign_addEV($arEventFields=array()) {
		global $DB;
		$EventTypeID = 0;
		$et = new CEventType;
		$EventTypeID = $et->Add($arEventFields);
		return $EventTypeID;
	}
}

$arData = array(
	'REDSIGN_RECALL',
	'REDSIGN_FEEDBACK',
	'REDSIGN_BUY1CLICK',
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
			'NAME'          => GetMessage('REDSIGN.EVENT_NAME_.'.$EVENT_TYPE),
			'DESCRIPTION'   => GetMessage('REDSIGN.EVENT_DESCRIPTION_.'.$EVENT_TYPE),
		);
		$EventTypeID = Redsign_addEV($arEventFields);
		if($EventTypeID>0) {
			$arTemplate = array(
				'ACTIVE' 		=> 'Y',
				'EVENT_NAME' 	=> $EVENT_TYPE,
				'LID'			=> $arSites,
				'EMAIL_FROM'	=> '#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO'		=> '#EMAIL_TO#',
				'BCC'			=> '',
				'SUBJECT'		=> GetMessage('REDSIGN.TEMPLATE_SUBJECT_.'.$EVENT_TYPE),
				'BODY_TYPE'		=> 'text',
				'MESSAGE'		=> GetMessage('REDSIGN.TEMPLATE_MESSAGE_.'.$EVENT_TYPE),
			);
			$EventTemplateID = $ev->Add($arTemplate);
		}
	}

}
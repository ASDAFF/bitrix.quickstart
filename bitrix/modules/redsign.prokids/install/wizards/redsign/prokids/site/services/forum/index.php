<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('forum')){
	return 0;
}
elseif(!$DB->TableExists('b_forum') && !$DB->TableExists('B_FORUM')){
	return 1;
}

$SITE_ID = WIZARD_SITE_ID;

$arFields = Array(
	'NAME' => GetMessage('FORUM_NAME'),
	'DESCRIPTION' => GetMessage('FORUM_DECRIPTION'),
	'SORT' => 101,
	'ACTIVE' => 'Y',
	'ALLOW_HTML' => 'N',
	'ALLOW_ANCHOR' => 'Y',
	'ALLOW_BIU' => 'Y',
	'ALLOW_IMG' => 'Y',
	'ALLOW_LIST' => 'Y',
	'ALLOW_QUOTE' => 'Y',
	'ALLOW_CODE' => 'Y',
	'ALLOW_FONT' => 'Y',
	'ALLOW_SMILES' => 'Y',
	'ALLOW_UPLOAD' => 'N',
	'ALLOW_NL2BR' => 'N',
	'MODERATION' => 'N',
	'ALLOW_MOVE_TOPIC' => 'Y',
	'ORDER_BY' => 'P',
	'ORDER_DIRECTION' => 'DESC',
	'LID' => LANGUAGE_ID,
	'PATH2FORUM_MESSAGE' => '',
	'ALLOW_UPLOAD_EXT' => '',
	'ASK_GUEST_EMAIL' => 'N',
	'USE_CAPTCHA' => 'Y',
	'GROUP_ID' => array(1 => 'Y', 2 => 'M'),
	'SITES' => array(
		$SITE_ID => WIZARD_SITE_DIR.'content/forum/#FORUM_ID#/#TOPIC_ID#/'
	),
);

$res = CForumNew::Add($arFields);
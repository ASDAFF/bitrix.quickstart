<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (!$USER->IsAdmin())
	return false;

$GLOBALS["APPLICATION"]->IncludeComponent(
	"bitrix:asd.event.log",
	"admin_gadget",
	Array(
		"TYPES" => $arGadgetParams["TYPES"],
		"SITE_ID" => $arGadgetParams["SITE_ID"],
		"NOT_SHOW_USER" => "N",
		"FILTER_USER" => "N",
		"COUNT" => $arGadgetParams["COUNT"],
		"PAGER_TEMPLATE" => "",
		"USER_LINK" => "/bitrix/admin/user_edit.php?lang=ru&ID=#ID#"
	)
);

$urlFilter = '';
if (!empty($arGadgetParams['TYPES']) && is_array($arGadgetParams['TYPES'])) {
	foreach ($arGadgetParams['TYPES'] as $type) {
		$urlFilter .= '&amp;find_audit_type[]='.$type;
	}
}
?>

<a href="event_log.php?lang=<?= LANG?>&amp;set_filter=Y&amp;find_type=audit_type_id<?= $urlFilter?>" title="<?= GetMessage('ASD_GADGET_EVENTS_ALL_TITLE')?>"><?= GetMessage('ASD_GADGET_EVENTS_ALL')?></a>
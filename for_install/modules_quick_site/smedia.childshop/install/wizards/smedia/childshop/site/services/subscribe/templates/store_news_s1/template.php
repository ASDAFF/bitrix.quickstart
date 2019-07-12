<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $SUBSCRIBE_TEMPLATE_RUBRIC;
$SUBSCRIBE_TEMPLATE_RUBRIC=$arRubric;
global $APPLICATION;
?>
<STYLE type=text/css>
.text {font-family: Verdana, Arial, Helvetica, sans-serif; font-size:12px; color: #1C1C1C; font-weight: normal;}
.newsdata{font-family: Arial, Helvetica, sans-serif; font-size:12px; font-weight:bold; color: #346BA0; text-decoration:none;}
H1 {font-family: Verdana, Arial, Helvetica, sans-serif; color:#346BA0; font-size:15px; font-weight:bold; line-height: 16px; margin-bottom: 1mm;}
</STYLE>

<P><?=GetMessage('smchild_subscribe_head')?></P>
<P><?$SUBSCRIBE_TEMPLATE_RESULT = $APPLICATION->IncludeComponent(
	"bitrix:subscribe.news",
	"",
	Array(
		"SITE_ID" => "#SITE_ID#",
		"IBLOCK_TYPE" => "news",
		"ID" => "",
		"SORT_BY" => "ACTIVE_FROM",
		"SORT_ORDER" => "DESC",
	),
	null,
	array(
		"HIDE_ICONS" => "Y",
	)
);?></P>
<?

if($SUBSCRIBE_TEMPLATE_RESULT)
	return array(
		"SUBJECT"=>$SUBSCRIBE_TEMPLATE_RUBRIC["NAME"],
		"BODY_TYPE"=>"html",
		"CHARSET"=>"windows-1251",
		"DIRECT_SEND"=>"Y",
		"FROM_FIELD"=>$SUBSCRIBE_TEMPLATE_RUBRIC["FROM_FIELD"],
	);
else
	return false;
?>
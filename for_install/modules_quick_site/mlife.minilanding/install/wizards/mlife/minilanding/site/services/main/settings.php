<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

//обновление почтовых событий
$siteList = array();
$rsSites = CSite::GetList($by="sort", $order="desc", Array());
while ($arSite = $rsSites->Fetch()) $siteList[] = $arSite["ID"];

$id1 = COption::GetOptionString("mlife.fitnes","event1");
$id2 = COption::GetOptionString("mlife.fitnes","event2");
$id3 = COption::GetOptionString("mlife.fitnes","event3");

$em = new CEventMessage;
$arFields = Array( "LID" => $siteList);
$em->Update($id1, $arFields);
$em->Update($id2, $arFields);
$em->Update($id3, $arFields);

?>
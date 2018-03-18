<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title","Наша компания");
$APPLICATION->SetPageProperty("description","Наша компания");
$APPLICATION->SetPageProperty("keywords","Наша компания");

?>

<?

   CModule::IncludeModule('iblock');

  //Вытаскиваем ID информационного блока                                     
  $res = CIBlock::GetList(Array(), Array('TYPE'=>'el_news'),true);
  while($ar_res = $res->Fetch())
  {
 	 $aridnn[]=$ar_res["ID"];
  }
 ?>



                 <?
                             $APPLICATION->IncludeComponent("design2u:news.list", "devtemp", array(
	"IBLOCK_TYPE" => "el_news",
	"IBLOCK_ID" =>$aridnn[0],
	"NEWS_COUNT" => "20",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "ncdate",
		1 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "/#SITE_DIR#/news/#ELEMENT_ID#/",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "modern",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);
					      ?>
         
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
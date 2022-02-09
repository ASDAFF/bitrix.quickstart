<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

     <section class="b-detail">
         <div class="b-detail-content clearfix">
             <? 
        
    
$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.cancel",
	"",
	array(
		"PATH_TO_LIST" => $arResult["PATH_TO_LIST"],
		"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
		"SET_TITLE" =>$arParams["SET_TITLE"],
		"ID" => $arResult["VARIABLES"]["ID"],
	),
	$component
);
?>
</div> 
         
         </section>
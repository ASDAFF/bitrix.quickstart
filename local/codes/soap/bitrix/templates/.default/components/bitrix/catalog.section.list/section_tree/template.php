<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
				<div class="b-nav-category m-menu">
					<a href="#" class="b-nav-category__link">Поиск<br>по категориям</a>
					<ul class="b-nav-menu">
<?
$CURRENT_DEPTH=$arResult["SECTION"]["DEPTH_LEVEL"]+1;
foreach($arResult["SECTIONS"] as $arSection):
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
	if($CURRENT_DEPTH<$arSection["DEPTH_LEVEL"])
		echo "<div class='b-nav-menu__inner'><div class='m-nav-promo clearfix'><ul class='b-nav-sub_menu'>";
	elseif($CURRENT_DEPTH>$arSection["DEPTH_LEVEL"]){
	echo "</ul>";
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "DETAIL_PAGE_URL", "PROPERTY_action_text_VALUE");
$arFilter = Array("IBLOCK_ID"=>$arSection["IBLOCK_ID"], "SECTION_ID"=>$arSection["IBLOCK_SECTION_ID"] ,"PROPERTY_actions_VALUE"=>"Да",  "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array("SORT"=> "RAND"), $arFilter, false, Array("nPageSize"=>1), $arSelect);
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  $arProps = $ob->GetProperties();?>
  <div class='b-nav-promo'><a href="<?=$arFields["DETAIL_PAGE_URL"]?>"><img src='<?=CFile::GetPath($arProps["action_text"]["VALUE"]);?>' alt='<?=$arFields["NAME"]?>' /></a></div>
  <?
}
echo "</div></div></li>";
		//echo str_repeat("</ul><div class='b-nav-promo'><img src='/upload/img1.png' alt='' /></div></div></div></li>", $CURRENT_DEPTH - $arSection["DEPTH_LEVEL"]);
		}
	$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
  	$arFilter = Array('IBLOCK_ID'=>$arSection["IBLOCK_ID"], 'GLOBAL_ACTIVE'=>'Y', 'SECTION_ID'=>$arSection["ID"]);
  	$db_list = CIBlockSection::GetList(Array($by=>$order), $arFilter, true)->GetNext();
?>
<?if(count($db_list)==1 && $arSection["DEPTH_LEVEL"]==1):?>
	<li class="b-nav-menu__item" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="b-nav-menu__link"><?=$arSection["NAME"]?></a></li>
<?elseif(count($db_list)>1 && $arSection["DEPTH_LEVEL"]==1):?>
						<li class="b-nav-menu__item m-menu__child" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
							<a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="b-nav-menu__link"><?=$arSection["NAME"]?></a>
<?else:?>
	<li class="b-nav-sub_menu__item" id="<?=$this->GetEditAreaId($arSection['ID']);?>"><a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="b-nav-sub_menu__link"><?=$arSection["NAME"]?></a></li>
<?endif;?>
<?
if(end($arResult["SECTIONS"])==$arSection && $arSection["DEPTH_LEVEL"]==2){
//pr($arSection);
echo "</ul>";
$arSelect = Array("ID", "NAME", "DATE_ACTIVE_FROM", "DETAIL_PAGE_URL", "PROPERTY_action_text_VALUE");
$arFilter = Array("IBLOCK_ID"=>$arSection["IBLOCK_ID"], "SECTION_ID"=>$arSection["IBLOCK_SECTION_ID"] ,"PROPERTY_actions_VALUE"=>"Да",  "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array("SORT"=> "RAND"), $arFilter, false, Array("nPageSize"=>1), $arSelect);
while($ob = $res->GetNextElement())
{
  $arFields = $ob->GetFields();
  $arProps = $ob->GetProperties();?>
  <div class='b-nav-promo'><a href="<?=$arFields["DETAIL_PAGE_URL"]?>"><img src='<?=CFile::GetPath($arProps["action_text"]["VALUE"]);?>' alt='<?=$arFields["NAME"]?>' /></a></div>
  <?
}
echo "</div></div></li>";
}
?>
<?endforeach?>
					</ul>
				</div>

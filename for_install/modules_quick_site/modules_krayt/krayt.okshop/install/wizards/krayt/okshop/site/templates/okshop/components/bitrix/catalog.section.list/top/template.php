<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$strTitle = "";
?>
<div class="top-section-list">
    <ul>
	<?
	foreach($arResult["SECTIONS"] as $arSection)
	{
        if(!isset($arSection['PICTURE']))
        {
           continue;
        }
        $select = "";
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
        if ($_REQUEST['SECTION_ID']==$arSection['ID'])
        {
            $select = "active";
        }?>
     <li class="<?=$select?>">
         <a href="<?=$arSection["SECTION_PAGE_URL"]?>" style="background-image: url('<?=$arSection['PICTURE']['SRC']?>')">
            <span class="name-top-section">
                <?=$arSection["NAME"]?>
            </span>
         </a>
     </li>

   <? }
	?></ul>
</div>
<script type="text/javascript">
    $('.top-section-list li a')
        .css( {backgroundPosition: "center 0"} )
        .mouseover(function(){
            $(this).stop().animate({"backgroundPositionY":"-10px"},300);
        })
        .mouseout(function(){
            $(this).stop().animate({"backgroundPositionY":"0px"},300);
        });
</script>

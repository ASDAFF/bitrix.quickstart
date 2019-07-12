<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult))
	return;

$strMenu = "";
?>
<?foreach($arResult as $itemIdex => $arItem):?>
<?$strMenu .= '<a href="'.$arItem["LINK"].'">';
$strMenu .= $arItem["TEXT"];
if($arItem["PARAMS"]["CLASS"]){
	$strMenu .= ' <span class="'.$arItem["PARAMS"]["CLASS"].'"></span>';
};
$strMenu .= '</a>';
?>
<?endforeach;?>
<div id="personal_menu" style="display:inline-block">
<?$frame = $this->createFrame("personal_menu", false)->begin();?>
<button href="#" class="bj-logo-space__icon glyphicon glyphicon-user hidden-sm hidden-xs" data-content='<?=$strMenu?>'></button>
<?$frame->beginStub();?>
<button href="#" class="bj-logo-space__icon glyphicon glyphicon-user hidden-sm hidden-xs" data-content=''></button>
<?$frame->end();?>
</div>
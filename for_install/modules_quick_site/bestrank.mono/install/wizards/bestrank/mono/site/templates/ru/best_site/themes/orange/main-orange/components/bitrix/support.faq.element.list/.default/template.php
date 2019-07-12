<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if (count($arResult["ITEMS"]) < 1)
	return;
?>

<div class="faq-list">

	<ul>
		<?foreach ($arResult["ITEMS"] as $val):?>
		<li><a href="#<?=$val["ID"]?>"><?=$val["NAME"]?></a></li>
		<?endforeach;?>
	</ul>

	<?foreach ($arResult["ITEMS"] as $val):?>
	<?
	$this->AddEditAction($val['ID'], $val['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($val['ID'], $val['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('FAQ_DELETE_CONFIRM')));
	?>
	<div class="faq-item" id="<?=$val["ID"]?>">

		<h2><?=$val["NAME"]?></h2>
		<div class="faq-item-answer">
			<?=$val["DETAIL_TEXT"]?>
		</div>

	</div>

	<?endforeach;?>


</div>
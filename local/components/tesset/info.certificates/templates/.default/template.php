<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<table>
    <?foreach ($arResult["ITEMS"] as $id => $item) : ?>
    <?
    $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
    <tr id="<?=$this->GetEditAreaId($item['ID']);?>">
        <td><img src="<?=$item["LOGO"]?>"/></td>
        <td><?=$item["PREVIEW_TEXT"]?></td>
        <td><div class="gallery"><a href="<?=$item["PREVIEW_PICTURE"]?>"><img src="<?=$item["DETAIL_PICTURE"]?>"/></a></div></td>
    </tr>
    <?endforeach;?>
</table>
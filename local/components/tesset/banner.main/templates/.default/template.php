<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="slider">
    <div class="container">
        <div class="navShadows"></div>
    </div>
    <div class="container">
        <ul id="slider2">
        <?foreach ($arResult["ITEMS"] as $id => $item) : ?>
        <?
        $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
            <li>
            <table id="<?=$this->GetEditAreaId($item['ID']);?>">
                <tr>
                    <td class="sliderFoto">
                        <img src="<?=$item["PREVIEW_PICTURE"]?>"/>
                    </td>
                    <td><?=$item["PREVIEW_TEXT"]?></td>
                    <td><a href="<?=$item["PROPERTY_LINK_VALUE"]?>" class="sliderButton">ПОДРОБНЕЕ</a></td>
                </tr>
            </table>  
            </li>
            <?endforeach;?>
        </ul>
    </div>
</div>
<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

$this->setFrameMode(true);

if(is_array($arResult['ITEMS']) && count($arResult['ITEMS']) > 0):
?>
<div class="row faq-page">

    <div class="col col-md-9 faq-page__filter">
        <?php if(is_array($arResult['FILTER']['VALUES']) && count($arResult['FILTER']['VALUES'])>0): ?>
            <?php foreach($arResult['FILTER']['VALUES'] as $arValue): ?>
                <button class="btn btn-default btn-button js-filter-button" type="button" data-filter="<?=htmlspecialcharsbx($arValue['XML_ID'])?>"><?=$arValue['VALUE']?></button>
            <?php endforeach; ?>
            <button class="btn btn-default btn-button active js-filter-button" type="button" data-filter=""><?=GetMessage('RS.FLYAWAY.FILTER_ALL')?></button>
        <?php endif; ?>
    </div>

    <div class="col col-md-9 faq-page__answers">

        <div class="panel-group" role="tablist" aria-multiselectable="true">
            <?php
            foreach($arResult["ITEMS"] as $index => $arItem):
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="item panel panel-default filter<?=$arItem['DISPLAY_PROPERTIES'][$arParams['RSMONOPOLY_PROP_FAQ_TYPE']]['VALUE_XML_ID']?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <div class="panel-heading" role="tab" id="heading<?=$index?>">
                    <div class="panel-title roboto">
                        <a class="<?php if($index>0):?>collapsed<?php endif; ?>"
                            data-toggle="collapse"
                            data-parent="#accordion<?=$index?>"
                            href="#collapse<?=$index?>"
                            aria-expanded="true"
                            aria-controls="collapseOne<?=$index?>"
                        ><?=$arItem['NAME']?></a>
                    </div>
                </div>
                <div id="collapse<?=$index?>" class="panel-collapse collapse<?if($index==0):?> in<?endif;?>" role="tabpanel" aria-labelledby="heading<?=$index?>">
                    <div class="panel-body"><?=$arItem['PREVIEW_TEXT']?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>

</div>
<?php endif;

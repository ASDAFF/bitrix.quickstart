<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$boolShowDepth = (1 < $arParams['TOP_DEPTH']);
$strDepthSym = '>';
?>

<?
if ('Y' == $arParams['SHOW_PARENT_NAME'] && 0 < $arResult['SECTION']['ID']) {
	$this->AddEditAction($arResult['SECTION']['ID'], $arResult['SECTION']['EDIT_LINK'], CIBlock::GetArrayByID($arResult['SECTION']["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arResult['SECTION']['ID'], $arResult['SECTION']['DELETE_LINK'], CIBlock::GetArrayByID($arResult['SECTION']["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));

	?>
    <? 
        $uf_name = array('UF_H1');
        $arHead = CIBlockSection::GetList(Array("SORT"=>"­­ASC"), Array("IBLOCK_ID" => $arParams['IBLOCK_ID'] , "CODE" => $arParams['SECTION_CODE']), false, $uf_name);
        while($element = $arHead->GetNext()){
            $uf_h1 = $element['UF_H1'];
        }
    ?>
    <h1><? if(!empty($uf_h1)){ echo $uf_h1;}else{echo $arResult['SECTION']['NAME']; }?></h1>
       <?=$arResult['SECTION']['DESCRIPTION'];?>
    <hr style="margin-bottom: <?if(!count($arResult['SECTIONS'])):?>-20px;<?else:?>10px;<?endif;?> " />
    <?
}
if (0 < $arResult["SECTIONS_COUNT"]): ?>
    
    <?foreach ($arResult['SECTIONS'] as &$arSection):?>
    <?$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
    $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));

    if (false === $arSection['PICTURE'])
        $arSection['PICTURE'] = array(
            'SRC' => $this->GetFolder().'/images/line-empty.png'
        );
    ?>
    <div id="<? echo $this->GetEditAreaId($arSection['ID']); ?>" class="category-item">
        <a class="thumbnail thumb" href="<? echo $arSection['SECTION_PAGE_URL']; ?>" >
        <img src="<? echo $arSection['PICTURE']['SRC']; ?>" alt="<?=$arSection['NAME']?>">
        </a>
        <a href="<? echo $arSection['SECTION_PAGE_URL']; ?>"><? echo $arSection['NAME']; ?><?/* <span class="count">(<? echo $arSection['ELEMENT_CNT']; ?>)</span>*/?></a>
    
    </div>
    <?/*<div class="row">
    <div class="col-xs-12">
        <?$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
        $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));

        if (false === $arSection['PICTURE'])
            $arSection['PICTURE'] = array(
                'SRC' => $this->GetFolder().'/images/line-empty.png'
            );
        ?>
        <div class="sect-item" id="<? echo $this->GetEditAreaId($arSection['ID']); ?>">
            <h2><? echo str_repeat($strDepthSym, $arSection['RELATIVE_DEPTH']);
            ?><a href="<? echo $arSection['SECTION_PAGE_URL']; ?>"><? echo $arSection['NAME']; ?> <span class="count">(<? echo $arSection['ELEMENT_CNT']; ?>)</span></a></h2>
            <a href="<? echo $arSection['SECTION_PAGE_URL']; ?>" class="thumbnail thumb"><img src="<? echo $arSection['PICTURE']['SRC']; ?>" /></a>
            <? echo $arSection['DESCRIPTION']; ?>
            <div class="clearfix"></div>
        </div>
    </div>
    </div>*/?><?
    endforeach;

    

   unset($arSection);?>
 
<?endif?>

<div class="clearfix"></div>
<?if(count($arResult['SECTIONS'])):?>
<hr class="new_HR" style="margin-bottom: -20px; margin-top: 2px;">
<?endif;?>
 <?/*
<? if ($arResult['SECTION']['ADD_DESCRIPTION']):?>
<div class="add-desc">
<?=$arResult['SECTION']['ADD_DESCRIPTION'];?>
</div>
<? endif?>

<div class="clearfix"></div>*/?>

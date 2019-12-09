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

$itemCount = count($arResult);

$idCompareCount = 'compareList'.$this->randString();
$obCompare = 'ob'.$idCompareCount;
$idCompareTable = $idCompareCount.'_tbl';
$idCompareRow = $idCompareCount.'_row_';
$idCompareAll = $idCompareCount.'_count';
$mainClass = 'cmp_items';
$isAjax = (
    isset($_REQUEST['ajax_id']) && $_REQUEST['ajax_id'] == $idCompareCount &&
    isset($_REQUEST["ajax_action"]) && $_REQUEST["ajax_action"] == "Y"
);
if ($arParams['POSITION_FIXED'] == 'Y')
{
	$mainClass .= ' fix '.($arParams['POSITION'][0] == 'bottom' ? 'bottom' : 'top').' '.($arParams['POSITION'][1] == 'right' ? 'right' : 'left');
}
$style = ($itemCount == 0 ? ' style="display: none;"' : '');
?><div id="<? echo $idCompareCount; ?>" class="<? echo $mainClass; ?> "<? echo $style; ?>><?
unset($style, $mainClass);
if ($isAjax)
{
	$APPLICATION->RestartBuffer();
}
$frame = $this->createFrame($idCompareCount)->begin('');

if (!empty($arResult))
{
?>
<div class="cmp_items__body">
    <ul id="<? echo $idCompareTable; ?>" class="cmp_items__list" itemscope itemtype="http://schema.org/ItemList">
        <?php
        $arrIDs = array();
        foreach($arResult as $arElement):
            $arrIDs[$arElement['ID']] = true;
        ?>
            <li class="cmp_items__item" id="<? echo $idCompareRow.$arElement['PARENT_ID']; ?>" itemprop="itemListElement" itemscope itemtype="http://schema.org/Product">
                <a class="cmp_items__pic" href="<?=$arElement["DETAIL_PAGE_URL"]?>" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                    <?php if (isset($arElement['FIRST_PIC'][0])): ?>
                    <img
                        class="cmp_items__img"
                        src="<?=$arElement['FIRST_PIC'][0]['RESIZE']['small']['src']?>"
                        title="<?=$arElement['NAME']?>" alt="<?=$arElement['NAME']?>" 
                    >
                    <?php else: ?>
                        <img
                            class="cmp_items__img"
                            src="<?=SITE_TEMPLATE_PATH?>/assets/img/noimg.png"
                            title="<?=$arElement['NAME']?>" alt="<?=$arElement['NAME']?>" 
                        >
                    <?php endif; ?>
                </a>
                <!--noindex-->
                    <a href="javascript:void(0);"  data-id="<? echo $arElement['PARENT_ID']; ?>" rel="nofollow">
                        <svg class="cmp_items__del icon-close icon-svg"><use xlink:href="#svg-close"></use></svg>
                    </a>
                <!--/noindex-->
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($itemCount > 0): ?>
        <a class="cmp__link" href="<? echo $arParams["COMPARE_URL"]; ?>" title="<? echo GetMessage('CP_BCCL_TPL_MESS_COMPARE_PAGE'); ?>">
            <svg class="icon-cmp icon-svg"><use xlink:href="#svg-cmp"></use></svg>
            <span class="badge" id="<? echo $idCompareAll; ?>"><? echo $itemCount; ?></span>
            <?/*<? echo GetMessage('CP_BCCL_TPL_MESS_COMPARE_PAGE'); ?>&nbsp;(<span id="<? echo $idCompareAll; ?>"><? echo $itemCount; ?></span>)*/?>
        </a>
    <?php endif; ?>
    <script type="text/javascript">appSLine.compareList = <?=json_encode($arrIDs)?>;</script>
</div>
<?
}
$frame->end();
if ($isAjax)
{
	die();
}
$currentPath = CHTTP::urlDeleteParams(
	$APPLICATION->GetCurPageParam(),
	array(
		$arParams['PRODUCT_ID_VARIABLE'],
		$arParams['ACTION_VARIABLE'],
		'ajax_action'
	),
	array("delete_system_params" => true)
);

$jsParams = array(
	'VISUAL' => array(
		'ID' => $idCompareCount,
	),
	'AJAX' => array(
		'url' => $currentPath,
		'params' => array(
			'ajax_action' => 'Y',
            'ajax_id' => $idCompareCount
		),
		'templates' => array(
			'delete' => (strpos($currentPath, '?') === false ? '?' : '&').$arParams['ACTION_VARIABLE'].'=DELETE_FROM_COMPARE_LIST&'.$arParams['PRODUCT_ID_VARIABLE'].'='
		)
	),
	'POSITION' => array(
		'fixed' => $arParams['POSITION_FIXED'] == 'Y',
		'align' => array(
			'vertical' => $arParams['POSITION'][0],
			'horizontal' => $arParams['POSITION'][1]
		)
	)
);
?></div>
<script type="text/javascript">
var <? echo $obCompare; ?> = new JCCatalogCompareList(<? echo CUtil::PhpToJSObject($jsParams, false, true); ?>)
</script>
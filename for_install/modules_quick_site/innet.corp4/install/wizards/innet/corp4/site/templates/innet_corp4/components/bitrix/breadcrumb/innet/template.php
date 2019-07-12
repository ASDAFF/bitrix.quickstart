<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
__IncludeLang(dirname(__FILE__) . '/lang/' . LANGUAGE_ID . '/' . basename(__FILE__));

$curPage = $GLOBALS['APPLICATION']->GetCurPage($get_index_page = false);

if ($curPage != SITE_DIR) {
    if (empty($arResult) || $curPage != $arResult[count($arResult) - 1]['LINK'])
        $arResult[] = array('TITLE' => htmlspecialcharsback($GLOBALS['APPLICATION']->GetTitle(false, true)), 'LINK' => $curPage);
}

if (empty($arResult))
    return "";

$strReturn = '<div class="breadcrumbs">';
$strReturn .= '<a title="' . GetMessage("INNET_BREADCRUMB_INDEX") . '" href="' . SITE_DIR . '">' . GetMessage("INNET_BREADCRUMB_INDEX") . '</a></li>';
$itemSize = count($arResult);

for ($index = 0; $index < $itemSize; $index++) {
//    $strReturn .= ' <li><span>/</span></li> ';

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if($arResult[$index]["LINK"] <> "" && $index < ($itemSize - 1))
        $strReturn .= '<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'">'.$title.'</a>';
    else
        $strReturn .= '<span>'.$title.'</span>';
}

$strReturn .= '</div>';

return $strReturn;
?>
<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

CModule::IncludeModule('redsign.flyaway');

$aTabs = array();
$aTabs[] = array('DIV' => 'redsign_flyaway', 'TAB' => GetMessage('RS.FLYAWAY.TAB_NAME_SETTINGS'), 'ICON' => '', 'TITLE' => GetMessage('RS.FLYAWAY.TAB_TITLE_SETTINGS'));
if(!empty($_REQUEST['dev'])) {
	$aTabs[] = array('DIV' => 'redsign_flyaway_dev', 'TAB' => GetMessage('RS.FLYAWAY.TAB_NAME_DEV'), 'ICON' => '', 'TITLE' => GetMessage('RS.FLYAWAY.TAB_TITLE_DEV'));
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

if( (isset($_REQUEST['save']) || isset($_REQUEST['apply']) ) && check_bitrix_sessid()){
	RsFlyaway::saveSettings();
}

$arOptionHeaderType = array(
	'REFERENCE' => array(
		GetMessage('RS.FLYAWAY.HEAD_TYPE_1'),
		GetMessage('RS.FLYAWAY.HEAD_TYPE_2'),
		GetMessage('RS.FLYAWAY.HEAD_TYPE_3'),
		// GetMessage('RS.FLYAWAY.HEAD_TYPE_4'),
	),
	'REFERENCE_ID' => array(
		'type1',
		'type2',
		'type3',
		// 'type4',
	),
);

$arOptionPresetsType = array(
	'REFERENCE' => array(
		"1",
		"2",
		"3",
		"4",
		"5",
		"6",
		"7",
		"8",
		"9",
		// GetMessage('RS.FLYAWAY.HEAD_TYPE_4'),
	),
	'REFERENCE_ID' => array(
		'preset_1',
		'preset_2',
		'preset_3',
		'preset_4',
		'preset_5',
		'preset_6',
		'preset_7',
		'preset_8',
		'preset_9',
		'preset_10',
		// 'type4',
	),
);

$arOptionBannerType = array(
	'REFERENCE' => array(
		GetMessage('RS.FLYAWAY.BANNER_TYPE_1'),
		GetMessage('RS.FLYAWAY.BANNER_TYPE_2'),
		GetMessage('RS.FLYAWAY.BANNER_TYPE_3'),
		GetMessage('RS.FLYAWAY.BANNER_TYPE_4'),
		GetMessage('RS.FLYAWAY.BANNER_TYPE_5'),
	),
	'REFERENCE_ID' => array(
		'type1',
		'type2',
		'type3',
		'type4',
		'type5',
	),
);

$arOptionFilterType = array(
	'REFERENCE' => array(
		GetMessage('RS.FLYAWAY.FILTER_TYPE_1'),
		GetMessage('RS.FLYAWAY.FILTER_TYPE_2'),
	),
	'REFERENCE_ID' => array(
		'left',
		'right',
	),
);

$arOptionMenuStyle = array(
	'REFERENCE' => array(
		GetMessage('RS.FLYAWAY.STYLE_MENU_1'),
		GetMessage('RS.FLYAWAY.STYLE_MENU_2'),
	),
	'REFERENCE_ID' => array(
		'type1',
		'type2',
	),
);

$arOptionOptionFrom = array(
	'REFERENCE' => array(
		GetMessage('RS.FLYAWAY.OPTION_FROM_MODULE'),
		GetMessage('RS.FLYAWAY.OPTION_FROM_SESSION'),
	),
	'REFERENCE_ID' => array(
		'module',
		'session',
	),
);

$tabControl->Begin();
?><form method="post" name="rsflyaway_option" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
echo bitrix_sessid_post();



$tabControl->BeginNextTab();
?><tr class="heading"><?
	?><td colspan="3"><?=GetMessage('RS.FLYAWAY.SOLUTION')?></td><?
?></tr><?
$gencolor = COption::GetOptionString('redsign.flyaway', 'gencolor', 'ffe062');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.GENCOLOR')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r">#<input type="text" name="gencolor" value="<?=$gencolor?>" /></td><?
?></tr><?
$secondColor = COption::GetOptionString('redsign.flyaway', 'secondColor', '555555');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.SECOND_COLOR')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r">#<input type="text" name="secondColor" value="<?=$secondColor?>" /></td><?
?></tr><?
$openMenuType = COption::GetOptionString('redsign.flyaway', 'openMenuType', 'type1');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MENU_STYLE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('openMenuType', $arOptionMenuStyle, $openMenuType)?></td><?
?></tr><?
$presets = COption::GetOptionString('redsign.flyaway', 'presets', 'preset_1');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.PRESETS_TYPE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('presets', $arOptionPresetsType, $presets)?></td><?
?></tr><?
$bannerType = COption::GetOptionString('redsign.flyaway', 'bannerType', 'type1');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.BANNER_TYPE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('headStyle', $arOptionBannerType, $bannerType)?></td><?
?></tr><?
$filterSide = COption::GetOptionString('redsign.flyaway', 'filterSide', 'left');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.FILTER_TYPE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('filterSide', $arOptionFilterType, $filterSide)?></td><?
?></tr><?
/*$blackMode = COption::GetOptionString('redsign.flyaway', 'blackMode', 'N');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.BLACK_MODE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="blackMode" value="Y"<?if($blackMode=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?*/

?><tr class="heading"><?
	?><td colspan="3"><?=GetMessage('RS.FLYAWAY.MAIN_SETTINGS')?></td><?
?></tr><?
$Fichi = COption::GetOptionString('redsign.flyaway', 'Fichi', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_FICHI')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="Fichi" value="Y"<?if($Fichi=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$SmallBanners = COption::GetOptionString('redsign.flyaway', 'SmallBanners', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_SMALL_BANNERS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="SmallBanners" value="Y"<?if($SmallBanners=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$New = COption::GetOptionString('redsign.flyaway', 'New', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_NEW')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="New" value="Y"<?if($New=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$PopularItem = COption::GetOptionString('redsign.flyaway', 'PopularItem', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_POPULAR_ITEM')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="PopularItem" value="Y"<?if($PopularItem=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$Service = COption::GetOptionString('redsign.flyaway', 'Service', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_SERVICE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="Service" value="Y"<?if($Service=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$AboutAndReviews = COption::GetOptionString('redsign.flyaway', 'AboutAndReviews', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_ABOUT_AND_REVIEWS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="AboutAndReviews" value="Y"<?if($AboutAndReviews=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$News = COption::GetOptionString('redsign.flyaway', 'News', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_NEWS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="News" value="Y"<?if($News=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$Partners = COption::GetOptionString('redsign.flyaway', 'Partners', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_PARTNERS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="Partners" value="Y"<?if($Partners=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$Gallery = COption::GetOptionString('redsign.flyaway', 'Gallery', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.MS_GALLERY')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="Gallery" value="Y"<?if($Gallery=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?


if(!empty($_REQUEST['dev'])) {
	$tabControl->BeginNextTab();
	?><tr><?
		?><td colspan="2"><?=BeginNote();?><?=GetMessage('RS.FLYAWAY.DEV_NOTE')?><?=EndNote();?></td><?
	?></tr><?
	?><tr class="heading"><?
		?><td colspan="3"><?=GetMessage('RS.FLYAWAY.DEVELOPER_SETTINGS')?></td><?
	?></tr><?
	$optionFrom = COption::GetOptionString('redsign.flyaway', 'optionFrom', 'module');
	?><tr><?
		?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.FLYAWAY.OPTION_FROM')?>:</td><?
		?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('optionFrom', $arOptionOptionFrom, $optionFrom)?></td><?
	?></tr><?
}



$tabControl->Buttons(array());
$tabControl->End();
?></form>
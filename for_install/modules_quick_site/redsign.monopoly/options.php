<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');

CModule::IncludeModule('redsign.monopoly');

$aTabs = array();
$aTabs[] = array('DIV' => 'redsign_monopoly', 'TAB' => GetMessage('RS.MONOPOLY.TAB_NAME_SETTINGS'), 'ICON' => '', 'TITLE' => GetMessage('RS.MONOPOLY.TAB_TITLE_SETTINGS'));
if(!empty($_REQUEST['dev'])) {
	$aTabs[] = array('DIV' => 'redsign_monopoly_dev', 'TAB' => GetMessage('RS.MONOPOLY.TAB_NAME_DEV'), 'ICON' => '', 'TITLE' => GetMessage('RS.MONOPOLY.TAB_TITLE_DEV'));
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

if( (isset($_REQUEST['save']) || isset($_REQUEST['apply']) ) && check_bitrix_sessid()){
	RSMonopoly::saveSettings();
}

$arOptionHeaderType = array(
	'REFERENCE' => array(
		GetMessage('RS.MONOPOLY.HEAD_TYPE_1'),
		GetMessage('RS.MONOPOLY.HEAD_TYPE_2'),
		GetMessage('RS.MONOPOLY.HEAD_TYPE_3'),
		// GetMessage('RS.MONOPOLY.HEAD_TYPE_4'),
	),
	'REFERENCE_ID' => array(
		'type1',
		'type2',
		'type3',
		// 'type4',
	),
);

$arOptionHeaderStyle = array(
	'REFERENCE' => array(
		GetMessage('RS.MONOPOLY.HEAD_STYLE_1'),
		GetMessage('RS.MONOPOLY.HEAD_STYLE_2'),
		GetMessage('RS.MONOPOLY.HEAD_STYLE_3'),
	),
	'REFERENCE_ID' => array(
		'style1',
		'style2',
		'style3',
	),
);

$arOptionFilterType = array(
	'REFERENCE' => array(
		GetMessage('RS.MONOPOLY.FILTER_TYPE_1'),
		GetMessage('RS.MONOPOLY.FILTER_TYPE_2'),
		GetMessage('RS.MONOPOLY.FILTER_TYPE_0'),
	),
	'REFERENCE_ID' => array(
		'ftype1',
		'ftype2',
		'ftype0',
	),
);

$arOptionSidebarPos = array(
	'REFERENCE' => array(
		GetMessage('RS.MONOPOLY.SIDEBAR_POS_1'),
		GetMessage('RS.MONOPOLY.SIDEBAR_POS_2'),
	),
	'REFERENCE_ID' => array(
		'pos1',
		'pos2',
	),
);

$arOptionOptionFrom = array(
	'REFERENCE' => array(
		GetMessage('RS.MONOPOLY.OPTION_FROM_MODULE'),
		GetMessage('RS.MONOPOLY.OPTION_FROM_SESSION'),
	),
	'REFERENCE_ID' => array(
		'module',
		'session',
	),
);

$tabControl->Begin();
?><form method="post" name="rsmonopoly_option" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
echo bitrix_sessid_post();



$tabControl->BeginNextTab();
?><tr class="heading"><?
	?><td colspan="3"><?=GetMessage('RS.MONOPOLY.SOLUTION')?></td><?
?></tr><?
$gencolor = COption::GetOptionString('redsign.monopoly', 'gencolor', '0084c9');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.GENCOLOR')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r">#<input type="text" name="gencolor" value="<?=$gencolor?>" /></td><?
?></tr><?
$textColorMenu = COption::GetOptionString('redsign.monopoly', 'textColorMenu', 'ffffff');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.TEXT_COLOR_MENU')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r">#<input type="text" name="textColorMenu" value="<?=$textColorMenu?>" /></td><?
?></tr><?
$headType = COption::GetOptionString('redsign.monopoly', 'headType', 'type1');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.HEAD_TYPE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('headType', $arOptionHeaderType, $headType)?></td><?
?></tr><?
$headStyle = COption::GetOptionString('redsign.monopoly', 'headStyle', 'style1');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.HEAD_STYLE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('headStyle', $arOptionHeaderStyle, $headStyle)?></td><?
?></tr><?
$filterType = COption::GetOptionString('redsign.monopoly', 'blackMode', 'N');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.FILTER_TYPE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('filterType', $arOptionFilterType, $filterType)?></td><?
?></tr><?
$blackMode = COption::GetOptionString('redsign.monopoly', 'blackMode', 'N');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.BLACK_MODE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="blackMode" value="Y"<?if($blackMode=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$sidebarPos = COption::GetOptionString('redsign.monopoly', 'sidebarPos', 'pos1');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.SIDEBAR_POS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('sidebarPos', $arOptionSidebarPos, $sidebarPos)?></td><?
?></tr><?

?><tr class="heading"><?
	?><td colspan="3"><?=GetMessage('RS.MONOPOLY.MAIN_SETTINGS')?></td><?
?></tr><?
$MSFichi = COption::GetOptionString('redsign.monopoly', 'MSFichi', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_FICHI')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSFichi" value="Y"<?if($MSFichi=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSCatalog = COption::GetOptionString('redsign.monopoly', 'MSCatalog', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_CATALOG')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSCatalog" value="Y"<?if($MSCatalog=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSService = COption::GetOptionString('redsign.monopoly', 'MSService', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_SERVICE')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSService" value="Y"<?if($MSService=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSAboutAndReviews = COption::GetOptionString('redsign.monopoly', 'MSAboutAndReviews', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_ABOUT_AND_REVIEWS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSAboutAndReviews" value="Y"<?if($MSAboutAndReviews=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSNews = COption::GetOptionString('redsign.monopoly', 'MSNews', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_NEWS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSNews" value="Y"<?if($MSNews=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSPartners = COption::GetOptionString('redsign.monopoly', 'MSPartners', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_PARTNERS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSPartners" value="Y"<?if($MSPartners=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSGallery = COption::GetOptionString('redsign.monopoly', 'MSGallery', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_GALLERY')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSGallery" value="Y"<?if($MSGallery=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?
$MSSmallBanners = COption::GetOptionString('redsign.monopoly', 'MSSmallBanners', 'Y');
?><tr><?
	?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.MS_SMALL_BANNERS')?>:</td><?
	?><td width="50%" class="adm-detail-content-cell-r"><input type="checkbox" name="MSSmallBanners" value="Y"<?if($MSSmallBanners=='Y'):?> checked="checked" <?endif;?> /></td><?
?></tr><?

if(!empty($_REQUEST['dev'])) {
	$tabControl->BeginNextTab();
	?><tr><?
		?><td colspan="2"><?=BeginNote();?><?=GetMessage('RS.MONOPOLY.DEV_NOTE')?><?=EndNote();?></td><?
	?></tr><?
	?><tr class="heading"><?
		?><td colspan="3"><?=GetMessage('RS.MONOPOLY.DEVELOPER_SETTINGS')?></td><?
	?></tr><?
	$optionFrom = COption::GetOptionString('redsign.monopoly', 'optionFrom', 'module');
	?><tr><?
		?><td width="50%" class="adm-detail-content-cell-l"><?=GetMessage('RS.MONOPOLY.OPTION_FROM')?>:</td><?
		?><td width="50%" class="adm-detail-content-cell-r"><?=SelectBoxFromArray('optionFrom', $arOptionOptionFrom, $optionFrom)?></td><?
	?></tr><?
}



$tabControl->Buttons(array());
$tabControl->End();
?></form>
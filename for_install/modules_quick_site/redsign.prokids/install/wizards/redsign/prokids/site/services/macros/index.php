<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('iblock'))
	return;

if(!CModule::IncludeModule('catalog'))
	return;

	$wiz_site_id = WIZARD_SITE_ID;

if(isset($wiz_site_id) && ($wiz_site_id!='s1'))
	$SITE_SUBCATALOG = WIZARD_SITE_DIR.'/';

	// replace macros #SITE_DIR#
	$sitDir = WIZARD_SITE_PATH;
	WizardServices::ReplaceMacrosRecursive($_SERVER['DOCUMENT_ROOT'].'/', Array('SITE_DIR' => WIZARD_SITE_DIR));
	WizardServices::ReplaceMacrosRecursive($sitDir.'/', Array('SITE_DIR' => WIZARD_SITE_DIR));

	$tmplDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/';
	WizardServices::ReplaceMacrosRecursive($tmplDir, Array('SITE_DIR' => WIZARD_SITE_DIR));
	
	$tmplDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/.default/';
	WizardServices::ReplaceMacrosRecursive($tmplDir, Array('SITE_DIR' => WIZARD_SITE_DIR));

	// ____________________________________________________________________________________________________________________________ //

	// take ID iblocks

	$arrFilter1 = array(
		array(
			'IBLOCK_TYPE' => 'catalog',
			'IBLOCK_CODE' => 'catalog',
			'IBLOCK_XML_ID' => 'catalog_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'catalog',
			'IBLOCK_CODE' => 'offers',
			'IBLOCK_XML_ID' => 'offers_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'presscenter',
			'IBLOCK_CODE' => 'news',
			'IBLOCK_XML_ID' => 'news_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'presscenter',
			'IBLOCK_CODE' => 'action',
			'IBLOCK_XML_ID' => 'action_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'presscenter',
			'IBLOCK_CODE' => 'banners',
			'IBLOCK_XML_ID' => 'banners_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'presscenter',
			'IBLOCK_CODE' => 'brands',
			'IBLOCK_XML_ID' => 'brands_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'presscenter',
			'IBLOCK_CODE' => 'files',
			'IBLOCK_XML_ID' => 'files_'.WIZARD_SITE_ID,
		),
		array(
			'IBLOCK_TYPE' => 'presscenter',
			'IBLOCK_CODE' => 'shops',
			'IBLOCK_XML_ID' => 'shops_'.WIZARD_SITE_ID,
		),
	);

	foreach($arrFilter1 as $filter1){
		$rsIBlock = CIBlock::GetList(array(), array( 'TYPE' => $filter1['IBLOCK_TYPE'], 'CODE' => $filter1['IBLOCK_CODE'], 'XML_ID' => $filter1['IBLOCK_XML_ID'] ));
		if($arIBlock = $rsIBlock->Fetch()){
			$code1 = $filter1['IBLOCK_CODE'];
			$arrIBlockIDs[$code1] = $arIBlock['ID'];
		}
	}

	// General macros
	// #SITE_DIR#

	// #IBLOCK_ID_catalog#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/_index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/auth/index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/brands/index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/catalog/index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/search/index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/personal/viewed/index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/personal/favorite/index.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/header.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/footer.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/redsign/easycart/gopro/compare.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/redsign/easycart/gopro/favorite.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/redsign/easycart/gopro/viewed_products.php', array('IBLOCK_ID_catalog' => $arrIBlockIDs['catalog']));

	// #IBLOCK_ID_offers#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/catalog/index.php', array('IBLOCK_ID_offers' => $arrIBlockIDs['offers']));

	// #IBLOCK_ID_news#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/_index.php', array('IBLOCK_ID_news' => $arrIBlockIDs['news']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/news/index.php', array('IBLOCK_ID_news' => $arrIBlockIDs['news']));

	// #IBLOCK_ID_action#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/_index.php', array('IBLOCK_ID_action' => $arrIBlockIDs['action']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/action/index.php', array('IBLOCK_ID_action' => $arrIBlockIDs['action']));
	
	// #IBLOCK_ID_banners#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/_index.php', array('IBLOCK_ID_banners' => $arrIBlockIDs['banners']));

	// #IBLOCK_ID_brands#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/_index.php', array('IBLOCK_ID_brands' => $arrIBlockIDs['brands']));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/brands/index.php', array('IBLOCK_ID_brands' => $arrIBlockIDs['brands']));

	// #IBLOCK_ID_files#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/docs/index.php', array('IBLOCK_ID_files' => $arrIBlockIDs['files']));

	// #IBLOCK_ID_shops#
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/shops/index.php', array('IBLOCK_ID_shops' => $arrIBlockIDs['shops']));
	// ____________________________________________________________________________________________________________________________ //

	if(CModule::IncludeModule('forum')){
		// #COMMENTS_FORUM_ID#
		$arFilter = array(
			'LID' => WIZARD_SITE_ID
		);
		$arOrder = array(
			'SORT' => 'ASC',
			'NAME' => 'ASC'
		);
		$db_Forum = CForumNew::GetList($arOrder, $arFilter);
		while($ar_Forum = $db_Forum->Fetch()){
			$FORUM_ID = $ar_Forum['ID'];
		}
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/catalog/index.php', Array('COMMENTS_FORUM_ID' => $FORUM_ID));
	}

	// ____________________________________________________________________________________________________________________________ //

	// #SHOP_PHONE#
	$shopPhone = $wizard->GetVar('siteTelephoneCode').$wizard->GetVar('siteTelephone');
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/include_areas/footer_phone1.php', array('SHOP_PHONE' => $shopPhone));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/include_areas/footer_phone2.php', array('SHOP_PHONE' => $shopPhone));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/include_areas/header_phone.php', array('SHOP_PHONE' => $shopPhone));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/contacts/index.php', array('SHOP_PHONE' => $shopPhone));
	
	// #SITE_COPYRIGHT#
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/include_areas/footer_sitecopy.php', array('SITE_COPYRIGHT' => GetMessage('SITE_COPYRIGHT')));
	
	// #SITE_JOIN#
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/include_areas/footer_socservice.php', array('SITE_JOIN' => GetMessage('SITE_JOIN')));
	
	// #SHOP_EMAIL#
	$EMail = $wizard->GetVar('shopEmail');
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/contacts/index.php', array('SHOP_EMAIL' => $EMail));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/feedback/index.php', array('SHOP_EMAIL' => $EMail));

	// #SITE_SCHEDULE#
	$siteSchedule = $wizard->GetVar('siteSchedule');
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/contacts/index.php', array('SITE_SCHEDULE' => $siteSchedule));
	
	// #SITE_SMALL_ADDRESS#
	$smallAdress = $wizard->GetVar('smallAdress');
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/contacts/index.php', array('SITE_SMALL_ADDRESS' => $smallAdress));

	// replace siteDiscription and siteKeywords
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/.section.php', array('SITE_DESCRIPTION' => $wizard->GetVar('siteMetaDescription')));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH.'/.section.php', array('SITE_KEYWORDS' => $wizard->GetVar('siteMetaKeywords')));

	// ____________________________________________________________________________________________________________________________ //

	// #CATALOG_TITLE#
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/footer.php', array('CATALOG_TITLE' => GetMessage('CATALOG_TITLE')));
	
	// #MENU_EXTRA#
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/footer.php', array('MENU_EXTRA' => GetMessage('MENU_EXTRA')));

	// #REDSIGN_COPYRIGHTS#
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/footer.php', array('REDSIGN_COPYRIGHTS' => GetMessage('REDSIGN_COPYRIGHTS')));
	
	// ____________________________________________________________________________________________________________________________ //
	
	// VIEW NAMES (for catalog.sorter)
	#CATALOG_VIEW_TABLE#, #CATALOG_VIEW_GALLERY#, #CATALOG_VIEW_SHOWCASE#
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/bitrix/catalog/gopro/section.php', array('CATALOG_VIEW_TABLE' => GetMessage('CATALOG_VIEW_TABLE')));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/bitrix/catalog/gopro/section.php', array('CATALOG_VIEW_GALLERY' => GetMessage('CATALOG_VIEW_GALLERY')));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/bitrix/catalog/gopro/section.php', array('CATALOG_VIEW_SHOWCASE' => GetMessage('CATALOG_VIEW_SHOWCASE')));
	
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/bitrix/catalog/gopro/element.php', array('CATALOG_VIEW_TABLE' => GetMessage('CATALOG_VIEW_TABLE')));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/bitrix/catalog/gopro/element.php', array('CATALOG_VIEW_GALLERY' => GetMessage('CATALOG_VIEW_GALLERY')));
	CWizardUtil::ReplaceMacros($_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.WIZARD_TEMPLATE_ID.'/components/bitrix/catalog/gopro/element.php', array('CATALOG_VIEW_SHOWCASE' => GetMessage('CATALOG_VIEW_SHOWCASE')));
	
	
	
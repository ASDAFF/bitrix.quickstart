<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;
if(!CModule::IncludeModule('catalog'))
    return;
if(COption::GetOptionString("siteclothing", "wizard_installed", "N", WIZARD_SITE_ID) == "Y")
	return;

if (!CModule::IncludeModule("blog")) return;
if (!CModule::IncludeModule("form")) return;
if (!CModule::IncludeModule("sale")) return;
if (!CModule::IncludeModule("search")) return; 

COption::SetOptionString('main', 'new_user_registration', 'Y');
COption::SetOptionString('main', 'captcha_registration', 'Y');

COption::SetOptionString('search', 'use_tf_cache', 'Y');
COption::SetOptionString('search', 'use_word_distance', 'Y');
COption::SetOptionString('search', 'use_social_rating', 'Y');

// создаем рубрики в подписке
if(!CModule::IncludeModule("subscribe")) {
	return;
}

COption::SetOptionString('subscribe', 'subscribe_section', '#SITE_DIR#cabinet/subscr/?');

//установка настроек сервиса уведомлений
$service_subscribe = COption::GetOptionString('novagr.shop', "service_subscribe");
$arReplace = array(
    'CATALOG_SUBSCRIBE_ENABLE' => ($service_subscribe == "Y") ? "Y" : "N",
);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . "/catalog/detail.php", $arReplace);


// id подписки на новости
$newsRubricId = false;
// id подписки на блоги
$blogsRubricId = false;

$siteId = WIZARD_SITE_ID;

$arOrder = Array("SORT"=>"ASC", "NAME"=>"ASC");
$arFilter = Array("ACTIVE"=>"Y", "LID"=>$siteId);
$rsRubric = CRubric::GetList($arOrder, $arFilter);
$arRubrics = array();
while ($arRubric = $rsRubric->GetNext())
{
	if ($arRubric["CODE"] == 'news') $newsRubricId = $arRubric["ID"];
	if ($arRubric["CODE"] == 'blogs') $blogsRubricId = $arRubric["ID"];
	//$arResult["RUBRIC_LIST"][] = $arRubric;
}

if (!$newsRubricId) {
	$rubric = new CRubric;
	$arFields = Array(
			"ACTIVE" => "Y",
			"NAME" => GetMessage('MACROS_12'),
			"SORT" => 1,
			"DESCRIPTION" => GetMessage('MACROS_13'),
			"CODE" => 'news',
			"LID" => $siteId
	);
	//deb($arFields);
	$newsRubricId = $rubric->Add($arFields);
	//if($ID == false)
	//	echo $rubric->LAST_ERROR;
}

if (!$blogsRubricId) {

    $rsIBlock = CIBlock::GetList(array(), array("CODE" => "blogs","LID"=>WIZARD_SITE_ID));
    if ($arIBlock = $rsIBlock -> Fetch())
    {
        $rubric = new CRubric;
        $arFields = Array(
            "ACTIVE" => "Y",
            "NAME" => GetMessage('MACROS_14'),
            "SORT" => 2,
            "DESCRIPTION" => GetMessage('MACROS_15'),
            "CODE" => 'blogs',
            "LID" => $siteId
        );
        $blogsRubricId = $rubric->Add($arFields);
    }
}

// add new user group, new user and add new user in new group (for 1c exchange)
$rsUser = CUser::GetByLogin("1cexchange");
if($arUser = $rsUser->Fetch())
{
    $userID = $arUser['ID'];
} else {
    $rsUser = new CUser;
    $arFields = Array(
        'NAME'                => "1C Exchange",
        'LAST_NAME'            => "System User",
        'LOGIN'                => "1cexchange",
        'EMAIL'                => "email@not.exists",
        'ACTIVE'            => "Y",
        'PASSWORD'            => '5c;4,@U&dBxbU$pKBH*',
        'CONFIRM_PASSWORD'    => '5c;4,@U&dBxbU$pKBH*'
    );
    $userID = $rsUser -> Add($arFields);   
}


$filter = Array
(
    "NAME"           => "1cexchange",
);
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); // выбираем группы
$groupID = false;
while($asGroups = $rsGroups->Fetch()) {
    $groupID = $asGroups['ID']; break;
}

if($groupID==false)
{
    $rsGroup = new CGroup;
    $arFields = Array(
        'ACTIVE'       => "Y",
        'C_SORT'       => 100,
        'NAME'         => "1cexchange",
        'DESCRIPTION'  => "",
        'USER_ID'      => array($userID)
    );
    $groupID = $rsGroup -> Add($arFields);
} else {
    $arGroups = CUser::GetUserGroup($userID);
    $arGroups[] = $groupID;
    CUser::SetUserGroup($userID, $arGroups); 
}
$arReplace = array(
	'GROUP_1C_EXCHANGE' => $groupID
);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/bitrix/admin/.access.php", $arReplace);

$arReplaceSub = array(
		'NEWS_RUBRIC_ID' => $newsRubricId,
		'BLOGS_RUBRIC_ID' => $blogsRubricId
);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplaceSub);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/subscr.php", $arReplaceSub);

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "materials","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'MATERIALS_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "countries","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'COUNTRIES_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "vendor","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'VENDOR_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "std_sizes","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'STD_SIZES_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);

    $SIZE_NO_RESULT = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arIBlock['ID'],"XML_ID"=>"1136"));
    if($SIZE_NO = $SIZE_NO_RESULT->Fetch())
    {
        $arReplace = array(
            'SIZE_NO_ID' => $SIZE_NO['ID']
        );
        CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
        CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    }
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "colors","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'COLORS_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);

    $COLOR_NO_RESULT = CIBlockElement::GetList(array(),array("IBLOCK_ID"=>$arIBlock['ID'],"XML_ID"=>"1332"));
    if($COLOR_NO = $COLOR_NO_RESULT->Fetch())
    {
        $arReplace = array(
            'COLOR_NO_ID' => $COLOR_NO['ID']
        );
        CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
        CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    }
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "LandingPages","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'LANDINGPAGES_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/product", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "novagr_standard_images","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'FASHION_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/header.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/footer.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/fashion.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/catalog.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/search/title.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/imageries/index.php", $arReplace);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/components/novagroup/catalog.element/templates/.default/template.php", $arReplace);
	//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/detail.php", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => COption::GetOptionString("novagr.shop", 'xml_products_file'.WIZARD_SITE_ID)));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'CATALOG_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/header.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/footer.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/fashion.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/catalog.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/search/title.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/imageries/index.php", $arReplace);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/ajax", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/product", $arReplace);
	//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/components/novagroup/catalog.element/templates/.default/template.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => COption::GetOptionString("novagr.shop", 'xml_products_offers_file'.WIZARD_SITE_ID)));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'OFFERS_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/footer.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/fashion.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/catalog.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/imageries/index.php", $arReplace);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/ajax", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/product", $arReplace);
	//CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/search/title.php", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "photos","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'PHOTOS_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/footer.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/fashion.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/filter/catalog.php", $arReplace);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "quickbuy_order","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'QUICKBUY_LIST_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog/cabinet", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog/element", $arReplace);
}
$rsIBlock = CIBlock::GetList(array(), array("CODE" => "quickbuy_product","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'QUICKBUY_PRODUCT_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog/cabinet", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog/element", $arReplace);
}

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "system","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'SYSTEM_IBLOCK_ID' => $arIBlock['ID']
	);                                                                    
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/header.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/search/title.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/news/top_contacts.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/news/pure_detail.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/404.php", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include", $arReplace);
}

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "seo_urls","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
    $arReplace = array(
        'SEO_IBLOCK_ID' => $arIBlock['ID']
    );
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}

//include("feedback.php");
$formId = COption::GetOptionInt("novagr.shop", 'formFeedbackID'.WIZARD_SITE_ID );
if ($formId >0)
{
	$arReplace = array(
		'FEEDBACK_WEB_FORM_ID' => $formId
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/form/feedback.php", $arReplace);
}  
$arReplace = array(
	'SITE_ID' => WIZARD_SITE_ID,
    'SITE_DIR' => WIZARD_SITE_DIR
);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/templates/demoshop/header.php", $arReplace);
CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/sect_search.php", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/about", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/about/howto", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/about/delivery", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/auth", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/auth/ajax", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/blogs", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/brands", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/cabinet", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/cabinet/order", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/cabinet/order/make", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/cabinet/order/payment", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/imageries", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/ajax", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/filter", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/form", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/js", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/news", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/search", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/news", $arReplace);
CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/", $arReplace);


$rsIBlock = CIBlock::GetList(array(), array("CODE" => "articles","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'ARTICLES_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/howto/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/about/delivery/index.php", $arReplace);
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/product", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/components/novagroup/catalog.element/templates/.default/template.php", $arReplace);
}

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "blogs","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'BLOG_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/blogs/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "news","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'NEWS_IBLOCK_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/news/index.php", $arReplace);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/".WIZARD_SITE_ID."/init.php", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/local/php_interface/ru/init.php", $arReplace);
}

$rsIBlock = CIBlock::GetList(array(), array("CODE" => "banners", "TYPE" => "banners","LID"=>WIZARD_SITE_ID));
if ($arIBlock = $rsIBlock -> Fetch())
{
	$arReplace = array(
		'BANNERS_ID' => $arIBlock['ID']
	);
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."_index.php", $arReplace);
}

// creating user groups and iblocks rights 
$filter = Array("ACTIVE"         => "Y");
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); // выбираем группы

$adminShopGroupExist = false;
$editorsGroupExist = false;
while($asGroups = $rsGroups->Fetch()) {
	if ($asGroups["NAME"] == GetMessage('MACROS_8')) {
		$saleAdminGroupId = $asGroups["ID"];
		$adminShopGroupExist = true;
	}
	if ($asGroups["NAME"] == GetMessage('MACROS_10')) {
		$editorGroupId = $asGroups["ID"];
		$editorsGroupExist = true;
	}	
}

// создаем группу Администраторы интернет-магазина
if ($adminShopGroupExist == false) {
	$group = new CGroup;
	$arFields = Array(
			"ACTIVE"       => "Y",
			"C_SORT"       => 100,
			"NAME"         => GetMessage('MACROS_8'),
			"DESCRIPTION"  => GetMessage('MACROS_9'),
			"USER_ID"      => array(),
			"STRING_ID"      => "sale_administrator",
	);
	$saleAdminGroupId = $group->Add($arFields);
	//if (strlen($group->LAST_ERROR)>0) ShowError($group->LAST_ERROR);
}
if ($editorsGroupExist == false) {
	$group = new CGroup;
	$arFields = Array(
			"ACTIVE"       => "Y",
			"C_SORT"       => 200,
			"NAME"         => GetMessage('MACROS_10'),
			"DESCRIPTION"  => GetMessage('MACROS_11'),
			"USER_ID"      => array(),
			"STRING_ID"      => "content_editor",
	);
	$editorGroupId = $group->Add($arFields);
}

//sale_administrator
$arFilter = array('STRING_ID' => "sale_administrator");
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $arFilter);
if($arGroup = $rsGroups -> Fetch())
{
    $arReplace = array('GROUP_SADMIN' => $arGroup['ID']);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/imageries/index.php", $arReplace);
}

//trade_group
$arFilter = array('STRING_ID' => "opt");
$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $arFilter);
if($arGroup = $rsGroups -> Fetch())
{
    $arReplace = array('GROUP_TRADE' => $arGroup['ID']);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/imageries", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/product", $arReplace);
}

//trade_price
$arFilter = array('NAME' => "opt");
$rsGroups = CCatalogGroup::GetList(array(), $arFilter);
if($arGroup = $rsGroups -> Fetch())
{
    $arReplace = array('PRICE_TRADE' => $arGroup['ID']);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/include/catalog", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/imageries", $arReplace);
    CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH."/product", $arReplace);
}


// права для ИБ Каталоги
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "catalog","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	$grRes[$editorGroupId] = "R";
	// администратор инт. магазина - изменения
	$grRes[$saleAdminGroupId] = "W";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}

// права для ИБ Новости
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "news","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на изменения
	$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	//$grRes[$saleAdminGroupId] = "W";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}
// права для ИБ Статьи
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "articles","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	//$grRes[$saleAdminGroupId] = "W";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}
// права для ИБ Баннеры
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "banners","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	//$grRes[$saleAdminGroupId] = "W";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}
// права для ИБ Торговые предложения
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "offers","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	$grRes[$editorGroupId] = "R";
	// администратор инт. магазина - изменения
	$grRes[$saleAdminGroupId] = "W";
	// все пользователи - чтение
	$grRes[2] = "R";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}
// права для ИБ Продукция фото
/*$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "products_photos"));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	$grRes[$saleAdminGroupId] = "W";
	// все пользователи - чтение
	$grRes[2] = "R";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}*/
// права для ИБ Справочники
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "references","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	//$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	$grRes[$saleAdminGroupId] = "W";
	// все пользователи - чтение
	//$grRes[2] = "R";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}
// права для ИБ Сервисы
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "services","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	//$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	$grRes[$saleAdminGroupId] = "W";
	// все пользователи - чтение
	$grRes[2] = "R";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}
// права для ИБ Системные
$rsIBlock = CIBlock::GetList(array(), array("TYPE" => "system","LID"=>WIZARD_SITE_ID));
while ($arIBlock = $rsIBlock -> Fetch())
{
	// получаем права для групп на ИБ
	$grRes = CIBlock::GetGroupPermissions($arIBlock["ID"]);
	// контент редактор на чтение
	$grRes[$editorGroupId] = "W";
	// администратор инт. магазина - изменения
	//$grRes[$saleAdminGroupId] = "W"; 
	// все пользователи - чтение
	$grRes[2] = "R";
	CIBlock::SetPermission($arIBlock["ID"], $grRes);
}

$NS = false;
$NS = CSearch::ReIndexAll(false, 60, $NS);


?>
<?
//error_reporting(E_ALL);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeModuleLangFile(__FILE__);

if(strlen($APPLICATION->GetTitle())<=0)
	$APPLICATION->SetTitle(GetMessage("MAIN_PROLOG_ADMIN_TITLE"));

$aUserOpt = CUserOptions::GetOption("admin_panel", "settings");
$aUserOptGlobal = CUserOptions::GetOption("global", "settings");

$adminPage->Init();
$adminMenu->Init($adminPage->aModules);

$bShowAdminMenu = !empty($adminMenu->aGlobalMenu);

if($bShowAdminMenu && class_exists("CUserOptions"))
{
	$aOptMenuPos = CUserOptions::GetOption("admin_menu", "pos", array());
	$bOptMenuMinimized = $aOptMenuPos['ver'] == 'off';
}

if (!defined('ADMIN_SECTION_LOAD_AUTH') || !ADMIN_SECTION_LOAD_AUTH):
	$direction = "";
	$direct = CLanguage::GetByID(LANGUAGE_ID);
	$arDirect = $direct->Fetch();
	if($arDirect["DIRECTION"] == "N")
		$direction = ' dir="rtl"';

?>
<!DOCTYPE html>
<html<?=$aUserOpt['fix'] == 'on' ? ' class="adm-header-fixed"' : ''?><?=$direction?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?=htmlspecialcharsbx(LANG_CHARSET)?>">
<meta name="viewport" content="initial-scale=1.0, width=device-width">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?echo COption::GetOptionString("main","site_name", $_SERVER["SERVER_NAME"])?> - <?echo htmlspecialcharsex($APPLICATION->GetTitle(false, true))?></title>
<?
else:
?>
<script type="text/javascript">
<?
	if ($aUserOpt['fix'] == 'on'):
?>
document.documentElement.className = 'adm-header-fixed';
<?
	endif;
?>
window.document.title = '<?echo CUtil::JSEscape(COption::GetOptionString("main","site_name", $_SERVER["SERVER_NAME"]));?> - <?echo CUtil::JSEscape($APPLICATION->GetTitle(false, true))?>';
</script>
<?
endif;

$APPLICATION->AddBufferContent(array($adminPage, "ShowCSS"));
echo $adminPage->ShowScript();
$APPLICATION->ShowHeadScripts();
$APPLICATION->ShowHeadStrings();
?>
<script type="text/javascript">
BX.message({MENU_ENABLE_TOOLTIP: <?=($aUserOptGlobal['start_menu_title'] <> 'N' ? 'true' : 'false')?>});
BX.InitializeAdmin();
</script>
<?
if (!defined('ADMIN_SECTION_LOAD_AUTH') || !ADMIN_SECTION_LOAD_AUTH):
?>
</head>
<body id="bx-admin-prefix">
<!--[if lte IE 7]>
<style type="text/css">
#bx-panel {display:none !important;}
.adm-main-wrap { display:none !important; }
</style>
<div id="bx-panel-error">
<?echo GetMessage("admin_panel_browser")?>
</div><![endif]-->
<?
endif;
if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/admin_header.php"))
	include($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/php_interface/admin_header.php");

?>
	<table class="adm-main-wrap">
		<tr>
			<td class="adm-header-wrap" colspan="2">
<?

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/top_panel.php");
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/favorite_menu.php");

?>
			</td>
		</tr>
		<tr>
<?

	CJSCore::Init(array('admin_interface'));
	$APPLICATION->AddHeadScript('/bitrix/js/main/dd.js');

	$aActiveSection = $adminMenu->ActiveSection();

	if(isset($GLOBALS["BX_FAVORITE_MENU_ACTIVE_ID"]) && $GLOBALS["BX_FAVORITE_MENU_ACTIVE_ID"])
		$openedSection ="desktop";
	else
		$openedSection = CUtil::JSEscape($aActiveSection["menu_id"]);

	$stick = (array_key_exists("global_menu_desktop", $adminMenu->aActiveSections) || $openedSection =="desktop" ) ? "Y" : "N";
	CUserOptions::SetOption('favorite', 'favorite_menu', array('stick' => $stick));
?>
			<td class="adm-left-side-wrap" id="menu_mirrors_cont">

<script type="text/javascript">
BX.adminMenu.setMinimizedState(<?=$bOptMenuMinimized ? 'true' : 'false'?>);
BX.adminMenu.setActiveSection('<?=$openedSection?>');
BX.adminMenu.setOpenedSections('<?=CUtil::JSEscape($adminMenu->GetOpenedSections());?>');
</script>
				<div class="adm-left-side<?=$bOptMenuMinimized ? ' adm-left-side-wrap-close' : ''?>"<?if(intval($aOptMenuPos["width"]) > 0) echo ' style="width:'.($bOptMenuMinimized ? 15 : intval($aOptMenuPos["width"])).'px" data-width="'.intval($aOptMenuPos["width"]).'"'?> id="bx_menu_panel"><div class="adm-menu-wrapper<?=$bOptMenuMinimized ? ' adm-main-menu-close' : ''?>" style="overflow:hidden; min-width:300px;">
						<div class="adm-main-menu">
<?
	$menuScripts = "";

	foreach($adminMenu->aGlobalMenu as $menu):

		$menuClass = "adm-main-menu-item adm-".$menu["menu_id"];

		if(($menu["items_id"] == $aActiveSection["items_id"] && $openedSection !="desktop" )|| $menu["menu_id"] == $openedSection)
			$menuClass .=' adm-main-menu-item-active';

		if ($menu['url']):
?>
						<a href="<?=htmlspecialcharsbx($menu["url"])?>" class="adm-default <?=$menuClass?>" onclick="BX.adminMenu.GlobalMenuClick('<?echo $menu["menu_id"]?>'); return false;" onfocus="this.blur();" id="global_menu_<?echo $menu["menu_id"]?>">
							<div class="adm-main-menu-item-icon"></div>
							<div class="adm-main-menu-item-text"><?echo htmlspecialcharsbx($menu["text"])?></div>
							<div class="adm-main-menu-hover"></div>
						</a>
<?
		else:
?>
						<span class="adm-default <?=$menuClass?>" onclick="BX.adminMenu.GlobalMenuClick('<?echo $menu["menu_id"]?>'); return false;" id="global_menu_<?echo $menu["menu_id"]?>">
							<div class="adm-main-menu-item-icon"></div>
							<div class="adm-main-menu-item-text"><?echo htmlspecialcharsbx($menu["text"])?></div>
							<div class="adm-main-menu-hover"></div>
						</span>
<?
		endif;
	endforeach;
?>
					</div>
					<div class="adm-submenu" id="menucontainer">
<?
		foreach($adminMenu->aGlobalMenu as $menu):

			if(
				(
					(
						$menu["menu_id"] == $aActiveSection["menu_id"]
						|| $menu["items_id"] == $aActiveSection["items_id"]

					)
					&& $openedSection !="desktop"
				)
				|| $menu["menu_id"] == $openedSection

			)
				$subMenuDisplay = "block";
			else
				$subMenuDisplay = "none";

?>
						<div class="adm-global-submenu<?=($subMenuDisplay == "block" ? " adm-global-submenu-active" : "")?>" id="global_submenu_<?echo $menu["menu_id"]?>">
<?
		if ($menu['menu_id'] == 'desktop')
		{
			require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/interface/desktop_menu.php");

			$menu["text"] = $favMenuText;
			$menu["items"] = $favMenuItems;
		}
?>
							<div class="adm-submenu-items-wrap">
								<div class="adm-submenu-items-stretch-wrap" onscroll="BX.adminMenu.itemsStretchScroll()">
									<table class="adm-submenu-items-stretch">
										<tr>
											<td class="adm-submenu-items-stretch-cell">
												<div class="adm-submenu-items-block">
													<div class="adm-submenu-items-title adm-submenu-title-<?=$menu['menu_id']?>"><?=htmlspecialcharsbx($menu["text"])?></div>
													<div id='<?="_".$menu['items_id']?>'>
<?
		if(!empty($menu["items"]))
		{
			foreach($menu["items"] as $submenu)
			{
				$menuScripts .= $adminMenu->Show($submenu);
			}
		}
		elseif ($menu['menu_id'] == 'desktop')
			echo CBXFavAdmMenu::GetEmptyMenuHTML();

		if($menu['menu_id'] == 'desktop')
			echo CBXFavAdmMenu::GetMenuHintHTML(empty($menu["items"]));

?>
													</div>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
<?
	endforeach;
?>
						<div class="adm-submenu-separator"></div>
<?
	if ($menuScripts != ""):
?>
<script type="text/javascript"><?=$menuScripts?></script>
<?
	endif;
?>
					</div>
				</div></div>
			</td>
			<td class="adm-workarea-wrap <?=defined('BX_ADMIN_SECTION_404') && BX_ADMIN_SECTION_404 == 'Y' ? 'adm-404-error' : 'adm-workarea-wrap-top'?>">
				<div class="adm-workarea adm-workarea-page" id="adm-workarea">
<?
//Title
if ($APPLICATION->GetCurPage(true) != "/bitrix/admin/index.php")
{
	$currentFavId = null;
	$currentItemsId = '';

	$pageTitle = htmlspecialcharsex($APPLICATION->GetTitle(false, true));

	if (!defined('BX_ADMIN_SECTION_404') || BX_ADMIN_SECTION_404 != 'Y')
	{
		$arLastItem = null;
		//Navigation chain
		$adminChain->Init();
		$arLastItem = $adminChain->Show();

		$currentFavId = CFavorites::GetIDByUrl($_SERVER["REQUEST_URI"]);
		//$currentItemsId = is_array($arLastItem) && isset($arLastItem['ID'])	? $arLastItem['ID'] : '';
		$currentItemsId = '';
	}
?>

		<h1 class="adm-title" id="adm-title"><?=$pageTitle?><?if(!defined('BX_ADMIN_SECTION_404') || BX_ADMIN_SECTION_404 != 'Y'):?><a href="javascript:void(0)" class="adm-fav-link<?=$currentFavId>0?' adm-fav-link-active':''?>" onclick="BX.adminFav.titleLinkClick(this, <?=intval($currentFavId)?>, '<?=$currentItemsId?>')" title="<?= $currentFavId ? GetMessage("MAIN_PR_ADMIN_FAV_DEL") : GetMessage("MAIN_PR_ADMIN_FAV_ADD")?>"></a><?endif;?><a id="navchain-link" href="<?echo htmlspecialcharsbx($_SERVER["REQUEST_URI"])?>" title="<?echo GetMessage("MAIN_PR_ADMIN_CUR_LINK")?>"></a></h1>
	<?
}

//Content

//wizard customization file
$bxProductConfig = array();
if(file_exists($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php"))
	include($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/.config.php");

if($USER->IsAuthorized()):
	if(defined("DEMO") && DEMO == "Y"):
		$vendor = COption::GetOptionString("main", "vendor", "1c_bitrix");
		$delta = $SiteExpireDate-time();
		$daysToExpire = ($delta < 0? 0 : ceil($delta/86400));
		$bSaas = (COption::GetOptionString('main', '~SAAS_MODE', "N") == "Y");

		echo BeginNote('style="position: relative; top: -15px;"');
		if(isset($bxProductConfig["saas"])):
			if($bSaas)
			{
				if($daysToExpire > 0)
				{
					if($daysToExpire <= $bxProductConfig["saas"]["days_before_warning"])
					{
						$sWarn = $bxProductConfig["saas"]["warning"];
						$sWarn = str_replace("#RENT_DATE#", COption::GetOptionString('main', '~support_finish_date'), $sWarn);
						$sWarn = str_replace("#DAYS#", $daysToExpire, $sWarn);
						echo $sWarn;
					}
				}
				else
				{
					echo str_replace("#RENT_DATE#", COption::GetOptionString('main', '~support_finish_date'), $bxProductConfig["saas"]["warning_expired"]);
				}
			}
			else
			{
				if($daysToExpire > 0)
					echo str_replace("#DAYS#", $daysToExpire, $bxProductConfig["saas"]["trial"]);
				else
					echo $bxProductConfig["saas"]["trial_expired"];
			}
		else: //saas
?>
	<span class="required"><?echo GetMessage("TRIAL_ATTENTION") ?></span>
	<?echo GetMessage("TRIAL_ATTENTION_TEXT1_".$vendor) ?>
	<?if ($daysToExpire >= 0):?>
	<?echo GetMessage("TRIAL_ATTENTION_TEXT2") ?> <span class="required"><b><?echo $daysToExpire?></b></span> <?echo GetMessage("TRIAL_ATTENTION_TEXT3") ?>.
	<?else:?>
	<?echo GetMessage("TRIAL_ATTENTION_TEXT4_".$vendor) ?>
	<?endif;?>
	<?echo GetMessage("TRIAL_ATTENTION_TEXT5_".$vendor) ?>
<?
		endif; //saas
		echo EndNote();

	elseif($USER->CanDoOperation('install_updates')):
		//show support ending warning
		$supportFinishDate = COption::GetOptionString('main', '~support_finish_date', '');
		if($supportFinishDate <> '' && is_array(($aSupportFinishDate=ParseDate($supportFinishDate, 'ymd'))))
		{
			$aGlobalOpt = CUserOptions::GetOption("global", "settings", array());
			if($aGlobalOpt['messages']['support'] <> 'N')
			{
				$supportFinishStamp = mktime(0,0,0, $aSupportFinishDate[1], $aSupportFinishDate[0], $aSupportFinishDate[2]);
				$supportDateDiff = ceil(($supportFinishStamp - time())/86400);

				$sSupportMess = '';
				if($supportDateDiff >= 0 && $supportDateDiff <= 30)
				{
					$sSupportMess = GetMessage("prolog_main_support1", array(
						'#FINISH_DATE#'=>GetTime($supportFinishStamp),
						'#DAYS_AGO#'=>($supportDateDiff == 0? GetMessage("prolog_main_today") : GetMessage('prolog_main_support_days', array('#N_DAYS_AGO#'=>$supportDateDiff))),
						'#LICENSE_KEY#'=>md5(LICENSE_KEY),
					));
				}
				elseif($supportDateDiff < 0 && $supportDateDiff >= -30)
				{
					$sSupportMess = GetMessage("prolog_main_support2", array(
						'#FINISH_DATE#'=>GetTime($supportFinishStamp),
						'#DAYS_AGO#'=>(-$supportDateDiff),
						'#LICENSE_KEY#'=>md5(LICENSE_KEY),
					));
				}
				elseif($supportDateDiff < -30)
				{
					$sSupportMess = GetMessage("prolog_main_support3", array(
						'#FINISH_DATE#'=>GetTime($supportFinishStamp),
						'#LICENSE_KEY#'=>md5(LICENSE_KEY),
					));
				}

				if($sSupportMess <> '')
				{
					echo BeginNote('style="position: relative; top: -15px;"');
					echo $sSupportMess;
					echo EndNote();
				}
			}
		}
	endif; //defined("DEMO") && DEMO == "Y"

	//diagnostic for spaces in init.php etc.
	//$aHeadersInfo set in the include.php
	if(!empty($aHeadersInfo))
		echo CAdminMessage::ShowMessage(GetMessage("prolog_admin_headers_sent", array("#FILE#"=>$aHeadersInfo['file'], "#LINE#"=>$aHeadersInfo['line'])));

endif; //$USER->IsAuthorized()
?>
<?
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

$adminPage->Init();
$adminMenu->Init($adminPage->aModules);

if(empty($adminMenu->aGlobalMenu))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(isset($_REQUEST["show_mode"]))
{
	$_SESSION["ADMIN_I_SHOW_MODE"] = $_REQUEST["show_mode"];
	CUserOptions::SetOption("view_mode", "index", $_SESSION["ADMIN_I_SHOW_MODE"]);
}
elseif(!isset($_SESSION["ADMIN_I_SHOW_MODE"]))
	$_SESSION["ADMIN_I_SHOW_MODE"] = CUserOptions::GetOption("view_mode", "index");

if(!in_array($_SESSION["ADMIN_I_SHOW_MODE"], array("icon", "list")))
	$_SESSION["ADMIN_I_SHOW_MODE"] = "icon";

$APPLICATION->SetAdditionalCSS("/bitrix/themes/".ADMIN_THEME_ID."/index.css");

$APPLICATION->SetTitle(GetMessage("admin_index_title"));
if($_REQUEST["mode"] <> "list"):
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	
	$vendor = COption::GetOptionString("main", "vendor", "1c_bitrix");

	//wizard customization file
	if(isset($bxProductConfig["admin"]["index"]))
		$sProduct = $bxProductConfig["admin"]["index"];
	else
		$sProduct = GetMessage("admin_index_product").' &quot;'.GetMessage("admin_index_product_name_".$vendor).'#VERSION#&quot;.<br>';
	$sVer = ($GLOBALS['USER']->CanDoOperation('view_other_settings')? " ".SM_VERSION : "");
	$sProduct = str_replace("#VERSION#", $sVer, $sProduct);
	?>

	<?echo BeginNote('width="100%"');?>
	<?echo GetMessage("admin_index_project")?><?if(($s = COption::GetOptionString("main", "site_name", "")) <> "") echo " &quot;<b>".$s."</b>&quot;"?>.<br>
	<div class="empty" style="height:4px"></div>
	<?echo $sProduct?>
	<?echo EndNote();?>

	<?
	$aGlobalOpt = CUserOptions::GetOption("global", "settings", array());
	$bShowSecurity = (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/security/install/index.php") && $aGlobalOpt['messages']['security'] <> 'N');
	$bShowPerfmon = (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/perfmon/install/index.php") && $aGlobalOpt['messages']['perfmon'] <> 'N');
	?>
	<?
	if($bShowSecurity || $bShowPerfmon):
		?>
		<?echo BeginNote('width="100%"');?>
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr valign="top">
			<?
			if($bShowSecurity):
				?>
				<td width="50%">

				<div class="bx-security-icon"></div>
				<div style="float:left;"><b><?echo GetMessage("admin_index_sec")?></b></div>
				<div style="float:right;">
				<?
				$bSecModuleInstalled = CModule::IncludeModule("security");
				if($bSecModuleInstalled):
					$bSecurityFilter = CSecurityFilter::IsActive();
					?>
					<?if($bSecurityFilter):?>
						<div class="lamp-green"></div><?echo GetMessage("admin_index_sec_on")?>
					<?else:?>
						<div class="lamp-yellow"></div><?echo GetMessage("admin_index_sec_check")?>
					<?endif?>
				<?else:?>
					<div class="lamp-red"></div><?echo GetMessage("admin_index_sec_off")?>
				<?endif?>
				</div>
				<div class="ruler" style="clear:both;"></div>
				<?if($bSecModuleInstalled):?>
					<?if($bSecurityFilter):?>
						<p><span class="green"><?echo GetMessage("admin_index_sec_filter_on")?></span></p>
						<?echo GetMessage("admin_index_sec_level", array("#LANGUAGE_ID#"=>LANGUAGE_ID));?>
					<?else:?>
						<p><span class="red"><?echo GetMessage("admin_index_sec_filter_off")?></span></p>
						<p><?echo GetMessage("admin_index_sec_filter_desc")?></p>
						<form method="get" action="security_filter.php">
						<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
						<input type="submit" name="" value="<?echo GetMessage("admin_index_sec_filter_turn_on")?>"<?if($APPLICATION->GetGroupRight("security")<"W"):?> disabled<?endif?>>
						</form>
					<?endif?>
				<?else:?>
					<p><span class="red"><?echo GetMessage("admin_index_sec_module")?></span></p>
					<p><?echo GetMessage("admin_index_sec_module_desc")?></p>
					<form method="get" action="module_admin.php">
					<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
					<input type="hidden" name="id" value="security">
					<?=bitrix_sessid_post()?>
					<input type="submit" name="install" value="<?echo GetMessage("admin_index_sec_module_install")?>"<?if(!$USER->CanDoOperation('edit_other_settings')):?> disabled<?endif?>>
					</form>
				<?endif?>
				</td>
				<?
				//security block
			endif;
			?>

			<?if($bShowSecurity && $bShowPerfmon):?>
				<td><div class="empty" style="width:15px;"></div></td>
				<td class="bx-messages-delimiter"><div class="empty" style="width:15px;"></div></td>
			<?endif?>

			<?
			if($bShowPerfmon):
				?>
				<td width="50%">

				<div class="bx-perfmon-icon"></div>
				<div style="float:left;"><b><?echo GetMessage("admin_index_perf")?></b></div>
				<div style="float:right;">
				<?
				$bPerfmonModuleInstalled = IsModuleInstalled("perfmon");
				if($bPerfmonModuleInstalled):
					$mark_value = (double)COption::GetOptionString("perfmon", "mark_php_page_rate", "");
					?>
					<?if($mark_value > 0):?>
						<div class="lamp-green"></div><?echo GetMessage("admin_index_perf_installed")?>
					<?else:?>
						<div class="lamp-yellow"></div><?echo GetMessage("admin_index_perf_check")?>
					<?endif?>
				<?else:?>
					<div class="lamp-red"></div><?echo GetMessage("admin_index_perf_not_installed")?>
				<?endif?>
				</div>
				<div class="ruler" style="clear:both;"></div>
				<?if($bPerfmonModuleInstalled):?>
					<?if($mark_value > 0):?>
						<p><?if($mark_value >= 5):?><span class="green"><?else:?><span class="red"><?endif;?><?echo GetMessage("admin_index_perf_current")?> <?echo $mark_value?></span></p>
						<?echo GetMessage("admin_index_perf_level", array("#LANGUAGE_ID#"=>LANGUAGE_ID));?>
					<?else:?>
						<p><span class="red"><?echo GetMessage("admin_index_perf_no_result")?></span></p>
						<p><?echo GetMessage("admin_index_perf_no_result_desc")?></p>
						<form method="get" action="perfmon_panel.php">
						<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
						<input type="submit" name="" value="<?echo GetMessage("admin_index_perf_test")?>"<?if($APPLICATION->GetGroupRight("perfmon")<"W"):?> disabled<?endif?>>
						</form>
					<?endif?>
				<?else:?>
					<p><span class="red"><?echo GetMessage("admin_index_perf_module_inst")?></span></p>
					<p><?echo GetMessage("admin_index_perf_module_inst_desc")?></p>
					<form method="get" action="module_admin.php">
					<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
					<input type="hidden" name="id" value="perfmon">
					<?=bitrix_sessid_post()?>
					<input type="submit" name="install" value="<?echo GetMessage("admin_index_sec_module_install")?>"<?if(!$USER->CanDoOperation('edit_other_settings')):?> disabled<?endif?>>
					</form>
				<?endif?>
				</td>
				<?
				//perfmon block
			endif;
			?>
			</tr>
		</table>
		<?echo EndNote();?>
	<?endif?>

	<div id="index_page_result_div">
	<?
else:
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
endif; //$_REQUEST["mode"] <> "list"

$page = $GLOBALS["APPLICATION"]->GetCurPage();
$param = DeleteParam(array("show_mode", "mode"));
$aContext = array(
	array(
		"TEXT"=>GetMessage("admin_lib_index_view"),
		"TITLE"=>GetMessage("admin_lib_index_view_title"),
		"MENU"=>array(
			array(
				"ICON"=>($_SESSION["ADMIN_I_SHOW_MODE"] == "icon"? "checked":""),
				"TEXT"=>GetMessage("admin_lib_index_view_icon"),
				"TITLE"=>GetMessage("admin_lib_index_view_icon_title"),
				"ACTION"=>"jsUtils.LoadPageToDiv('".$page."?show_mode=icon&mode=list".($param<>""? "&".$param:"")."', 'index_page_result_div');"
			),
			array(
				"ICON"=>($_SESSION["ADMIN_I_SHOW_MODE"] == "list"? "checked":""),
				"TEXT"=>GetMessage("admin_lib_index_view_list"),
				"TITLE"=>GetMessage("admin_lib_index_view_list_title"),
				"ACTION"=>"jsUtils.LoadPageToDiv('".$page."?show_mode=list&mode=list".($param<>""? "&".$param:"")."', 'index_page_result_div');"
			),
		),
	),
);
$context = new CAdminContextMenu($aContext);
$context->Show();
?>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<?
$i=0;
foreach($adminMenu->aGlobalMenu as $menu):
	?>
	<?if($i>0):?>
		<tr>
			<td><div class="section-line">&nbsp;</div></td>
			<td></td>
		</tr>
	<?endif;?>
	<tr valign="top">
		<td align="center" class="section-container">
			<a href="<?echo $menu["url"]?>" title="<?echo $menu["title"]?>">
				<div class="section-icon" id="<?echo $menu["index_icon"]?>"></div>
				<div class="section-text"><?echo $menu["text"]?></div>
			</a>
		</td>
		<td class="items-container">
		<?
		foreach($menu["items"] as $submenu):
			if($_SESSION["ADMIN_I_SHOW_MODE"] == "list"):
				?>
				<div class="item-container">
				<?if($submenu["url"] <> ""):?>
					<a href="<?echo $submenu["url"]?>" title="<?echo $submenu["title"]?>"><div class="item-icon" id="<?echo $submenu["icon"]?>"></div></a>
					<div class="item-block"><a href="<?echo $submenu["url"]?>" title="<?echo $submenu["title"]?>"><?echo $submenu["text"]?></a></div>
				<?else:?>
					<div class="item-icon" id="<?echo $submenu["icon"]?>"></div>
					<div class="item-block"><?echo $submenu["text"]?></div>
				<?endif?>
				</div>
				<?
			else: //icon
				?>
				<div class="icon-container" align="center">
				<?if($submenu["url"] <> ""):?>
					<a href="<?echo $submenu["url"]?>" title="<?echo $submenu["title"]?>">
						<div class="icon-icon" id="<?echo $submenu["page_icon"]?>"></div>
						<div class="icon-text"><?echo $submenu["text"]?></div>
					</a>
				<?else:?>
						<div class="icon-icon" id="<?echo $submenu["page_icon"]?>"></div>
						<div class="icon-text"><?echo $submenu["text"]?></div>
				<?endif;?>
				</div>
				<?
			endif;
		endforeach;
		?>
		</td>
	</tr>
	<?
	$i++;
endforeach;
?>
</table>
<?
if($_REQUEST["mode"] <> "list")
	echo '</div>';
else
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");
?>
<br>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>
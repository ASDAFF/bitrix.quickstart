<?
define("BX_DONT_SKIP_PULL_INIT", true);
require($_SERVER["DOCUMENT_ROOT"]."/desktop_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
if (intval($USER->GetID()) <= 0) return;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/im/install/public/desktop_app/index.php");
$isWebDavInstalled = IsModuleInstalled('webdav');
?>
<script type="text/javascript">
	BX.ready(function(){
		if(<?=($isWebDavInstalled? 'false' : 'true')?> || window.BXFileStorage == undefined)
		{
			BX('desktop_tab_disk_header').style.display = 'none';
		}
	});
</script>
<div class="bx-desktop">
	<div class="bx-desktop-links" id="bx-desktop-links"><a href="#logout" onclick="return BXIM.desktop.logout();" class="bx-desktop-link"><?=GetMessage('DESKTOP_LOGOUT')?></a></div>
	<div class="bx-desktop-tabs" id="bx-desktop-tabs"><span class="bx-desktop-tab bx-desktop-tab-active" onclick="BXIM.desktop.changeTab(this)"><?=GetMessage('DESKTOP_TAB_IM')?></span><span id="desktop_tab_disk_header" class="bx-desktop-tab" onclick="BXIM.desktop.changeTab(this)"><?=GetMessage('DESKTOP_TAB_DISK')?></span><span><span><a href="javascript:BXIM.desktop.openLF()" class="bx-desktop-link"><?=GetMessage('DESKTOP_TAB_LF')?></a><span id="bx-desktop-tab-lf-count"></span></span></div>
	<div class="bx-desktop-contents" id="bx-desktop-contents">
		<div class="bx-desktop-content" data-page="im"><div id="placeholder-messenger" class="placeholder-messenger"></div></div>
		<div class="bx-desktop-content bx-desktop-content-hide bx-desktop-content-overflow" data-page="disk">
			<div id="placeholder-disk" class="placeholder-disk">
				<?php	
					if($isWebDavInstalled)				
					{
						$APPLICATION->IncludeComponent("bitrix:webdav.disk", '');
					}
				?>
			</div>
		</div>
	</div>
</div>
<?$APPLICATION->IncludeComponent("bitrix:im.messenger", "", Array("DESKTOP" => "Y"), false, Array("HIDE_ICONS" => "Y"));?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>
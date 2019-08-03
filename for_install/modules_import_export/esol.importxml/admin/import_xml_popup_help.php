<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$imgPath = '/bitrix/panel/'.$moduleId.'/images/video_icons/';
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>

<div class="esol-ix-tabs" id="esol-ix-help-tabs">
	<div class="esol-ix-tabs-heads">
		<a href="javascript:void(0)" onclick="EHelper.SetTab(this);" class="active" title="<?echo htmlspecialcharsex(GetMessage("ESOL_IX_HELP_TAB1_ALT"));?>"><?echo GetMessage("ESOL_IX_HELP_TAB1");?></a>
		<a href="javascript:void(0)" onclick="EHelper.SetTab(this);" title="<?echo htmlspecialcharsex(GetMessage("ESOL_IX_HELP_TAB2_ALT"));?>"><?echo GetMessage("ESOL_IX_HELP_TAB2");?></a>
	</div>
	<div class="esol-ix-tabs-bodies">
		<div class="active">
			<div>&nbsp;</div>
			<div class="esol-ix-video-list">
				<a href="https://www.youtube.com/watch?v=2gMUw1Mtolg" target="_blank" title="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_COMMON"));?>">
					<img src="<?echo $imgPath;?>common.jpg" width="196px" height="110px" alt="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_COMMON"));?>" title="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_COMMON"));?>">
					<span><?echo GetMessage("ESOL_IX_HELP_VIDEO_COMMON");?></span>
				</a>
				<a href="https://www.youtube.com/watch?v=08mjH8A7J_4" target="_blank" title="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_YML_PARAMS"));?>">
					<img src="<?echo $imgPath;?>yml_params.jpg" width="196px" height="110px" alt="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_YML_PARAMS"));?>" title="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_YML_PARAMS"));?>">
					<span><?echo GetMessage("ESOL_IX_HELP_VIDEO_YML_PARAMS");?></span>
				</a>
				<a href="https://www.youtube.com/watch?v=kjNY2FqjdUk" target="_blank" title="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_OFFERS"));?>">
					<img src="<?echo $imgPath;?>offers.jpg" width="196px" height="110px" alt="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_OFFERS"));?>" title="<?echo htmlspecialcharsbx(GetMessage("ESOL_IX_HELP_VIDEO_OFFERS"));?>">
					<span><?echo GetMessage("ESOL_IX_HELP_VIDEO_OFFERS");?></span>
				</a>
			</div>
			<div>&nbsp;</div>
		</div>
		<div>
			<div>&nbsp;</div>
			<p class="esol-ix-help-faq-prolog"><i><?echo sprintf(GetMessage("ESOL_IX_FAQ_PROLOG"), 'app@esolutions.su', 'app@esolutions.su');?></i></p>
			<ol id="esol-ix-help-faq">
				<li>
					<a href="#"><?echo GetMessage("ESOL_IX_FAQ_QUEST_SLOW_IMPORT");?></a>
					<div><?echo GetMessage("ESOL_IX_FAQ_ANS_SLOW_IMPORT");?></div>
				</li>
				<li>
					<a href="#"><?echo GetMessage("ESOL_IX_FAQ_QUEST_BOOL");?></a>
					<div><?echo GetMessage("ESOL_IX_FAQ_ANS_BOOL");?></div>
				</li>
			</ol>
			<div>&nbsp;</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>
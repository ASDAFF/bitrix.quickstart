<?
global $APPLICATION, $DB, $USER, $CACHE_MANAGER;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule("parnas.khayrcomment");
CModule::IncludeModule("iblock");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("KHAYR_COMMENT_LAST"));
$CAT_RIGHT = KhayRComment::GetRightsMax();
if ($CAT_RIGHT < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
else
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	if (isset($_GET["action"]))
	{
		if ($_GET["action"] == "active")
		{
			$ID = intval($_GET["ID"]);
			if ($ID > 0)
			{
				$arParams = Array(
					"MODIFIED_BY"	=> $USER->GetID(),
					"ACTIVE" 		=> "Y"
				);
				$el = new CIBlockElement;
				$res = $el->Update($ID, $arParams);
			}
		}
		elseif ($_GET["action"] == "nonactive")
		{
			$ID = intval($_GET["ID"]);
			if ($ID > 0)
			{
				$arParams = Array(
					"MODIFIED_BY"	=> $USER->GetID(),
					"ACTIVE" 		=> "N"
				);
				$el = new CIBlockElement;
				$res = $el->Update($ID, $arParams);
			}
		}
		elseif ($_GET["action"] == "delete")
		{
			$ID = intval($_GET["ID"]);
			if ($ID > 0)
			{
				CIBlockElement::Delete($ID);
			}
		}
		LocalRedirect("parnas.khayrcomment_list.php?".($_REQUEST["tabControl_active_tab"] ? "tabControl_active_tab=".$_REQUEST["tabControl_active_tab"]."&" : "")."lang=".LANGUAGE_ID);
	}
	$ibs = KhayRComment::GetIblocks();
	$aTabs = array();
	foreach ($ibs as $sid => $ib)
	{
		if ($ib["RIGHTS"] < "W")
			continue;
		$count = KhayRComment::GetCount(0, false, $sid);
		$aTabs[] = array(
			"DIV" => "com_site_".$sid,
			"TAB" => '['.$sid.'] '.htmlspecialcharsbx($ib["SITE_NAME"]).($count ? " (".$count.")" : ""),
			'TITLE' => '['.$sid.'] '.htmlspecialcharsbx($ib["SITE_NAME"])
		);
	}
	$tabControl = new CAdminViewTabControl("tabControl", $aTabs);
	?>
	<style>
		.khayr_comment_tab {
			border-spacing: 0px;
			width: 100%;
		}
		.khayr_comment_tab th, .khayr_comment_tab td {
			border: 1px solid #cecece;
			padding: 5px;
		}		
	</style>
	<?
	$tabControl->Begin();
	foreach ($ibs as $sid => $ib)
	{
		if ($ib["RIGHTS"] < "W")
			continue;
		$tabControl->BeginNextTab();
		$IBLOCK_ID = $ib["IBLOCK_ID"];
		$IBLOCK = CIBlock::GetByID($IBLOCK_ID)->Fetch();
		$IBLOCK_TYPE_ID = $IBLOCK["IBLOCK_TYPE_ID"];
		$arCom = CIBlockElement::GetList(
			array("DATE_CREATE" => "DESC"), 
			array("IBLOCK_ID" => $IBLOCK_ID),
			false,
			array("nPageSize" => 15),
			array()
		);
		?>
		<table class='khayr_comment_tab'> 
			<thead>
				<tr>
					<th><?=GetMessage("KHAYR_COMMENT_ID")?></th>
					<th><?=GetMessage("KHAYR_COMMENT_OBJECT")?></th>
					<th><?=GetMessage("KHAYR_COMMENT_DATE")?></th>
					<th><?=GetMessage("KHAYR_COMMENT_AUTHOR")?></th>
					<th><?=GetMessage("KHAYR_COMMENT_COMMENT")?></th>
					<th style="width: 125px;"><?=GetMessage("KHAYR_COMMENT_STATUS")?></th>
				</tr>
			</thead>
			<tbody>
				<?
				while ($obElement = $arCom->GetNextElement())
				{
					$arItem = $obElement->GetFields();
					$arItem["PROPERTIES"] = $obElement->GetProperties();
					foreach ($arItem["PROPERTIES"] as $pid => &$prop)
					{
						if ( (is_array($prop["VALUE"]) && (count($prop["VALUE"]) > 0)) || (!is_array($prop["VALUE"]) && (strlen($prop["VALUE"]) > 0)) )
						{
							$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "");
						}
					}
					$user = $GLOBALS["USER"]->GetByID($arItem["DISPLAY_PROPERTIES"]["USER"]["DISPLAY_VALUE"])->Fetch();
					?>		
					<tr>
						<td><a href="iblock_element_edit.php?IBLOCK_ID=<?=$IBLOCK_ID?>&type=<?=$IBLOCK_TYPE_ID?>&ID=<?=$arItem["ID"]?>&lang=<?=LANGUAGE_ID?>&find_section_section=-1&WF=Y" target="_blank"><?=$arItem["ID"]?></a></td>
						<td><?=$arItem["DISPLAY_PROPERTIES"]["OBJECT"]["DISPLAY_VALUE"]?></td>
						<td><?=$arItem["DATE_CREATE"]?></td>
						<td>
							<?=($user ? "[<a href=\"user_edit.php?ID=".$arItem["DISPLAY_PROPERTIES"]["USER"]["DISPLAY_VALUE"]."&lang=".LANG."\" target=\"_blank\">".$arItem["DISPLAY_PROPERTIES"]["USER"]["DISPLAY_VALUE"]."</a>] ".$user["LOGIN"] : $arItem["PROPERTIES"]["NONUSER"]["VALUE"])?><br />
							<?=($user ? $user["EMAIL"] : $arItem["PROPERTIES"]["EMAIL"]["VALUE"])?>
						</td>
						<td><?=KhayRComment::GetText($arItem["~PREVIEW_TEXT"])?></td>
						<td>
							<?=($arItem["ACTIVE"] == "Y" ? GetMessage("KHAYR_COMMENT_ACTIVE") : GetMessage("KHAYR_COMMENT_NONACTIVE"))?><br />
							<?=($arItem["ACTIVE"] == "Y" ? "<a href=\"parnas.khayrcomment_list.php?lang=".LANGUAGE_ID."&action=nonactive&ID=".$arItem["ID"]."&tabControl_active_tab=com_site_".$sid."\">".GetMessage("KHAYR_COMMENT_TO_NONACTIVE")."</a>" : "<a href=\"parnas.khayrcomment_list.php?lang=".LANGUAGE_ID."&action=active&ID=".$arItem["ID"]."&tabControl_active_tab=com_site_".$sid."\">".GetMessage("KHAYR_COMMENT_TO_ACTIVE")."</a>")?><br />
							<br />
							<a href="parnas.khayrcomment_list.php?lang=<?=LANGUAGE_ID?>&action=delete&ID=<?=$arItem["ID"]?>&tabControl_active_tab=com_site_<?=$sid?>" onclick="return confirm('<?=GetMessage("KHAYR_COMMENT_TO_DELETE_CONFIRM")?>')"><?=GetMessage("KHAYR_COMMENT_TO_DELETE")?></a>
						</td>
					</tr>
					<?
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">
						<?=$arCom->GetPageNavStringEx($navComponentObject, "", "arrows_adm");?>
					</td>
				</tr>
			</tfoot>
		</table>
		<?
	}
	$tabControl->End();
	echo "<div style=\"clear: both;\"></div>".BeginNote()."<a href='/bitrix/admin/settings.php?lang=".LANGUAGE_ID."&mid=parnas.khayrcomment&mid_menu=1'>".GetMessage("KHAYR_COMMENT_SETTINGS_LINK")."</a>".EndNote();
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
}
?>
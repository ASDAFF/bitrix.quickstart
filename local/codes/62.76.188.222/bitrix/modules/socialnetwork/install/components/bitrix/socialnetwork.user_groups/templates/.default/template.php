<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["FatalError"])>0)
{
	?><span class='errortext'><?=$arResult["FatalError"]?></span><br /><br /><?
}
else
{
	?><?
	$APPLICATION->IncludeComponent(
		"bitrix:socialnetwork.group.iframe.popup",
		".default",
		array(
			"PATH_TO_GROUP" => $arParams["PATH_TO_GROUP"],
			"PATH_TO_GROUP_CREATE" => $arResult["Urls"]["GroupsAdd"],
			"ON_GROUP_ADDED" => "BX.DoNothing",
			"ON_GROUP_CHANGED" => "BX.DoNothing",
			"ON_GROUP_DELETED" => "BX.DoNothing"
		),
		null,
		array("HIDE_ICONS" => "Y")
	);
	?><?

	if(strlen($arResult["ErrorMessage"])>0)
	{
		?><span class='errortext'><?=$arResult["ErrorMessage"]?></span><br /><br /><?
	}

	if ($arParams["PAGE"] == "groups_list")
	{
		?><?
		$GLOBALS["APPLICATION"]->IncludeComponent(
			"bitrix:socialnetwork.user_groups.link.add",
			".default",
			array(
				"HREF" => $arResult["Urls"]["GroupsAdd"],
				"ALLOW_CREATE_GROUP" => ($arResult["CurrentUserPerms"]["IsCurrentUser"] && $arResult["ALLOW_CREATE_GROUP"] ? "Y" : "N")
			),
			null,
			array("HIDE_ICONS" => "Y")
		);
		?><?

		$arFilterKeys = array("filter_my", "filter_archive", "filter_extranet");
		?>
		<div class="sonet-groups-menu-items-block">
			<a href="<?=(strlen($arResult["WORKGROUPS_PATH"]) > 0 ? $arResult["WORKGROUPS_PATH"] : $APPLICATION->GetCurPageParam("", $arFilterKeys, false))?>" class="sonet-groups-menu-item<?=(!$arResult["filter_my"] && !$arResult["filter_archive"] && !$arResult["filter_extranet"] && !$arResult["filter_tags"] ? " sonet-groups-menu-item-active" : "")?>"><span class="sonet-groups-menu-items-l"></span><span class="sonet-groups-menu-items-t"><?=GetMessage("SONET_C36_T_F_ALL")?></span><span class="sonet-groups-menu-items-r"></span></a><?
			if (!$arResult["bExtranet"] && $GLOBALS["USER"]->IsAuthorized())
			{			
				?><a href="<?=(strlen($arResult["WORKGROUPS_PATH"]) > 0 ? $arResult["WORKGROUPS_PATH"]."?filter_my=Y" : $APPLICATION->GetCurPageParam("filter_my=Y", $arFilterKeys, false))?>" class="sonet-groups-menu-item<?=($arResult["filter_my"] ? " sonet-groups-menu-item-active " : "")?>"><span class="sonet-groups-menu-items-l"></span><span class="sonet-groups-menu-items-t"><?=GetMessage("SONET_C33_T_F_MY")?></span><span class="sonet-groups-menu-items-r"></span></a><?
			}

			if (COption::GetOptionString("socialnetwork", "work_with_closed_groups", "N") != "Y")
			{
				?><a href="<?=(strlen($arResult["WORKGROUPS_PATH"]) > 0 ? $arResult["WORKGROUPS_PATH"]."?filter_archive=Y" : $APPLICATION->GetCurPageParam("filter_archive=Y", $arFilterKeys, false))?>" class="sonet-groups-menu-item<?=($arResult["filter_archive"] ? " sonet-groups-menu-item-active " : "")?>"><span class="sonet-groups-menu-items-l"></span><span class="sonet-groups-menu-items-t"><?=GetMessage("SONET_C33_T_F_ARCHIVE")?></span><span class="sonet-groups-menu-items-r"></span></a><?
			}

			if (IsModuleInstalled("extranet") && !$arResult["bExtranet"])
			{
				?><a href="<?=(strlen($arResult["WORKGROUPS_PATH"]) > 0 ? $arResult["WORKGROUPS_PATH"]."?filter_extranet=Y" : $APPLICATION->GetCurPageParam("filter_extranet=Y", $arFilterKeys, false))?>" class="sonet-groups-menu-item<?=($arResult["filter_extranet"] ? " sonet-groups-menu-item-active " : "")?>"><span class="sonet-groups-menu-items-l"></span><span class="sonet-groups-menu-items-t"><?=GetMessage("SONET_C33_T_F_EXTRANET")?></span><span class="sonet-groups-menu-items-r"></span></a><?
			}

			if (IsModuleInstalled("search"))
			{
				?><a href="<?=(strlen($arResult["WORKGROUPS_PATH"]) > 0 ? $arResult["WORKGROUPS_PATH"]."?filter_tags=Y" : $APPLICATION->GetCurPageParam("filter_tags=Y", $arFilterKeys, false))?>" class="sonet-groups-menu-item<?=($arResult["filter_tags"] ? " sonet-groups-menu-item-active " : "")?>"><span class="sonet-groups-menu-items-l"></span><span class="sonet-groups-menu-items-t"><?=GetMessage("SONET_C33_T_F_TAGS")?></span><span class="sonet-groups-menu-items-r"></span></a><?
			}
			?>
			<span class="sonet-groups-search"><?$GLOBALS["APPLICATION"]->IncludeComponent(
				"bitrix:socialnetwork.user_groups.search_form",
				".default",
				array(
				),
				null,
				array("HIDE_ICONS" => "Y")
			);?></span><?

		?></div>
		<div class="sonet-groups-separator"></div><?

		if ($arResult["filter_tags"] == "Y")
		{
			if (IsModuleInstalled("search"))
			{
				?>
				<div class="sonet-groups-tags-block">
				<?
				$arrFilterAdd = array("PARAMS" => array("entity" => "sonet_group"));
				$APPLICATION->IncludeComponent(
					"bitrix:search.tags.cloud",
					"",
					Array(
						"FONT_MAX" => (IntVal($arParams["FONT_MAX"]) >0 ? $arParams["FONT_MAX"] : 20), 
						"FONT_MIN" => (IntVal($arParams["FONT_MIN"]) >0 ? $arParams["FONT_MIN"] : 10),
						"COLOR_NEW" => (strlen($arParams["COLOR_NEW"]) >0 ? $arParams["COLOR_NEW"] : "3f75a2"),
						"COLOR_OLD" => (strlen($arParams["COLOR_OLD"]) >0 ? $arParams["COLOR_OLD"] : "8D8D8D"),
						"ANGULARITY" => $arParams["ANGULARITY"], 
						"PERIOD_NEW_TAGS" => $arResult["PERIOD_NEW_TAGS"], 
						"SHOW_CHAIN" => "N", 
						"COLOR_TYPE" => $arParams["COLOR_TYPE"], 
						"WIDTH" => $arParams["WIDTH"], 
						"SEARCH" => "", 
						"TAGS" => "", 
						"SORT" => "NAME", 
						"PAGE_ELEMENTS" => "150", 
						"PERIOD" => $arParams["PERIOD"], 
						"URL_SEARCH" => $arResult["PATH_TO_GROUP_SEARCH"], 
						"TAGS_INHERIT" => "N", 
						"CHECK_DATES" => "Y", 
						"FILTER_NAME" => "arrFilterAdd",
						"arrFILTER" => Array("socialnetwork"), 
						"CACHE_TYPE" => "A", 
						"CACHE_TIME" => "3600"
					),
					$component
				);
				?>
				</div>
				<?
			}
			else
				echo "<br /><span class='errortext'>".GetMessage("SONET_C36_T_NO_SEARCH_MODULE")."</span><br /><br />";
		}
	}
	?>
	<div class="sonet-groups-content-wrap">
		<div class="sonet-groups-group-block-shift">
		<?
			if (in_array($arParams["PAGE"], array("groups_list", "groups_subject")) || ($arResult["CurrentUserPerms"]["Operations"]["viewprofile"] && $arResult["CurrentUserPerms"]["Operations"]["viewgroups"]))
			{
				if ($arResult["Groups"] && $arResult["Groups"]["List"])
				{
					foreach ($arResult["Groups"]["List"] as $group)
					{
						?><span class="sonet-groups-group-block">
							<span class="sonet-groups-group-img" <?=($group["GROUP_PHOTO_RESIZED"] ? "style=\"background:url('".$group["GROUP_PHOTO_RESIZED"]["src"]."') no-repeat;\"" : "")?>></span>
							<span class="sonet-groups-group-text">
								<span class="sonet-groups-group-title"><a href="<?=$group["GROUP_URL"]?>" class="sonet-groups-group-link"><?=$group["GROUP_NAME"]?></a><?=($group["IS_EXTRANET"]=="Y" ? '<span class="sonet-groups-group-signature">'.GetMessage("SONET_C33_T_IS_EXTRANET").'</span>' : '')?></span><?
								?><?=(strlen($group["GROUP_DESCRIPTION"]) > 0 ? '<span class="sonet-groups-group-description">'.$group["GROUP_DESCRIPTION"].'</span>' : "")?><?
							?></span>
						</span><?
					}

					if (StrLen($arResult["NAV_STRING"]) > 0):
						?><?=$arResult["NAV_STRING"]?><br /><br /><?
					endif;
				}
				else
				{
					?>
					<span class="sonet-groups-group-message"><?=GetMessage("SONET_C36_T_NO_GROUPS");?></span>
					<?	
				}
			}
			else
			{
				?>
				<span class="sonet-groups-group-message"><?=GetMessage("SONET_C36_T_GR_UNAVAIL");?></span>
				<?
			}
		?>
		</div>
	</div><?
	}
?>
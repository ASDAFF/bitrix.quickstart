<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if(strlen($arResult["FatalError"])>0)
{
	?>
	<span class='errortext'><?=$arResult["FatalError"]?></span><br /><br />
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<span class='errortext'><?=$arResult["ErrorMessage"]?></span><br /><br />
		<?
	}
	?>	
	<?
	if ($arResult["CurrentUserPerms"]["Operations"]["viewprofile"] && $arResult["CurrentUserPerms"]["Operations"]["viewfriends"])
	{
		if ($arResult["Users"] && $arResult["Users"]["List"])
		{
			?>
			<div class="sonet-cntnr-user-birthday">
			<table width="100%" border="0" class="sonet-user-profile-friend-box">
			<?
			foreach ($arResult["Users"]["List"] as $friend)
			{
				echo "<tr>";
				echo "<td align=\"left\">";

				$APPLICATION->IncludeComponent("bitrix:main.user.link",
					'',
					array(
						"ID" => $friend["ID"],
						"HTML_ID" => "user_birthday_".$friend["ID"],
						"NAME" => htmlspecialcharsback($friend["NAME"]),
						"LAST_NAME" => htmlspecialcharsback($friend["LAST_NAME"]),
						"SECOND_NAME" => htmlspecialcharsback($friend["SECOND_NAME"]),
						"LOGIN" => htmlspecialcharsback($friend["LOGIN"]),
						"PERSONAL_PHOTO_IMG" => $friend["PERSONAL_PHOTO_IMG"],
						"PROFILE_URL" => htmlspecialcharsback($friend["PROFILE_URL"]),
						"PATH_TO_SONET_MESSAGES_CHAT" => $arParams["~PATH_TO_MESSAGES_CHAT"],
						"PATH_TO_SONET_USER_PROFILE" => $arParams["~PATH_TO_USER"],
						"PATH_TO_VIDEO_CALL" => $arParams["~PATH_TO_VIDEO_CALL"],
						"SHOW_FIELDS" => $arParams["SHOW_FIELDS_TOOLTIP"],
						"USER_PROPERTY" => $arParams["USER_PROPERTY_TOOLTIP"],
						"THUMBNAIL_LIST_SIZE" => $arParams["THUMBNAIL_LIST_SIZE"],
						"DATE_TIME_FORMAT" => $arParams["DATE_TIME_FORMAT"],
						"SHOW_YEAR" => $arParams["SHOW_YEAR"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"NAME_TEMPLATE" => $arParams["NAME_TEMPLATE"],
						"SHOW_LOGIN" => $arParams["SHOW_LOGIN"],
						"PATH_TO_CONPANY_DEPARTMENT" => $arParams["~PATH_TO_CONPANY_DEPARTMENT"],
					),
					false
					, array("HIDE_ICONS" => "Y")
				);

				echo "<div style=\"padding-top:5px;\">";
				if ($friend["NOW"])
					echo "<b>";
				echo $friend["BIRTHDAY"];
				if ($friend["NOW"])
					echo "</b>";
				echo "</div>";
				echo "</td>";
				echo "</tr>";
			}
			?>
			</table>
			</div>
			<?
		}
		else
		{
			echo GetMessage("SONET_C33_T_NO_FRIENDS");
		}
	}
	else
	{
		echo GetMessage("SONET_C33_T_FR_UNAVAIL");
	}
}
?>
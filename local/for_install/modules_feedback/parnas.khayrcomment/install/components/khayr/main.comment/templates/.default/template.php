<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
//$($USER->IsAdmin()) { echo "<pre>"; print_r($arParams); print_r($arResult); echo "</pre>"; die(); }

$APPLICATION->SetAdditionalCSS("/bitrix/modules/parnas.khayrcomment/libs/rateit.js/1.0.23/rateit.css");
$APPLICATION->AddHeadScript("/bitrix/modules/parnas.khayrcomment/libs/rateit.js/1.0.23/jquery.rateit.js");

function KHAYR_MAIN_COMMENT_ShowTree($arItem, $arParams, $arResult)
{
	?>
	<div class="stock">
		<div class="userInfo">
			<span><?=$arItem["PUBLISH_DATE"]?> | <?=$arItem["AUTHOR"]["FULL_NAME"]?></span>
		</div>
		<div class="userText">
			<p>
				<?if ($arItem["AUTHOR"]["AVATAR"]) {?>
					<img src="<?=$arItem["AUTHOR"]["AVATAR"]["SRC"]?>" alt="<?=$arItem["AUTHOR"]["FULL_NAME"]?>" />
				<?}?>
				<?if ($arItem["MARK"]) {?>
					<?=GetMessage("KHAYR_MAIN_COMMENT_MARK")?>:
					<?/*<div class="rateit" data-rateit-value="<?=$arItem["MARK"]?>" data-rateit-ispreset="true" data-rateit-readonly="true"></div>*/?>
					<div class="rates" id="rate_<?=$arItem["ID"]?>_result"></div>
					<script type="text/javascript">
						$(function() {
							$('#rate_<?=$arItem["ID"]?>_result').rateit({ value: <?=$arItem["MARK"]?>, ispreset: true, readonly: true });
						});
					</script>
					<br /><br />
				<?}?>
				<?if ($arItem["DIGNITY"]) {?>
					<b><?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?></b><br />
					<?=$arItem["DIGNITY"]?>
					<br /><br />
				<?}?>
				<?if ($arItem["FAULT"]) {?>
					<b><?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?></b><br />
					<?=$arItem["FAULT"]?>
					<br /><br />
				<?}?>
				<?=$arItem["PUBLISH_TEXT"]?>
				<?
				if (!empty($arItem["ADDITIONAL"]))
				{
					?><br /><?
					$str = array();
					foreach ($arItem["ADDITIONAL"] as $addit => $val)
					{
						if (!empty($addit) && !empty($val))
							$str[] = $addit.": ".$val;
					}
					echo implode(" ", $str);
				}
				?>
			</p>
			<div class='action'>
				<?if ($arItem["CAN_COMMENT"]) {?>
					<a href="javascript:void();" onclick='KHAYR_MAIN_COMMENT_add(this, <?=$arItem["ID"]?>); return false;' title='<?=GetMessage("KHAYR_MAIN_COMMENT_COMMENT")?>'><?=GetMessage("KHAYR_MAIN_COMMENT_COMMENT")?></a>
				<?}?>
				<?if ($arItem["CAN_MODIFY"]) {?>
					<?if ($arItem["CAN_COMMENT"]) {?> | <?}?>
					<a href="javascript:void();" onclick='KHAYR_MAIN_COMMENT_edit(this, <?=$arItem["ID"]?>); return false;' title="<?=GetMessage("KHAYR_MAIN_COMMENT_EDIT")?>"><?=GetMessage("KHAYR_MAIN_COMMENT_EDIT")?></a>
				<?}?>
				<?if ($arItem["CAN_DELETE"]) {?>
					<?if ($arItem["CAN_COMMENT"] || $arItem["CAN_MODIFY"]) {?> | <?}?>
					<a href='javascript:void(0)' onclick='KHAYR_MAIN_COMMENT_delete(this, <?=$arItem["ID"]?>, "<?=GetMessage("KHAYR_MAIN_COMMENT_DEL_MESS")?>"); return false;' title='<?=GetMessage("KHAYR_MAIN_COMMENT_DELETE")?>'><?=GetMessage("KHAYR_MAIN_COMMENT_DELETE")?></a>
				<?}?>
				<?if ($arItem["SHOW_RATING"]) {?>
					<?if ($arItem["CAN_COMMENT"] || $arItem["CAN_MODIFY"] || $arItem["CAN_DELETE"]) {?> | <?}?>
					<?
					$arRatingParams = Array(
						"ENTITY_TYPE_ID" => "IBLOCK_ELEMENT",
						"ENTITY_ID" => $arItem["ID"],
						"OWNER_ID" => $arItem["PROPERTIES"]["USER"]["VALUE"],
						"PATH_TO_USER_PROFILE" => ""
					);
					if (!isset($arItem['RATING']))
						$arItem['RATING'] = Array(
							"USER_HAS_VOTED" => 'N',
							"TOTAL_VOTES" => 0,
							"TOTAL_POSITIVE_VOTES" => 0,
							"TOTAL_NEGATIVE_VOTES" => 0,
							"TOTAL_VALUE" => 0
						);
					$arRatingParams = array_merge($arRatingParams, $arItem['RATING']);
					$GLOBALS["APPLICATION"]->IncludeComponent(
						"bitrix:rating.vote",
						"standart",
						$arRatingParams,
						$component,
						Array("HIDE_ICONS" => "Y")
					);
					?>
				<?}?>
				<?if ($arItem["CAN_MODIFY"]) {?>
					<div class="form comment form_for" id='edit_form_<?=$arItem["ID"]?>'<?=($arResult["POST"]["COM_ID"] == $arItem["ID"] && !$arResult["SUCCESS"] ? " style='display: block;'" : "")?>>
						<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
							<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'><?=$arItem["~PREVIEW_TEXT"]?></textarea>
							<input type='hidden' name='ACTION' value='update' />
							<input type='hidden' name='COM_ID' value='<?=$arItem["ID"]?>' />
							<input type="submit" value="<?=GetMessage("KHAYR_MAIN_COMMENT_SAVE")?>" />
							<a href="javascript:void(0)" onclick='KHAYR_MAIN_COMMENT_back(); return false;' style='margin-top: -25px; text-decoration: none;'><?=GetMessage("KHAYR_MAIN_COMMENT_BACK_BUTTON")?></a>
						</form>
					</div>
				<?}?>
				<?if ($arItem["CAN_COMMENT"]) {?>
					<div class="form comment form_for" id='add_form_<?=$arItem["ID"]?>'<?=($arResult["POST"]["PARENT"] == $arItem["ID"] && !$arResult["SUCCESS"] ? " style='display: block;'" : "")?>>
						<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
							<input type="text" name='NONUSER' value='<?=$arResult["POST"]["NONUSER"]?>' <?=($arResult["USER"]["ID"] ? "readonly='readonly'" : "")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_FNAME")?>" class="w-45" />
							<?if ($arResult["LOAD_AVATAR"]) {?>
								<input type="file" name='AVATAR' value='' placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_AVATAR")?>" class="w-45" />
							<?}?>
							<?if ($arResult["LOAD_EMAIL"]) {?>
								<input type="text" name='EMAIL' <?=($arResult["USER"]["ID"] ? "value='".$arResult["USER"]["EMAIL"]."' readonly='readonly'" : "value='".$arResult["POST"]["EMAIL"]."'")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_EMAIL")?>" class="w-45" />
							<?}?>
							<?foreach ($arParams["ADDITIONAL"] as $additional) {?>
								<input type="text" name='<?=urlencode($additional)?>' value='<?=$arResult["POST"][$additional]?>' placeholder="<?=$additional?>" class="w-45" />
							<?}?>
							<div class="clear pt10"></div>
							<?if ($arParams["LOAD_MARK"]) {?>
								<?=GetMessage("KHAYR_MAIN_COMMENT_MARK")?>:
								<?/*<input type="range" name="MARK" value="0" step="1" id="rate_<?=$arItem["ID"]?>">
								<div class="rateit" data-rateit-backingfld="#rate_<?=$arItem["ID"]?>" data-rateit-resetable="false" data-rateit-min="0" data-rateit-max="5"></div>*/?>
								<input type="hidden" name="MARK" value="0" id="rate_<?=$arItem["ID"]?>" />
								<div class="rates" id="rate_<?=$arItem["ID"]?>_control"></div>
								<script type="text/javascript">
									$(function() {
										$('#rate_<?=$arItem["ID"]?>_control').rateit({ min: 0, max: 5, step: 1, backingfld: '#rate_<?=$arItem["ID"]?>', resetable: false });
									});
								</script>
								<div class="clear pt10"></div>
							<?}?>
							<?if ($arParams["LOAD_DIGNITY"]) {?>
								<textarea name="DIGNITY" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?>'><?=$arResult["POST"]["DIGNITY"]?></textarea>
							<?}?>
							<?if ($arParams["LOAD_FAULT"]) {?>
								<textarea name="FAULT" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?>'><?=$arResult["POST"]["FAULT"]?></textarea>
							<?}?>
							<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'></textarea>
							<input type='hidden' name='PARENT' value='<?=$arItem["ID"]?>' />
							<input type='hidden' name='ACTION' value='add' />
							<input type='hidden' name='DEPTH' value='<?=($arItem["PROPERTIES"]["DEPTH"]["VALUE"]+1)?>' />
							<?if ($arParams["USE_CAPTCHA"]) {?>
								<div>
									<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_1")?></div>
									<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
									<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_2")?></div>
									<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
									<input type='hidden' name='clear_cache' value='Y' />
								</div>
							<?}?>
							<?if ($arParams["LEGAL"]) {?>
								<input type='checkbox' id="LEGAL_<?=$arItem["ID"]?>_form" name='LEGAL' value='Y' <?=($arResult["POST"]["LEGAL"] == "Y" ? "checked" : "")?> />
								<label for="LEGAL_<?=$arItem["ID"]?>_form"><?=$arParams["LEGAL_TEXT"]?></label>
								<div class="clear pt10"></div>
							<?}?>
							<input type="submit" value="<?=GetMessage("KHAYR_MAIN_COMMENT_ADD")?>" />
							<a href="javascript:void(0)" onclick='KHAYR_MAIN_COMMENT_back(); return false;' style='margin-top: -25px; text-decoration: none;'><?=GetMessage("KHAYR_MAIN_COMMENT_BACK_BUTTON")?></a>
						</form>
					</div>
				<?}?>
			</div>
		</div>
		<?if (!empty($arItem["CHILDS"])) {?>
			<?foreach ($arItem["CHILDS"] as $item) {?>
				<?=KHAYR_MAIN_COMMENT_ShowTree($item, $arParams, $arResult)?>
			<?}?>
		<?}?>
	</div>
	<?
}
?>
<div class='khayr_main_comment' id='KHAYR_MAIN_COMMENT_container'>
	<?if (strlen($_POST["ACTION"]) > 0) $GLOBALS["APPLICATION"]->RestartBuffer();?>
	<h1><span><?=GetMessage("KHAYR_MAIN_COMMENT_NAME")?></span></h1>
	<p style='color: green; display: none;' class='suc'><?=$arResult["SUCCESS"]?></p>
	<p style='color: red; display: none;' class='err'><?=$arResult["ERROR_MESSAGE"]?></p>
	<div class="form comment main_form"<?=($arResult["POST"]["PARENT"] > 0 && !$arResult["SUCCESS"] ? " style='display: none;' " : "")?>>
		<?if ($arResult["CAN_COMMENT"]) {?>
			<form enctype="multipart/form-data" action="<?=$GLOBALS["APPLICATION"]->GetCurUri()?>" method='POST' onsubmit='return KHAYR_MAIN_COMMENT_validate(this);'>
				<input type="text" name='NONUSER' value='<?=$arResult["POST"]["NONUSER"]?>' <?=($arResult["USER"]["ID"] ? "readonly='readonly'" : "")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_FNAME")?>" class="w-45" />
				<?if ($arResult["LOAD_AVATAR"]) {?>
					<input type="file" name='AVATAR' value='' placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_AVATAR")?>" class="w-45" />
				<?}?>
				<?if ($arResult["LOAD_EMAIL"]) {?>
					<input type="text" name='EMAIL' <?=($arResult["USER"]["ID"] ? "value='".$arResult["USER"]["EMAIL"]."' readonly='readonly'" : "value='".$arResult["POST"]["EMAIL"]."'")?> placeholder="<?=GetMessage("KHAYR_MAIN_COMMENT_EMAIL")?>" class="w-45" />
				<?}?>
				<?foreach ($arParams["ADDITIONAL"] as $additional) {?>
					<input type="text" name='<?=urlencode($additional)?>' value='<?=$arResult["POST"][$additional]?>' placeholder="<?=$additional?>" class="w-45" />
				<?}?>
				<div class="clear pt10"></div>
				<?if ($arParams["LOAD_MARK"]) {?>
					<?=GetMessage("KHAYR_MAIN_COMMENT_MARK")?>:
					<?/*<input type="range" name="MARK" value="0" step="1" id="rate_0">
					<div class="rateit" data-rateit-backingfld="#rate_0" data-rateit-resetable="false" data-rateit-min="0" data-rateit-max="5"></div>*/?>
					<input type="hidden" name="MARK" value="0" id="rate_0" />
					<div class="rates" id="rate_0_control"></div>
					<script type="text/javascript">
						$(function() {
							$('#rate_0_control').rateit({ min: 0, max: 5, step: 1, backingfld: '#rate_0', resetable: false });
						});
					</script>
					<div class="clear pt10"></div>
				<?}?>
				<?if ($arParams["LOAD_DIGNITY"]) {?>
					<textarea name="DIGNITY" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_DIGNITY")?>'><?=$arResult["POST"]["DIGNITY"]?></textarea>
				<?}?>
				<?if ($arParams["LOAD_FAULT"]) {?>
					<textarea name="FAULT" rows="3" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_FAULT")?>'><?=$arResult["POST"]["FAULT"]?></textarea>
				<?}?>
				<textarea name="MESSAGE" rows="10" placeholder='<?=GetMessage("KHAYR_MAIN_COMMENT_MESSAGE")?>'><?=$arResult["POST"]["MESSAGE"]?></textarea>
				<input type='hidden' name='PARENT' value='' />
				<input type='hidden' name='ACTION' value='add' />
				<input type='hidden' name='DEPTH' value='1' />
				<?if ($arParams["USE_CAPTCHA"]) {?>
					<div>
						<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_1")?></div>
						<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
						<div><?=GetMessage("KHAYR_MAIN_COMMENT_CAP_2")?></div>
						<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
						<input type='hidden' name='clear_cache' value='Y' />
					</div>
				<?}?>
				<?if ($arParams["LEGAL"]) {?>
					<input type='checkbox' id="LEGAL_main_form" name='LEGAL' value='Y' <?=($arResult["POST"]["LEGAL"] == "Y" ? "checked" : "")?> />
					<label for="LEGAL_main_form"><?=$arParams["LEGAL_TEXT"]?></label>
					<div class="clear pt10"></div>
				<?}?>
				<input type="submit" value="<?=GetMessage("KHAYR_MAIN_COMMENT_ADD")?>" />
			</form>
		<?} else {?>
			<div style='background: #FFFFFF;'>
				<?=GetMessage("KHAYR_MAIN_COMMENT_DO_AUTH", array("#LINK#" => $arParams["AUTH_PATH"]))?>
			</div>
		<?}?>
	</div>
	<?if ($arResult["ITEMS"]) {?>
		<?if ($arParams["DISPLAY_TOP_PAGER"]) {?>
			<div class="nav"><?=$arResult["NAV_STRING"]?></div>
		<?}?>
		<div class="comments">
			<?
			foreach ($arResult["ITEMS"] as $k => $arItem)
			{
				echo KHAYR_MAIN_COMMENT_ShowTree($arItem, $arParams, $arResult);
			}
			?>
		</div>
		<?if ($arParams["DISPLAY_BOTTOM_PAGER"]) {?>
			<div class="nav"><?=$arResult["NAV_STRING"]?></div>
		<?}?>
	<?}?>
	<?if (strlen($_POST["ACTION"]) > 0) die();?>
</div>
<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	$(function() {
		$(".callback_anch").simpleCallback();
	});
</script>
<a class="callback_anch" href="#"><?=$arParams["HREF_TEXT"]?></a>

<script type="text/javascript">
	function checkForm () {
		var title;
		var elem;
		var dutyField = "<?=GetMessage('EMPTY_FIELD')?>";
		var check = true;

		function checkError (field, str) {
			document.getElementById("alert").innerHTML = str;
			document.forms.preview.field.focus();
			check = false;
		}

		document.getElementById("alert").innerHTML = "";

		<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?>
			if (check) {
				title = '"<?=GetMessage("MFT_NAME")?>"';
				elem = document.preview.user_name.value;
				if (elem == '') checkError('user_name', dutyField + title);
			}
		<?endif?>
		<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TEL", $arParams["REQUIRED_FIELDS"])):?>
			if (check) {
				title = '"<?=GetMessage("MFT_TEL")?>"';
				elem = document.preview.user_tel.value;
				if (elem == '') checkError('user_tel', dutyField + title);
			}
		<?endif?>
		<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TIME", $arParams["REQUIRED_FIELDS"])):?>
			if (check) {
				title = '"<?=GetMessage("MFT_TIME")?>"';
				elem = document.preview.user_time.value;
				if (elem == '') checkError('user_time', dutyField + title);
			}
		<?endif?>
		<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("REGION", $arParams["REQUIRED_FIELDS"])):?>
			if (check) {
				title = '"<?=GetMessage("MFT_REGION")?>"';
				elem = document.preview.user_region.value;
				if (elem == '') checkError('user_region', dutyField + title);
			}
		<?endif?>
		if (check) { 
			document.preview.submit();
		}
		return check;
	}
</script>

<div class="callback_body"></div>
<div class="callback">
	<a href="#" class="callback_close"></a>
	<div class="title"><?=$arParams["HEAD_TEXT"]?></div>
	<div class="mcallback">
		<form name="preview" action="<?=$APPLICATION->GetCurPage()?>" method="POST">
			<div class="mf-name">
				<div class="mf-text">
					<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>"/>
			</div>
			<div class="mf-tel">
				<div class="mf-text">
					<?=GetMessage("MFT_TEL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TEL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<input type="text" name="user_tel" value="<?=$arResult["AUTHOR_TEL"]?>"/>
			</div>
			<div class="mf-time">
				<div class="mf-text">
					<?=GetMessage("MFT_TIME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("TIME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<input type="text" name="user_time" value="<?=$arResult["AUTHOR_TIME"]?>"/>
			</div>
			<div class="mf-region">
				<div class="mf-text">
					<?=GetMessage("MFT_REGION")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("REGION", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<input type="text" name="user_region" value="<?=$arResult["AUTHOR_REGION"]?>"/>
			</div>
			<div class="submit">
				<span id="alert"></span>
				<input type="button" name="button" value="" onclick="checkForm()"/>
			</div>
		</form>
	</div>
</div>
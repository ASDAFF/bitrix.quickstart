# BitrixHelper

Набор классов упрощающий работу с Битриксом.


## Установка

 * Скачиваем архив, распаковываем
 * Копируем сюда: /local/php_interface/vendor/bitrix-helper/
 
	Должно получиться приблизительно следуующее дерево файлов:

	/local/php_interface/vendor/bitrix-helper/

	/local/php_interface/vendor/bitrix-helper/src/

	/local/php_interface/vendor/bitrix-helper/src/BitrixHelper/...

	/local/php_interface/vendor/bitrix-helper/src/autoload.php

	/local/php_interface/vendor/bitrix-helper/README.md
 * в файле /local/php_interface/init.php подключаем наши классы
 
	 ```php
	 require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/vendor/bitrix-helper/src/autoload.php'); // BitrixHelper
	 ```

## Работа с формами

Все разработчики на битриксе знают как выглядят стандартные шаблоны этой CMS. Приблизительно так:

```
<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?if ($arResult["isFormErrors"] == "Y"):?><?=$arResult["FORM_ERRORS_TEXT"];?><?endif;?>

<?=$arResult["FORM_NOTE"]?>

<?if ($arResult["isFormNote"] != "Y")
{
?>
<?=$arResult["FORM_HEADER"]?>

<table>
<?
if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y")
{
?>
	<tr>
		<td><?
/***********************************************************************************
					form header
***********************************************************************************/
if ($arResult["isFormTitle"])
{
?>
	<h3><?=$arResult["FORM_TITLE"]?></h3>
<?
} //endif ;

	if ($arResult["isFormImage"] == "Y")
	{
	?>
	<a href="<?=$arResult["FORM_IMAGE"]["URL"]?>" target="_blank" alt="<?=GetMessage("FORM_ENLARGE")?>"><img src="<?=$arResult["FORM_IMAGE"]["URL"]?>" <?if($arResult["FORM_IMAGE"]["WIDTH"] > 300):?>width="300"<?elseif($arResult["FORM_IMAGE"]["HEIGHT"] > 200):?>height="200"<?else:?><?=$arResult["FORM_IMAGE"]["ATTR"]?><?endif;?> hspace="3" vscape="3" border="0" /></a>
	<?//=$arResult["FORM_IMAGE"]["HTML_CODE"]?>
	<?
	} //endif
	?>

			<p><?=$arResult["FORM_DESCRIPTION"]?></p>
		</td>
	</tr>
	<?
} // endif
	?>
</table>
<br />
<?
/***********************************************************************************
						form questions
***********************************************************************************/
?>
<table class="form-table data-table">
	<thead>
		<tr>
			<th colspan="2">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}
		else
		{
	?>
		<tr>
			<td>
				<?if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])):?>
				<span class="error-fld" title="<?=$arResult["FORM_ERRORS"][$FIELD_SID]?>"></span>
				<?endif;?>
				<?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
				<?=$arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />".$arQuestion["IMAGE"]["HTML_CODE"] : ""?>
			</td>
			<td><?=$arQuestion["HTML_CODE"]?></td>
		</tr>
	<?
		}
	} //endwhile
	?>
<?
if($arResult["isUseCaptcha"] == "Y")
{
?>
		<tr>
			<th colspan="2"><b><?=GetMessage("FORM_CAPTCHA_TABLE_TITLE")?></b></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="hidden" name="captcha_sid" value="<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" /><img src="/bitrix/tools/captcha.php?captcha_sid=<?=htmlspecialcharsbx($arResult["CAPTCHACode"]);?>" width="180" height="40" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("FORM_CAPTCHA_FIELD_TITLE")?><?=$arResult["REQUIRED_SIGN"];?></td>
			<td><input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" /></td>
		</tr>
<?
} // isUseCaptcha
?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="2">
				<input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
				<?if ($arResult["F_RIGHT"] >= 15):?>
				&nbsp;<input type="hidden" name="web_form_apply" value="Y" /><input type="submit" name="web_form_apply" value="<?=GetMessage("FORM_APPLY")?>" />
				<?endif;?>
				&nbsp;<input type="reset" value="<?=GetMessage("FORM_RESET");?>" />
			</th>
		</tr>
	</tfoot>
</table>
<p>
<?=$arResult["REQUIRED_SIGN"];?> - <?=GetMessage("FORM_REQUIRED_FIELDS")?>
</p>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)
?>
```

При этом работать с таким шаблоном неудобно. И заастую тратится много времени на то чтобы кастомизировать шаблон.

С помощью нашего класса ваш шаблон будет выглядеть приблизительно так:

```html
<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$formHelper = new \BitrixHelper\Form($arResult);
?>
<?= $formHelper->Start(); ?>
	<div class="row">
		<div class="col-md-4">
			<?= $formHelper->Errors(); ?>
			<div class="form-group">
				<?= $formHelper->Label(1) ?>
				<?= $formHelper->Widget(1) ?>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<?= $formHelper->Label(2) ?>
						<?= $formHelper->Widget(2) ?>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<?= $formHelper->Label(3) ?>
						<?= $formHelper->Widget(3) ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?= $formHelper->Label(4) ?>
				<?= $formHelper->Widget(4) ?>
			</div>
			<div class="form-group">
				<?= $formHelper->Label(5) ?>
				<?= $formHelper->Widget(5) ?>
			</div>
			<div class="form-group text-center">
				<?= $formHelper->Submit() ?>
			</div>
		</div>
	</div>
<?= $formHelper->End(); ?>
```
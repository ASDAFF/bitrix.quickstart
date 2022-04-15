<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$disabled = (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");
$value = strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"];
?>
<? if ($arResult["isFormErrors"] == "Y"): ?><?= $arResult["FORM_ERRORS_TEXT"]; ?><? endif; ?>
<?= $arResult["FORM_HEADER"] ?>

<? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion): ?>
    <?
    if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
        echo $arQuestion["HTML_CODE"];
    }
endforeach;
?>

	<div class="last-b">
      <?= $arResult["QUESTIONS"]["SIMPLE_QUESTION_527"]["HTML_CODE"] ?>
      <?= $arResult["QUESTIONS"]["email"]["HTML_CODE"] ?>
      <?= $arResult["QUESTIONS"]["telephone"]["HTML_CODE"] ?>
		<div class="group-but-one">

			<input <?= $disabled ?>
							class="input-feed but-feed BlissPro-Regular"
							type="submit"
							name="web_form_submit"
							value="<?= htmlspecialcharsbx($value) ?>"/>
			<i class="fal fa-edit send-ic fa-flip-horizontal"></i>
		</div>

	</div>


<?= $arResult["FORM_FOOTER"] ?>

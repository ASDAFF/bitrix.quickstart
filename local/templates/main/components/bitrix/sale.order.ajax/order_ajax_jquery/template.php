
<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<? if($_POST["AJAX_QUERY"] == "Y"): ?>
	<? // AJAX-запрос. Очищаем весь вывод битрикса и включаем скрипт обработки AJAX-запроса. ?>
	<? $APPLICATION->RestartBuffer(); ?>
	<? include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/ajax-gate.php"); ?>
	<? die(); ?>
<? endif; ?>

<?
	$confirm = false;
		// Проверяем, авторизован ли пользователь
	if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N") {
		// Пользователь не авторизован, и зарегистрировать его автоматически нельзя

		// Показываем сообщения и ошибки, если они есть
		if(!empty($arResult["ERROR"])) {
			foreach($arResult["ERROR"] as $v)
				echo ShowError($v);
		} elseif(!empty($arResult["OK_MESSAGE"])) {
			foreach($arResult["OK_MESSAGE"] as $v)
				echo ShowNote($v);
		}

		// Авторизация
		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
	} else {
		// Пользователь авторизован
		// Сформирован ли заказ?
		if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y") {
			if(strlen($arResult["REDIRECT_URL"]) > 0) {
				$APPLICATION->RestartBuffer();
				echo json_encode(Array("redirect" => $arResult["REDIRECT_URL"]));
				die();
			} else {
				$confirm = true;
			}
		} else {
			if($_POST["AJAX_QUERY"] == "Y") {
				$isAjaxQuery = true;
			} else {
				$isAjaxQuery = false;
			}

			if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
				echo $arResult["PREPAY_ADIT_FIELDS"];
		}
	}
?>

<? if ($confirm): ?>
	<? include('confirm.php'); ?>
<? else: ?>

	<NOSCRIPT>
		<div class="errortext">Для оформления заказа необходимо включить JavaScript. По-видимому, JavaScript либо не поддерживается браузером, либо отключен. Измените настройки браузера и затем <a href="">повторите попытку</a>.</div>
	</NOSCRIPT>
		
		<div class="order-form order-errors hidden"></div>

	<form action="" method="POST" name="ORDER_FORM" id="ORDER_FORM" class="theform">
		<?=bitrix_sessid_post()?>

		<div id="order_form_content">
			<div class="orderform-persontype"></div>
			<div class="orderform-properties"></div>
			<div class="orderform-delivery"></div>
			<div class="orderform-paysystem"></div>
			<div class="orderform-summary"></div>
		</div>

		<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
		<input type="hidden" name="profile_change" id="profile_change" value="N">
		<input type="hidden" name="AJAX_QUERY" id="AJAX_QUERY" value="Y">
				
		<div align="right">
			<input type="button" name="submitbutton" onClick="submitForm();" value="Оформить заказ">
		</div>
	</form>
<? endif; ?>
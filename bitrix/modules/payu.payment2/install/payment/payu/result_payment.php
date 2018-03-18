<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_GET['result'])) {
	if ($_GET['result'] === '-1') {
		$message = ' Вам выставлен счёт в системе Qiwi. Пожалуйста перейдите в личный кабинет Qiwi по адресу http://qiwi.com/ и подтвердите оплату. ';
	} elseif ($_GET['result'] === '0') {
		$message = ' Ваш платёж успешно выполнен. Ваш заказ будет обработан...';
	} elseif ($_GET['result'] === '1') {
		$message = ' Не удалось обработать ваш платёж. Пожалуйста попробуйте повторить платёж или обратитесь к нешему консультанту...';
	} else {
		$message = 'Информация о платеже не доступна.';
	}
}
?>

<div>
	<h1> Информация о статусе оплаты </h1>
	<h4> Уважаемый покупатель! </h4>
	<p> <?= $message ?> </p>
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
<?
global $MESS;

$MESS["SPCP_DTITLE"] = "Яндекс.Деньги 3.x";
$MESS["SHOP_ID"] = "Идентификатор магазина в ЦПП (ShopID)";
$MESS["SHOP_ID_DESCR"] = "Код магазина, который получен от Яндекс";
$MESS["SCID"] = "Номер витрины магазина в ЦПП (scid)";
$MESS["SCID_DESCR"] = "";
$MESS["SCID_DESCT"] = "";
$MESS["ORDER_ID"] = "Номер заказа";
$MESS["ORDER_ID_DESCR"] = "";
$MESS["SHOP_KEY"] = "Пароль магазина";
$MESS["SHOP_KEY_DESCR"] = "Пароль магазина на Яндекс";
$MESS["SHOULD_PAY"] = "Сумма заказа";
$MESS["SHOULD_PAY_DESCR"] = "Сумма к оплате";
$MESS["ORDER_DATE"] = "Дата создания заказа";
$MESS["ORDER_DATE_DESCR"] = "";
$MESS["IS_TEST"] = "Тестовый режим";
$MESS["IS_TEST_DESCR"] = "Если пустое значение - магазин будет работать в обычном режиме";
$MESS["PYM_CHANGE_STATUS_PAY"] = "Автоматически оплачивать заказ при получении успешного статуса оплаты";
$MESS["PYM_CHANGE_STATUS_PAY_DESC"] = "Y - оплачивать, N - не оплачивать.";
$MESS["SALE_TYPE_PAYMENT"] = "Тип платёжной системы";
$MESS["SALE_YMoney"] = "Яндекс.Деньги";
$MESS["SALE_YCards"] = "Банковские карты";
$MESS["SALE_YTerminals"] = "Терминалы";
$MESS["SALE_YMobile"] = "Мобильные платежи";
$MESS["SALE_YSberbank"] = "Сбербанк Онлайн";
$MESS["SALE_YmPOS"] = "Мобильный терминал (mPOS)";

$MESS["SPCP_DDESCR"] = "Работа через Центр Приема Платежей <a href=\"http://money.yandex.ru\" target=\"_blank\">http://money.yandex.ru</a>
<br/>Используется протокол commonHTTP-3.0
<br/><br/>
<input
	id=\"https_check_button\"
	type=\"button\"
	value=\"Проверка HTTPS\"
	title=\"Проверка доступности сайта по протоколу HTTPS. Необходимо для корректной работы платежной системы\"
	onclick=\"
		var checkHTTPS = function(){
			BX.showWait();
			BX.ajax.post('/bitrix/admin/sale_pay_system_edit.php', '".CUtil::JSEscape(bitrix_sessid_get())."&https_check=Y', function (result)
			{
				BX.closeWait();
				var res = eval( '('+result+')' );
				BX('https_check_result').innerHTML = '&nbsp;' + res['text'];

				BX.removeClass(BX('https_check_result'), 'https_check_success');
				BX.removeClass(BX('https_check_result'), 'https_check_fail');

				if (res['status'] == 'ok')
					BX.addClass(BX('https_check_result'), 'https_check_success');
				else
					BX.addClass(BX('https_check_result'), 'https_check_fail');
			});
		};
		checkHTTPS();\"
	/>
<span id=\"https_check_result\"></span>
<br/>";
?>

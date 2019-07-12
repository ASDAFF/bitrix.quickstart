<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//$APPLICATION->SetTitle("Моя корзина");

if ($_REQUEST['action']=='add' && intval($_REQUEST['QTY'])>0 && $_REQUEST['ID']>0) {
	//$res = Add2BasketByProductID(intval($_REQUEST['ID']), intval($_REQUEST['QTY']));
	if (CModule::IncludeModule("sale")) {
		// получаем товар
		$dbEl = CIBlockElement::GetList(array(), array('ID'=>intval($_REQUEST['ID'])), FALSE, FALSE, array('IBLOCK_ID', 'ID', 'DETAIL_PAGE_URL', 'NAME'));
		$arEl = $dbEl->GetNext();

		$arPrice = CCatalogProduct::GetOptimalPrice($arEl['ID'], '1');
		$price['PRICE'] = $arPrice["DISCOUNT_PRICE"];
		
		$CALLBACK_FUNC = "CatalogBasketCallback";
		$arFields = array(
			"PRODUCT_ID" => intval($_REQUEST['ID']),
			"PRICE" => $price["PRICE"],
			"CURRENCY" => "RUB",
			"QUANTITY" => intval($_REQUEST['QTY']),
			"LID" => LANG,
			"NAME" => $arEl['NAME'],
			"DETAIL_PAGE_URL" => $arEl['DETAIL_PAGE_URL'],
			"CALLBACK_FUNC" => $CALLBACK_FUNC
		);
		CSaleBasket::Add($arFields);
	}
	LocalRedirect('#SITE_DIR#basket/');
}

if ($_REQUEST['action']=='delete' && $_REQUEST['ID']>0) {
	$res = CSaleBasket::Delete($_REQUEST['ID']);
	LocalRedirect('#SITE_DIR#basket/');
}

CSaleBasket::Init();

$STEP=1;
if ($_REQUEST['OK1'] && strlen(trim($_REQUEST['USER_LOGIN']))>0) { // сверхбыстрая регистрация
	$arError=array();
	$_REQUEST['USER_LOGIN'] = trim($_REQUEST['USER_LOGIN']);
	if (!check_email($_REQUEST['USER_LOGIN'])) {
		$arError[]='Введите корректный E-mail!';
	}
	
	if (count($arError)<=0) {
		$pass = randString(7);
		
		$user = new CUser;
		$arFields = Array(
		  "EMAIL"             => $_REQUEST['USER_LOGIN'],
		  "LOGIN"             => $_REQUEST['USER_LOGIN'],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",
		  "GROUP_ID"          => array(2, 3, 6, 7),
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass
		);
		
		$ID = $user->Add($arFields);
		if (intval($ID)>0) {
			CUser::SendUserInfo($ID, SITE_ID, "Приветствуем Вас как нового пользователя нашего сайта!");
			$USER->Authorize($ID);
			LocalRedirect($APPLICATION->GetCurPageParam().'#step-order');
		} else {
			$err = explode("<br>", $user->LAST_ERROR);
			$arError=array_merge($arError, $err);
		}
	}
}

$showpass=FALSE;
foreach ($arError as $key => $value) {
	if (strpos($value, 'Пользователь с таким e-mail')!==FALSE) {
		$arError[$key] = $value.' Пожалуйста, авторизуйтесь.';
		$showpass=TRUE;
	}
	if (strpos($value, "Пользователь с логином")!==FALSE) unset($arError[$key]);
}

if ($USER->IsAuthorized()) { // авторизация с паролем
	$STEP=2;
}
?>
<div class="contentclose contenttext nomart" id="basket">
	<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket", "basket_order", array(
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
	"COLUMNS_LIST" => array(
		0 => "NAME",
		1 => "PRICE",
		2 => "QUANTITY",
		3 => "DELETE",
	),
	"PATH_TO_ORDER" => "#SITE_DIR#order/",
	"HIDE_COUPON" => "N",
	"QUANTITY_FLOAT" => "N",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"SET_TITLE" => "Y"
	),
	false
);?>
</div>
<? $dbBasketItems = CSaleBasket::GetList(
	array("NAME" => "ASC","ID" => "ASC"),
	array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
	false,
	false,
	array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT")
);

if ($dbBasketItems->SelectedRowsCount()>0) { ?>
	<h1 id="order-h1" style="display: none;">Оформление заказа</h1>
	<div class="contentclose contenttext nomart">
		<div class="order_in_basket" style="display: none;" id="step-order">
			<? if (count($arError)>0) { ?>
				<span style="color: red;"><?=implode("<br />", $arError);?></span><br />
			<? } ?>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<col width="70" />
				<col width="185" />
				<col width="" />
				<tr>
					<td class="step_num">Шаг 1</td>
					<td class="step_descr">
						На Ваш E-mail будут приходить<br />пароли и уведомления o статусе заказа.
					</td>
					<td>
						<form action="?login=yes#step-order" method="POST">
							<input type="hidden" name="AUTH_FORM" value="Y" />
							<input type="hidden" name="AUTH_FORM" value="Y">
							<input type="hidden" name="TYPE" value="AUTH">
							E-mail:
							<input type="email" name="USER_LOGIN" value="<?=($USER->IsAuthorized()) ? $USER->GetEmail() : $_REQUEST['USER_LOGIN']?>" placeholder="введите E-mail"<? if ($STEP==2) { ?> disabled="disabled"<? } ?> />
							<? if ($STEP==1) { ?>
								<input type="submit" value="OK" class="btn do wd-30 hg-21 pt-1 pb-2 pl-5 pr-5" name="OK1" />
								<a href="javascript:void(0);" id="havepass" title="Если вы уже регистрировались ранее на #SERVER_NAME#">У меня есть пароль</a>
								<span class="havepass-block">
									<input type="submit" value="OK" class="btn do wd-30 hg-21 pt-1 pb-2 pl-5 pr-5" name="Login" />
									<a href="javascript:void(0);" id="havepassno" title="Если вы впервые на #SERVER_NAME#">У меня нет пароля</a>
									<a href="/personal/?forgot_password=yes" id="forgotpass"><small>я забыл пароль</small></a>
								</span>
							<? } else { ?>
								<br /><small>вы уже авторизованы, ввод E-mail не требуется.</small>
							<? } ?>
						</form>
					</td>
				</tr>
				<? if ($STEP==2) { ?>
					<tr>
						<td class="step_num">Шаг 2</td>
						<td colspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">
							<?$APPLICATION->IncludeComponent("bitrix:sale.order.ajax", "order-list-mini", array(
	"PAY_FROM_ACCOUNT" => "N",
	"COUNT_DELIVERY_TAX" => "N",
	"COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
	"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
	"ALLOW_AUTO_REGISTER" => "Y",
	"SEND_NEW_USER_NOTIFY" => "Y",
	"DELIVERY_NO_AJAX" => "Y",
	#PROPS#
	"PATH_TO_BASKET" => "#SITE_DIR#basket/",
	"PATH_TO_PERSONAL" => "#SITE_DIR#personal/order/",
	"PATH_TO_PAYMENT" => "#SITE_DIR#personal/order/payment/",
	"PATH_TO_AUTH" => "#SITE_DIR#auth/",
	"SET_TITLE" => "Y"
	),
	false
);?>
						</td>
					</tr>
				<? } ?>
			</table>
		</div>
		<script type="text/javascript">
			$('#havepass').bind('click', function () {
				$('.order_in_basket .havepass-block').prepend('<input type="password" name="USER_PASSWORD" placeholder="введите пароль" />');
				$('.order_in_basket .havepass-block').css('display', 'inline');
				$('.order_in_basket input[name=OK1]').css('display', 'none');
				$(this).css('display', 'none');
				return false;
			});
			$('#havepassno').bind('click', function () {
				$('.order_in_basket .havepass-block').css('display', 'none');
				$('.order_in_basket .havepass-block input[name=USER_PASSWORD]').remove();
				$('.order_in_basket input[name=OK1]').css('display', 'inline');
				$('#havepass').css('display', 'inline');
				return false;
			});
			
			<? if ($showpass) { ?>
				$('#havepass').trigger('click');
			<? } ?>
			
			var hash = window.location.hash;
			if (hash=='#step-order') {
				basketOrderView();
			}
		</script>
	</div>
<? } ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
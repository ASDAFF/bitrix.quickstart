# Favorites
[Bitrix] Битрикс — избранное (добавление/удаление)

Часто встречается на сайтах блок Избранное. Сделаем велосипед для Битрикса.

Скрипт кнопочки:
```javascript
if (window.jQuery) {
$(document).ready(function(){ 
    $('.like a').click(function () {
        var item = $(this).data('itemid');
        var act = $(this).data('act');
        var lnk_like = $(this);
        var lnk = '/ajax/fav.php';
        $.post(lnk, { id: item, act: act }, function(data) {
            // alert(data.res);
            if (act == 'add') {
                lnk_like.find('span').html('В избранном');
                lnk_like.data('act', 'del');
            }
            else {
                lnk_like.find('span').html('В избранное');
                lnk_like.data('act', 'add');
            }
        }, "json");
        return false;
    });
});
}
```

php (файл /ajax/fav.php):
```php
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use \Bitrix\Main\Application;
use \Bitrix\Main\Web\Cookie;
global $APPLICATION;
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
header('Content-type: text/html; charset=utf-8');
CModule::IncludeModule('iblock');
$act = $_REQUEST['act'];
$ID = intval($_REQUEST['id']);
if($ID > 0)
   {
      $arrItemID = array();      
      if(!$USER->IsAuthorized()){
         $arElements = unserialize($APPLICATION->get_cookie('bo_favorites'));
         if ($act == 'del') {
            unset($arElements[$ID]);
         }
         else {
            $arElements[$ID] = $ID;
         }
        //старый вариант установки cookie, который с недавних пор перестал работать
         //$APPLICATION->set_cookie("bo_favorites",serialize($arElements));
// UPD: новый рабочий вариант работы с cookie в D7
$value = serialize($arElements);
$time = 3600*24*30;//30 d
$cookie = new Cookie("bo_favorites", $value, time() + $time);
$cookie->setDomain($context->getServer()->getHttpHost());
// $cookie->setHttpOnly(false);
$cookie->setHttpOnly(true);
$cookie->setSecure(false);
$context->getResponse()->addCookie($cookie);
$context->getResponse()->flush("");
      }
      else{
         $idUser = $USER->GetID();
         $rsUser = CUser::GetByID($idUser);
         $arUser = $rsUser->Fetch();
         $arElements = unserialize($arUser['UF_FAVORITES']);
         if ($act == 'del') {
            unset($arElements[$ID]);
         }
         else {
            $arElements[$ID] = $ID;
         }
         $USER->Update($idUser, Array("UF_FAVORITES"=>serialize($arElements)));
      }
    $array = array("res" => serialize($arElements));
    echo json_encode($array);
    exit;
   }
?>
```

Перед подключением компонента (я использовал catalog.section, но можно и news.list — смотря нужны цены или нет):

```php
<?
if(!$USER->IsAuthorized()){
    $arElements = unserialize($APPLICATION->get_cookie('bo_favorites'));
}
else{
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arElements = unserialize($arUser['UF_FAVORITES']);
}
if (!empty($arElements)) {
    global $arrFilterFav;
    $arrFilterFav = array(
        "ID" => $arElements
    );
//компонент с настройкой "FILTER_NAME" => "arrFilterFav", и выключенным кешированием
}
?>
```

Готово! 

У пользователя добавляем строковое свойство UF_FAVORITES, там будет храниться сериализованный массив товаров, добавленных в Избранное. У неавторизованных пользователей данные будут храниться в куках/cookie.
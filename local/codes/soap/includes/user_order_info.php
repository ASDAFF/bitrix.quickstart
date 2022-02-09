<?
global $USER;
if ($USER->IsAuthorized()){
// Выведем даты всех заказов текущего пользователя за текущий месяц, отсортированные по дате заказа
$arFilter = Array(
   "USER_ID" => $USER->GetID()
   );

$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
while ($ar_sales = $db_sales->Fetch())
{
$dbBasketItems = CSaleBasket::GetList(
   array(
      "NAME" => "ASC",
      "ID" => "ASC"
   ),
   array(
      "FUSER_ID" => $ar_sales['USER_ID'],
      "LID" => SITE_ID,
      "ORDER_ID" => $ar_sales['ID']
   ),
        array("QUANTITY")
);
while ($arItems = $dbBasketItems->Fetch())
{
   $kol = $kol + $arItems['QUANTITY']*$arItems['CNT'];            
}
$sum_price += $ar_sales[PRICE];
}?>
<?if($kol>0){?>
<div class="b-sidebar__text">Вы заказали:<br><b><?=$kol?> товара на сумму <?=CurrencyFormat($sum_price, "RUB");?></b></div>
<?}?>
<?
$arFilter = array("ID" => $USER->GetID());
$arParams["SELECT"] = array("UF_TYPE");
$arRes = CUser::GetList($by,$desc,$arFilter,$arParams);
while ($ar_user = $arRes->Fetch()){
$arfield = CUserFieldEnum::GetList(array(), array("ID" => $ar_user['UF_TYPE']));
      if($UserFieldAr = $arfield->GetNext())
      {
$us_type = $UserFieldAr['VALUE'];
}
?>
<div class="b-sidebar__text">Информация о покупателе:<br><b><?=$us_type?><br><?=$ar_user['EMAIL']?><br><?=$ar_user['LAST_NAME']?><br><?=$ar_user['PERSONAL_PHONE']?></b></div>
<?
}
}else{
?>

					<div class="b-sidebar__text"><b>Зачем регистрироваться?</b></div>
					<div class="b-sidebar__text">Сохранять вишлисты, списки просмотренных товаров и списки сравнения и историю покупок</div>
					<div class="b-sidebar__text">Быстрее оформлять покупку товара</div>
<?}?>
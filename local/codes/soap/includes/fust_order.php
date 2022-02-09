<?
if($_POST["phone"]) {
if(filter_var($_POST["phone"], FILTER_SANITIZE_NUMBER_INT)){
    $PHONE = $_POST["phone"];
	$ORDER = intval($_POST["el"]);
	
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	
    /* получим значение пользователя */
    /*if ($USER->IsAuthorized()){
        global $USER;
       $USER = $USER->GetID() ;
    }
    else {
       $USER = NULL ;
    }*/
    if(!CModule::IncludeModule("iblock"))

		return; 

		$res = CIBlockElement::GetByID($ORDER);
		if($ar_res = $res->GetNext())
			$ORDER = $ar_res['NAME'];
			
    /* создадим массив на отправку быстрого заказа */
    $arFields = Array(
        //"USER" => $USER,
        "PHONE" => $PHONE,
		"ORDER" => $ORDER,
		"SALE_MAIL" => COption::GetOptionString("sale", "order_email"),
    );
	$fustorder = CEvent::Send("FUST_ORDER", "s1", $arFields, "Y", 67);

    if($fustorder) {
      $popuptitle = '<span style="color: green">Удачно</span>';
      $popuptext =  'Вам перезвонят на номер'.$PHONE;
    }
    else {
      $popuptitle = '<span style="color: red">Ошибка</span>';
      $popuptext =  'Неудалось оформить быстрый заказ. Проверьте ваш номер:'.$PHONE;
    }
    /* если ajax не подключен */
    if ($_POST["action"] != "ajax") {
       header('Location: '.$_SERVER['HTTP_REFERER']);
    }
}else{
    $PHONE = $_POST["phone"];
      $popuptitle = '<span style="color: red">Ошибка</span>';
      $popuptext =  'Неудалось оформить быстрый заказ. Проверьте ваш номер:'.$PHONE;
    /* если ajax не подключен */
    if ($_POST["action"] != "ajax") {
       header('Location: '.$_SERVER['HTTP_REFERER']);
    }	
}
}
?>
<? if($popuptitle || $popuptext):?>
<script type="text/javascript" >
$.gritter.add({
    title: '<? echo $popuptitle;?> ',
    text: '<? echo $popuptext;?> ',
    sticky: false,
    time: 2500
});
</script>
<? endif ;?>
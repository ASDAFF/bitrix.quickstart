<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("subscribe");
// если есть post запрос с почтой то исполняем код
if($_POST["email"]){
if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $EMAIL = $_POST["email"];
    /* получим значение пользователя */
    if ($USER->IsAuthorized()){
        global $USER;
       $USER = $USER->GetID() ;
    }
    else {
       $USER = NULL ;
    }
    /* определим рубрики активные рубрики подписок */
    $RUB_ID = array(1);// Новости
    
    /* создадим массив на подписку */
    $subscr = new CSubscription;
    $arFields = Array(
        "USER_ID" => $USER,
        "FORMAT" => "html/text",
        "EMAIL" => $EMAIL,
        "ACTIVE" => "Y",
        "RUB_ID" => $RUB_ID,
        "SEND_CONFIRM" => "N",
        "CONFIRMED" => "Y"
    );
    $idsubrscr = $subscr->Add($arFields);

    if($idsubrscr) {
      $popuptitle = '<span style="color: green">Удачно</span>';
      $popuptext =  $EMAIL .' подписан на рассылку';
    }
    else {
      $popuptitle = '<span style="color: red">Ошибка</span>';
      $popuptext =   $EMAIL .' уже был подписан на рассылку';
    }
    /* если ajax не подключен */
    if ($_POST["action"] != "ajax") {
       header('Location: '.$_SERVER['HTTP_REFERER']);
    }
}else{
   	$EMAIL = $_POST["email"];
	  $popuptitle = '<span style="color: red">Ошибка</span>';
      $popuptext =  'В '.$EMAIL .' допущена ошибка';
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

			<div class="b-footer-subcribe">
				<div class="b-footer__title">Подписаться на рассылку</div>
				<form action="/includes/subscribe.php" name="subscribe" method="post">
					<div class="b-footer-form">
						<input type="text" class="b-footer-form__text" placeholder="E-mail" name="email"/>
						<input type="submit" class="b-footer-form__submit" value="" id="mailing-submit"/>
					</div>
				</form>
			</div>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if($arResult["FORM_TYPE"] == "login"):?>
<a href="#b-login" class="b-header-user__link m-login__link" >Войти</a>
<?
if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']){
      $popuptitle = '<span style="color: red">Ошибка авторизации</span>';
      $popuptext =  'Неверный логин или пароль.';
?>
<script type="text/javascript">
$(document).ready(
            function(){
$.gritter.add({
    title: '<? echo $popuptitle;?>',
    text: '<?echo $popuptext;?>',
    sticky: false,
    time: 4500
});
});
</script>
<?}?>	

	<div class="b-popup" id="b-login">
	<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?foreach ($arResult["POST"] as $key => $value):?>
	<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="AUTH" />
		<div class="b-popup__wrapper">
			<h2 class="b-login__h2">Войти в магазин</h2>
			<div class="b-login__user"><input type="text" name="USER_LOGIN" class="b-cart-field__input" placeholder="<?=GetMessage("AUTH_LOGIN")?>" value="<?=$arResult["USER_LOGIN"]?>"/></div>
			<div class="b-login__pass"><input type="password" class="b-cart-field__input" name="USER_PASSWORD" placeholder="<?=GetMessage("AUTH_PASSWORD")?>" /></div>
			<div class="b-login__btn clearfix">
<?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>

				<a href="<?=$arResult["AUTH_REGISTER_URL"]?>" class="b-login__btn-auth"><?=GetMessage("AUTH_REGISTER")?></a>
<?endif;?>
				<button class="b-login__button m-login__btn-login">Войти</button>
			</div>
			<div class="b-login__text">Регистрация позволит Вам:</div>
			<ul class="b-list-dot m-login__list">
				<li class="b-list-dot__item">Сохранять вишлисты, списки просмотренных покупок</li>
				<li class="b-list-dot__item">Быстрее оформлять покупку товара, используя сохраненный ранее адрес доставки</li>
			</ul>
		</div>
</form>
	</div>

<?if($arResult["AUTH_SERVICES"]):?>
<?
$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "", 
	array(
		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
		"AUTH_URL"=>$arResult["AUTH_URL"],
		"POST"=>$arResult["POST"],
		"POPUP"=>"Y",
		"SUFFIX"=>"form",
	), 
	$component, 
	array("HIDE_ICONS"=>"Y")
);
?>
<?endif?>

<?
//if($arResult["FORM_TYPE"] == "login")
else:
?>

<?if(substr($arResult["USER_NAME"],0,7)=="820eb5b"){
	$arResult["USER_NAME"] = "Продолжите регистрацию";
}?>
<a href="<?=($arResult["USER_NAME"]=="Продолжите регистрацию"?"/user/profile/":"#b-user")?>" class="b-header-user__link m-user__auth"><?=$arResult["USER_NAME"]?></a>
	<div class="b-popup" id="b-user">
		<div class="b-popup__wrapper">
			<div class="b-user__link"><a href="<?=$arResult["ORDER_URL"]?>">Мои заказы</a></div>
			<div class="b-user__link"><a href="<?=$arResult["ADRESS_URL"]?>">Адреса доставки</a></div>
			<div class="b-user__address"><a href="?logout=yes">Выход</a></div>
		</div>
	</div>
<?endif?>
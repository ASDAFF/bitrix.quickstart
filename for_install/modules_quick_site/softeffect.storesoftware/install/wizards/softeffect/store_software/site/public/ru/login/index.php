<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход на сайт");
?> 
<div class="autorization"> 	
  <h1><?=$APPLICATION->ShowTitle()?></h1>
 
<div class="a_right"> 			
  
   <?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "template1", array(
	"REGISTER_URL" => "#SITE_DIR#login/register.php",
	"FORGOT_PASSWORD_URL" => "#SITE_DIR#login/forgotpass.php",
	"PROFILE_URL" => "",
	"SHOW_ERRORS" => "Y"
	),
	false
);?> 		</div>


   	



<div id="option2" class="loginoption">
<h2>
Новый пользователь?
</h2>


<div class="logincontent clearfix">
<div class="loginbuttons">
<a rel="noindex, nofollow" title="Регистрация" href="#SITE_DIR#login/register.php">
<img width="144" height="25" alt="Register" src="#SITE_DIR#images/buttons/login_register.gif">
</a>
</div>
<h3>Предлагаем вам пройти процедуру регистрации. Это позволит вам:</h3>
<ul>
<li>Получить персональную скидку</li>
<li>Следить за состоянием заказов</li>
<li>Сохранять персональные настройки</li>
</ul>
<ul>
<li>И много чего еще</li>
<li>Накапливать бонусы</li>
<li>Регистрация - БЕСПЛАТНА!</li>
</ul>
</div>
</div>

</div>

 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
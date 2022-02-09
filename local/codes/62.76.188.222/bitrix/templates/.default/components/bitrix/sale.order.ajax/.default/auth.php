<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<section class="b-detail">
    <div class="b-detail-content">
        <div class="clearfix">
            <div class="b-login_or_reg__login">
                <h3 class="b-h3">Для вернувшихся покупателей</h3>
                <p>Если вы помните свой логин и пароль, то введите их в соответствующие поля:</p>
               
                
	<form method="post" action="" name="order_auth_form">
				<?=bitrix_sessid_post()?>
				<?
				foreach ($arResult["POST"] as $key => $value)
				{
				?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
				<?
				}
				?>
			 
                                
                                
     <table class="b-subcribe__table" style="width: 80%;">
            <tbody><tr>
                    <td>Логин:</td>
                </tr>
                <tr>
                    <td><input name="USER_LOGIN" type="text" class="b-text" value="<?=$arResult["AUTH"]["USER_LOGIN"]?>"></td>
                </tr>
                <tr>
                    <td>Пароль:</td>
                </tr>
                <tr>
                    <td><input type="password" name="USER_PASSWORD" class="b-text"></td>
                </tr>
            </tbody></table> 
                <p><a href="/personal/?forgot_password=yes">Забыли свой пароль?</a></p>
                <button class="b-button">Продолжить оформление заказа</button>
        <input type="hidden" name="do_authorize" value="Y">
        
        </form>
            </div>
            <div class="b-login_or_reg__reg">
                <h3 class="b-h3">Для новых покупателей</h3>
                 
    <?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
            <form method="post" action="" name="order_reg_form">
                    <?=bitrix_sessid_post()?>
                    <?
                    foreach ($arResult["POST"] as $key => $value)
                    {
                    ?>
                    <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
                    <?
                    }
                    ?>                    
                                    
                <table class="b-subcribe__table" style="width: 80%;">
                    <tbody><tr>
                            <td>Имя:</td>
                        </tr>
                        <tr>
                            <td><input  name="NEW_NAME" type="text" class="b-text"  value="<?=$arResult["AUTH"]["NEW_NAME"]?>"></td>
                        </tr>
                        <tr>
                            <td>Фамилия:</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="NEW_LAST_NAME" value="<?=$arResult["AUTH"]["NEW_LAST_NAME"]?>" class="b-text"></td>
                        </tr>
                        <tr>
                            <td>E-Mail:</td>
                        </tr>
                        <tr>
                            <td><input type="text"  name="NEW_EMAIL" value="<?=$arResult["AUTH"]["NEW_EMAIL"]?>" class="b-text"></td>
                        </tr>
                    </tbody></table>
                <div class="b-select-login"><div><label class="b-radio m-radio_gp_1 ">
                            <input  type="radio" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N" value="1">Задать логин и пароль</label>
                    </div></div>
                <div class="b-select-login__table" style="display:none;">
                    <table class="b-subcribe__table" style="width: 80%;">
                        <tbody><tr>
                                <td>Логин:</td>
                            </tr>
                            <tr>
                                <td><input type="text"  name="NEW_LOGIN"  value="<?=$arResult["AUTH"]["NEW_LOGIN"]?>" class="b-text"></td>
                            </tr>
                            <tr>
                                <td>Пароль:</td>
                            </tr>
                            <tr>
                                <td><input  type="password" name="NEW_PASSWORD"  class="b-text"></td>
                            </tr>
                            <tr>
                                <td>Повтор пароля:</td>
                            </tr>
                            <tr>
                                <td><input  type="password" name="NEW_PASSWORD_CONFIRM"  class="b-text"></td>
                            </tr>
                        </tbody></table>
                </div>
                <div class="b-select-login"><div><label class="b-radio m-radio_gp_1 b-checked">
                            <input type="radio"  checked=""  id="NEW_GENERATE_Y" name="NEW_GENERATE" value="Y"   value="2">
                            Логин и пароль сгенерировать автоматически</label></div></div>
                <button class="b-button">Продолжить оформление заказа</button>
          
                <input type="hidden" name="do_register" value="Y">
                
				</form>
			<?endif;?>    
            </div>
        </div>
        <p>Символом "звездочка" (*) отмечены обязательные для заполнения поля.</p>
        <p>После регистрации вы получите информационное письмо.</p>
        <p>Личные сведения, полученные в распоряжение интернет-магазина при регистрации или каким-либо иным образом, не будут без разрешения пользователей передаваться третьим организациям и лицам за исключением ситуаций, когда этого требует закон или судебное решение.</p>
                                                     
    </div>
</section>
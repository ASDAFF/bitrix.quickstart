<form name="CALLBACK_MODAL" action="#SITE_DIR#include/" method="POST" class="login-form">	
	<div id="results-callback-modal">
		<!--
		<div class="alert alert-danger" id="beforesend-callback-modal">
			Пожалуйста заполните все поля.
		</div>
		-->
		<div class="alert alert-danger" id="error-callback-modal">
			Ошибка отправки формы.
		</div>		
		<div class="alert alert-success" id="success-callback-modal">
			Спасибо, ждите звонка.
		</div>
	</div>	
	<div class="clearfix"><img src="#SITE_DIR#images/loading.gif" alt="Loading" id="form-loading-callback-modal" class="pull-right" /></div>	
	<input type="hidden" name="CALLBACK_MODAL[SITE_ID]" value="<?=SITE_ID?>"/>
	<input type="hidden" name="CALLBACK_MODAL[TITLE]" value="Обратный звонок"/>
	<div class="form-group has-feedback">
		<label class="control-label">Ваше имя</label>
		<input type="text" name="CALLBACK_MODAL[NAME]" placeholder="Имя" class="form-control req">
		<i class="fa fa-user form-control-feedback"></i>
	</div>
	<div class="form-group has-feedback">
		<label class="control-label">Телефон</label>
		<input type="tel" name="CALLBACK_MODAL[PHONE]" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" placeholder="+7 (000) 000 00 00" class="form-control req">
		<i class="fa fa-phone form-control-feedback"></i>
	</div>
	<button type="submit" class="btn btn-group btn-default btn-sm pull-right">Заказать звонок</button>
</form>
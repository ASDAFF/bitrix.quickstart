<div id="results-callback">
	<div class="alert alert-danger" id="beforesend-callback">
		Пожалуйста заполните обязательные поля.
	</div>
	<div class="alert alert-danger" id="error-callback">
		Ошибка отправки сообщения.
	</div> 
	<div class="alert alert-success" id="success-callback">
		Спасибо, ждите звонка.
	</div>
</div>
<div class="contact-form mb-35">
	<img src="#SITE_DIR#images/loading.gif" alt="Loading" id="form-loading-callback" class="pull-right" />
	<div class="clearfix"></div>
	<form name="CALLBACK" action="#SITE_DIR#include/" method="POST" role="form">
		<input type="hidden" name="CALLBACK[SITE_ID]" value="<?=SITE_ID?>"/>
		<div class="form-group has-feedback">
			<label for="name">Имя*</label>
			<input type="text" name="CALLBACK[NAME]" class="form-control req">
			<i class="fa fa-user form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="phone">Телефон*</label>
			<input type="tel" name="CALLBACK[PHONE]" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="form-control req">
			<i class="fa fa-phone form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="subject">Тема*</label>
			<input type="text" name="CALLBACK[TITLE]" class="form-control req">
			<i class="fa fa-navicon form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="message">Комментарий</label>
			<textarea name="CALLBACK[COMMENT]" class="form-control" rows="6"></textarea>
			<i class="fa fa-pencil form-control-feedback"></i>
		</div>
		<input type="submit" value="Отправить" class="submit-button btn btn-default pull-right">
	</form>
</div>
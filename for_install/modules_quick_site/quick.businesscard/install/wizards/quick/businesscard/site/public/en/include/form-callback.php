<div id="results-callback">
	<div class="alert alert-danger" id="beforesend-callback">
		Please complete the required fields.
	</div>
	<div class="alert alert-danger" id="error-callback">
		Error sending message.
	</div> 
	<div class="alert alert-success" id="success-callback">
		Thank you, wait for the call.
	</div>
</div>
<div class="contact-form mb-35">
	<img src="#SITE_DIR#images/loading.gif" alt="Loading" id="form-loading-callback" class="pull-right" />
	<div class="clearfix"></div>
	<form name="CALLBACK" action="#SITE_DIR#include/" method="POST" role="form">
		<input type="hidden" name="CALLBACK[SITE_ID]" value="<?=SITE_ID?>"/>
		<div class="form-group has-feedback">
			<label for="name">Name*</label>
			<input type="text" name="CALLBACK[NAME]" class="form-control req">
			<i class="fa fa-user form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="phone">Phone*</label>
			<input type="tel" name="CALLBACK[PHONE]" pattern="(([ ]*[\+]?[ ]*\d{1,5})[ ]*[\-]?[ ]*)?(\(?\d{1,5}\)?[ ]*[\-]?[ ]*)?[\d\- ]{5,13}" class="form-control req">
			<i class="fa fa-phone form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="subject">Theme*</label>
			<input type="text" name="CALLBACK[TITLE]" class="form-control req">
			<i class="fa fa-navicon form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="message">Comment</label>
			<textarea name="CALLBACK[COMMENT]" class="form-control" rows="6"></textarea>
			<i class="fa fa-pencil form-control-feedback"></i>
		</div>
		<input type="submit" value="Send" class="submit-button btn btn-default pull-right">
	</form>
</div>
<div id="results-contacts">
	<div class="alert alert-danger" id="beforesend-contacts">
		Please complete the required fields.
	</div>
	<div class="alert alert-danger" id="error-contacts">
		Error sending message.
	</div> 
	<div class="alert alert-success" id="success-contacts">
		Thank you, your message has been sent to the site administration.
	</div>
</div>
<img src="#SITE_DIR#images/loading.gif" alt="Loading" id="form-loading-contacts" class="pull-right" />
<div class="clearfix"></div>
<div class="contact-form mb-35">
	<form name="CONTACTS" action="#SITE_DIR#include/" method="POST" role="form">
		<input type="hidden" name="CONTACTS[SITE_ID]" value="<?=SITE_ID?>"/>
		<div class="form-group has-feedback">
			<label for="name">Name*</label>
			<input type="text" name="CONTACTS[NAME]" class="form-control req">
			<i class="fa fa-user form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="email">Email*</label>
			<input type="email"  name="CONTACTS[EMAIL]" class="form-control req">
			<i class="fa fa-envelope form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="subject">Theme*</label>
			<input type="text" name="CONTACTS[TITLE]" class="form-control req">
			<i class="fa fa-navicon form-control-feedback"></i>
		</div>
		<div class="form-group has-feedback">
			<label for="message">Message*</label>
			<textarea name="CONTACTS[COMMENT]" class="form-control req" rows="6"></textarea>
			<i class="fa fa-pencil form-control-feedback"></i>
		</div>
		<input type="submit" value="Send" class="submit-button btn btn-default pull-right">
		<div class="clearfix"></div>
	</form>
</div>
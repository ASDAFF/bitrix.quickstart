<script>
if (!window.SubmitBlissRegForm)
{
	SubmitBlissRegForm = function()
	{
		var phone = document.getElementById('bliss_user_phone').value;
		var name = document.getElementById('bliss_user_name').value;
		var company = document.getElementById('bliss_user_company').value;
		var email = document.getElementById('bliss_user_email').value;
		var path = document.location.href;
		document.location.href = path+"&bliss_user_phone="+phone+"&bliss_user_name="+name+"&bliss_user_company="+company+"&bliss_user_email="+email+"&SMSBLISS_REGISTER=Y&tabControl_active_tab=edit2";
	}
}
</script>
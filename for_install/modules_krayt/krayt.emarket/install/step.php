<?

if(!check_bitrix_sessid())
	return;
IncludeModuleLangFile(__FILE__);
$moduleId = 'krayt.emarket';
?>

<style>
    .wrp_step{
        background: #FFFFFF;
        padding: 20px;
    }
	.data-collection-wrapper
	{
		display:block;
	}

	.data-collection-left
	{
        width: calc(40% - 20px);
        float: left;
        padding-right: 20px;
	}
	.data-collection-right
	{
        width:60%;
        float:left;
	}
    .data-collection-right > h1{
        margin-top: 0px;
        padding-top: 0;

    }

	.data-collection-left__top-text
	{

		font-size:13px;
		color:#7f848f;
		margin-bottom:30px;
	}
	.data-collection-wrapper .wizard-input-title
	{
        display: block;
        margin-bottom: 5px;
		font-size:14px;
		color:#535b6b;
	}

	.data-collection-left__bottom-text
	{
        margin-bottom: 10px;
		font-size:11px;
		color:#7f848f;
	}

	.data-collection-wrapper .wizard-field
	{
		width:calc(100% - 30px);
	}


	.data-collection-left__bottom-text a
	{
		color:#7f848f;
		margin-top:20px;
		margin-bottom: 20px;
	}
	.wizard-field {
		background: #ffffff!important;
		border: 1px solid #c6c9ce!important;
		-webkit-box-shadow: inset 0 1px 3px rgba(0,0,1,.18)!important;
		box-shadow: inset 0 1px 3px rgba(0,0,1,.18)!important;
		border-radius: 3px!important;
		font-family: 'Open Sans', Helvetica, Arial, sans-serif!important;
		font-size: 16px!important;
		font-weight: bold!important;
		height: 36px!important;
		outline: none!important;
		padding: 0 15px!important;

	}
	.wizard-input-form-block
	{
		margin-bottom:30px;
	}
	.error-message
	{
        color: red;
        margin-top: 5px;
        display: block;
	}
    input[type="checkbox"].ios8-switch {
        position: absolute;
        margin: 8px 0 0 16px;
    }
    input[type="checkbox"].ios8-switch + label {
        position: relative;
        padding: 5px 0 0 50px;
        line-height: 2.0em;
    }
    input[type="checkbox"].ios8-switch + label:before {
        content: "";
        position: absolute;
        display: block;
        left: 0;
        top: 0;
        width: 40px; /* x*5 */
        height: 24px; /* x*3 */
        border-radius: 16px; /* x*2 */
        background: #fff;
        border: 1px solid #b3b0b0;
        -webkit-transition: all 0.3s;
        transition: all 0.3s;
    }
    input[type="checkbox"].ios8-switch + label:after {
        content: "";
        position: absolute;
        display: block;
        left: 0px;
        top: 0px;
        width: 24px; /* x*3 */
        height: 24px; /* x*3 */
        border-radius: 16px; /* x*2 */
        background: #fff;
        border: 1px solid #b3b0b0;
        -webkit-transition: all 0.3s;
        transition: all 0.3s;
    }
    input[type="checkbox"].ios8-switch + label:hover:after {
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    input[type="checkbox"].ios8-switch:checked + label:after {
        margin-left: 16px;
    }
    input[type="checkbox"].ios8-switch:checked + label:before {
        background: #55D069;
    }
    .iframe-banner{
        width: 100%;
        height: 600px;
        border: none;
    }
</style>
<script>
	function showError(container, errorMessage) {
		var msgElem = document.createElement('span');
		msgElem.className = "error-message";
		msgElem.innerHTML = errorMessage;
		container.appendChild(msgElem);
	}

	function resetError(container) {
		if (container.lastChild.className == "error-message") {
			container.removeChild(container.lastChild);
		}
	}

	function validate(form) {
		var elems = form.elements;
		var error = false;
		resetError(elems.Name.parentNode);
		if (!elems.Name.value) {
			error = true;
			showError(elems.Name.parentNode, ' <?=GetMessage("krayt_error_name")?>');
		}
		resetError(elems.Email.parentNode);
		if (!elems.Email.value) {
			error = true;
			showError(elems.Email.parentNode, ' <?=GetMessage("krayt_error_email")?>');
		}
		if(elems.Email.value)
		{
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var email = elems.Email.value;
			if(reg.test(email) == false)
			{
				error = true;
				showError(elems.Email.parentNode, ' <?=GetMessage("krayt_error_email_wrong")?>');
			}
		}

		resetError(elems.Phone.parentNode);
		if (!elems.Phone.value) {
			error = true;
			showError(elems.Phone.parentNode, ' <?=GetMessage("krayt_error_phone")?>');
		}
		if(elems.Phone.value)
		{
			var reg = /^[0-9()+-\s]+$/;
			var phone = elems.Phone.value;
			if(reg.test(phone) == false)
			{
				error = true;
				showError(elems.Phone.parentNode, ' <?=GetMessage("krayt_error_phone_wrong")?>');
			}
		}
		if(error == false)
		{
			form.submit();
		}
	}
</script>
<div class="wrp_step">

    <form method="post" action="<?echo $APPLICATION->GetCurPage(); ?>">
        <?echo bitrix_sessid_post(); ?>
        <input type="hidden" name="lang" value="<?echo LANG ?>">
        <input type="hidden" name="step" value="1">
        <input type="hidden" name="id" value="<?=$moduleId?>">
        <input type="hidden" name="install" value="Y">
        <div class="data-collection-wrapper">
            <div class="data-collection-left">
                <div class="data-collection-left__top-text"><?=GetMessage("krayt_datacollection_top_text")?></div>
                <div class="wizard-input-form-block">
                    <label for="dataCollectionName" class="wizard-input-title"><?=GetMessage("krayt_datacollection_name")?></label>
                    <input type="text" name="Name" value="" class="wizard-field" id="dataCollectionName">
                </div>
                <div class="wizard-input-form-block">
                    <label for="dataCollectionPhone" class="wizard-input-title"><?=GetMessage("krayt_datacollection_phone")?></label>
                    <input type="tel" name="Phone" value="" class="wizard-field" id="dataCollectionPhone" pattern="^[0-9
				()+\s]+$">
                </div>
                <div class="wizard-input-form-block">
                    <label for="dataCollectionEmail" class="wizard-input-title"><?=GetMessage("krayt_datacollection_email")?></label>
                    <input type="email" name="Email" value="" class="wizard-field" id="dataCollectionEmail">
                </div>
                <div class="wizard-input-form-block">
                    <input name="IS_PARTNER" id="IS_PARTNER" class="ios8-switch" type="checkbox" value="Y">
                    <label for="IS_PARTNER"><?=GetMessage("krayt_IS_PARTNER")?></label>
                </div>
                <div class="data-collection-left__bottom-text"><?=GetMessage("krayt_datacollection_bottom_text")?></div>
            </div>
            <div class="data-collection-right">
                <h1><?echo GetMessage("krayt_title_more_product"); ?></h1>
                <iframe class="iframe-banner" src="https://krayt.ru/send_data_free/banners.php?module_id=<?=$moduleId?>" width="100%"></iframe>
            </div>
            <div style="clear: both"></div>
        </div>
        <input type="button" name="" onclick="validate(this.form)" value="<?echo GetMessage("krayt_APPLY"); ?>">
        <form>

</div>
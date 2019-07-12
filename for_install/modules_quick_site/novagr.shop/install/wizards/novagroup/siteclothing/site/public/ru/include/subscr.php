<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><div class="content-block content-block-subscribe">
<?php 
$currentUri = $APPLICATION->GetCurPage();
if (startsWith($currentUri, '#SITE_DIR#news/')) {
	$subsctTitle = "Новости";
	$RUB_ID = "#NEWS_RUBRIC_ID#";
} elseif (startsWith($currentUri, '#SITE_DIR#blogs/')) {
	$subsctTitle = "Блог";
	$RUB_ID = "#BLOGS_RUBRIC_ID#";
} 

?>	
	<h3>Подписка на <?=$subsctTitle?></h3>
	
	<form id="blog-subscr" action="#SITE_DIR#include/ajax/subsrc.php" method="post">
	<?php 
	echo bitrix_sessid_post();?>
	<input type="hidden" name="PostAction" value="Add" />
	<input type="hidden" name="ID" value="" />
	<input type="hidden" name="RUB_ID[]" value="<?=$RUB_ID?>" />
	
	<div id="subscribe"  class="form-box">
		<div class="form-textbox">
			<div class="form-textbox-border"><input type="text" id="email_subscr" value="введите ваш e-mail" onblur="if (this.value=='')this.value='введите ваш e-mail'" onclick="if (this.value=='введите ваш e-mail')this.value=''" name="sf_EMAIL" /></div>
		</div>
		<div class="form-button">
			<input type="submit" class="btn" value="Подписаться" />
		</div>
		
	</div>
	<div class="clear" ></div>
	</form>
	
</div>
<script>
function ValidateFormSub()
{

	var email = document.getElementById('email_subscr');
	
	var error_str = '';
	var error = Array();

	if (email.value == '') {
		error.push('Не заполнено поле Email');	
	} else if (!CheckEmail(email.value)){
		error.push('Поле Email заполнено неверно');
	}	
	
	var str_error = error.join("\n");	
	if (str_error != '') {
		alert(str_error);
		return false;	
	}

}

$(document).ready(function(){
//назначаем обработчик ajax формы 
	$('#blog-subscr').ajaxForm({ 
        
        dataType:  'json',
        beforeSubmit: ValidateFormSub,
        success: function(json) { 

        	/*if (json.result == "ERROR") {
				alert(json.message);
				document.location.href = json.redirect_uri;
					json.result == "OK" && 
			} else */
			if (json.redirect_uri) {
				
				document.location.href = json.redirect_uri;
			} 					
        }			
	});
});	
</script>
<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

//foreach($_POST as $key=>$val) $_POST[$key] = iconv("utf-8","windows-1251",$val);

if($_POST['name']=='') print 'error Не заполнено имя';
elseif($_POST['phone']=='') print 'error  Пожалуйста, укажите ваш телефон';
elseif(strlen($_POST['phone'])<7) print 'error  Телефон не может содержать менее 7 знаков';
elseif(strlen($_POST['phone'])>12) print 'error  Телефон не может содержать более 12 знаков';
elseif(preg_match("#[^0-9\-\s\+]#",$_POST['phone'])) print 'error  Телефон не может содержать букв';
else{
	CEvent::SendImmediate("FEEDBACK_FORM","s1",Array("AUTHOR"=>$_POST['name'],"TEXT"=>$_POST['phone'],"EMAIL_TO"=>COption::GetOptionString("main", "email_from"),));
	print 'success <h1>Сообщение отправлено</h1> Мы свяжемся с вами как только сможем.';
}
?>
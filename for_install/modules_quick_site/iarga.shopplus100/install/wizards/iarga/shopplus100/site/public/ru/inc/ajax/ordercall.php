<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

//foreach($_POST as $key=>$val) $_POST[$key] = iconv("utf-8","windows-1251",$val);

if($_POST['name']=='') print 'error �� ��������� ���';
elseif($_POST['phone']=='') print 'error  ����������, ������� ��� �������';
elseif(strlen($_POST['phone'])<7) print 'error  ������� �� ����� ��������� ����� 7 ������';
elseif(strlen($_POST['phone'])>12) print 'error  ������� �� ����� ��������� ����� 12 ������';
elseif(preg_match("#[^0-9\-\s\+]#",$_POST['phone'])) print 'error  ������� �� ����� ��������� ����';
else{
	CEvent::SendImmediate("FEEDBACK_FORM","s1",Array("AUTHOR"=>$_POST['name'],"TEXT"=>$_POST['phone'],"EMAIL_TO"=>COption::GetOptionString("main", "email_from"),));
	print 'success <h1>��������� ����������</h1> �� �������� � ���� ��� ������ ������.';
}
?>
<?

	$MESS['SIGN_UP_FORM'] = "
	
		<p style='width: 500px'>������ �� �������������� �����, ���� ��������� ������������ ������� JivoSite � ������ ����� ".COption::GetOptionString('main', 'server_name').". ���� ��� ����� ������ - ����������, �������� ��� �� info@jivosite.ru ��� <a href='http://jivosite.copiny.com/' target='_blank'>������� ������ �� ������</a></p>

		<form method='post'>

		<p><b>��� e-mail (�� �� �����)</b> 
		<input type='text' name='email' value='".CUser::GetEmail()."'/>
		<p class='comment'>������� ����� e-mail, ������� �� ������ ������������ ��� ����� � ������ ���������� JivoSite, � ��� �� ��� ����� � ���������� ������ � ��������� ����������� �� JivoSite. ���� � ��� ��� ���� ������� JivoSite - ������� ��� e-mail � ������, ������� �� ������������ ��� �����������</p>

		<p><b>������ � JivoSite</b>
		<input type='password' name='password'/>
		<p class='comment'>���������� ������ ��� ����������� � ������� JivoSite. � ����� ������������, ���� ������ �� ������ ��������� � ������� �� �������. ���� � ��� ��� ���� ������� JivoSite - ������� ������ �� ����</p>

		<p><b>���� ���</b> 
		<input type='text' name='userDisplayName' value='".CUser::GetFullName()."'/>
		<p class='comment'>���� ��� ��-������, ������� ����� ������������ ����������� ����� � ����</p>

		<input type='hidden' name='step' value='2'/>

		<p><input type='submit' value='���������� ������-����������� JivoSite!'>
		</form>
	
	";
	
	$MESS['BACK_TO_MODULE_LIST'] = "��������� � ������ �������";
?>

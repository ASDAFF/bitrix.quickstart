<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* ���������� ������� */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."users`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* ���� ����� ������� ������ ���������� ��������� - �������� ������� ��������� ��� */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* ��� ��� ������� ��������������� ��������� ��� � ������ ��������� � ���������� �����(���� �� ����������) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	global $USER;

	$query = "SELECT * FROM ".$arResult["prefix"]."users LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "DLE-AbC".rand(300, 1999);
		
		$arFields = Array(
		  "NAME"             => $arItem["fullname"],
		  "EMAIL"             => $arItem["email"],
		  "LOGIN"             => $arItem["name"],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["user_id"]
		);


		$ID = $user->Add($arFields);		
		

	}

	/* ����������� ����� � ������ ������� */
	$left += 10;
	$right += 10;

}

/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

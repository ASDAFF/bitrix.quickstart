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

	$query = "SELECT * FROM ".$arResult["prefix"]."users LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "DRUPAL-AbC".rand(300, 1999);

		$arFields = Array(
		  "NAME"             => $arItem["name"],
		  "EMAIL"             => $arItem["mail"],
		  "LOGIN"             => $arItem["name"],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["id"]
		);
		//if($arItem["uid"] == "Super Administrator") 
			//$arFields["GROUP_ID"] = array(1,2);

		$ID = $user->Add($arFields);
		

		$u = CUser::GetByID($ID);
		//$ar = $USER->SendPassword($USER->GetLogin(), $USER->GetParam("EMAIL"));

	}

	/* ����������� ����� � ������ ������� */
	$left += 10;
	$right += 10;

}

/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

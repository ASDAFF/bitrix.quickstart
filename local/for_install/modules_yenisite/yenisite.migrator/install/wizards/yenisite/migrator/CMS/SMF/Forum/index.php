<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* ���������� ������� */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."members`";	
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
	$query = "SELECT * FROM ".$arResult["prefix"]."members LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "SMF-AbC".rand(300, 1999);

		$arFields = Array(
		  "NAME"             => $arItem["realName"],
		  "EMAIL"             => $arItem["emailAddress"],
		  "LOGIN"             => $arItem["memberName"],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["ID_MEMBER"]
		);
		if($arItem["ID_GROUP"] == 1) 
			$arFields["GROUP_ID"] = array(1,2);
	
		$rsUser = CUser::GetByLogin($arItem["memberName"])->GetNext();
		if($rsUser)
			$users[$arItem["ID_MEMBER"]] = $rsUser["ID"];
		else
		{
			$ID = $user->Add($arFields);
			$u = CUser::GetByID($ID);
			$ar = $USER->SendPassword($USER->GetLogin(), $USER->GetParam("EMAIL"));
			$users[$arItem["ID_MEMBER"]] = $ID;
		}
	}

	/* ����������� ����� � ������ ������� */
	$left += 10;
	$right += 10;

}

/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* ���������� ������� */


$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}forum_category";	
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

	
	$query = "SELECT * FROM {$arResult["prefix"]}forum_category LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		continue;

			$arFields = array("SORT" => 500, "XML_ID"   => $arItem["sid"]);
			$arSysLangs = array("ru", "en", "de");
			for ($i = 0; $i<count($arSysLangs); $i++)
			{
			  $arFields["LANG"][] = array(
				"LID" => $arSysLangs[$i],
				"NAME" => $arItem["cat_name"]
						
				);
			}

			$ID = CForumGroup::Add($arFields);

	}

	/* ����������� ����� � ������ ������� */
	$left += 10;
	$right += 10;

}

/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

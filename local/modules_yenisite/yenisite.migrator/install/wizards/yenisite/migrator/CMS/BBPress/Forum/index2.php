<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."forums`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{

	$query = "SELECT * FROM ".$arResult["prefix"]."forums LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	$res = CForumGroup::GetList(array(), array("SORT" => 777))->GetNext();
	
	while($arItem = mysql_fetch_assoc($result))
	{
		if(CForumNew::GetList(array(), array("XML_ID" => $arItem["forum_id"]))->GetNext()) continue;
		$arFields = Array(
		   "XML_ID" => $arItem["forum_id"],
		   "ACTIVE" => "Y",
		   "NAME" => $arItem["forum_name"],
		   "DESCRIPTION" => $arItem["forum_desc"],
		   "FORUM_GROUP_ID" => $res["ID"],
		   "GROUP_ID" => array(1 => "Y"), 
		   "SITES" => array(
		       $wizard->GetVar("siteID") => "/url/"));
		$ID = CForumNew::Add($arFields);		
	}



	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

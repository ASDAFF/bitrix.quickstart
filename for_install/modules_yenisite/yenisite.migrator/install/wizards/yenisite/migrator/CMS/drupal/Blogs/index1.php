<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("blog");

$user = new CUser;

$users = array();

/* количество записей */



$query = "SELECT  COUNT(*) as CNT  FROM {$arResult["prefix"]}comments, {$arResult["prefix"]}node, {$arResult["prefix"]}node_type WHERE {$arResult["prefix"]}node.nid = {$arResult["prefix"]}comments.nid AND {$arResult["prefix"]}node_type.type = 'blog' AND {$arResult["prefix"]}node_type.type=node.type";	
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
	global $USER;
	
	$query = "SELECT {$arResult["prefix"]}users.name as USER_NAME,{$arResult["prefix"]}comments.nid, comments.cid, {$arResult["prefix"]}comments.pid, {$arResult["prefix"]}comments.comment, {$arResult["prefix"]}comments.subject, {$arResult["prefix"]}comments.timestamp  FROM {$arResult["prefix"]}users, {$arResult["prefix"]}comments, {$arResult["prefix"]}node, {$arResult["prefix"]}node_type WHERE {$arResult["prefix"]}users.uid={$arResult["prefix"]}comments.uid AND {$arResult["prefix"]}node.nid = {$arResult["prefix"]}comments.nid AND {$arResult["prefix"]}node_type.type = 'blog' AND {$arResult["prefix"]}node_type.type=node.type ORDER BY pid ASC LIMIT ".$left.", ".$right;
	
		//print_r($query);
		//die();
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		
		global $USER;
		$usr = $USER->GetByLogin($arItem["USER_NAME"])->GetNext();
		
		$arPost = CBlogPost::GetList(array(), array("CATEGORY_ID" => $arItem["nid"]))->GetNext();
		
		if($arItem["pid"] == 0)
		{
		
			$arFields = array(
				"TITLE" => $arItem['subject'],
				"POST_TEXT" => $arItem['comment'],
				"BLOG_ID" => $arPost["BLOG_ID"],
				"POST_ID" => $arPost["ID"],
				"PARENT_ID" => 0, 
				"AUTHOR_ID" => $usr["ID"],  
				"ICON_ID" => $arItem['cid'],
				"DATE_CREATE" => ConvertTimeStamp($arItem['timestamp'], "FULL"),
			);
			$CID = CBlogComment::Add($arFields);
	
		
		}
		else
		{
		
			$bc = CBlogComment::GetList(array(), array("ICON_ID" => $arItem['pid']))->GetNext();
			$arFields = array(
				"TITLE" =>  $arItem['subject'],
				"POST_TEXT" => $arItem['comment'],
				"BLOG_ID" => $arPost["BLOG_ID"],
				"POST_ID" => $arPost["ID"],
				"PARENT_ID" => $bc["ID"], 
				"AUTHOR_ID" => $usr["ID"],  
				"ICON_ID" => $arItem['cid'],
				"DATE_CREATE" => ConvertTimeStamp($arItem['timestamp'], "FULL"),
			);
			$CID = CBlogComment::Add($arFields);
		
		print_r($arFields);
		//die();

		
		}
		//global $APPLICATION;
		
	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

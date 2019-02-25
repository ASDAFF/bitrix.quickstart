<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */

$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}comments, {$arResult["prefix"]}node, {$arResult["prefix"]}node_type WHERE {$arResult["prefix"]}node.type={$arResult["prefix"]}node_type.type AND {$arResult["prefix"]}node.nid = {$arResult["prefix"]}comments.nid AND {$arResult["prefix"]}node_type.module='forum' ORDER BY {$arResult["prefix"]}comments.timestamp ASC";	
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
	
	$query = "SELECT  {$arResult["prefix"]}users.name as USER_NAME, {$arResult["prefix"]}term_node.tid, {$arResult["prefix"]}comments.nid, {$arResult["prefix"]}comments.comment, {$arResult["prefix"]}comments.timestamp FROM {$arResult["prefix"]}users,{$arResult["prefix"]}term_node, {$arResult["prefix"]}comments, {$arResult["prefix"]}node, {$arResult["prefix"]}node_type WHERE {$arResult["prefix"]}users.uid={$arResult["prefix"]}node.uid AND {$arResult["prefix"]}node.type={$arResult["prefix"]}node_type.type AND {$arResult["prefix"]}node.nid = {$arResult["prefix"]}comments.nid AND {$arResult["prefix"]}term_node.nid={$arResult["prefix"]}node.nid AND {$arResult["prefix"]}node_type.module='forum' ORDER BY {$arResult["prefix"]}comments.timestamp ASC LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	
	

	while($arItem = mysql_fetch_assoc($result))
	{		
			
			$db_res = CForumTopic::GetList(array(), array("ICON_ID"=>$arItem["tid"]))->GetNext();;

			$usr = $USER->GetByLogin($arItem["USER_NAME"])->GetNext();
				
			$arFields = Array(
				  "POST_MESSAGE" => $arItem["comment"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  //"POST_DATE" => date("Y-m-d h:i:s", $arItem["timestamp"]),
				  "AUTHOR_NAME" => $usr["NAME"],
				  "AUTHOR_ID" => $usr["NAME"]?$usr["NAME"]:1,
				  "FORUM_ID" => $db_res["FORUM_ID"],
				  "TOPIC_ID" => $db_res["ID"],				  
				  "NEW_TOPIC" => "N"
				);

				$ID = CForumMessage::Add($arFields);
			
/*	print_r($arItem);			
	echo "<br/>";
	print_r($arFields);
	die();
	*/
	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

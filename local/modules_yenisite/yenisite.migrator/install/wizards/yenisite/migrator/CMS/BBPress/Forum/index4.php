<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."posts`";	
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
	$query = "SELECT * FROM ".$arResult["prefix"]."posts  LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
			$u = CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arItem["poster_id"]))->GetNext(); 
			$f = CForumNew::Getlist(array(), array("XML_ID" => $arItem["forum_id"]))->GetNext();
			$ft = CForumTopic::GetList(array(), array('XML_ID' => $arItem['topic_id']))->GetNext();
			$arFields = Array(
				  "POST_MESSAGE" => $arItem["post_text"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  "AUTHOR_NAME" => $u["NAME"],
				  "AUTHOR_ID" => $u["ID"]?$u["ID"]:1,
				  "FORUM_ID" => $f["ID"],
				  "TOPIC_ID" => $ft['ID'],
				  "AUTHOR_IP" => ($arItem["poster_ip"]) ? $arItem["poster_ip"] : "<no address>",
				  "NEW_TOPIC" => "N",
				  "AUTHOR_REAL_IP" => $arItem['post_id'],
				);
				$ID = CForumMessage::Add($arFields);
				global $APPLICATION;
				$ex=$APPLICATION->GetException();
				echo $ex->GetString();

	}


	
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

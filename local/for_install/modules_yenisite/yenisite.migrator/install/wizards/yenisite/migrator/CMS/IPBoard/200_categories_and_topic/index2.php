<?
global $APPLICATION;
global $DB;
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
// echo $left.' | '.$count['CNT'];
if($left > $count["CNT"])
{	
	echo "{$left} из {$count['CNT']}";
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
            $u = CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arItem["author_id"]))->GetNext(); 
            $ft = CForumTopic::GetList(array(), array('XML_ID' => $arItem['topic_id']))->GetNext();
            $text = str_replace('&lt;br /&gt;', '', $arItem["post"]);
        //    $text = preg_replace('|\[quote(.*)\](.*)\[/quote\]|Uis', '', $text);
        //    $text = preg_replace('|\[img(.*)\](.*)\[/img\]|Uis', '', $text);
            $text = preg_replace('|<img(.*)/>|Uis', '', $text);
            $arFields = Array(
                    "AUTHOR_ID" => $u["ID"]?$u["ID"]:1,
                    "AUTHOR_NAME" => $arItem['author_name'],
                    "AUTHOR_IP" => ($arItem["ip_address"]) ? $arItem["ip_address"] : "<no address>",
                    "USE_SMILES" => "Y",
                    "POST_DATE" => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['post_date']),
                    "POST_MESSAGE" => $arItem["post"],
                    "FORUM_ID" => $ft["FORUM_ID"],
                    "TOPIC_ID" => $ft['ID'],
                    "APPROVED" => "Y",
                    "NEW_TOPIC" => "N",
                    "AUTHOR_REAL_IP" => $arItem['pid']
                );
/*             // echo '<br/>text:::::<br/>'.$text.'<br/>';
            // echo '<pre>' ; print_r($arFields); // echo '</pre>'; */
            $NewPostID = CForumMessage::Add($arFields);
/*             // echo 'newpostid = '.$NewPostID.'<br/>'; */
            if (IntVal($NewPostID)<=0)
                if($ex = $APPLICATION->GetException())
                {
                    $StrError = $ex->GetString();
                    // echo 'strerror = '.$StrError;
                } 
	}


	
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
  //  die('killed');
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("forum");


$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}comments";

//print_r($query);
//die();

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
	$query = "SELECT * FROM  {$arResult['prefix']}comments LIMIT ".$left.",  10";
	$result = mysql_query($query, $link);	
	while($arItem = mysql_fetch_assoc($result))
	{
	
		//print_r($arItem);
		//die();
		
		$el = CIBlockElement::GetList(array(), array('XML_ID' => $arItem['post_id']))->GetNextElement();
		$prop = $el->GetProperties();
		$filed = $el->GetFields();
		
		$usr = $USER->GetByLogin($arItem["autor"])->GetNext();
		
		$ft = CForumTopic::GetListEx(array(), array('XML_ID' => $arItem["post_id"]))->GetNext();
		
		$f = CForumNew::GetListEx(array(), array("SORT" => "123123"))->GetNext();	
		
		if(!$ft)
		{	
			$arFields = Array(
				"TITLE" => $filed['NAME'],
				"STATE" => "Y",
				"USER_START_NAME" => $usr["ID"],
				"START_DATE" => date("Y-m-d h:i:s"),
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" => $usr["ID"],
				"LAST_POSTER_NAME" => $usr["NAME"],
				"LAST_POST_DATE" => date("Y-m-d h:i:s"),
				"LAST_MESSAGE_ID" => 1,
				"XML_ID" => $arItem["post_id"]
			);

			$TID = CForumTopic::Add($arFields);
		}
		else		
			$TID = $ft['ID'];
			
			
		CIBlockElement::SetPropertyValueCode($filed['ID'], 'FORUM_TOPIC_ID', $TID);

		$arFields = Array(
				  "POST_MESSAGE" => $arItem["text"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  "AUTHOR_NAME" => $usr["NAME"],
				  "AUTHOR_ID" => $usr["ID"]?$usr["ID"]:1,
				  "FORUM_ID" => $f["ID"],
				  "TOPIC_ID" => $TID,				  
				  "NEW_TOPIC" => "N",				  
		);

		
		
		$ID = CForumMessage::Add($arFields);
		
	
	}

	
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");

if(CModule::IncludeModule("forum"))
{
	if(!$fg = CForumGroup::GetListEx(array(), array("SORT"=>"111222"))->GetNext())
	{
		$arFields = array("SORT" => 111222);
		$arSysLangs = array("ru", "en", "de");
		for ($i = 0; $i<count($arSysLangs); $i++)
		{
		  $arFields["LANG"][] = array(
			"LID" => $arSysLangs[$i],
			"NAME" => "SC_COMMENTS",	 		
			);
		}

		$ID = CForumGroup::Add($arFields);
		
	}
	else $ID = $fg['ID'];
	
	
	
	if(!$f = CForumNew::GetListEx(array(), array(" FORUM_GROUP_ID"=>$ID))->GetNext())
	{
	
			$arFields = Array(							   
			   "ACTIVE" => "Y",
			   "NAME" => "sc-comments",			  
			   "FORUM_GROUP_ID" => $ID,
			   "GROUP_ID" => array(1 => "Y"), 
			   "SORT" => "111222",
			   "SITES" => array(
				   $wizard->GetVar("siteID") => "/url/")
			);
			$ID = CForumNew::Add($arFields);	
		
	}
	
	
	

		
		
}


$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}SC_discussions";

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
	$query = "SELECT * FROM  {$arResult['prefix']}SC_discussions LIMIT ".$left.",  10";
	$result = mysql_query($query, $link);	
	while($arItem = mysql_fetch_assoc($result))
	{

		
		$el = CIBlockElement::GetList(array(), array('XML_ID' => $arItem['productID']))->GetNextElement();
		$prop = $el->GetProperties();
		$filed = $el->GetFields();
		
		$ft = CForumTopic::GetListEx(array(), array('XML_ID' => $arItem["productID"]))->GetNext();
		
		$f = CForumNew::GetListEx(array(), array("SORT" => "111222"))->GetNext();	
		
		if(!$ft)
		{	
			
			$arFields = Array(
				"TITLE" => $filed['NAME'],
				"STATE" => "Y",
				"USER_START_NAME" => $arItem["Author"],
				"START_DATE" => date("Y-m-d h:i:s"),//date("Y-m-d h:i:s", $arItem["timestamp"]),
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" => 1,
				"LAST_POSTER_NAME" => $arItem["Author"],
				"LAST_POST_DATE" => date("Y-m-d h:i:s"),
				"LAST_MESSAGE_ID" => 1,
				"XML_ID" => $arItem["productID"]
			);
			
            //print_r($arFields);
            //die();
			$TID = CForumTopic::Add($arFields);
		}
		else		
			$TID = $ft['ID'];
			
			
		CIBlockElement::SetPropertyValueCode($filed['ID'], 'FORUM_TOPIC_ID', $TID);

		$arFields = Array(
				  "POST_MESSAGE" => $arItem["Body"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  "AUTHOR_NAME" => $arItem["Author"],
				  "AUTHOR_ID" => 1,
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

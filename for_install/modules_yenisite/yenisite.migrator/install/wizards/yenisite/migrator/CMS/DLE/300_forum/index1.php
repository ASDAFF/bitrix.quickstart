<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */


$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}forum_forums";	
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
	
	
	
	$query = "SELECT * FROM {$arResult["prefix"]}forum_forums  LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	
	//print_r($query); die();

	while($arItem = mysql_fetch_assoc($result))
	{
	
			$query = "SELECT * FROM b_forum_group WHERE XML_ID='DLE'";
			global $DB;
			$fg = $DB->query($query)->GetNext();
			if(!$fg)
			{
				$arFields = array("SORT" => 500, "XML_ID"   => 'DLE');
				$arSysLangs = array("ru", "en", "de");
				for ($i = 0; $i<count($arSysLangs); $i++)
				{
			  	$arFields["LANG"][] = array(
					"LID" => $arSysLangs[$i],
					"NAME" => "DLE Forum Group"
							
					);
				}

				$fg['ID'] = CForumGroup::Add($arFields);
			}			
			
			
			$usr = $USER->GetByLogin($arItem["USER_NAME"])->GetNext();
			//$f = CForumNew::Getlist(array(), array("XML_ID" => $arItem["tid"]))->GetNext();
				
			$arFields = Array(
			   "NAME" => $arItem['name'], 
			   "DESCRIPTION" => $arItem['description'],
			   "FORUM_GROUP_ID" => $fg['ID'],
			   "GROUP_ID" => array(1 => "Y", 2 => "I"), 
			   "SITES" => array(), 
			   "ACTIVE" => "Y", 
			   "MODERATION" => "N",
			   "INDEXATION" => "Y",
			   "SORT" => 150,
			   "ASK_GUEST_EMAIL" => "N",
			   "USE_CAPTCHA" => "N",
			   "ALLOW_HTML" => "N",
			   "ALLOW_ANCHOR" => "Y",
			   "ALLOW_BIU" => "Y",
			   "ALLOW_IMG" => "Y",
			   "ALLOW_VIDEO" => "Y",
			   "ALLOW_LIST" => "Y",
			   "ALLOW_QUOTE" => "Y",
			   "ALLOW_CODE" => "Y",
			   "ALLOW_FONT" => "Y",
			   "ALLOW_SMILES" => "Y",
			   "ALLOW_UPLOAD" => "Y", 
			   "ALLOW_UPLOAD_EXT" => "",
			   "ALLOW_TOPIC_TITLED" => "N", 
			   "EVENT1" => "forum",
			   "XML_ID" => $arItem['id']
			   );
			   
			$db_res = CSite::GetList($lby="sort", $lorder="asc");
			while ($res = $db_res->Fetch()):
			   $arFields["SITES"][$res["LID"]] = "/".$res["LID"]."/forum/#FORUM_ID#/#TOPIC_ID#/";
			endwhile;
			$res = CForumNew::Add($arFields);

	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

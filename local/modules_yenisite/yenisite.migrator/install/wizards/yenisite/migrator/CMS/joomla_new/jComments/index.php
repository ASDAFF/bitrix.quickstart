<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("forum");

//SELECT wp_terms.name, wp_terms.slug, wp_terms.term_id, wp_term_taxonomy.parent FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.taxonomy='wpsc_product_category' AND wp_term_taxonomy.term_id=wp_terms.term_id ORDER BY term_id ASC
/* количество записей */

$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}jcomments";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);



/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");



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
				"NAME" => "JOS_COMMENTS",	 		
				);
			}
	
			$FGID = CForumGroup::Add($arFields);
			
		}
		else $FGID = $fg['ID'];
		
		
		
		if(!$f = CForumNew::GetListEx(array(), array("FORUM_GROUP_ID"=>$FGID))->GetNext())
		{
		
				$arFields = Array(							   
				   "ACTIVE" => "Y",
				   "NAME" => "jos-comments",			  
				   "FORUM_GROUP_ID" => $FGID,
				   "GROUP_ID" => array(1 => "Y", 2 => "M"), 
				   "SORT" => "111222",
				   "SITES" => array(
					   $wizard->GetVar("siteID") => "/url/")
				);
				$FID = CForumNew::Add($arFields);	
			
		}
		else $FID = $f['ID'];
		
		
	
			
			
	}
	

	



		
		

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

	$query = "SELECT * FROM {$arResult['prefix']}jcomments LIMIT {$left}, 10";
	//echo $query;
	
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
		
		$el = CIBlockElement::GetList(array(), array('XML_ID' => $arItem['object_id']))->GetNextElement();
		if(!$el) continue;
		$prop = $el->GetProperties();
		$filed = $el->GetFields();
		global $USER;
		$usr = $USER->GetByLogin($arItem["username"])->GetNext();
		
		$ft = CForumTopic::GetListEx(array(), array('XML_ID' => $arItem["object_id"]))->GetNext();
		
		$f = CForumNew::GetListEx(array(), array("SORT" => "1111222"))->GetNext();	
		
		if(!$ft)
		{	
			$arFields = Array(
				"TITLE" => $filed['NAME'],
				"STATE" => "Y",
				"USER_START_NAME" => $usr["ID"]?$usr["ID"]:1,
				"START_DATE" => date("Y-m-d h:i:s"),
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" => $usr["ID"]?$usr["ID"]:1,
				"LAST_POSTER_NAME" => $usr["NAME"]?$usr["NAME"]:'admin',
				"LAST_POST_DATE" => date("Y-m-d h:i:s"),
				"LAST_MESSAGE_ID" => 1,
				"XML_ID" => $arItem["object_id"]
			);

			$TID = CForumTopic::Add($arFields);
		}
		else		
			$TID = $ft['ID'];
			
			
		CIBlockElement::SetPropertyValueCode($filed['ID'], 'FORUM_TOPIC_ID', $TID);

		$arFields = Array(
				  "POST_MESSAGE" => $arItem["comment"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  "AUTHOR_NAME" => $arItem["name"],
				  "AUTHOR_ID" => $usr["ID"]?$usr["ID"]:"",
				  "FORUM_ID" => $f["ID"],
				  "TOPIC_ID" => $TID,				  
				  "NEW_TOPIC" => "N",				  
		);
		
		$ID = CForumMessage::Add($arFields);
		if ($ID<=0 && $ex=$APPLICATION->GetException())
			$this->content .= '<b style="color: red">'.$ex->GetString().' : '. $arItem["comment"].'</b><br/>';
		
		
		
	}
	
		
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

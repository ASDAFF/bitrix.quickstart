<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");


/*
SELECT node.nid as XML_ID, content_type_product.*, content_type_page.* FROM node 
LEFT JOIN content_type_product ON node.nid=content_type_product.nid 
LEFT JOIN content_type_page ON node.nid=content_type_page.nid
*/

$user = new CUser;

$JOIN = '';

$SELECT = 'SELECT node.nid as XML_ID';
$SELECT_CNT = 'SELECT COUNT(*) as CNT';

$query = "SHOW TABLES LIKE 'content_type_%'";
$res = mysql_query($query, $link);
if(!$res) exit("Произошла ошибка: ".mysql_error());
if(mysql_num_rows($res))
{
    while($result = mysql_fetch_array($res))
    {
	$JOIN .= " LEFT JOIN {$result[0]} ON {$arResult['prefix']}node.nid={$result[0]}.nid";
	$SELECT .= ", {$result[0]}.*";

    }

} 

//echo $SELECT." FROM node ".$JOIN;
//echo $SELECT_CNT." FROM node ".$JOIN;


//$count = mysql_fetch_assoc($count);


/* количество записей */
$query = $SELECT_CNT." FROM node ".$JOIN;	
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
	
	$query = $SELECT." FROM node ".$JOIN." LIMIT {$left},10";

	$res = mysql_query($query, $link);
	while( $arItem = mysql_fetch_assoc($res) )
	{
		$el = CIBlockElement::GetList(array(), array("XML_ID" => $arItem["XML_ID"]))->GetNext();


		foreach($arItem as $arField => $arValue)
		{
			if(substr_count($arField, "field_") && substr_count($arField, "_fid"))
			{

				$pn = str_replace('field_','',$arField);
				$pn = str_replace('_fid','',$pn);
				if($arValue)					
				{
					if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el["IBLOCK_ID"], "CODE" => $pn))->GetNext())
					{				
				
							echo 'create property'.$pn.'<br/>';
							$arFields = Array(
							  "NAME" => $pn,
							  "ACTIVE" => "Y",
							  "SORT" => "100",
							  "CODE" => $pn,
							  "PROPERTY_TYPE" => "F",
							  "IBLOCK_ID" => $el["IBLOCK_ID"]
							  );

							$ibp = new CIBlockProperty;
							$prop['ID'] = $ibp->Add($arFields);

					}
					




					$query = "SELECT filepath FROM files WHERE fid={$arValue}";	
					$img = mysql_query($query, $link);
					$img = mysql_fetch_assoc($img);

					$path = $arResult["site"].'/'.$img['filepath'];
					$path = str_replace('www.','',$path);
					if(!substr_count($path, 'http://')) $path = 'http://'.$path;

					$arFile = CFile::MakeFileArray($path);
					$obel = new CIBlockElement;
					if(!$el["DETAIL_PICTURE"]) $obel->Update($el["ID"], array("DETAIL_PICTURE" => $arFile) );
					if(!$el["PREVIEW_PICTURE"]) $obel->Update($el["ID"], array("PREVIEW_PICTURE" => $arFile) );


					CIBlockElement::SetPropertyValueCode($el['ID'], $pn, $arFile);
					

				}

				
			}


		}
	}
	

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

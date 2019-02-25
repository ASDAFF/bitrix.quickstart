<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");


/* количество записей */
$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}SC_news_table";

$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);


$arFields = Array(
	'ID'=>'webasyst',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'WebAsyst',
			)
		)
	);
	
$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);



$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");


$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "webasyst",
	"NAME" => "news",
	"CODE" => "shopscript_news",		
	"SORT" => "100",
);
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "shopscript_news"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);				





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
	$query = "SELECT * FROM {$arResult['prefix']}SC_news_table LIMIT {$left}, 10";
	$result = mysql_query($query, $link);
	
	
	while($arItem = mysql_fetch_assoc($result))
	{
	
	
	
	

	
	
	    $el = new CIBlockElement;		
	    $arLoadProductArray = Array(		  			
			    "IBLOCK_ID"      => $id,		  
			    "CODE" => $arItem['title_en'],
			    "NAME"           => $arItem['title_ru'],
			    "ACTIVE"         => "Y",            // активен		  
			    "DETAIL_TEXT"    => $arItem['textToPublication_ru'],		
			    "DETAIL_TEXT_TYPE" => 'html',
			    "PREVIEW_TEXT"    => $arItem['textToPublication_ru'],		
			    "PREVIEW_TEXT_TYPE" => 'html',
			    "XML_ID" => $arItem['NID'],
	    );
	

	
	    $e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['NID']))->GetNext();
	
	    if(!$e)
	    {		
		    $PRODUCT_ID = $el->Add($arLoadProductArray);
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

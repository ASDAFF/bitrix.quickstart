<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

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

//SELECT wp_terms.name, wp_terms.slug, wp_terms.term_id, wp_term_taxonomy.parent FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.taxonomy='wpsc_product_category' AND wp_term_taxonomy.term_id=wp_terms.term_id ORDER BY term_id ASC
/* количество записей */


/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");


$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "webasyst",
	"NAME" => "catalog",
	"CODE" => "shopscript_catalog",		
	"SORT" => "100",
);
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "shopscript_catalog"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);					





if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $id))->GetNext())
{
			CCatalog::Add(array( "IBLOCK_ID" => $id,  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" , "OFFERS_IBLOCK_ID" => $id_pr) );			
}		



if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => "KEYWORDS"))->Fetch())
$arFields = Array(
		    "NAME" => "KEYWORDS",
		    "ACTIVE" => "Y",
		    "SORT" => "100",
		    "CODE" => "KEYWORDS",
		    "PROPERTY_TYPE" => "S",
		    "IBLOCK_ID" => $id
		    
		    );
	
$ibp = new CIBlockProperty;
$PropID = $ibp->Add($arFields);



if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => "DESCRIPTION"))->Fetch())
$arFields = Array(
		    "NAME" => "DESCRIPTION",
		    "ACTIVE" => "Y",
		    "SORT" => "100",
		    "CODE" => "DESCRIPTION",
		    "PROPERTY_TYPE" => "S",
		    "IBLOCK_ID" => $id		    
		    );	
$ibp = new CIBlockProperty;
$PropID = $ibp->Add($arFields);


if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => "TITLE"))->Fetch())
$arFields = Array(
		    "NAME" => "TITLE",
		    "ACTIVE" => "Y",
		    "SORT" => "100",
		    "CODE" => "TITLE",
		    "PROPERTY_TYPE" => "S",
		    "IBLOCK_ID" => $id		    
		    );	
$ibp = new CIBlockProperty;
$PropID = $ibp->Add($arFields);



if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => "FORUM_TOPIC_ID"))->Fetch())
$arFields = Array(
		    "NAME" => "FORUM_TOPIC_ID",
		    "ACTIVE" => "Y",
		    "SORT" => "100",
		    "CODE" => "FORUM_TOPIC_ID",
		    "PROPERTY_TYPE" => "S",
		    "IBLOCK_ID" => $id		    
		    );	
$ibp = new CIBlockProperty;
$PropID = $ibp->Add($arFields);

if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => "PHOTOS"))->Fetch())
$arFields = Array(
		    "NAME" => "PHOTOS",
		    "ACTIVE" => "Y",
		    "SORT" => "100",
		    "CODE" => "PHOTOS",
		    "PROPERTY_TYPE" => "F",
		    "MULTIPLE" => "Y",
		    "IBLOCK_ID" => $id		    
		    );	
$ibp = new CIBlockProperty;
$PropID = $ibp->Add($arFields);


$query = "SELECT * FROM {$arResult["prefix"]}SC_product_options";	
$result = mysql_query($query, $link);
while($arItem = mysql_fetch_assoc($result))
{

    $prop = array();
    
    if(!$prop = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "NAME" => $arItem["name_ru"]))->GetNext())
    {
		
	    $arFields = Array(
		    "NAME" => $arItem["name_ru"],
		    "ACTIVE" => "Y",
		    "SORT" => "100",
		    "CODE" => $arItem["name_en"],
		    "PROPERTY_TYPE" => "L",
		    "IBLOCK_ID" => $id
		    
		    );
	
	    $ibp = new CIBlockProperty;
	    $PropID = $ibp->Add($arFields);
    }
    else {$PropID = $prop["ID"];}



    $ibpenum = new CIBlockPropertyEnum;
    $query1 = "SELECT * FROM {$arResult["prefix"]}SC_product_options_values WHERE optionID=".$arItem['optionID'];	
    $result1 = mysql_query($query1, $link);
    while($arItem1 = mysql_fetch_assoc($result1))
    {
        
        
        if(!$arItem1['variantID'])
        {
            $value = $arItem1['option_value_ru'];
            $xmlid = 0;
        }
        else{
            //               
                $query2 = "SELECT * FROM {$arResult["prefix"]}SC_products_opt_val_variants WHERE variantID=".$arItem1['variantID'];	
                $result2 = mysql_query($query2, $link);
                $arItem2 = mysql_fetch_assoc($result2);
                $value = $arItem2['option_value_ru'];
                $xmlid = $arItem2['variantID'];
                
                //print_r($arItem2);
                //die();
                
        }

        if(!CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("PROPERTY_ID"=>$PropID, "NAME"=>$value, "XML_ID" => $xmlid))->Fetch())
            $ibpenum->Add(Array('PROPERTY_ID'=>$PropID, 'VALUE'=>$value, "XML_ID" => $xmlid));
       
       $query2 = "SELECT * FROM {$arResult["prefix"]}SC_products_opt_val_variants WHERE optionID=".$arItem['optionID'];
       $result2 = mysql_query($query2, $link);
       while($arItem2 = mysql_fetch_assoc($result2))
       {
        $value = $arItem2['option_value_ru'];
        $xmlid = $arItem2['variantID'];
        if(!CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("PROPERTY_ID"=>$PropID, "NAME"=>$value, "XML_ID" => $xmlid))->Fetch())
            $ibpenum->Add(Array('PROPERTY_ID'=>$PropID, 'VALUE'=>$value, "XML_ID" => $xmlid));
        }
       
    }






}




	$left = 0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);


		



/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

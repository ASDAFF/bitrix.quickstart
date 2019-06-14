<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$res = CIBlock::GetList(array(), array("CODE" => "shopscript_catalog"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);	

/* количество записей */


$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}SC_products";

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


    if(!$price = CCatalogGroup::GetList(array("SORT" => "ASC"), array("NAME" => "Price") )->Fetch())
    {
    
        $arFields = array(
           "NAME" => "Price",
           "SORT" => 100,
           "USER_LANG" => array(
              "ru" => "Price",
              "en" => "Price"
              )
        );

        $price[ID] = CCatalogGroup::Add($arFields);
    
    }
    
    if(!$price_list = CCatalogGroup::GetList(array("SORT" => "ASC"), array("NAME" => "List Price") )->Fetch())
    {
    
        $arFields = array(
           "NAME" => "List Price",
           "SORT" => 100,
           "USER_LANG" => array(
              "ru" => "List Price",
              "en" => "List Price"
              )
        );

        $price[ID] = CCatalogGroup::Add($arFields);
    }   


    

	$query = "SELECT * FROM {$arResult['prefix']}SC_products LIMIT ".$left.",  10";
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
    	$sec = CIBlockSection::GetList(array(), array("XML_ID" => $arItem["categoryID"]))->Fetch();
	    $arLoadProductArray = Array(		  
			    "IBLOCK_ID"      => $id,		  
			    "NAME"           => $arItem['name_ru'],
			    "ACTIVE"         => "Y",	  
			    "DETAIL_TEXT"    => $arItem['description_ru'],
			    "DETAIL_TEXT_TYPE" => "html",
			    "PREVIEW_TEXT"    => $arItem['meta_description_ru'],
   			    "PREVIEW_TEXT_TYPE" => "html",
			    "XML_ID" => $arItem['productID'],
			    "IBLOCK_SECTION_ID" => $sec[ID],
			    "CODE" => $arItem["slug"],
			    "PROPERTY_VALUES" => array(
			        "KEYWORDS" => $arItem['meta_keywords_ru'],
   			        "DESCRIPTION" => $arItem['meta_description_ru'],
   			        "TITLE" => $arItem['meta_title_ru'],
			    )
	    );
	    
	    $arFile = array();
	    $query1 = "SELECT * FROM {$arResult['prefix']}SC_product_pictures WHERE productID=".$arItem['productID'];
    	$result1 = mysql_query($query1, $link);
	    while($arItem1 = mysql_fetch_assoc($result1))
	    {
	        $path = $arResult["site"].'attachments/SC/products_pictures/'.$arItem1['thumbnail'];	
			$path = str_replace('www.','',$path);
			if(!substr_count($path, 'http://')) $path = 'http://'.$path;						
		    	$arFile[] = array("VALUE" => CFile::MakeFileArray($path));	    
	    }
	    
	    $arLoadProductArray["PROPERTY_VALUES"]["PHOTOS"] = $arFile;
	    $arLoadProductArray["PREVIEW_PICTURE"] = $arFile[0]["VALUE"];
  	    $arLoadProductArray["DETAIL_PICTURE"] = $arFile[0]["VALUE"];
        
        
        
        $query3 = "SELECT * FROM {$arResult['prefix']}SC_product_options_values WHERE productID=".$arItem['productID'];

    	$result3 = mysql_query($query3, $link);
	    while($arItem3 = mysql_fetch_assoc($result3))
	    {
	        $value = "";
	    
	        if(!$arItem3['variantID'])
                {
                    $value = $arItem3['option_value_ru'];
                    $xmlid = 0;
                }
            else{        
                $query4 = "SELECT * FROM {$arResult["prefix"]}SC_products_opt_val_variants WHERE variantID=".$arItem3['variantID'];	
                $result4 = mysql_query($query4, $link);
                $arItem4 = mysql_fetch_assoc($result4);
                $value = $arItem4['option_value_ru'];
                $xmlid = $arItem4['variantID'];                
            }
	    

            if($value){            
	            $pro = CIBlockProperty::GetList(array(), array('XML_ID' => $arItem3['optionID']))->Fetch();	        
                $enum = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("VALUE"=>$value))->Fetch();
                $arLoadProductArray["PROPERTY_VALUES"][$enum["PROPERTY_ID"]] = array("VALUE" => $enum[ID]);
            }
            

	       
            
	    
        }
        

	
	
	    $el = new CIBlockElement;	
	    $e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['productID']))->GetNext();		
	    if(!$e)
		    $PRODUCT_ID = $el->Add($arLoadProductArray);
	    else
		     $PRODUCT_ID = $e['ID'];


        $arPrice = Array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "CATALOG_GROUP_ID" => $price[ID],
            "PRICE" =>  $arItem['Price'],
            "CURRENCY" => "RUB",
        );
        
        $res = CPrice::GetList( array(), array("PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => $price[ID]));
        if ($arr = $res->Fetch())    
            CPrice::Update($arr["ID"], $arPrice);        
        else
            CPrice::Add($arPrice);	


        $arPrice = Array(
            "PRODUCT_ID" => $PRODUCT_ID,
            "CATALOG_GROUP_ID" => $price_list[ID],
            "PRICE" =>  $arItem['list_price'],
            "CURRENCY" => "RUB",
        );
                    
        $res = CPrice::GetList( array(), array("PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => $price_list[ID]));
        if ($arr = $res->Fetch())    
            CPrice::Update($arr["ID"], $arPrice);        
        else
            CPrice::Add($arPrice);	
            
            
	}


	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>

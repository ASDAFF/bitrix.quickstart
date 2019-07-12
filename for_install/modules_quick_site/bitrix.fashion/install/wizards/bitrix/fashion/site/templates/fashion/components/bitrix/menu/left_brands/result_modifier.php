<?
CModule::IncludeModule("iblock");
foreach($arResult as &$arItem)
{
    $elems = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $arParams["MODELS_IBLOCK_ID"], "PROPERTY_fil_models_brand" => $arItem["PARAMS"]["BRAND_ID"]), false, false);
    $subsections = array();
    $sections = array();
    $sub_sections = array();
    while($elem = $elems->GetNext())
    {
        $subsections[$elem["IBLOCK_SECTION_ID"]]++;
    }
    if(!empty($subsections))
    {
        foreach($subsections as $key => $val)
        {
            $section = CIBlockSection::GetByID($key);
            $section = $section->GetNext();
            $active = false;
            if(strlen(strstr($APPLICATION->GetCurUri(), $arItem["PARAMS"]["BRAND_URL"].$section["CODE"]."/")) > 0)
                $active = true;
            $sub_sections[$section["IBLOCK_SECTION_ID"]][] = array("SECTION_PAGE_URL" => $arItem["PARAMS"]["BRAND_URL"].$section["CODE"]."/", "NAME" => $section["NAME"], "COUNT" => $subsections[$section["ID"]], "ACTIVE" => $active);
            $sections[$section["IBLOCK_SECTION_ID"]] += $subsections[$section["ID"]];
        }
    }
    if(!empty($sections))
    {
        foreach($sections as $key => $val)
        {
            $section = CIBlockSection::GetByID($key);
            $section = $section->GetNext();
            $active = false;
            foreach($sub_sections[$section["ID"]] as $sect)
            {
                if($sect["ACTIVE"])
                    $active = true;
            }
            if(strlen(strstr($APPLICATION->GetCurUri(), $arItem["PARAMS"]["BRAND_URL"].$section["CODE"]."/")) > 0)
                $active = true;
            $arItem["SUB_SECTIONS"][] = array("SECTION_PAGE_URL" => $arItem["PARAMS"]["BRAND_URL"].$section["CODE"]."/", "NAME" => $section["NAME"], "COUNT" => $sections[$section["ID"]], "SUB_SECTIONS" => $sub_sections[$section["ID"]], "ACTIVE" => $active);
        }
    }
}
?>
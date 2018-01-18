<?
class CAATIBlockPropSection
{
	public function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => "G",
			"USER_TYPE" => "AATSection",
			"DESCRIPTION" => "Привязка к разделам (checkbox/radio)",		        
			"GetPropertyFieldHtml" => array("CAATIBlockPropSection", "GetPropertyFieldHtml"),
			"GetPropertyFieldHtmlMulty" => array("CAATIBlockPropSection", "GetPropertyFieldHtml"),
		);
	}
	
	public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
	    
	    if (!$arProperty["LINK_IBLOCK_ID"])
	    {
    	    echo '<span class="errortext">Необходимо выбрать «Информационный блок» для привязки к разделам</span>';
    	    return false;
	    }
	    
	    $multiple = $arProperty['MULTIPLE'] == 'Y';
	    
	    $arSelected = array();
	    if ($multiple) 
	    {
    	    foreach ($value as $key => $id)
    	        $arSelected[$key] = $id['VALUE'];
	    }
	    else 
	    {
	        $arSelected[] = $value['VALUE'];
	    }

	    $rsSection = CIBlockSection::GetList(
            array('left_margin' => 'asc'),
            array('IBLOCK_ID' => $arProperty["LINK_IBLOCK_ID"]),
            false,
            array('ID', 'NAME', 'DEPTH_LEVEL', 'IBLOCK_SECTION_ID')
	    );
	    
	    while ($arSection = $rsSection->Fetch())
	    {
	        $arSections[$arSection['ID']] = array(
                "ID" => $arSection["ID"],
                "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                "VALUE" => $arSection["NAME"],
                "SELECTED" => in_array($arSection['ID'], $arSelected)
	        );
	    
	        if ($arSection['IBLOCK_SECTION_ID'])
	            $arSections[$arSection["IBLOCK_SECTION_ID"]]['IS_PARENT'] = true;
	    }
	    
	    if ($multiple)
	        $strReturn = '<ul class="aat-iblockprops-list aat-iblockprops-multy">';
	    else 
	        $strReturn = '<ul class="aat-iblockprops-list">';
	    
	    $previousLevel = 0;
	    
	    foreach ($arSections as $arSection)
	    {
	        if ($previousLevel && $arSection["DEPTH_LEVEL"] < $previousLevel)
	            $strReturn .= str_repeat("</ul></li>", ($previousLevel - $arSection["DEPTH_LEVEL"]));
	        
	        $strReturn .= '<li>';
	        
	        $type = $multiple ? 'checkbox' : 'radio';
	        $name = $multiple ? $strHTMLControlName['VALUE'] . '[]' : $strHTMLControlName['VALUE'];
	        
	        if ($arSection['SELECTED'])
	            $strReturn .= '<input type="'.$type.'" name="'.$name.'" value="'.$arSection['ID'].'" id="'.$strHTMLControlName['VALUE'].'_'.$arSection['ID'].'" checked />&nbsp;';
            else
                $strReturn .= '<input type="'.$type.'" name="'.$name.'" value="'.$arSection['ID'].'" id="'.$strHTMLControlName['VALUE'].'_'.$arSection['ID'].'" />&nbsp;';
            
            if ($arSection["IS_PARENT"])
                $strReturn .= '<label class="parent">'.$arSection['VALUE'].'</label>';
            else
                $strReturn .= '<label for="'.$strHTMLControlName['VALUE'].'_'.$arSection['ID'].'">'.$arSection['VALUE'].'</label>';
            
			if ($arSection["IS_PARENT"])
			    $strReturn .= '<ul>';
			else 
			    $strReturn .= '</li>';
			
		    $previousLevel = $arSection["DEPTH_LEVEL"];
		}
		
        if ($previousLevel > 1)
        	$strReturn .= str_repeat("</ul></li>", ($previousLevel - 1));

        $strReturn .= '</ul>';
        
        $strReturn .= '<div class="reset"><span>Снять выделение</span></div>';
        
        CJSCore::Init(array('aat_iblockprops'));
	    
        return $strReturn;		
	}
	
}
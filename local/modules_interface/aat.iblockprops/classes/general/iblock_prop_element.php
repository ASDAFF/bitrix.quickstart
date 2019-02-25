<?
class CAATIBlockPropElement
{
	public function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => "E",
			"USER_TYPE" => "AATElement",
			"DESCRIPTION" => "Привязка к элементам (checkbox/radio)",		        
			"GetPropertyFieldHtml" => array("CAATIBlockPropElement", "GetPropertyFieldHtml"),
			"GetPropertyFieldHtmlMulty" => array("CAATIBlockPropElement", "GetPropertyFieldHtml"),
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

	    $rsElement = CIBlockElement::GetList(
            array('sort' => 'asc'),
            array('IBLOCK_ID' => $arProperty["LINK_IBLOCK_ID"]),
            false,
            false,
            array('ID', 'NAME', 'IBLOCK_SECTION_ID')
	    );
	     
	    while ($arElement = $rsElement->Fetch())
	    {
	        $iblock_section_id = $arElement['IBLOCK_SECTION_ID'] ? $arElement['IBLOCK_SECTION_ID'] : 0;
	        $arElements[$iblock_section_id][$arElement['ID']] = array(
                'ID' => $arElement['ID'],
                'VALUE' => $arElement['NAME'],
                'SELECTED' => in_array($arElement['ID'], $arSelected)
	        );
	    }
	    
	    while ($arSection = $rsSection->Fetch())
	    {
	        $arSections[$arSection['ID']] = array(
                "ID" => $arSection["ID"],
                "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                "VALUE" => $arSection["NAME"]
	        );
	        
	        if ($arElements[$arSection['ID']])
	        {
	            $arSections[$arSection['ID']]['ELEMENTS'] = $arElements[$arSection['ID']];
	        }
	    
	        if ($arSection['IBLOCK_SECTION_ID'])
	            $arSections[$arSection["IBLOCK_SECTION_ID"]]['IS_PARENT'] = true;
	    }
	    
	    if ($multiple)
	        $strReturn = '<ul class="aat-iblockprops-list aat-iblockprops-multy">';
	    else 
	        $strReturn = '<ul class="aat-iblockprops-list">';

	    if ($arElements[0])
	    {
	        foreach ($arElements[0] as $arElement)
	        {
	            $type = $multiple ? 'checkbox' : 'radio';
	            $name = $multiple ? $strHTMLControlName['VALUE'] . '[]' : $strHTMLControlName['VALUE'];
				$id = $strHTMLControlName['VALUE'] . '_' . $arElement['ID'];
	    
	            $strReturn .= '<li><label>';
	    
	            if ($arElement['SELECTED'])
	                $strReturn .= '<input type="'.$type.'" name="'.$name.'" value="'.$arElement['ID'].'" id="'.$id.'" checked />&nbsp;'.$arElement['VALUE'];
	            else
	                $strReturn .= '<input type="'.$type.'" name="'.$name.'" value="'.$arElement['ID'].'" id="'.$id.'" />&nbsp;'.$arElement['VALUE'];
	    
	            $strReturn .= '</label></li>';
	        }
	    }
	    
	    $previousLevel = 0;
	    
	    foreach ($arSections as $arSection)
	    {	        
	        if ($previousLevel && $arSection["DEPTH_LEVEL"] < $previousLevel)
	            $strReturn .= str_repeat("</ul></li>", ($previousLevel - $arSection["DEPTH_LEVEL"]));
	        
		    $previousLevel = $arSection["DEPTH_LEVEL"];
	        
	        if (!$arSection["IS_PARENT"] && !$arSection['ELEMENTS'])
	            continue;
	        
	        $strReturn .= '<li>';

            $strReturn .= '<label class="parent">'.$arSection['VALUE'].'</label>';
            
            if ($arSection['ELEMENTS'])
            {
                $strReturn .= '<ul>';
                
                foreach ($arSection['ELEMENTS'] as $arElement)
                {                    
        	        $type = $multiple ? 'checkbox' : 'radio';
        	        $name = $multiple ? $strHTMLControlName['VALUE'] . '[]' : $strHTMLControlName['VALUE'];
					$id = $strHTMLControlName['VALUE'] . '_' . $arElement['ID'];
        	        
                    $strReturn .= '<li><label>';
        	        
        	        if ($arElement['SELECTED'])
        	            $strReturn .= '<input type="'.$type.'" name="'.$name.'" value="'.$arElement['ID'].'" id="'.$id.'" checked />&nbsp;'.$arElement['VALUE'];
                    else
                        $strReturn .= '<input type="'.$type.'" name="'.$name.'" value="'.$arElement['ID'].'" id="'.$id.'" />&nbsp;'.$arElement['VALUE'];        	        
                     
                    $strReturn .= '</label></li>';
                }
                
                $strReturn .= '</ul>';
            }
            
			if ($arSection["IS_PARENT"])
			    $strReturn .= '<ul>';
			else 
			    $strReturn .= '</li>';			
			
		}
		
        if ($previousLevel > 1)
        	$strReturn .= str_repeat("</ul></li>", ($previousLevel - 1));

        $strReturn .= '</ul>';
        
        $strReturn .= '<div class="reset"><span>Снять выделение</span></div>';
        
        CJSCore::Init(array('aat_iblockprops'));
	    
        return $strReturn;		
	}
	
}
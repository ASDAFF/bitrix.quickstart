<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/
 
class SmartRealt_Common
{
	/**
	* форматирует дату для вывода в списках
	*/
	public static function FormatDateForList($dDate)
	{
		$arDate = explode(' ', $dDate);
		$sResult = $arDate[0].'<br>';
		if (count($arDate)>1)
			$sResult .= '<span style="color: silver; font-size: 11px;">'.$arDate[1].'</span>';
		return $sResult;
	}
	
	/*public static function DateToDB($sDate)
	{
		global $DB;
		$sType = 'SHORT';
		if (strpos($sDate, ' ')!==false)
			$sType = 'FULL';
		$sFormat = CSite::GetDateFormat($sType);
		$sNewFormat = 'YYYY-MM-DD';
		if ($sType=='FULL')
			$sNewFormat .= ' HH:MI:SS';
		return $DB->FormatDate($sDate, $sFormat, $sNewFormat);
	} */
	
	public static function DateToPHP($sDate)
	{
		global $DB;
		$sType = 'SHORT';
		if (strpos($sDate, ' ')!==false)
			$sType = 'FULL';
		$sFormat = CSite::GetDateFormat($sType);
		$sOldFormat = 'YYYY-MM-DD';
		if ($sType=='FULL')
			$sOldFormat .= ' HH:MI:SS';
		return $DB->FormatDate($sDate, $sOldFormat, $sFormat);
	}
	
	public function OnReindex($NS, $oCallback, $callback_method)
	{
        global $DB;
        $arResult = array();
        $sLID = 's1';
        
        $oCatalogElement = new SmartRealt_CatalogElement();
        $rsCatalogElement = $oCatalogElement->GetList(array('Deleted'=>'Y', 'Status' => 'PUBLISH'));
        while ($arCatalogElement = $rsCatalogElement->GetNext())
        {
            $sAddress = SmartRealt_CatalogElement::GetAddress($arCatalogElement);
            
            $sBody = '';
            foreach ($arCatalogElement as $key=>$val)
            {
                if (!in_array($key, $oCatalogElement->arTextFilterFields))
                    continue;
                
                $sBody += ' '.$val;
            }
            
            $arResult[] = array(
                'ID' => $arCatalogElement['Number'],
                'LID' => SITE_ID,
                'DATE_CHANGE' => $DB->FormatDate($arCatalogElement['UpdateDate'], 'YYYY-MM-DD HH:MI:SS', 'DD.MM.YYYY HH:MI:SS'),
                'URL' => SmartRealt_CatalogElement::GetDetailUrl($arCatalogElement),
                'PERMISSIONS' => array(2),
                'TITLE' => $arCatalogElement['SectionFullNameSign'].' '.$sAddress,
                'BODY' => strip_tags($sBody )
            );    
        }
        
        return $arResult;    
	}
    
    public static function ParseUrl($sUrl)
    {
        preg_match('/^(http[s]?):\/\/([^\/]+)(.*)$/i', $sUrl, $matches);
        $arResult = array();
        
        if (count($matches) > 0)
        {
            $arResult = array(
                'protocol' => $matches[1],
                'host' => $matches[2],
                'path' => $matches[3],
            );
        }
        
        return $arResult;
    }
    
    /*public static function GetPhotoUrlTemplate()
    {
        if (!self::$sPhotoUrlTemplate)
        {
            self::$sPhotoUrlTemplate = COption::GetOptionString('webdoka.smartrealt', 'PHOTO_URL', SMARTREALT_PHOTO_URL_DEF);
        }
        
        return self::$sPhotoUrlTemplate;
    }*/
    
    
    public function GetListEditFieldHTML($sFieldName, $arItems, $arSelectedIds, $arAtributes)
    {
        $sAtributes = '';
        foreach ($arAtributes as $sName=>$sValue)
        {
            $sAtributes .= sprintf(' %s="%s"', $sName, $sValue);
        }
        
        $sHTML = sprintf('<select name="%s" %s>', $sFieldName, $sAtributes);
        foreach ($arItems as $Id=>$sName)    
        {
            $sSelected = in_array($Id, $arSelectedIds)?'selected="selected"':'';
            $sHTML .= sprintf('<option value="%d" %s>%s</option>', $Id, $sSelected, $sName);
        }
        $sHTML .= '</select>';
        
        return $sHTML;
    }
    
    public function GetListViewFieldHTML($arItems, $arSelectedIds)
    {
        $sHTML = '';
        
        foreach ($arSelectedIds as $Id)    
        {
            $sHTML .= $arItems[$Id]. ' / ';
        }
        $sHTML = substr($sHTML, 0, strlen($sHTML) - 3);
        
        return $sHTML;
    }
    
    public function DisableModuleAgents($value = true)
    {
        $rs = CAgent::GetList(array(), array('MODULE_ID' => 'webdoka.smartrealt'));
            
        while ($arAgent = $rs->Fetch())
        {
            CAgent::Update($arAgent['ID'], array('ACTIVE' => !$value?"Y":'N'));
        }
    }
    
    public function IconvArray($from, $to, $var)
    {
        if (is_array($var))
        {
            $new = array();
            foreach ($var as $key => $val)
            {
                $new[self::IconvArray($from, $to, $key)] = self::IconvArray($from, $to, $val);
            }
            $var = $new;
        }
        else if (is_string($var))
        {
            $var = iconv($from, $to, $var);
        }
        return $var;
    }  
	
    public static function IsTokenDemo($sKey)
    {      
        if (md5($sKey.'any_string') == 'd834d0c78288d658826e55529f8e1102')
            return true;    
    } 
    
    public static function IsTokenEmpty($sKey)
    {      
        if (strlen($sKey) != 36)
            return true;    
    }
    
    public static function CheckToken()
    {
        IncludeModuleLangFile(__FILE__); 
        
        if (strlen(SmartRealt_Options::GetToken()) != 36)
        {
            echo BeginNote(' width="100%"');
            echo '<span class="required">'.GetMessage('SMARTREALT_TOKEN_EMPTY_TITLE').'</span><br>';
            echo GetMessage('SMARTREALT_TOKEN_EMPTY_TEXT');
            echo EndNote();
        }
        else if (SmartRealt_Options::IsTokenDemo())
        {
            echo BeginNote(' width="100%"');
            echo '<span class="required">'.GetMessage('SMARTREALT_TOKEN_DEMO_TITLE').'</span><br>';
            echo GetMessage('SMARTREALT_TOKEN_DEMO_TEXT');
            echo EndNote();
        }
    }
    
    public static function GetElementCountonPage()
    {
        return COption::GetOptionInt('webdoka.smartrealt', 'ELEMENT_ON_PAGE', 20);
    }         
}
?>

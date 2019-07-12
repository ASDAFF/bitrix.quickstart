<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_Filter
{
    private static $sFilterName = 'arFilter'; 
    
    public function GetFilter($arTypeId)
    {
        $arFilter = array();
        
        if (!is_array($arTypeId))
        {
            $arTypeId = explode(";", $arTypeId);
        }                                 
        if (isset($_GET[SmartRealt_Filter::$sFilterName]))
        {
            $arFilter = $_GET[SmartRealt_Filter::$sFilterName];

            foreach ($arTypeId as $sType)
            {                 
                if (empty($sType))
                    continue;
                
                if (!in_array($sType, array(2,4,5,19)))
                {
                    unset($arFilter['EstateMarket']);
                    unset($arFilter['RoomQuantity']);
                    break;
                }
            }
            
            $_SESSION[SmartRealt_Filter::$sFilterName] = $arFilter;
        }
        else if (isset($_SESSION[SmartRealt_Filter::$sFilterName]))
        {
            $arFilter = $_SESSION[SmartRealt_Filter::$sFilterName];
            
            foreach ($arTypeId as $sType)
            {
                if (!in_array($sType, array(2,4,5,19)))
                {
                    if (empty($sType))
                        continue;
                
                    unset($arFilter['EstateMarket']);
                    unset($arFilter['RoomQuantity']);
                    break;
                }
            }
            
            $_SESSION[SmartRealt_Filter::$sFilterName] = $arFilter;
        }
        
        return $arFilter;
    }
    
    public function GetFilterName()
    {
        return self::$sFilterName;
    }
    
    public function Clear()
    {
        $_SESSION[SmartRealt_Filter::$sFilterName] = array();
    }
    
    public function IsSetFilter($sTypeId)
    {
        $bSet = false;
        $arFilter = self::GetFilter($sTypeId) ;

        foreach ($arFilter as $f=>$v)
        {
            if (in_array($f, array('TransactionType', 'TypeId')))
                continue;
            if ($f=='LocationType' && $v=='City')
                continue;
            if ((!is_array($v) && strlen($v)) || (is_array($v) && count($v)))
            {
                $bSet = true;
                break;
            }
        }
        
        return $bSet;
    }
}
?>
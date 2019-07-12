<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_WebServiceDataObject extends SmartRealt_DataObject
{
    protected $sWebServiceMethodName;
    const TYPE_NAME = __CLASS__;
    
    public function LoadFromWebservice($sUpdateDate, $iOffset, $iRowsCount, $bDeletedRows = true)
    {
        $oWebService = SmartRealt_WebService::GetInstance();
        
        //$sPrevUpdateDate = '';

        if (!$oWebService)
            return false;
        
        $arResult = $oWebService->SoapCall($this->sWebServiceMethodName, array("UpdateDate" => $sUpdateDate,"Offset" => $iOffset,"RowsCount" => $iRowsCount, "Deleted" => $bDeletedRows));
        
        $arResult = $this->OnAfterWebServiceDataLoad($arResult);
        
        foreach ($arResult as $arResultElement)
        {
            if (strlen($arResultElement[$this->sPrimaryKeyName]) == 0)
                continue;
                

            if ($this->GetByID($arResultElement[$this->sPrimaryKeyName]))
            {
                $this->Add($arResultElement, $arResultElement[$this->sPrimaryKeyName]);
            }
            else
            {   
                $this->Add($arResultElement);
            }
        }

        if (count($arResult) < $iRowsCount)
        {
            $this->ClearCurrentUpdateDate();
            $this->ClearCurrentOffset();

            $rsObject = $this->GetList(array("Limit" => 1), array('UpdateDate' => 'desc'));
            if ($arObject = $rsObject->Fetch())
            {
                $sUpdateDate = $arObject['UpdateDate'];
            }

            $this->SetLastUpdateDate($sUpdateDate);
        }
        else
        {
            $this->SetCurrentOffset($iOffset + $iRowsCount);
        }
        
        return count($arResult);
    }
    
    static public function LoadFromWebserviceAgent($sUpdateDate, $iOffset, $iRowsCount)
    {
        //Создадим объект наследованного класса
        if (class_exists(static::TYPE_NAME))
        {
            $sTypeName = static::TYPE_NAME;
            $oObject = new $sTypeName();

            $sUpdateDate = $oObject->GetCurrentUpdateDate();
            $iOffset = $oObject->GetCurrentOffset();

            if (strlen($sUpdateDate) == 0 && $iOffset == 0)
            {
                $sUpdateDate = $oObject->GetLastUpdateDate();

                if (strlen($sUpdateDate) == 0)
                {
                    $sUpdateDate = '0000-00-00 00:00:00';
                }

                $oObject->SetCurrentUpdateDate($sUpdateDate);

            }
            

            
            $iReturnRowsCount = $oObject->LoadFromWebservice($sUpdateDate, $iOffset, $iRowsCount);
            
            if ($iReturnRowsCount == $iRowsCount && $iReturnRowsCount > 0)
            {
                return sprintf("%s::LoadFromWebserviceAgent('%s', %d, '%s');", static::TYPE_NAME, $sUpdateDate, $iOffset+$iReturnRowsCount, $iRowsCount);  
            }
            else
            {
                return sprintf("%s::LoadFromWebserviceAgent('%s', %d, '%s');", static::TYPE_NAME, '', 0, $iRowsCount);
            }
        }
    }
    
    protected function OnAfterWebServiceDataLoad($arData)
    {
        $arResult = array(); 

        foreach ($arData as $arItem)
        {
            foreach ($this->arFields as $key)
            {
                $val = $arItem[$key];
                
                if (strlen($val)==0)
                    $arItem[$key] = 'NULL';
                else
                {
                    if (!defined('BX_UTF') || !BX_UTF)
                        $arItem[$key] = iconv('UTF-8', 'WINDOWS-1251', $val);
                    else
                        $arItem[$key] = $val;
                }    
            }
            $arResult[] = $arItem;
        } 
        
        return $arResult;
    }
    
    public function GetLastUpdateDate()
    {
        /*$rsObject = $this->GetList(array("Limit" => 1), array('UpdateDate' => 'desc'));
                
        if ($arObject = $rsObject->Fetch())
        {
            return $arObject['UpdateDate'];
        }*/
        return COption::GetOptionString("webdoka.smartrealt", self::GetLastUpdateDateOptionName());
    }
    
    public function SetLastUpdateDate($LastUpdateDate)
    {
        if (strlen($LastUpdateDate) == 0 || $LastUpdateDate == 'NULL')
            return false;
            
        if (MakeTimeStamp($LastUpdateDate, "YYYY-MM-DD HH:MI:SS") > time())
            return false;
        
        return COption::SetOptionString("webdoka.smartrealt", self::GetLastUpdateDateOptionName(), $LastUpdateDate);
    }

    public function ClearLastUpdateDate()
    {
        return COption::SetOptionString("webdoka.smartrealt", self::GetLastUpdateDateOptionName(), '');
    }
    
    public function GetLastUpdateDateOptionName()
    {
        return static::TYPE_NAME."LastUpdateDate";
    }

    public function GetCurrentUpdateDate()
    {
        return COption::GetOptionString("webdoka.smartrealt", self::GetCurrentUpdateDateOptionName());
    }

    public function SetCurrentUpdateDate($UpdateDate)
    {
        return COption::SetOptionString("webdoka.smartrealt", self::GetCurrentUpdateDateOptionName(), $UpdateDate);
    }

    public function ClearCurrentUpdateDate()
    {
        return COption::SetOptionString("webdoka.smartrealt", self::GetCurrentUpdateDateOptionName(), '');
    }

    public function GetCurrentUpdateDateOptionName()
    {
        return static::TYPE_NAME."CurrentUpdateDate";
    }

    public function GetCurrentOffset()
    {
        return COption::GetOptionString("webdoka.smartrealt", self::GetCurrentOffsetOptionName());
    }

    public function SetCurrentOffset($Offset)
    {
        return COption::SetOptionString("webdoka.smartrealt", self::GetCurrentOffsetOptionName(), $Offset);
    }

    public function ClearCurrentOffset()
    {
        return COption::SetOptionString("webdoka.smartrealt", self::GetCurrentOffsetOptionName(), '');
    }

    public function GetCurrentOffsetOptionName()
    {
        return static::TYPE_NAME."CurrentOffset";
    }
}
?>

<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/
class SmartRealt_WebService
{
    protected $soapClient;
    protected static $instance;
    protected static $errorMessage;
    
    private function __constructor() { /* ... */ }
 
    private function __clone() { /* ... */ }
 
    private function __wakeup() { /* ... */ }

    public static function Clear()
    {
        self::$instance = null;
    }
    
    public static function GetInstance()
    {            
        if (is_null(self::$instance))
        {   
            self::$instance = new SmartRealt_WebService();
            
            $sUrl = SmartRealt_Options::GetWebServiceUrl();
            $sTokenHash = md5(SmartRealt_Options::GetToken());     
            $sLogin = substr($sTokenHash, 0, 16) ;
            $sPassword = substr($sTokenHash, 16) ;
            self::$instance->soapClient = new nusoap_client($sUrl."?wsdl", 'wsdl');
            self::$instance->soapClient->setUseCurl(0);
            self::$instance->soapClient->authtype = 'basic';
            self::$instance->soapClient->decode_utf8 = 0;
            //self::$instance->soapClient->soap_defencoding = 'UTF-8';
            self::$instance->soapClient->setCredentials($sLogin, $sPassword);

            $arResult = self::$instance->soapClient->call('CheckConnection', array()); 
            if (strtolower($arResult['Result']) != 'true')
            {
                self::$errorMessage = str_replace("\n", "", 'ConnectionError');
                return null;
            }   
        }
        
        return self::$instance;
    }
    
    public function GetErrorMessage()
    {
        return self::$errorMessage;
    }
    
    public function GetObjectsEx($UpdateDate, $iOffset, $iRowsCount, $bDeleted = true)
    {
        if (is_null($UpdateDate))
            $UpdateDate = "";
 
        if (is_null($iOffset))
            $iOffset = "";
 
        if (is_null($iRowsCount))
            $iRowsCount = "";
         
        if (is_null($this->soapClient))
            return array();
            
        $arParamsList = array();
        
        $arParamsList[] = array(
                'Name' => 'UpdateDate',
                'Value' => $UpdateDate,
            );
        
        if (!$bDeleted)
        {
            $arParamsList[] = array(
                'Name' => 'Deleted',
                'Value' => 'False',
            );
        }
    
        $arParamsList = array("ArrayOfParamsEl" => $arParamsList); 
        
        $arDataList = $this->soapClient->call('GetObjectsExByParams',
            array("ArrayOfParamList" => $arParamsList,
                "Offset" => $iOffset,
                "RowsCount" => $iRowsCount));

        return self::FormatSoapResult($arDataList); 
    }
    
    public function GetPhotosByParams($UpdateDate, $iOffset, $iRowsCount, $bDeleted = true)
    {
        if (is_null($UpdateDate))
            $UpdateDate = "";
 
        if (is_null($iOffset))
            $iOffset = "";
 
        if (is_null($iRowsCount))
            $iRowsCount = "";
        
        if (is_null($this->soapClient))
            return array();
            
        $arParamsList = array();
        
        $arParamsList[] = array(
                'Name' => 'UpdateDate',
                'Value' => $UpdateDate,
            );
        
        if (!$bDeleted)
        {
            $arParamsList[] = array(
                'Name' => 'Deleted',
                'Value' => 'False',
            );
        }
    
        $arParamsList = array("ArrayOfParamsEl" => $arParamsList);  
        $arDataList = $this->soapClient->call('GetPhotosByParams',
                array('ArrayOfParamList' => $arParamsList,
                "Offset" => $iOffset,
                "RowsCount" => $iRowsCount));
        
        return self::FormatSoapResult($arDataList);   
    }
    
    public function GetObjectsTypes($UpdateDate, $iOffset, $iRowsCount)
    {
        if (is_null($UpdateDate))
            $UpdateDate = "";
 
        if (is_null($iOffset))
            $iOffset = "";
 
        if (is_null($iRowsCount))
            $iRowsCount = "";
        
        if (is_null($this->soapClient))
            return array();                  
 
        $arDataList = $this->soapClient->call('GetObjectsTypes', array("UpdateDate" => $UpdateDate,"Offset" => $iOffset,"RowsCount" => $iRowsCount));

        return self::FormatSoapResult($arDataList);  
    }
    
    public function GetObjectsCount($UpdateDate = "", $bDeletedRows = true)
    {
        if (is_null($UpdateDate))
            $UpdateDate = "";
        
        if (is_null($this->soapClient))
            return 0;
            
        $arParamsList = array();
        
        $arParamsList[] = array(
                'Name' => 'UpdateDate',
                'Value' => $UpdateDate,
            );
        
        if (!$bDeleted)
        {
            $arParamsList[] = array(
                'Name' => 'Deleted',
                'Value' => 'False',
            );
        }
    
        $arParamsList = array("ArrayOfParamsEl" => $arParamsList);
            
        $arResult = $this->soapClient->call('GetObjectsCountByParams',
            array('ArrayOfParamList' => $arParamsList));    
                
        return $arResult['Count'];
    }
    
    public function GetPhotosCount($UpdateDate = "", $bDeleted = true)
    {
        if (is_null($UpdateDate))
            $UpdateDate = "";
        
        if (is_null($this->soapClient))
            return 0;
        
        $arParamsList = array();
        
        $arParamsList[] = array(
                'Name' => 'UpdateDate',
                'Value' => $UpdateDate,
            );
        
        if (!$bDeleted)
        {
            $arParamsList[] = array(
                'Name' => 'Deleted',
                'Value' => 'False',
            );
        }
    
        $arParamsList = array("ArrayOfParamsEl" => $arParamsList);
            
        $arResult = $this->soapClient->call('GetPhotosCountByParams', array('ArrayOfParamList' => $arParamsList));

        return $arResult['Count'];
    }
    
    public function SoapCall($sMethodName, $arParams)
    {
        foreach ($arParams as $key=>$val)
        {
            if (is_null($arParams[$key]))
                $arParams[$key] = "";
        }
        
        switch ($sMethodName)
        {
            case 'GetObjectsEx':
                return $this->GetObjectsEx($arParams['UpdateDate'], $arParams['Offset'], $arParams['RowsCount'], $arParams['Deleted']);
                break;
            case 'GetPhotosByParams':
                return $this->GetPhotosByParams($arParams['UpdateDate'], $arParams['Offset'], $arParams['RowsCount'], $arParams['Deleted']);
                break;
            case 'GetObjectsTypes':
                return $this->GetObjectsTypes($arParams['UpdateDate'], $arParams['Offset'], $arParams['RowsCount']);
                break;
            case 'GetObjectsCount':
                return $this->GetObjectsCount($arParams['UpdateDate'], $arParams['Deleted']);
                break;
            case 'GetPhotosCount':
                return $this->GetPhotosCount($arParams['UpdateDate'], $arParams['Deleted']);
                break;
            default:
                throw new Exception("Метод не определен");     
        }   
    }
    
    private function FormatSoapResult($oSoapResult)
    {
        $oSoapResult = each($oSoapResult);  
        $oSoapResult = each($oSoapResult['value']);      
        $oSoapResult = $oSoapResult['value'];      

        if (!isset($oSoapResult[0]))
        {                                                   
            $ar = each($oSoapResult);
            if (!empty($ar['value']))
                return array($oSoapResult);
            else
                return array();
        }
        else
        {
            return $oSoapResult;
        }
    }    
}           

?>

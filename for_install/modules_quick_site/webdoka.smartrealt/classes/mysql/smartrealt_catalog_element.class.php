<?php
/**
* ###################################
* # Copyright (c) 2013 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

class SmartRealt_CatalogElement extends SmartRealt_WebServiceDataObject
{
    const TYPE_NAME = __CLASS__;
    
    public function __construct()
    {
        $this->sTableName = 'smartrealt_catalog_element';
        $this->sWebServiceMethodName  = 'GetObjectsEx';
        $this->sPrimaryKeyName = 'Id';
        $this->sCreateDateField = 'CreateDate';
        $this->sUpdateDateField = 'UpdateDate';
        $this->arFields = array( 
            'Id',
            'Number',
            'SectionId',
            'SectionFullName',
            'SectionFullNameSign',
            'Status',
            'TypeId',
            'TypeName',
            'TransactionType',
            'EstateMarket',
            'Developer',
            'DesignDeclaration',
            'BuiltYear',
            'BuiltQuarter',
            'BuildingName',
            'ComplexDemand',
            'AgentName',
            'AgentPhone',
            'AgentEmail',
            'Latitude',
            'Longitude',
            'Zoom',    
            'LatitudeYandex',
            'LongitudeYandex',
            'ZoomYandex',
            'CountryId',
            'CountryName',
            'RegionId',
            'RegionName',
            'RegionAreaId',
            'RegionAreaName',
            'CityId',
            'CityName',
            'ForeignCity',
            'TownId',
            'TownName',
            'CityAreaId',
            'CityAreaName',
            'MetroStationId',
            'MetroStationName',
            'HighwayId',
            'HighwayName',
            'Remoteness',
            'Street',
            'HouseNumber',
            'Floor',
            'FloorQuantity',
            'FloorHeight',
            'RoomQuantity',
            'RoomOffer',
            'GeneralArea',
            'KitchenArea',
            'LifeArea',
            'AreaUnitId',
            'AreaUnitName',
            'LandArea',
            'LandAreaUnitId',
            'LandAreaUnitName',
            'Description',
            'SpecialOffer',
            'Price',
            'PricePerMetr',
            'CurrencyId',
            'CurrencySymbol',
            'CurrencyPattern',
            'CurrencyPatternThousand',
            'PriceCurrencyRate',
            'Mortgage',
            'ConditionId',
            'ConditionName',
            'ToiletTypeId',
            'ToiletTypeName',
            'BalconyTypeId',
            'BalconyTypeName',
            'LiftTypeId',
            'LiftTypeName',
            'HouseTypeName',
            'HouseProjectName',
            'LandCategoryId',
            'LandCategoryName',
            'LandUseTypeId',
            'LandUseTypeName',
            'ElectricPower',
            'StreetRetail',
            'Deleted',
            'CreateDate',
            'UpdateDate'
        );
        $this->arTextFilterFields = array(
            'SectionFullName',
            'SectionFullNameSign',
            'TypeName',
            'AgentName',
            'AgentPhone',
            'AgentEmail',
            'CountryName',
            'RegionName',
            'RegionAreaName',
            'CityName',
            'ForeignCity',
            'TownName',
            'CityAreaName',
            'Street',
            'Description',
            'ConditionName',
            'ToiletTypeName',
            'BalconyTypeName',
            'LiftTypeName',
            'HouseTypeName',
            'HouseProjectName',
            'LandCategoryName',
            'LandUseTypeName',
            'Developer',
            'BuildingName',
            'CreateDate',
            'UpdateDate'
        );
    }
    
    public function GetList($arFilter = array(), $arSort = array(), $arGroup = array())
    {
        global $DB;
        
        $sWhrSql = $this->FilterToWhere($arFilter);       
        
        $sLimit = $this->GetLimitSql($arFilter);
        
        $sGroup = $this->GetGroupSql($arGroup);
                                                
        
        $sSort = $this->GetSortSql($arSort);
        
        $sSqlForSelect = '';
        if (in_array($this->sPrimaryKeyName,$this->arFields) && in_array($this->sTitleFieldName,$this->arFields))
        {
            $sSqlForSelect = ", T." . $this->sPrimaryKeyName . " as REFERENCE_ID, T." . $this->sTitleFieldName . " as REFERENCE";        
        }
        
        if ($arFilter['MinMaxPrice'] == 'Y')
        {
            $sSqlForSelect .= ", MIN(T.Price) AS PriceMin, MAX(T.Price) AS PriceMax";        
        }
        
        if ($arFilter['MinMaxArea'] == 'Y')
        {
            $sSqlForSelect .= ", MIN(T.GeneralArea) AS GeneralAreaMin, MAX(T.GeneralArea) AS GeneralAreaMax";        
        }
        
        if ($arFilter['MinMaxPricePerMetr'] == 'Y')
        {
            $sSqlForSelect .= ", MIN(T.PricePerMetr) AS PricePerMetrMin, MAX(T.PricePerMetr) AS PricePerMetrMax";        
        }
          
        $sSql = "SELECT *" . $sSqlForSelect . ", RAND() as rand
                FROM " . $this->sTableName . " T
                " . $sJoin . "
                WHERE " . $sWhrSql .                                                      
                $sGroup. $sSort . $sLimit;
        //echo $sSql;
        $rs = $DB->Query($sSql, false, 'FILE: '.__FILE__.'<br>LINE: ' . __LINE__);
        return $rs;
    }
    
    public function GetDetailUrl($arCatalogElement, $sTypeCode, $sRubricCode)
    {
        if (!is_array($arCatalogElement))
            return;
        
        $oRubric = new SmartRealt_Rubric();
        $sSEFFolder = COption::GetOptionString('webdoka.smartrealt', 'SEF_FOLDER');
        $sCatalogDetailUrl = COption::GetOptionString('webdoka.smartrealt', 'CATALOG_DETAIL_URL', SMARTREALT_CATALOG_DETAIL_URL_DEF);
        
        if (empty($sTypeCode) || empty($sRubricCode))
        {
            $arFilter = array(
                    'TypeId' => $arCatalogElement['TypeId'],
                    'TransactionType' => $arCatalogElement['TransactionType'],
                    'SectionId' => $arCatalogElement['SectionId'],
                    'EstateMarket' => $arCatalogElement['EstateMarket'],
                );

            $rsRubric = $oRubric->GetList($arFilter, array('SectionId' => 'desc', 'EstateMarket' => 'desc'));
            
            if ($arRubric = $rsRubric->Fetch())
            {
                $sTypeCode = $arRubric['RubricGroupCode'];
                $sRubricCode = $arRubric['Code'];
            }
        }
        
        return str_replace(
            array(                        
                    '#TYPE_CODE#',
                    '#RUBRIC_GROUP_CODE#',
                    '#TRANSACTION_TYPE#',
                    '#RUBRIC_CODE#',
                    '#NUMBER#',
                ),
            array(
                    $sTypeCode,
                    $sTypeCode,
                    //$sTypeCode?$sTypeCode:$oRubric->GetTypeCodeById($arCatalogElement['TypeId']),
                    $sRubricCode,
                    $sRubricCode,
                    //$sRubricCode?$sRubricCode:strtolower($arCatalogElement['TransactionType']),
                    $arCatalogElement['Number'],
                ),
            $sSEFFolder.$sCatalogDetailUrl    
            );
    }
    
    public function GetAddress($arCatalogElement)
    {
        if (!is_array($arCatalogElement))
            return;
        
        $sAddress = '';
        /*$sAddress = strlen($arCatalogElement['CountryName'])?$arCatalogElement['CountryName']:'';
        
        if (strlen($arCatalogElement['RegionName'])>0)
        {
            $sAddress .= strlen($sAddress)>0?", ".$arCatalogElement['RegionName']:$arCatalogElement['RegionName'];
        }*/
        
        if ($arCatalogElement['SectionId'] == 5 && strlen($arCatalogElement['ForeignCity']) > 0)
        {
            $sAddress .= $arCatalogElement['CountryName'].", ". $arCatalogElement['ForeignCity']; 
            
            if (strlen($arCatalogElement['Street']) > 0)
            {
                $sAddress .= ', '.$arCatalogElement['Street'];
                
                if (strlen($arCatalogElement['HouseNumber'] ) > 0)
                {
                    $sAddress .= ', '.$arCatalogElement['HouseNumber'] ;
                }
            }
        }
        else if (strlen($arCatalogElement['CityName']) > 0)
        {
            $sAddress .= strlen($sAddress)>0?", ".$arCatalogElement['CityName']:$arCatalogElement['CityName'];
            
            if (strlen($arCatalogElement['TownName']) > 0)
            {
                $sAddress .= ', ';
                
                if (strlen($arCatalogElement['TownTypeName']) > 0)
                    $sAddress .=  $arCatalogElement['TownTypeName'].' ';

                $sAddress .= $arCatalogElement['TownName']; 
            }
            
            if (strlen($arCatalogElement['Street']) > 0)
            {
                $sAddress .= ', '.$arCatalogElement['Street'];
                
                if (strlen($arCatalogElement['HouseNumber'] ) > 0)
                {
                    $sAddress .= ', '.$arCatalogElement['HouseNumber'] ;
                }
            }
        }
        else if (strlen($arCatalogElement['RegionAreaName']) > 0)
        {
            $sAddress .= strlen($sAddress)>0?", ":"";
            $sAddress .= $arCatalogElement['RegionAreaName'];
            $sAddress .= strlen($arCatalogElement['RegionAreaTypeName'])>0?' ' .$arCatalogElement['RegionAreaTypeName']:"";
            
            if (strlen($arCatalogElement['TownName']) > 0)
            {
                $sAddress .= ', ';
                
                if (strlen($arCatalogElement['TownTypeName']) > 0)
                    $sAddress .=  $arCatalogElement['TownTypeName'].' ';

                $sAddress .= $arCatalogElement['TownName']; 
            }
        }
        
        return $sAddress;
    }
    
    public function FormatPrice($arElement, $bShort = true)
    {
        IncludeModuleLangFile(__FILE__);
        
        if ($bShort)
            return self::PriceByFormat($arElement['Price']/1000, $arElement['CurrencyPatternThousand']);
        else
            return self::PriceByFormat($arElement['Price'], $arElement['CurrencyPattern']);
    }

    public function FormatPricePerMetr($arElement, $bShort = true)
    {
        IncludeModuleLangFile(__FILE__);

        if ($bShort)
            return self::PriceByFormat($arElement['PricePerMetr']/1000, $arElement['CurrencyPatternThousand']);
        else
            return self::PriceByFormat($arElement['PricePerMetr'], $arElement['CurrencyPattern']);
    }

    public function PriceByFormat($Price, $sFormat)
    {
        IncludeModuleLangFile(__FILE__);

        return sprintf($sFormat, number_format($Price, 0, '.', ' ')) ;
    }

    public function GetListUrl($sCode, $sRubricCode)
    {
        $sSEFFolder = SmartRealt_Options::GetSEFFolder(); 
        $sCatalogListUrl = SmartRealt_Options::GetListUrl(); 
        
        return str_replace(
            array(
                    '#TYPE_CODE#',
                    '#RUBRIC_GROUP_CODE#',
                    '#TRANSACTION_TYPE#',
                    '#RUBRIC_CODE#',
                ),
            array(
                    $sCode,
                    $sCode,
                    $sRubricCode,
                    $sRubricCode
                ),
            $sSEFFolder.$sCatalogListUrl    
            );
    }
    
    public function GetAreaString($arCatalogElement)
    {                                                              
        switch ($arCatalogElement['AreaUnitId'])
        {
            case '2':
                $arCatalogElement['GeneralArea'] = $arCatalogElement['GeneralArea']/100;
                break;
            case '3':
                $arCatalogElement['GeneralArea'] = $arCatalogElement['GeneralArea']/10000;
                break;                                                               
        }
        
        $iAccuracy = doubleval($arCatalogElement['GeneralArea']) < 10?1:0;
        $sArea = round($arCatalogElement['GeneralArea'], $iAccuracy);  
        if (in_array($arCatalogElement['TypeId'], array(2, 3, 4, 5, 6, 19)))
        {
            $sArea .= "/".(intval($arCatalogElement['LifeArea'])>0?$arCatalogElement['LifeArea']:"-");
            $sArea .= "/".(intval($arCatalogElement['KitchenArea'])>0?$arCatalogElement['KitchenArea']:"-");
        }
        return sprintf('%s %s', $sArea, $arCatalogElement['AreaUnitName']);
    }
    
    protected function OnAfterWebServiceDataLoad($arData)
    {
        $arResult = array();
        
        foreach ($arData as $arObject)
        {
            //Переименование полей  
            if (strlen($arObject['Longtitude']) > 0)
                $arObject['Longitude'] = $arObject['Longtitude'];

            if (strlen($arObject['OwnerName']) > 0)
                $arObject['AgentName'] = $arObject['OwnerName'];   

            if (strlen($arObject['OwnerTelephone']) > 0)
                $arObject['AgentPhone'] = $arObject['OwnerTelephone'];

            if (strlen($arObject['OwnerEmail']) > 0)
                $arObject['AgentEmail'] = $arObject['OwnerEmail'];

            if (strlen($arObject['HousetypeName']) > 0)
                $arObject['HouseTypeName'] = $arObject['HousetypeName'];

            if (strlen($arObject['HouseprojectName']) > 0)
                $arObject['HouseProjectName'] = $arObject['HouseprojectName'];

            if (strlen($arObject['ForeingCity']) > 0)
                $arObject['ForeignCity'] = $arObject['ForeingCity'];

            if (strlen($arObject['Address']) > 0)
                $arObject['Street'] = $arObject['Address'];
            
            $arObject['ComplexDemand'] = $arObject['ComplexDemand'] == 'True' ? "Y": "N";   
            $arObject['Mortgage'] = $arObject['Mortgage'] == 'True' ? "Y": "N";   
            $arObject['StreetRetail'] = $arObject['StreetRetail'] == 'True' ? "Y": "N";   
            $arObject['Deleted'] = $arObject['Deleted'] == 'True' ? "Y": "N";   
            $arObject['SpecialOffer'] = $arObject['SpecialOffer'] == 'True' ? "Y": "N";   
            $arObject['ExportInCSV'] = $arObject['ExportInCSV'] == 'True' ? "Y": "N";
            
            $arResult[] = $arObject;
        }
        
        return parent::OnAfterWebServiceDataLoad($arResult);
    }
    
    public static function GetCatalogElementProperties()
    {
        IncludeModuleLangFile(__FILE__); 
        return array(
            'RoomQuantity'       => GetMessage('SMARTREALT_ROOM_QUANTITY'),
            'Floor'              => GetMessage('SMARTREALT_FLOOR'),
            'FloorHeight'        => GetMessage('SMARTREALT_FLOOR_HEIGHT'),
            'ToiletTypeName'       => GetMessage('SMARTREALT_TOILET_TYPE'),
            'BalconyTypeName'      => GetMessage('SMARTREALT_BALCONY_TYPE'),
            'LiftTypeName'         => GetMessage('SMARTREALT_LIFT_TYPE'),
            'ConditionName'      => GetMessage('SMARTREALT_CONDITION'),
            'EstateMarket'       => GetMessage('SMARTREALT_ESTATE_MARKET'),
            'HouseTypeName'      => GetMessage('SMARTREALT_HOUSE_TYPE'),
            'HouseProjectName'   => GetMessage('SMARTREALT_HOUSE_PROJECT'),
            'BuildingName'       => GetMessage('SMARTREALT_BUILDING_NAME'),
            'Developer'             => GetMessage('SMARTREALT_DEVELOPER'),  
            'DesignDeclaration'   => GetMessage('SMARTREALT_DESIGN_DECLARATION'),
            'BuiltYear'             => GetMessage('SMARTREALT_BUILT_YEAR'),
            'LandCategoryName'      => GetMessage('SMARTREALT_LAND_CATEGORY'),
            'LandUseTypeName'       => GetMessage('SMARTREALT_LAND_USE_TYPE'),
            'HighwayName'       => GetMessage('SMARTREALT_HIGHWAY'),
            'ElectricPower'       => GetMessage('SMARTREALT_ELECTRIC_POWER'),
            'StreetRetail'       => GetMessage('SMARTREALT_STREET_RETAIL'), 
            'Mortgage'       => GetMessage('SMARTREALT_MORTGAGE'), 
        );
    }
}
?>

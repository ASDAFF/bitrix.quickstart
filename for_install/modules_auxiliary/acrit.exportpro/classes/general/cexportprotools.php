<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule( "acrit.exportpro" );

Loc::loadMessages( __FILE__ );

class CAcritExportproTools{
    public function RoundNumber( $number, $precision, $mode, $precision_default = false ){
        switch( $mode ){
            case "UP":
                $mode = PHP_ROUND_HALF_UP;
                break;
            case "DOWN":
                $mode = PHP_ROUND_HALF_DOWN;
                break;
            case "EVEN":
                $mode = PHP_ROUND_HALF_EVEN;
                break;
            case "ODD":
                $mode = PHP_ROUND_HALF_ODD;
                break;
            default:
                $mode = PHP_ROUND_HALF_UP;
                break;
        }
        
        if( !is_numeric( $number ) && !is_float( $number ) ){
            return $number;
        }
        
        if( is_numeric( $precision ) ){
            return round( $number, abs( $precision ), $mode );
        }
        elseif( $precision_default !== false ){
            return round( $number, abs( $precision_default ), $mode );
        }
        
        return $number;
    }
    
    public function ArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){
        if( $arTuple ){
            reset( $arTuple );
            list( $key, $head ) = each( $arTuple );
            unset( $arTuple[$key] );
            $arTemp[$key] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    if( empty( $arTuple ) )
                        $arResult[] = $arTemp;
                    else
                        $this->ArrayMultiply( $arResult, $arTuple, $arTemp );
                }
                else{
                    foreach( $head as $value ){
                        $arTemp[$key] = $value;
                        if( empty( $arTuple ) )
                            $arResult[] = $arTemp;
                        else
                            $this->ArrayMultiply( $arResult, $arTuple, $arTemp );
                    }
                }
            }
            else{
                $arTemp[$key] = $head;
                if( empty( $arTuple ) )
                    $arResult[] = $arTemp;
                else
                    $this->ArrayMultiply( $arResult, $arTuple, $arTemp );
            }
        }
        else{
            $arResult[] = $arTemp;
        }
    }
    
    public function ExportArrayMultiply( &$arResult, $arTuple, $arTemp = array() ){        
        if( count( $arTuple ) == 0 ){
            $arResult[] = $arTemp;
        }
        else{
            $head = array_shift( $arTuple );
            $arTemp[] = false;
            if( is_array( $head ) ){
                if( empty( $head ) ){
                    $arTemp[count( $arTemp ) - 1] = "";
                    $this->ArrayMultiply( $arResult, $arTuple, $arTemp );
                }
                else{
                    foreach( $head as $key => $value ){
                        $arTemp[count( $arTemp ) - 1] = $value;
                        self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
                    }
                }
            }
            else{
                $arTemp[count( $arTemp ) - 1] = $head;
                self::ExportArrayMultiply( $arResult, $arTuple, $arTemp );
            }
        }
    }
    
    public function GetYandexDateTime( $dateTime ){
        global $DB;
        $resultTime = false;
        
        $localTime = new DateTime();
        $dateTimeZoneDiff = $localTime->getOffset() / 3600;
        
        $dateTimeZone = ( ( intval( $dateTimeZoneDiff ) > 0 ) ? "+" : "-" ).date( "H:i", mktime( $dateTimeZoneDiff, 0, 0, 0, 0, 0 ) );
        
        $dateTimeValue = MakeTimeStamp( $dateTime );
        $dateTimeFormattedValue = date( "Y-m-d", $dateTimeValue )."T".date( "H:i:s", $dateTimeValue );
        
        $resultTime = $dateTimeFormattedValue.$dateTimeZone;
        
        return $resultTime;
    }
    
    public function GetIblockUserFields( $iblockId ){
        $result = false;
        $dbSectionUserFields = CUserTypeEntity::GetList(
            array(),
            array(
                "ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION",
                "LANG" => LANGUAGE_ID
            )
        );
        
        while( $arSectionUserFields = $dbSectionUserFields->Fetch() ){
            if( !$result ) $result = array();
            $result[] = $arSectionUserFields;
        }
        
        return $result;
    }
    
    public function CheckCondition( $arItem, $code ){
        unset( $GLOBALS["CHECK_COND"] );
        if( is_array( $arItem["SECTION_ID"] ) && is_array( $arItem["SECTION_PARENT_ID"] ) )
            $arItem["SECTION_ID"] = array_merge( $arItem["SECTION_ID"], $arItem["SECTION_PARENT_ID"] );
        
        $GLOBALS["CHECK_COND"] = $arItem;
        
        return eval( "return $code;" );
    }
    
    public function GetStringCharset( $str ){ 
        $resEncoding = "cp1251";
        
        if( preg_match( "#.#u", $str ) ){
            $resEncoding = "utf8";
        }
        
        return $resEncoding;
    }
    
    public function GetSectionNavChain( $sectionId ){
        static $arResult = null;
        if( !is_null( $arResult ) )
            return $arResult;

        $arResult = array();

        $dbSectionList = CIBlockSection::GetNavChain(
            false,
            $sectionId
        );

        while( $arSection = $dbSectionList->GetNext() ){
            $arResult[] = $arSection["ID"];
        }

        return $arResult;
    }
}
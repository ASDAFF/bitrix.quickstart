<?php

IncludeModuleLangFile( __FILE__ );

class CExportproProfileDB{
	private $serial = array(
		"IBLOCK_TYPE_ID",
		"IBLOCK_ID",
		"CATEGORY",
		"FORMAT",
		"CURRENCY",
		"CONDITIONS",
		"SETUP",
        "XMLDATA",
		"CONVERT_DATA",
		"MARKET_CATEGORY",
		"CONDITION",
		"OFFER_TEMPLATE",
		"CURRENCY",
		"LOG",
		"NAMESCHEMA",
		"VARIANT",
	);
    
    private $tableName = "acrit_exportpro_profile";
    
    
    /*
        ACTIVE
        ID
        NAME
        TIMESTAMP
        START_LAST_TIME_X
        TYPE
        TYPE_RUN
        CODE
        DESCRIPTION
        SHOPNAME
        COMPANY
        DOMAIN_NAME
        LID
        ENCODING
    */
    public function GetProcessList( $aSort = array(), $aFilter = array() ){
        global $DB;
        $this->LAST_ERROR = "";
        $arFilter = array();
        if( is_array( $aFilter ) ){
            foreach( $aFilter as $key => $val ){
                if( !is_array( $val ) && ( ( strlen( $val ) <= 0 ) || ( $val == "NOT_REF" ) ) ){
                    continue;
                }

                switch( strtoupper( $key ) ){
                    case "NAME":
                        $arFilter[] = "P.NAME like '%".$val."%'";
                        break;

                    case "ID":
                        $arFilter[] = GetFilterQuery( "P.ID", $val, "N" );
                        break;

                    case "ACTIVE":
                        $arFilter[] = "P.ACTIVE='".$val."'";
                        break;

                    case "TIMESTAMP":
                        if( $DB->IsDate( $val ) ){
                            $arFilter[] = "P.TIMESTAMP_X>=".$DB->CharToDateFunction( $val, "SHORT" );
                        }
                        else{
                            $this->LAST_ERROR .= GetMessage( "PARSER_WRONG_TIMESTAMP_FROM" )."<br>";
                        }
                        break;

                    case "TYPE":
                        $arFilter[] = "P.TYPE='".$val."'";
                        break;

                    case "TYPE_RUN":
                        $arFilter[] = "P.TYPE_RUN='".$val."'";
                        break;

                    case "START_LAST_TIME":
                        if( $DB->IsDate( $val ) ){
                            $arFilter[] = "P.START_LAST_TIME_X>=".$DB->CharToDateFunction( $val, "SHORT" );
                        }
                        else{
                            $this->LAST_ERROR .= GetMessage( "PARSER_WRONG_START_LAST_TIME_FROM" )."<br>";
                        }
                        break;
                }
            }
        }
        
        $arOrder = array();
        if( is_array( $aSort ) ){
            foreach( $aSort as $key => $ord ){
                $key = strtoupper( $key );
                $ord = ( ( strtoupper( $ord ) <> "ASC" ) ? "DESC" : "ASC" );
                $arOrder[$key] = "P.$key ".$ord;
            }
        }

        if( count( $arOrder ) == 0 ){
            $arOrder[] = "P.ID DESC";
        }

        $sOrder = "\nORDER BY ".implode( ", ", $arOrder );

        if( count( $arFilter ) == 0 ){
            $sFilter = "";
        }
        else{
            $sFilter = "\nWHERE ".implode( "\nAND ", $arFilter );
        }

        $strSql = "
            SELECT
                 ID
                ,ACTIVE
                ,NAME
                ,TYPE
                ,TYPE_RUN
                ,SETUP
                ,".$DB->DateToCharFunction( "TIMESTAMP_X" )." TIMESTAMP_X
                ,".$DB->DateToCharFunction( "START_LAST_TIME_X" )." START_LAST_TIME_X FROM ".$this->tableName;
        return $DB->Query( $strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__ );
    }
	
	/*
		ACTIVE
		ID
		NAME
		TIMESTAMP
		START_LAST_TIME_X
		TYPE
		TYPE_RUN
		CODE
		DESCRIPTION
		SHOPNAME
		COMPANY
		DOMAIN_NAME
		LID
		ENCODING
	*/
    public function GetList( $aSort = array(), $aFilter = array() ){
		global $DB;
		$this->LAST_ERROR = "";
		$arFilter = array();
		if( is_array( $aFilter ) ){
			foreach( $aFilter as $key => $val ){
				if( !is_array( $val ) && ( ( strlen( $val ) <= 0 ) || ( $val == "NOT_REF" ) ) ){
					continue;
				}

				switch( strtoupper( $key ) ){
					case "NAME":
						$arFilter[] = "P.NAME like '%".$val."%'";
					    break;

					case "ID":
						$arFilter[] = GetFilterQuery( "P.ID", $val, "N" );
					    break;

					case "ACTIVE":
						$arFilter[] = "P.ACTIVE='".$val."'";
					    break;

					case "TIMESTAMP":
						if( $DB->IsDate( $val ) ){
							$arFilter[] = "P.TIMESTAMP_X>=".$DB->CharToDateFunction( $val, "SHORT" );
						}
						else{
							$this->LAST_ERROR .= GetMessage( "PARSER_WRONG_TIMESTAMP_FROM" )."<br>";
						}
					    break;

					case "TYPE":
						$arFilter[] = "P.TYPE='".$val."'";
					    break;

					case "TYPE_RUN":
						$arFilter[] = "P.TYPE_RUN='".$val."'";
					    break;

					case "START_LAST_TIME":
						if( $DB->IsDate( $val ) ){
							$arFilter[] = "P.START_LAST_TIME_X>=".$DB->CharToDateFunction( $val, "SHORT" );
						}
						else{
							$this->LAST_ERROR .= GetMessage( "PARSER_WRONG_START_LAST_TIME_FROM" )."<br>";
						}
					    break;
				}
			}
		}
		$arOrder = array();
		if( is_array( $aSort ) ){
			foreach( $aSort as $key => $ord ){
				$key = strtoupper( $key );
				$ord = ( ( strtoupper( $ord ) <> "ASC" ) ? "DESC" : "ASC" );
				$arOrder[$key] = "P.$key ".$ord;
			}
		}

		if( count( $arOrder ) == 0 ){
			$arOrder[] = "P.ID DESC";
		}

		$sOrder = "\nORDER BY ".implode( ", ", $arOrder );

		if( count( $arFilter ) == 0 ){
			$sFilter = "";
		}
		else{
			$sFilter = "\nWHERE ".implode( "\nAND ", $arFilter );
		}

		$strSql = "
			SELECT
                 P.ID
				,P.ACTIVE
                ,P.NAME
				,P.TYPE
				,P.TYPE_RUN
				,P.SETUP
				," . $DB->DateToCharFunction( "P.TIMESTAMP_X" )." TIMESTAMP_X
                ," . $DB->DateToCharFunction( "P.START_LAST_TIME_X" )." START_LAST_TIME_X
                ,P.UNLOADED_OFFERS
                ,P.UNLOADED_OFFERS_CORRECT
                ,P.UNLOADED_OFFERS_ERROR FROM ".$this->tableName." P ".$sFilter.$sOrder;
		return $DB->Query( $strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__ );
    }
	
	public function Update( $ID, $arFields ){
		global $DB;
		$ID = intval($ID);
		$arFields = $this->SerializeData($arFields);      
		$strUpdate = $DB->PrepareUpdate($this->tableName, $arFields);
		
		if ($strUpdate != ""){
			$strSql = "UPDATE " . $this->tableName . " SET " . $strUpdate . " WHERE ID=" . $ID;
			$arBinds = array();
			if (!$DB->QueryBind($strSql, $arBinds)){
				return false;
			}
		}
		return true;
	}

	public function Add($arFields) {
		global $DB;
		$arFields = $this->SerializeData($arFields);
		$ID = $DB->Add($this->tableName, $arFields);
		return $ID;
	}

	public function Delete($ID){
		global $DB;
		CModule::IncludeModule("main");
		$ID = intval($ID);
		// Удаляем агент
		$arAgent = CAgent::GetList(array(), array("NAME" => "CAcritExport::StartAgent(".$ID.");"))->Fetch();
		CAgent::Delete($arAgent["ID"]);

		$DB->StartTransaction();
		$res = $DB->Query("DELETE FROM " . $this->tableName . " WHERE ID='" . $ID . "'", false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
		if ($res){
			$DB->Commit();
		}
		else{
			$DB->Rollback();
		}
		return $res;
	}

	public function GetByID($ID) {
		global $DB;
		$ID = intval($ID);

		$strSql = "SELECT P.* FROM " . $this->tableName . " P WHERE P.ID = '" . $ID . "'";

		$res = $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
		return $this->UnserializeData($res);
	}
	
	private function SerializeData($arFields) {
		$arFields["TYPE_RUN"] = $arFields["SETUP"]["TYPE_RUN"];
		foreach ($this->serial as $type){
			$arFields[$type] = base64_encode(serialize($arFields[$type]));
		}

		return $arFields;
	}

	private function UnserializeData($arFields) {
		$arFields = $arFields->Fetch();
		if (!$arFields){
			return false;
		}

		foreach ($this->serial as $type){
			$arFields[$type] = unserialize(base64_decode($arFields[$type]));
		}

		return $arFields;
	}
}
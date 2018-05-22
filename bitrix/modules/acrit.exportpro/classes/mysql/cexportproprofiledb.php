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
    
    private $profileTableName = "acrit_exportpro_profile";
    private $dataTableName = "acrit_exportpro_profile_data";
    private $toolsTableName = "acrit_exportpro_profile_tools";
    
    
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
                ,".$DB->DateToCharFunction( "START_LAST_TIME_X" )." START_LAST_TIME_X FROM ".$this->profileTableName;
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
				    case "USE_REMARKETING":
				        $arFilter[] = "P.USE_REMARKETING='".$val."'";
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
                ,P.UNLOADED_OFFERS_ERROR FROM ".$this->profileTableName." P ".$sFilter.$sOrder;
		return $DB->Query( $strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__ );
    }
	
	public function Update( $ID, $arFields ){
		global $DB;
		$ID = intval( $ID );
		$arFields = $this->SerializeData( $arFields );       
        if( isset( $arFields["ID"] ) ){
            unset( $arFields["ID"] );
        }
        
        $arProfileDataFields = array_keys( $DB->GetTableFields( $this->dataTableName ) );
        $arProfileToolsFields = array_keys( $DB->GetTableFields( $this->toolsTableName ) );
        
        $arProfileData = array();
        $arProfileTools = array();
        $arProfileData["PROFILE_ID"] = $ID;
        $arProfileTools["PROFILE_ID"] = $ID;
        
        foreach( $arFields as $fieldsItemIndex => $fieldsItemValue ){
            $bMarketField = false;
            if( in_array( $fieldsItemIndex, $arProfileDataFields ) ){
                $arProfileData[$fieldsItemIndex] = $fieldsItemValue;
                $bMarketField = true;
            }
            if( in_array( $fieldsItemIndex, $arProfileToolsFields ) ){
                $arProfileTools[$fieldsItemIndex] = $fieldsItemValue;
                $bMarketField = true;
            }
            if( $bMarketField ){
                unset( $arFields[$fieldsItemIndex] );
            }
        }
                          
        $strDataTableUpdate = $DB->PrepareUpdate( $this->dataTableName, $arProfileData );
        $strToolsTableUpdate = $DB->PrepareUpdate( $this->toolsTableName, $arProfileTools );
        $strProfileTableUpdate = $DB->PrepareUpdate( $this->profileTableName, $arFields );
		
		if( $strDataTableUpdate != "" ){
            $strSql = "UPDATE ".$this->dataTableName." SET ".$strDataTableUpdate." WHERE PROFILE_ID = ".$ID;
            $arBinds = array();
            if( !$DB->QueryBind( $strSql, $arBinds ) ){
                return false;
            }
        }
        
        if( $strToolsTableUpdate != "" ){
            $strSql = "UPDATE ".$this->toolsTableName." SET ".$strToolsTableUpdate." WHERE PROFILE_ID = ".$ID;
            $arBinds = array();
            if( !$DB->QueryBind( $strSql, $arBinds ) ){
                return false;
            }
        }
        
        if( $strProfileTableUpdate != "" ){
			$strSql = "UPDATE ".$this->profileTableName." SET ".$strProfileTableUpdate." WHERE ID = ".$ID;
			$arBinds = array();
			if( !$DB->QueryBind( $strSql, $arBinds ) ){
				return false;
			}
		}
		
        return true;
	}

	public function Add( $arFields ){
		global $DB;
        
		$arFields = $this->SerializeData( $arFields );   
        
        $arProfileDataFields = array_keys( $DB->GetTableFields( $this->dataTableName ) );
        $arProfileToolsFields = array_keys( $DB->GetTableFields( $this->toolsTableName ) );
        $arProfileData = array();
        $arProfileTools = array();
        
        foreach( $arFields as $fieldsItemIndex => $fieldsItemValue ){
            $bMarketField = false;
            if( in_array( $fieldsItemIndex, $arProfileDataFields ) ){
                $arProfileData[$fieldsItemIndex] = $fieldsItemValue;
                $bMarketField = true;
            }
            if( in_array( $fieldsItemIndex, $arProfileToolsFields ) ){
                $arProfileTools[$fieldsItemIndex] = $fieldsItemValue;
                $bMarketField = true;
            }
            if( $bMarketField ){
                unset( $arFields[$fieldsItemIndex] );
            }
        }
        
        $profileId = $DB->Add( $this->profileTableName, $arFields );
        $arProfileData = array_merge( $arProfileData, array( "PROFILE_ID" => $profileId ) );
        $arProfileTools = array_merge( $arProfileTools, array( "PROFILE_ID" => $profileId ) );
        
        $dataRowId = $DB->Add( $this->dataTableName, $arProfileData );
        $toolsRowId = $DB->Add( $this->toolsTableName, $arProfileTools );

		return $profileId;
	}

	public function Delete( $ID ){
		global $DB;
		CModule::IncludeModule( "main" );
		$ID = intval( $ID );
		// delete agent
		$arAgent = CAgent::GetList( array(), array( "NAME" => "CAcritExport::StartAgent(".$ID.");" ) )->Fetch();
		CAgent::Delete( $arAgent["ID"] );

		$DB->StartTransaction();
		
        $toolsTableRes = $DB->Query( "DELETE FROM ".$this->toolsTableName." WHERE PROFILE_ID='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__ );
        if( $toolsTableRes ){
            $DB->Commit();
        }
        else{
            $DB->Rollback();
        }
        
        $dataTableRes = $DB->Query( "DELETE FROM ".$this->dataTableName." WHERE PROFILE_ID='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__ );
        if( $dataTableRes ){
            $DB->Commit();
        }
        else{
            $DB->Rollback();
        }
        
        $profileTableRes = $DB->Query( "DELETE FROM ".$this->profileTableName." WHERE ID='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__ );
		if( $profileTableRes ){
			$DB->Commit();
		}
		else{
			$DB->Rollback();
		}
		
        return $res;
	}

	public function GetByID( $ID ){
		global $DB;
		$ID = intval( $ID );
        
        $strSql = "SELECT P.*, P_D.*, P_T.*
                    FROM
                    ".$this->profileTableName." P
                    INNER JOIN ".$this->dataTableName." P_D ON P.ID = P_D.PROFILE_ID
                    INNER JOIN ".$this->toolsTableName." P_T ON P.ID = P_T.PROFILE_ID
                    WHERE P.ID = '".$ID."'";
        
		$res = $DB->Query( $strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__ );
		$arUnserializedData = $this->UnserializeData( $res );
        $arUnserializedData["ID"] = $ID;
        
        return $arUnserializedData;
	}
	
	private function SerializeData( $arFields ){
		$arFields["TYPE_RUN"] = $arFields["SETUP"]["TYPE_RUN"];
		foreach( $this->serial as $type ){
			$arFields[$type] = base64_encode( serialize( $arFields[$type] ) );
		}

		return $arFields;
	}

	private function UnserializeData( $arFields ){
		$arFields = $arFields->Fetch();
        
        if( isset( $arFields["ID"] ) ){
            unset( $arFields["ID"] );
        }
        
		if( !$arFields ){
			return false;
		}

		foreach( $this->serial as $type ){
			$arFields[$type] = unserialize( base64_decode( $arFields[$type] ) );
		}

		return $arFields;
	}
}
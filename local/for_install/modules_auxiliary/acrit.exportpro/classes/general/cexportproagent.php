<?php

class CExportproAgent{
    public static function StartExport( $profileId ){
        AcritExportproSession::Init( 0 );
        AcritExportproSession::DeleteSession( $profileId );
        $arAgent = CAgent::GetList(
            array(),
            array(
                "NAME" => "CExportproAgent::StartExport(".$profileId.");"
            )
        )->Fetch();
        
        if( $arAgent ){
            $dbProfile = new CExportproProfileDB();
            $arProfile = $dbProfile->GetByID( $profileId );
    
            if( isset( $arProfile["SETUP"]["IS_PERIOD"] ) && ( $arProfile["SETUP"]["IS_PERIOD"] == "Y" ) ){
                register_shutdown_function( array( "CExportproAgent", "UpdateAgent" ), $profileId , $arAgent, $arProfile );
            }
        }
        
        $export = new CAcritExportproExport( intval( $profileId ) );
        $export->Export( "agent" );
        return __CLASS__."::".__FUNCTION__."(".$profileId.");";
    }

    public static function AddAgent( $profileID, $setup ){
        COption::SetOptionString( "main", "agents_use_crontab", "Y" );
        
        $agent_ID = 0;
        $agent_period = intval( $setup["PERIOD"] ) * 60;

        if( $agent_period <= 0 ){
            $agent_period = 86400;
        }
        
        $setupDateStamp = MakeTimeStamp( $setup["DAT_START"] );
        $currentDateStamp = time() + 120;
        
        $runTime = date(
            "d.m.Y H:i",
            $setupDateStamp
        );

        if( $profileID > 0 ){
            $arAgent = CAgent::GetList(
                array(),
                array(
                    "NAME" => "CExportproAgent::StartExport(".$profileID.");"
                )
            )->Fetch();
            
            if( !$arAgent ){                        
                if( $setupDateStamp < $currentDateStamp ){
                    $runTime = date(
                        "d.m.Y H:i",
                        $currentDateStamp
                    );    
                }
                
                $agent_ID = CAgent::AddAgent(
                    "CExportproAgent::StartExport(".$profileID.");",
                    "acrit.exportpro",
                    "N",
                    $agent_period,
                    "",
                    "Y",
                    $runTime
                );
            }
            elseif( $arAgent ){
                $agent_ID = $arAgent["ID"];
                
                $agentNextStart = MakeTimeStamp( $arAgent["NEXT_EXEC"] );
                if( ( $agentNextStart == $setupDateStamp ) && ( $agentNextStart > $currentDateStamp ) ){
                    $runTime = date(
                        "d.m.Y H:i",
                        $agentNextStart
                    );
                }
                
                CAgent::Update(
                    $arAgent["ID"],
                    array(
                        "AGENT_INTERVAL" => ( $agent_period != $arAgent["AGENT_INTERVAL"] ) ? $agent_period : $arAgent["AGENT_INTERVAL"],
                        "NEXT_EXEC" => $runTime
                    )
                );                
            }            
        }
        
        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" ) ){
            $cfgFileSize = filesize( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" );
            $fp = fopen( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "rb" );
            $cfgData = fread( $fp, $cfgFileSize );
            fclose( $fp );

            $cfgData = preg_replace( "#.*bitrix\/modules\/main\/tools\/cron_events.php(\r)*\n#i", "", $cfgData);
            $cfgData = preg_replace( "#.*bitrix\/modules\/main\/tools\/cron_events.php#i", "", $cfgData);
            $cfgData = preg_replace( "#.*bitrix\/modules\/acrit.exportpro\/tools\/cron_events.php(\r)*\n#i", "", $cfgData);
            $cfgData = preg_replace( "#.*bitrix\/modules\/acrit.exportpro\/tools\/cron_events.php#i", "", $cfgData);
            $cronTask = "* * * * * php -f {$_SERVER["DOCUMENT_ROOT"]}/bitrix/modules/acrit.exportpro/tools/cron_events.php";
            if( PHP_EOL == substr( $cfgData, "-".strlen( PHP_EOL ) ) ){
               $cfgData .= $cronTask.PHP_EOL;
            }
            else{
               $cfgData .= PHP_EOL.$cronTask.PHP_EOL;
            }
            
            file_put_contents( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", $cfgData );
            @exec( "crontab ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" );
        }
        
        return $agent_ID;
    }

    public static function DelAgent( $profileID ){
        if( $profileID > 0 ){
            CAgent::RemoveAgent(
                "CExportproAgent::StartExport(".$profileID.");",
                "acrit.exportpro"
            );
        }
    }
    
    public static function UpdateAgent( $profileID, $arAgent, $arProfile ){
		$format = CSite::GetDateFormat();
        $arAgentAfter = CAgent::GetList(
            array(),
            array(
                "NAME" => "CExportproAgent::StartExport(".$profileID.");"
            )
        )->Fetch();
        
        if( !$arAgentAfter ) return false;
        
        if( $arAgentAfter["ID"] !== $arAgent["ID"] ) return false;
			
		$currentDateStamp = time() + 120;
        $agent_period = intval( $arAgent["AGENT_INTERVAL"] );
		
        if( $agent_period <= 0 ){
            $agent_period = 86400;
        }

        $agent_next_exec = MakeTimeStamp( $arAgent["NEXT_EXEC"], $format );
		$next_exec = $agent_next_exec + $agent_period;
		
		$runTime = date(
            "d.m.Y H:i",
            $next_exec
        );
		
		if( isset( $arAgentAfter["LAST_EXEC"] ) && !is_null( $arAgentAfter["LAST_EXEC"] ) ){			
			$agent_last_exec = MakeTimeStamp( $arAgentAfter["LAST_EXEC"], $format );		
			$agent_last_exec = $agent_last_exec + 120;
			
			if( $next_exec < $agent_last_exec){
				while( $next_exec < $agent_last_exec ){
				    $next_exec = $next_exec+$agent_period;
				}
			}
			
			$runTime = date(
				"d.m.Y H:i",
				$next_exec
			); 
		}
        elseif( $next_exec < $currentDateStamp ){
			while( $next_exec < $currentDateStamp ){
				$next_exec = $next_exec + $agent_period;
			}		
	
			$runTime = date(
				"d.m.Y H:i",
				$next_exec
			);    
        }
    
        CAgent::Update(
            $arAgent["ID"],
            array(
                "NEXT_EXEC" => $runTime
            )
        );
    }
}

class CExportproCron{
    public static function StartExport( $profileId ){
        $export = new CAcritExportproExport( intval( $profileId ) );
        $export->Export( "cron" );
        $arAgent = CAgent::GetList(
            array(),
            array(
                "NAME" => "CExportproAgent::StartExport(".$profileId.");"
            )
        )->Fetch();
        
        if( $arAgent ){
            $dbProfile = new CExportproProfileDB();
		    $arProfile = $dbProfile->GetByID( $profileId );
		    
		    if( isset( $arProfile["SETUP"]["IS_PERIOD"] ) && ( $arProfile["SETUP"]["IS_PERIOD"] == "Y" ) ){
		        register_shutdown_function( array( "CExportproCron", "UpdateAgent" ), $profileId , $arAgent, $arProfile );  
		    }    
		}
    }
    
    public static function CronRun( $profileID, $setup, $delete_cron = false ){
		$path2export = $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/exportpro/";
        $logPath = $_SERVER["DOCUMENT_ROOT"]."/upload/exportpro_log/";
        
        $cron_period = intval( $setup["PERIOD"] );
        $dirExport = !preg_match( "/.+\..{3,}/", $setup["URL_DATA_FILE"], $arMatches );
        if( $dirExport ){
            $path2export = $_SERVER["DOCUMENT_ROOT"].$setup["URL_DATA_FILE"];
        }
        
        // get row list from crontab
        $cfg_data = "";
		$cron_list = array();
		@exec( "crontab -l", $cron_list );
		
        CheckDirPath( $path2export );
        CheckDirPath( $logPath );
		
		$datetime = $setup["DAT_START"];
		$format = CSite::GetDateFormat();
		$date = ParseDateTime( $datetime, $format );
		
		$starttime = intval( $date["HH"] );
		$cronTime = array();
		
		if( $cron_period <= 0 ){
			$cronTime[] = intval( $date["MI"])." ".intval( $date["HH"] )." ".intval( $date["DD"] )." ".intval( $date["MM"] )." * ";
		}
		elseif( $cron_period < 24 ){
			$cronHours = array( $starttime );
			   
			for( $i = 0; $i < 24; $i++ ){
				$starttime += $cron_period;                                              
				$starttime = $starttime > 23 ? $starttime - 24 : $starttime;
				$cronHours[] = $starttime;
			}
			sort( $cronHours );
			$cronHours = array_unique( $cronHours );
			$cronTime[] = intval( $date["MI"] )." ".implode( ",", $cronHours )." * * * ";
		}
		elseif( $cron_period > 23 && $cron_period < 720 ){
			$cron_period_mod = $cron_period % 24;
			$cron_period_div = intval( $cron_period / 24 );
			if( $cron_period_mod == 0 ){
				$cron_period_div --;
				$i = 2;
			}
			else{
				$i = 1;
			}
			
			$dateVal = 1 + $date["DD"] - 1;
			$dateVal = $dateVal > 30 ? $dateVal - 30 : $dateVal;
			$cronDaysHours = array( $starttime => array( $dateVal ) );
			
			for( $i; $i < 31; $i++ ){
				$i += $cron_period_div;
				$starttime += $cron_period_mod;
				if( $starttime > 23 ){
					$starttime = $starttime - 24;
					$i++;
				}
				$dateVal = $i + $date["DD"] - 1;
				$dateVal = ( $dateVal > 30 ) ? ( $dateVal - 30 ) : $dateVal;
				$cronDaysHours[$starttime][] = $dateVal;
			}
			foreach( $cronDaysHours as $hour => $days ){
				$cronTime[] = intval( $date["MI"] )." ".$hour." ".implode( ",", $days )." * * ";
			}
		}

		foreach( $cron_list as $id => $cronRecord ){
            if( strpos( $cronRecord, "/acrit.exportpro/tools/cronrun.php $profileID" ) ){
                unset( $cron_list[$id] );
            }
        }

        if( !$delete_cron ){
			foreach( $cronTime as $strTime ){
				$cron_list[] = "$strTime php -f {$_SERVER["DOCUMENT_ROOT"]}/bitrix/modules/acrit.exportpro/tools/cronrun.php $profileID \"{$_SERVER["DOCUMENT_ROOT"]}\"";
			}
		}

		CheckDirPath( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/" );
		file_put_contents( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", implode( PHP_EOL, $cron_list ).PHP_EOL );
		@exec( "crontab ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" );
	}
	
	public static function UpdateAgent($profileID, $arAgent, $arProfile){
	    $arAgentAfter = CAgent::GetList(
	        array(),
	        array(
	            "NAME" => "CExportproAgent::StartExport(".$profileID.");"
	        )
	    )->Fetch();
        
        if( !$arAgentAfter ) return false;
	    
        if( $arAgentAfter["ID"] !== $arAgent["ID"] ) return false;
        $currentDateStamp = time() + 120;
        $agent_period = intval( $arAgent["AGENT_INTERVAL"] );

        if( $agent_period <= 0 ){
            $agent_period = 86400;
        }
		
		$format = CSite::GetDateFormat();
        $agent_next_exec = MakeTimeStamp( $arAgent["NEXT_EXEC"], $format );
		$next_exec = $agent_next_exec + $agent_period;
		
		$runTime = date(
            "d.m.Y H:i",
            $next_exec
        );
		
		if( isset( $arAgentAfter["LAST_EXEC"] ) && !is_null( $arAgentAfter["LAST_EXEC"] ) ){
			$agent_last_exec = MakeTimeStamp( $arAgentAfter["LAST_EXEC"], $format );
			if(  ( $agent_last_exec + $agent_period ) < $next_exec ){				
			}
			
			$agent_last_exec = $agent_last_exec + 120;
			if( $next_exec < $agent_last_exec ){
				while( $next_exec < $agent_last_exec ){
				    $next_exec = $next_exec + $agent_period;
				}
			}
			
			$runTime = date(
				"d.m.Y H:i",
				$next_exec
			); 
		
		}
        elseif( $next_exec < $currentDateStamp ){
			while( $next_exec < $currentDateStamp ){
				$next_exec = $next_exec + $agent_period;
			}		
	
			$runTime = date(
				"d.m.Y H:i",
				$next_exec
			);    
        }
		
	    CAgent::Update(
	        $arAgent["ID"],
	        array(
	            "NEXT_EXEC" => $runTime
	        )
        );
	}
}
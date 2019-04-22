<?
if (!defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true)
    die();
   
IncludeModuleLangFile(__FILE__);
 
class ParserEventHandler {
    function OnParserStart($ID, &$_this){                                                                                
        if($_this->settings["notification"]['start']=='Y'){
            $parser = new ShsParserContent();
            $parser = $parser->GetList(array(),array('ID'=>$ID))->fetch();   
            \Bitrix\Main\Loader::includeModule('iblock');
            $block = CIBlockElement::GetByID($_this->iblock_id)->fetch();          
            $res = \Bitrix\Main\Mail\Event::send(array(
                "EVENT_NAME" => "SOTBIT_PARSER_START",
                "LID" => $block['LID'],
                "C_FIELDS" => array(
                    "EMAIL_TO" => $_this->settings["notification"]['email'],
                    "START_TIME" => $parser['START_LAST_TIME_X'], 
                    'PARSER_NAME'=>$parser['NAME'],
                    'PARSER_ID'=>$ID,
                    
                ),));                                                     
        }
    }
    
    function OnParserEnd($ID){      
        $parser = new ShsParserContent();   
        $set = $parser->GetSettingsById($ID)->fetch();       
        $settings = unserialize(base64_decode($set['SETTINGS']));  
        $parser = $parser->GetList(array(),array('ID'=>$ID))->fetch();   
        if($settings["notification"]['end']=='Y'){              
           global $DB;                        
           $now = time()+CTimeZone::GetOffset();
           $now = date($DB->DateFormatToPHP(FORMAT_DATETIME), $now); 
           \Bitrix\Main\Loader::includeModule('iblock');    
           $block = CIBlockElement::GetByID($set['IBLOCK_ID'])->fetch();   
           $res = \Bitrix\Main\Mail\Event::send(array(
                "EVENT_NAME" => "SOTBIT_PARSER_END",
                "LID" => $block['LID'],
                "C_FIELDS" => array(
                    "EMAIL_TO" => $settings["notification"]['email'],
                    "END_TIME" => $now, 
                    'PARSER_NAME'=>$parser['NAME'],
                    'PARSER_ID'=>$ID,    
                ),));                
           /*$e = mail($settings["notification"]['email'], 
                     GetMessage('event_parser_end_title').$ID.' "'.$parser['NAME'].'"', 
                     GetMessage('event_parser_end_text',array(
                        '#ID#'=>$ID,
                        '#END_TIME#'=>$now,
                        '#NAME#'=>$parser['NAME']
                     )));                                                   */
        }
    }
}
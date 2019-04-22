<?php
namespace Bitrix\Shs;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity,
    Bitrix\Shs\ParserResultProductTable;
Loc::loadMessages(__FILE__);

/**
 * Class ParserResultTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PARSER_ID int optional
 * <li> START_LAST_TIME datetime optional
 * <li> END_LAST_TIME datetime optional
 * </ul>
 *
 * @package Bitrix\Shs
 **/

class ParserResultTable extends Main\Entity\DataManager
{ 
    
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_shs_parser_result';
    }

    /**
     * Returns entity map definition.
     *  Status -1 - error
     *  Status 2 - debug
     *  Status 1 - ok
     *  Status 0 - not complete
     * 
     * @return array
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('PARSER_RESULT_ENTITY_ID_FIELD'),
            )),
            new Entity\IntegerField('PARSER_ID', array(
                'required' => true,
                'title' => Loc::getMessage('PARSER_RESULT_ENTITY_PARSER_ID_FIELD'),
            )),
            new Entity\DateTimeField('START_LAST_TIME',array(
                'title' => Loc::getMessage('PARSER_RESULT_ENTITY_START_LAST_TIME_FIELD'),                
            )),
            new Entity\DateTimeField('END_LAST_TIME',array(
                'title' => Loc::getMessage('PARSER_RESULT_ENTITY_END_LAST_TIME_FIELD'),                
            )),
            new \Bitrix\Main\Entity\TextField('SETTINGS', array(
            )),
            new Entity\IntegerField('STATUS', array(
                'title' => Loc::getMessage('PARSER_RESULT_ENTITY_STATUS'),
            )),
        );
    }
    
    public static function saveParserResult($parser_id = null, $settings = array(), $result_count = 1){
        if($parser_id == null){
            return false;
        }
        $result = ParserResultTable::add(array(
            'PARSER_ID' => $parser_id,
            'START_LAST_TIME' => new \Bitrix\Main\Type\DateTime(),
            'END_LAST_TIME' => new \Bitrix\Main\Type\DateTime(),
            'SETTINGS' => base64_encode(serialize($settings)),
            'STATUS' => 0,
        ));
        if ($result->isSuccess()) {
            $id = $result->getId();
        } else {
            $id = false;
        }
        $results_count = count(ParserResultTable::getList(array(
            'filter' => array(
                'PARSER_ID' => $parser_id,
            )
        ))->fetchAll());
        $results = ParserResultTable::getList(array(
            'filter' => array(
                'PARSER_ID' => $parser_id,
            ),
            'order'=>array('ID'=>'DESC'),
            'limit'=>$results_count,
            'offset'=>$result_count,
        ))->fetchAll();
        foreach($results as $res){
            ParserResultTable::delete(
                    $res['ID']
            );
            ParserResultProductTable::deleteByResultId($res['ID']);
        }
        
        return $id;
    }
    
    public static function updateEndTime($result_id = null){
        $result = ParserResultTable::update($result_id,array(
            'END_LAST_TIME' => new \Bitrix\Main\Type\DateTime(),
            'STATUS' => 1,
        ));
        if ($result->isSuccess()) {
            $id = $result->getId();
        } else {
            $id = false;
        }
        return $id;
    }
    
    public static function updateStatus($result_id = null, $status = 0){
           $result = ParserResultTable::update($result_id,array(
            'STATUS' => $status,
        ));
     }

    public static function getStatus($status){
        switch($status){
            case -1:
                $mes = 'paser_result_status_error';
                break;
            case 0:
                $mes = 'paser_result_status_not_complete';
                break;
            case 1:
                $mes = 'paser_result_status_ok';
                break;     
        }
        return GetMessage($mes);
    }
     
}
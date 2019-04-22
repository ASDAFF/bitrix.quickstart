<?php
namespace Bitrix\Shs;

use \Bitrix\Main,
    \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ParserResultProductTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> RESULT_ID int optional
 * <li> PRODUCT_ID int optional
 * <li> OLD_PRICE double optional
 * <li> NEW_PRICE double optional
 * </ul>
 *
 * @package Bitrix\Shs
 **/

class ParserResultProductTable extends Main\Entity\DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_shs_parser_result_product';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return array(
            new \Bitrix\Main\Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('PARSER_RESULT_PRODUCT_ENTITY_ID_FIELD'),
            )),
            new \Bitrix\Main\Entity\IntegerField('RESULT_ID', array(
                'required' => true,
                'title' => Loc::getMessage('PARSER_RESULT_PRODUCT_ENTITY_RESULT_ID_FIELD'),
            )),
            new \Bitrix\Main\Entity\ReferenceField(
                'PARSER',
                'Bitrix\Shs\ParserResultTable',
                array('=this.RESULT_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\IntegerField('PRODUCT_ID', array(
                'required' => true,
                'title' => Loc::getMessage('PARSER_RESULT_PRODUCT_ENTITY_PRODUCT_ID_FIELD'),
            )),
            new \Bitrix\Main\Entity\ReferenceField(
                'IBLOCK',
                'Bitrix\Iblock\ElementTable',
                array('=this.PRODUCT_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
            new \Bitrix\Main\Entity\FloatField('OLD_PRICE', array(
                'title' => Loc::getMessage('PARSER_RESULT_PRODUCT_ENTITY_OLD_PRICE_FIELD'),
            )),
            new \Bitrix\Main\Entity\FloatField('NEW_PRICE', array(
                'title' => Loc::getMessage('PARSER_RESULT_PRODUCT_ENTITY_NEW_PRICE_FIELD'),
            )),
            new \Bitrix\Main\Entity\TextField('PROPERTIES', array(
                'title' => Loc::getMessage('PARSER_RESULT_PRODUCT_ENTITY_PROPERTIES_FIELD'),
            )),
            new \Bitrix\Main\Entity\DateTimeField('UPDATE_TIME',array(
                'title' => Loc::getMessage('PARSER_RESULT_ENTITY_UPDATE_FIELD'),                
            )),
        );
    }
    
    public static function deleteByResultId($result_id){
        $arId = ParserResultProductTable::GetList(array(
            'select' => array('ID'),
            'filter' => array('RESULT_ID'=>$result_id),
        ));
        $res = true;
        while($id = $arId->fetch()){
            if(!ParserResultProductTable::delete($id)){
                $res=false;
            }
        }
        return $res;
    }
}
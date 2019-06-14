<?
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class ShsParserSectionTable extends Entity\DataManager
{
    public static function getFilePath()
    {
       return __FILE__;
    }

   public static function getTableName()
   {
      return 'b_shs_parser_section';
   }
   
   public static function getMap()
   {
      return array(
         'ID' => array(
            'data_type' => 'integer',
            'primary' => true,
            'autocomplete' => true,
            'title' => "ID",
         ),
         'TIMESTAMP_X' => array(
            'data_type' => 'datetime',
            //'required' => true,
            'title' => "TIMESTAMP_X",
         ),
         'DATE_CREATE' => array(
            'data_type' => 'datetime',
            //'required' => true,
            'title' => Loc::getMessage("shs_parser_section_date_title"),
         ),
         'ACTIVE' => array(
            'data_type' => 'boolean',
            'required' => true,
            'values' => array('N', 'Y'),
            'title' => Loc::getMessage("shs_parser_section_active_title"),
         ),
         'SORT' => array(
            'data_type' => 'integer',
            'required' => true,
            'title' => Loc::getMessage("shs_parser_section_sort_title"),
         ),
         'NAME' => array(
            'data_type' => 'string',
            'required' => true,
            'title' => Loc::getMessage("shs_parser_section_name_title"),
         ),
         'DESCRIPTION' => array(
            'data_type' => 'string',
            'title' => Loc::getMessage("shs_parser_section_description_title"),
         ),
         'PARENT_CATEGORY_ID' => array(
            'data_type' => 'integer',
            'title' => Loc::getMessage("shs_parser_section_parent_title"),
         ),

      );
   }
}
?>
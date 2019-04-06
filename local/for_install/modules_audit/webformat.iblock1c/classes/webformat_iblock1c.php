<?php
if(!defined('B_PROLOG_INCLUDED') || (B_PROLOG_INCLUDED !== true)){die();}
IncludeModuleLangFile( __FILE__ );

class WebformatIblock1C{
    private static $MODE_CATALOG = 1;
    private static $MODE_OFFERS = 2;
    private static $LANG_PREFIX = 'WEBFORMAT_IBLOCK1C_';
    
	public function __construct(){}
    
    public static function Adapt($post){
        if(!isset($post['extra'])){$post['extra'] = array();}
        $adapted = array();
        if(isset($post['catalog']) && (bool)(int)$post['catalog']){
            self::AdaptIblock((int)$post['catalog'], self::$MODE_CATALOG, $post['extra']);
            $adapted[] = 'catalog';
        }
        if(isset($post['offers']) && (bool)(int)$post['offers']){
            self::AdaptIblock((int)$post['offers'], self::$MODE_OFFERS, $post['extra']);
            $adapted[] = 'offers';
        }
        if(!(bool)$adapted){return GetMessage(self::$LANG_PREFIX.'EMPTY');}
        return true;
    }
    
    private static function AdaptIblock($iblockID, $mode = 1, Array $extra = array()){
        $iblock = new CIBlock();
        $iblock->Update($iblockID, array(
            'XML_ID' => ($mode == self::$MODE_CATALOG ? 'FUTURE-1C-CATALOG' : 'FUTURE-1C-OFFERS')
        ));
        if((bool)$extra){
            CIBlock::SetFields($iblockID, $extra);
        }
        return true;
    }

}
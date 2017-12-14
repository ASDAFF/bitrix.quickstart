<?php

/**
 * @author darkfriend <hi@darkfriend.ru>
 * @copyright darkfriend
 * @version 0.1.7
 */

namespace Dev2fun\ImageCompress;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

IncludeModuleLangFile(__FILE__);

class Compress
{
    private $jpegoptim = false;
    private $pngoptim = false;
    private $MODULE_ID = 'dev2fun.imagecompress';
    private $png = false;
    private $tableName = 'b_d2f_imagecompress_files';
    public $LAST_ERROR;

    private $jpegOptimPath = '',
        $pngOptimPath = '';

    private
        $enableElement = false,
        $enableSection = false,
        $enableResize = false,
        $enableSave = false,
        $jpegProgress = false;

    private
        $jpegOptimCompress,
        $pngOptimCompress;

    private static $instance;

    private function __construct() {
        $this->pngOptimPath = Option::get($this->MODULE_ID,'path_to_optipng');
        $this->jpegOptimPath = Option::get($this->MODULE_ID,'path_to_jpegoptim');

        $this->enableElement = (Option::get($this->MODULE_ID,'enable_element')=='Y');
        $this->enableSection = (Option::get($this->MODULE_ID,'enable_section')=='Y');
        $this->enableResize = (Option::get($this->MODULE_ID,'enable_resize')=='Y');
        $this->enableSave = (Option::get($this->MODULE_ID,'enable_save')=='Y');

        $this->jpegOptimCompress = Option::get($this->MODULE_ID,'jpegoptim_compress', 80);
        $this->pngOptimCompress = Option::get($this->MODULE_ID,'optipng_compress', 3);

        $this->jpegProgress = (Option::get($this->MODULE_ID,'jpeg_progressive')=='Y');
    }

    /**
     * @static
     * @return Compress
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    public function isPNGOptim() {
        if(!$this->pngoptim) {
            exec($this->pngOptimPath.'/optipng -v',$s);
            if($s) $this->pngoptim = true;
        }
        return $this->pngoptim;
    }

    public function isJPEGOptim() {
        if(!$this->jpegoptim) {
            exec($this->jpegOptimPath.'/jpegoptim --version',$s);
            if($s) $this->jpegoptim = true;
        }
        return $this->jpegoptim;
    }

    public function compressJPG($strFilePath) {
        $res = false;
        if(!$this->isJPEGOptim()){
            $this->LAST_ERROR = Loc::getMessage('DEV2FUN_IMAGECOMPRESS_NO_MODULE',array('#MODULE#'=>'jpegoptim'));
            return $res;
        }
        if(file_exists($strFilePath)) {
            $strFilePath = strtr(
                $strFilePath,
                array(
                    ' '=>'\ ',
                    '('=>'\(',
                    ')'=>'\)',
                    ']'=>'\]',
                    '['=>'\[',
                )
            );
            $strCommand = '';
            if($this->jpegProgress) {
                $strCommand .= '--all-progressive';
            }
            $strCommand .= ' --strip-all -t';
            if($this->jpegOptimCompress) {
                $strCommand .= " -m{$this->jpegOptimCompress}";
            }
            exec($this->jpegOptimPath."/jpegoptim $strCommand $strFilePath 2>&1", $res);
            chmod($strFilePath,0777);
        }
        return $res;
    }

    public function compressPNG($strFilePath) {
        $res = false;
        if(!$this->isPNGOptim()){
            $this->LAST_ERROR = Loc::getMessage('DEV2FUN_IMAGECOMPRESS_NO_MODULE',array('#MODULE#'=>'optipng'));
            return $res;
        }
        if(file_exists($strFilePath)) {
            $strFilePath = strtr(
                $strFilePath,
                array(
                    ' '=>'\ ',
                    '('=>'\(',
                    ')'=>'\)',
                    ']'=>'\]',
                    '['=>'\[',
                )
            );
            exec($this->pngOptimPath."/optipng -strip all -o{$this->pngOptimCompress} $strFilePath 2>&1", $res);
            chmod($strFilePath,0777);
        }
        return $res;
    }

    public function compressImageByID($intFileID){
        global $DB;
        $res = false;
        if(!$intFileID) return null;
        $arFile  = \CFile::GetByID($intFileID)->GetNext();

        if($arFile["CONTENT_TYPE"] != 'image/jpeg' && $arFile["CONTENT_TYPE"] != 'image/png'){
            return null;
        }

        $strFilePath = $_SERVER["DOCUMENT_ROOT"] . \CFile::GetPath($intFileID);

        if(file_exists($strFilePath)){

            $oldSize = $arFile["FILE_SIZE"]; // filesize($strFilePath);

            switch ($arFile["CONTENT_TYPE"]) {
                case 'image/jpeg' :
                    $isCompress = $this->compressJPG($strFilePath);
                    break;
                case 'image/png' :
                    $isCompress = $this->compressPNG($strFilePath);
                    break;
                default :
                    $this->LAST_ERROR = Loc::getMessage('DEV2FUN_IMAGECOMPRESS_CONTENT_TYPE',array('#TYPE#'=>$arFile["CONTENT_TYPE"]));
                    return null;
            }

            if($isCompress) {
                clearstatcache(true,$strFilePath);
                $newSize = filesize($strFilePath);
                if($newSize!=$oldSize) {
                    $DB->Query("UPDATE b_file SET FILE_SIZE='" . $DB->ForSql($newSize, 255) . "' WHERE ID=" . intval($intFileID));
                }
                $arFields = Array(
                    'FILE_ID' => $intFileID,
                    'SIZE_BEFORE' => $oldSize,
                    'SIZE_AFTER' => $newSize,
                );

                $rs = ImageCompressTable::getById($intFileID);

                if($rs->getSelectedRowsCount() <= 0){
                    $el =  new ImageCompressTable();
                    $res = $el->add($arFields);
                } else {
                    $res = ImageCompressTable::update($intFileID, $arFields);
                }
            }
        } else {
            $res = $this->addCompressTable($intFileID,Array(
                'FILE_ID' => $intFileID,
                'SIZE_BEFORE' => 0,
                'SIZE_AFTER' => 0,
            ));
        }
        return $res;
    }

    public function addCompressTable($intFileID,$arFields) {
        $rs = ImageCompressTable::getById($intFileID);

        if($rs->getSelectedRowsCount() <= 0){
            $el =  new ImageCompressTable();
            $res = $el->add($arFields);
        } else {
            $res = ImageCompressTable::update($intFileID, $arFields);
        }
        return $res;
    }

    /**
     * Сжатие картинок на событии в разделах
     * @param array $arFields
     */
    public static function CompressImageOnSectionEvent(&$arFields){
        $instance = self::getInstance();
        if($instance->enableSection && $arFields['PICTURE']) {
            $rsSection = \CIBlockSection::GetByID($arFields["ID"]);
            $arSection = $rsSection->GetNext();
            $instance->compressImageByID($arSection['PICTURE']);
        }
    }

    /**
     * Сжатие картинок на событии в элементах
     * @param array $arFields
     */
    public static function CompressImageOnElementEvent(&$arFields){
        $instance = self::getInstance();
        if(!$instance->enableElement) return;
        if(intval($arFields["PREVIEW_PICTURE_ID"]) > 0){
            $instance->compressImageByID($arFields["PREVIEW_PICTURE_ID"]);
        }

        if(intval($arFields["DETAIL_PICTURE_ID"]) > 0){
            $instance->compressImageByID($arFields["DETAIL_PICTURE_ID"]);
        }

        $arEl = false;

        if($arFields["PROPERTY_VALUES"]) {
            foreach ($arFields["PROPERTY_VALUES"] as $key => $values) {
                foreach ($values as $k => $v) {
                    if ($v['VALUE']['type'] == 'image/png' || $v['VALUE']['type'] == 'image/jpeg') {

                        if (!$arEl) {
                            $rsEl = \CIBlockElement::GetByID($arFields["ID"]);
                            if ($obEl = $rsEl->GetNextElement()) {
                                $arEl = $obEl->GetFields();
                                $arEl["PROPERTIES"] = $obEl->GetProperties();
                            }
                        }

                        foreach ($arEl["PROPERTIES"] as $strPropCode => $arProp) {
                            if ($arProp["ID"] == $key) {
                                if ($arProp["MULTIPLE"]!='N') {
                                    foreach ($arProp["VALUE"] as $intFileID) {
                                        $instance->compressImageByID($intFileID);
                                    }
                                } else {
                                    $instance->compressImageByID($arProp["VALUE"]);
                                }
                            }
                        }

                    }
                }
            }
        }
    }

    /**
     * Сжатие картинок на событии в сохранения в таблице
     */
    public static function CompressImageOnFileEvent(&$arFile, $strFileName, $strSavePath, $bForceMD5, $bSkipExt){
        $instance = self::getInstance();
        if(!$instance->enableSave) return;
        if ((!isset($arFile["MODULE_ID"]) || $arFile["MODULE_ID"] != "iblock")){
            if ($arFile["type"] == "image/jpeg" || $arFile["type"] == "image/png") {
                switch ($arFile["type"]) {
                    case 'image/jpeg' :
                        $isCompress = $instance->compressJPG($arFile["tmp_name"]);
                        break;
                    case 'image/png' :
                        $isCompress = $instance->compressPNG($arFile["tmp_name"]);
                        break;
                }
                if($isCompress) {
                    $arFile["size"] = filesize($arFile["tmp_name"]);
                }
            }
        }
    }

    /**
     *  delete picture
     */
    public static function CompressImageOnFileDeleteEvent($arFile){
//        var_dump($arFile);
//        die();
        ImageCompressTable::delete($arFile['ID']);
    }

    public static function CompressImageOnResizeEvent(
        $arFile,
        $arParams,
        &$callbackData,
        &$cacheImageFile,
        &$cacheImageFileTmp,
        &$arImageSize
    ) {
        $instance = self::getInstance();
        if(!$instance->enableResize) return;
        if ($arFile["CONTENT_TYPE"] == "image/jpeg" || $arFile["CONTENT_TYPE"] == "image/png") {
            switch ($arFile["CONTENT_TYPE"]) {
                case 'image/jpeg' :
                    $instance->compressJPG($cacheImageFileTmp);
                    break;
                case 'image/png' :
                    $instance->compressPNG($cacheImageFileTmp);
                    break;
            }
        }
    }

    public function queryBuilder($arOrder = array(), $arFilter = array())
    {
        global $DB;
        $arSqlSearch = array();
        $arSqlOrder = array();
        $strSqlSearch = $strSqlOrder = "";

        if(is_array($arFilter))
        {
            foreach($arFilter as $key => $val)
            {
                $key = strtoupper($key);

                $strOperation = '';
                if(substr($key, 0, 1)=="@")
                {
                    $key = substr($key, 1);
                    $strOperation = "IN";
                    $arIn = is_array($val)? $val: explode(',', $val);
                    $val = '';
                    foreach($arIn as $v)
                    {
                        $val .= ($val <> ''? ',':'')."'".$DB->ForSql(trim($v))."'";
                    }
                } elseif(substr($val, 0, 1) == ">"){
                    $val = substr($val, 1);
                    $strOperation = ">";
                    $arIn = is_array($val)? $val: explode(',', $val);
                    $val = '';
                    foreach($arIn as $v)
                    {
                        $val .= ($val <> ''? ',':'')."'".$DB->ForSql(trim($v))."'";
                    }
                } elseif(substr($val, 0, 1) == "<"){
                    $val = substr($val, 1);
                    $strOperation = "<";
                    $arIn = is_array($val)? $val: explode(',', $val);
                    $val = '';
                    foreach($arIn as $v)
                    {
                        $val .= ($val <> ''? ',':'')."'".$DB->ForSql(trim($v))."'";
                    }
                } else {
                    $val = $DB->ForSql($val);
                }

                if($val == '')
                    continue;

                switch($key)
                {
                    case "MODULE_ID":
                    case "ID":
                    case "EXTERNAL_ID":
                    case "SUBDIR":
                    case "FILE_NAME":
                    case "FILE_SIZE":
                    case "ORIGINAL_NAME":
                    case "CONTENT_TYPE":
                        if ($strOperation == "IN")
                            $arSqlSearch[] = "f.".$key." IN (".$val.")";
                        elseif($strOperation == ">")
                            $arSqlSearch[] = "f.".$key." > ".$val."";
                        elseif($strOperation == "<")
                            $arSqlSearch[] = "f.".$key." < ".$val."";
                        else
                            $arSqlSearch[] = "f.".$key." = '".$val."'";
                        break;
                    case "COMRESSED":
                        if($val == "Y")
                            $arSqlSearch[] = "tf.FILE_ID > 0";
                        else
                            $arSqlSearch[] = "tf.FILE_ID is NULL";
                        break;
                    case "COMPRESSED":
                        $arSqlSearch[] = "tf.COMPRESSED = $val";
                        break;
                }
            }
        }

        if(!empty($arSqlSearch))
            $strSqlSearch = " WHERE (".implode(") AND (", $arSqlSearch).")";

        if(is_array($arOrder))
        {
            static $aCols = array(
                "ID" => 1,
                "TIMESTAMP_X" => 1,
                "MODULE_ID" => 1,
                "HEIGHT" => 1,
                "WIDTH" => 1,
                "FILE_SIZE" => 1,
                "CONTENT_TYPE" => 1,
                "SUBDIR" => 1,
                "FILE_NAME" => 1,
                "ORIGINAL_NAME" => 1,
                "EXTERNAL_ID" => 1,
            );
            foreach($arOrder as $by => $ord)
            {
                $by = strtoupper($by);
                if(array_key_exists($by, $aCols))
                    $arSqlOrder[] = "f.".$by." ".(strtoupper($ord) == "DESC"? "DESC":"ASC");
            }
        }
        if(empty($arSqlOrder))
            $arSqlOrder[] = "f.ID ASC";
        $strSqlOrder = " ORDER BY ".implode(", ", $arSqlOrder);

        $strSql =
            "SELECT f.*, ".$DB->DateToCharFunction("f.TIMESTAMP_X")." as TIMESTAMP_X, tf.* " .
            "FROM b_file f ".
            "LEFT JOIN {$this->tableName} as tf ON f.ID = tf.FILE_ID".
            $strSqlSearch.
            $strSqlOrder;

        return $strSql;
    }

    public function getFileList($arOrder = array(), $arFilter = array(), $limit=100, $offset=0)
    {
        global $DB;

        $strSql = $this->queryBuilder($arOrder,$arFilter);

//        if($limit) {
//            $strSql .= ' LIMIT '.$limit;
//        }
//
//        if($offset) {
//            $strSql .= ' OFFSET '.$offset;
//        }

        $res = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

        return $res;
    }

    public function getNiceFileSize($fileSize, $digits = 2) {
        $sizes = array("TB", "GB", "MB", "KB", "B");
        $total = count($sizes);
        while ($total-- && $fileSize > 1024) {
            $fileSize /= 1024;
        }
        return round($fileSize, $digits) . " " . $sizes[$total];
    }

}
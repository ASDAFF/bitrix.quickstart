<?php

namespace Entity;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    CIBlockElement,
    Defa\Core\Tools\CIblock;


/**
 * Class PageDates
 * @package Entity
 */
class PageDates
{
    /**
     * @var null|PageDates
     */
    protected static $instance = null;
    protected $datePub = null;
    protected $dateEdit = null;
    protected $arFields = null;

    public function __construct()
    {
        if (Loader::includeModule('iblock')) {
            $rs = CIBlockElement::GetList(
                [],
                [
                    "IBLOCK_ID" => CIblock::GetIDByCode('output_pages'),
                    'ACTIVE'    => 'Y',
                    'NAME'      => $GLOBALS['APPLICATION']->GetCurDir(),
                ],
                false,
                ['nTopCount' => 1],
                ['ID', 'IBLOCK_ID', 'PROPERTY_DATE_PUB', 'PROPERTY_DATE_EDIT']
            );
            if ($arItem = $rs->Fetch()) {
                $this->arFields = $arItem;
                $this->setDatePub($arItem['PROPERTY_DATE_PUB_VALUE']);
                $this->setDateEdit($arItem['PROPERTY_DATE_EDIT_VALUE']);
            }
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function setDatePub($date)
    {
        if (empty($this->datePub)) {
            if (empty($date)) {
                return null;
            }
            try {
                $obDate = new \DateTime($date);
                $obNow = new \DateTime();
                if ($obDate > $obNow) {
                    return null;
                }
                $this->datePub = $obDate;
            } catch (\Exception $e) {
                return null;
            }
        }
        return $this->datePub;
    }

    public function getDatePub()
    {
        return $this->datePub;
    }

    public function getDateEdit()
    {
        return $this->dateEdit;
    }

    public function setDateEdit($date)
    {
        if (empty($this->dateEdit)) {
            if (empty($date)) {
                return null;
            }
            try {
                $obDate = new \DateTime($date);
                $obNow = new \DateTime();
                if ($obDate > $obNow) {
                    return null;
                }
                $this->dateEdit = $obDate;
            } catch (\Exception $e) {
                return null;
            }
        }
        return $this->dateEdit;
    }
}
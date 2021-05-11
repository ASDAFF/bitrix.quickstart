<?php
/**
 * Created by PhpStorm.
 * User: anastasiya.zayarnaya
 * Date: 27.08.2018
 * Time: 13:13
 */

namespace Entity;

use Bitrix\Highloadblock\HighloadBlockTable as HLBT,
    Bitrix\Main\Entity,
    Bitrix\Main\Loader,
    Bitrix\Main\Diag\Debug;


class HLNumbersContracts
{
    /** @var int */
    private $intHlID = 2;
    /** @var string */
    private $strDateTimeFormat = 'd.m.Y H:i:s';
    /** @var Entity\Base|null */
    private $entity = null;
    /** @var Entity\DataManager|null */
    private $objHL = null;
    /** @var HLSearchProgram */
    protected static $instance = null;

    /** @var string Путь к логам */
    private $strDirLog = '../logs/';
    /** @var string патерн для имени файла лога */
    private $strFileNameLogPattern = 'hl_contacts_#DATE#.log';

    private function log($data)
    {
        if (false) {
            return;
        }
        Debug::writeToFile(
            $data,
            date($this->strDateTimeFormat),
            $this->strDirLog . str_replace('#DATE#', date('Y-m-d'), $this->strFileNameLogPattern)
        );
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    private function __construct()
    {
        Loader::IncludeModule('highloadblock');
        $arHlBlock = HLBT::getById($this->intHlID)->fetch();
        $this->entity = HLBT::compileEntity($arHlBlock);
        $this->objHL = $this->entity->getDataClass();
        /**
         * UF_NUMBER
         */
    }

    public function prepareNumber($strNumber)
    {
        return preg_replace(
            '#[^0-9a-zа-я]*$#i',
            '',
            preg_replace(
                '#^[^0-9a-zа-я]*#i',
                '',
                $strNumber
            )
        );
    }

    public function checkNumber($strNumber)
    {
        $strNumber = $this->prepareNumber($strNumber);
        if (empty($strNumber)) {
            return false;
        }
        $arContract = $this->getList([
            'filter' => ['UF_NUMBER' => $strNumber],
            'limit'  => 1,
        ])->fetch();
        return isset($arContract['ID']);
    }

    public function alternativeCheckNumber($strNumber, $strRule)
    {
        $bResult = false;
        $sPattern = '/^[0-9a-zA-Z]+$/';
        $orgPattern = '/^[0-9][0-9a-zA-Z]{3}[0-9]+$/';
        $iContractLen = 14;
        $arFistElements = ['0', '1'];
        $arLastElements = ['08', '09', '10', '11', '12'];
        $sozPattern = '/^TSZ20[0-3]\d((0[1-9])|(1[012]))((0[1-9])|([1-2][0-9])|(3[01]))\d{3}$/i';

        switch ($strRule) {
            case 'digit_only':
                $bResult = is_numeric($strNumber);
                break;
            case 'digit_limit':
                if (is_numeric($strNumber) && strlen($strNumber) == $iContractLen) {
                    $bResult = (in_array(substr($strNumber, -2), $arLastElements) && in_array(substr($strNumber, 0, 1),
                            $arFistElements));
                } else {
                    $bResult = preg_match($sozPattern, $strNumber) ? true : false;
                }
                break;
            case 'digit_lat':
                $bResult = preg_match($sPattern, $strNumber) ? true : false;
                break;
            case 'digit_org':
                $bResult = preg_match($sPattern, $strNumber) ? true : false;
                break;
        }
        return $bResult;
    }

    public function addNumber($strNumber)
    {
        return $this->add(['UF_NUMBER' => $strNumber]);
    }

    public function add($arFields)
    {
        $arFields['UF_NUMBER'] = $this->prepareNumber($arFields['UF_NUMBER']);

        $entityClass = $this->objHL;
        $obResult = $entityClass::add($arFields);

        if ($obResult->isSuccess()) {
            $this->log(['MESSAGE' => 'Добавлен эллемент', 'DATA' => $arFields]);
            return $obResult;
        } else {
            $this->log(['MESSAGE' => 'Ошибка добавления', 'ERROR' => $obResult->getErrorMessages()]);
            return false;
        }
    }

    /**
     * Метод для очистчи
     * @return \Bitrix\Main\DB\Result
     */
    public function clearTable()
    {
        $this->log('Очистка таблицы');
        $connection = \Bitrix\Main\Application::getConnection();
        $obResult = $connection->truncateTable($this->entity->getDBTableName());

        if ($obResult) {
            $this->log(['MESSAGE' => 'База очищена']);
            return true;
        } else {
            $this->log(['MESSAGE' => 'Ошибка очистки базы', 'ERROR' => $obResult->getErrorMessages()]);
            return false;
        }
    }

    public function getList($arParams)
    {
        $entityClass = $this->objHL;
        return $entityClass::getList($arParams);
    }
}
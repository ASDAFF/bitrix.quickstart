<?php
namespace Ns\Bitrix\Helper\User;

/**
 *
 */
class CRUD extends \Ns\Bitrix\Helper\HelperCore
{
    const SALT_FOR_USER_DATA = "alfa";
    const DEFAULT_GROUP_ID = 5;

    private $defaultFields = array();
    private $arFields = array();

    public function Add() {
        global $USER;
        if ($USER->isAuthorized()) {
            return \CUser::GetID();
        }
        $this->arFields = array_merge($this->getFields(), $this->defaultFields);
        if (!$this->arFields["EMAIL"]) {
            throw new CRUDException("Please, set email for new user");
        }
        $u = \CUser::GetList($by = "ID", $order = "DESC", array("EMAIL" => $this->arFields["EMAIL"]))->Fetch();
        if ($u) {
            return $u["ID"];
        }
        try {
            $this->generate('LOGIN')->generate('PASSWORD')->generate('GROUP_ID');
        } catch (CRUDGenerateException $e) {
            prentExpection($e->getMessage());
        }
        $objUser = new \CUser;
        $newUserID = $objUser->Add($this->arFields);
        if (!intval($newUserID)) {
            throw new CRUDException("An error was occured while adding new User in CRUD: " . $objUser->LAST_ERROR . ". Line: " . __LINE__);
        } else {
            return $newUserID;
        }
    }

    public function UpdateProperties() {
        if (!$this->getUser()) {
            throw new CRUDException("Unexpected user ID");
        }
        if (!$this->getProperties()) {
            throw new CRUDException("Unexpected update information");
        }
        $objUser = new \CUser;
        $objUser->Update($this->getUser(), $this->getProperties());
        if ($objUser->LAST_ERROR) {
            throw new CRUDException($objUser->LAST_ERROR);
        } else {
            return True;
        }
    }


    private function generate($type=False) {
        if (!$arFields[$type]) {
            if ($type == 'LOGIN') {
                $this->arFields[$type] = $this->arFields["EMAIL"];
            } elseif ($type == 'PASSWORD') {
                $this->arFields[$type] = substr(md5($this->arFields["EMAIL"] . self::SALT_FOR_USER_DATA), 10, 6);
            } elseif ($type == 'GROUP_ID') {
                if ($this->getGroupId() > 0) {
                    $this->arFields[$type] = $this->getGroupId();
                } else {
                    $this->arFields[$type] = self::DEFAULT_GROUP_ID;
                }
            } else {
                throw new CRUDGenerateException("Unexpected type of data generating" . ". Line: " . __LINE__);
            }
        }
        return $this;
    }
}
<?php

class UserHelper
{
    /**
     * Get user by id
     *
     * @param integer $id
     * @return array
     */
    public static function getByID($id)
    {
        $rsUser = CUser::GetByID($id);
        $arUser = $rsUser->Fetch();
        return ($arUser["ID"]) ? $arUser : false;
    }

    /**
     * Get user by email
     *
     * @param string $email
     * @return array
     */
    public static function getByEmail($email)
    {
        $rsUser = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL" => $email));
        $arUser = $rsUser->Fetch();
        return ($arUser["ID"]) ? $arUser : false;
    }

    /**
     * Get user by login
     *
     * @param string $login
     * @return array
     */
    public static function getByLogin($login)
    {
        $rsUser = CUser::GetList(($by="id"), ($order="desc"), array("LOGIN_EQUAL" => $login));
        $arUser = $rsUser->Fetch();
        return ($arUser["ID"]) ? $arUser : false;
    }

    /**
     * Get only active users by email
     *
     * @param string $email
     * @return array
     */
    public static function getActiveByEmail($email)
    {
        $rsUser = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL" => $email ,"ACTIVE" => "Y"));
        $arUser = $rsUser->Fetch();
        return ($arUser["ID"]) ? $arUser : false;
    }

    /**
     * Get only active users by login
     *
     * @param string $email
     * @return array
     */
    public static function getActiveByLogin($login)
    {
        $rsUser = CUser::GetList(($by="id"), ($order="desc"), array("LOGIN" => $login ,"ACTIVE" => "Y"));
        $arUser = $rsUser->Fetch();
        return ($arUser["ID"]) ? $arUser : false;
    }

    /**
     * Check user exists in the system by id
     *
     * @param integer $id
     * @return boolean
     */
    public static function checkExistsByID($id)
    {
        return (self::getByID($id)) ? true : false;
    }

    /**
     * Check user exists in the system by email
     *
     * @param string $email
     * @return boolean
     */
    public static function checkExistsByEmail($email)
    {
        return (self::getByEmail($email)) ? true : false;
    }

    /**
     * Check user exists in the system by login
     *
     * @param string $login
     * @return boolean
     */
    public static function checkExistsByLogin($login)
    {
        return (self::getByLogin($login)) ? true : false;
    }

    /**
     * Check user exists in the system and user is active by email
     *
     * @param string $email
     * @return boolean
     */
    public static function checkActiveByEmail($email)
    {
        return (self::getActiveByEmail($email)) ? true : false;
    }

    /**
     * Check user exists in the system and user is active by email
     *
     * @param string $email
     * @return boolean
     */
    public static function checkActiveByLogin($login)
    {
        return (self::getActiveByLogin($login)) ? true : false;
    }

    /**
     * Generate random password for user
     *
     * @return string
     */
    public static function generateSimplePassword($length = 6)
    {
        return randString($length, array(
            "abcdefghijklnmopqrstuvwxyz",
            "ABCDEFGHIJKLNMOPQRSTUVWXYZ",
            "0123456789"
        ));
    }

    /**
     * Generate random password for user. 
     * Strong version.
     *
     * @return string
     */
    public static function generateStrongPassword($length = 6)
    {
        return randString($length, array(
            "abcdefghijklnmopqrstuvwxyz",
            "ABCDEFGHIJKLNMOPQRSTUVWX­YZ",
            "0123456789",
            "!@#\$%^&*()"
        ));
    }

    /**
     * Generate random confirmation code for user
     *
     * @return string
     */
    public static function generateConfirmCode($length)
    {
        return randString($length);
    }

    /**
     * Get subscription id by user's email
     *
     * @param string $email
     * @return array|boolean
     */
    public static function getSubscriptionByEmail($email)
    {
        if (!CModule::IncludeModule("subscribe")) {
            return false;
        }
        $rsSubscription = CSubscription::GetByEmail($email);
        $arSubscription = $rsSubscription->Fetch();
        return ($arSubscription["ID"]) ? $arSubscription : false;
    }

    /**
     * Check email is subscribed
     *
     * @param string $email
     * @return boolean
     */
    public static function checkSubscriptionExistByEmail($email)
    {
        return (self::getSubscriptionByEmail($email)) ? true : false;
    }

    /**
     * Subscribe user by email
     *
     * @global object $APPLICATION
     * @param string $email
     * @return array
     */
    public static function subscribe($email, $subscribeID, $siteID)
    {
        global $APPLICATION;
        $arResult["SUCCESS"] = true;
        $arFields = array(
            "EMAIL"        => $email,
            "FORMAT"       => "html",
            "ACTIVE"       => "Y",
            "RUB"          => $subscribeID,
            "SEND_CONFIRM" => "Y",
            "CONFIRM_CODE" => self::GenerateConfirmCode(8)
        );

        if (!CModule::IncludeModule("subscribe")) {
            $arResult["SUCCESS"] = false;
            $arResult["MESSAGE"] = "Модуль подписок не установлен на сайте";
        }

        $subscription = new CSubscription;
        $sId = $subscription->Add($arFields, $siteID);
        $sId = intval($sId);
        if ($sId > 0) {
            $arResult["ID"]      = $sId;
            $arResult["MESSAGE"] = "На указанный e-mail отправлено письмо для потверждения подписки";
        } else {
            $arResult["SUCCESS"] = false;
            $arResult["MESSAGE"] = strip_tags($subscription->LAST_ERROR);
        }

        return $arResult;
    }

    /**
     * Get checkword for password changing
     *
     * @global object $DB
     * @param integer $id
     * @param string $siteId
     * @return string
     */
    public static function getCheckword($id, $siteId)
    {
        global $DB;

        $id = intval($id);
        $salt = randString(8);
        $checkword = randString(8);

        $query = "UPDATE b_user SET ".
            "   CHECKWORD = '".$salt.md5($salt.$checkword)."', ".
            "   CHECKWORD_TIME = ".$DB->CurrentTimeFunction().", ".
            "   LID = '".$DB->ForSql($siteId, 2)."' ".
            "WHERE ID = '".$id."'".
            "   AND (EXTERNAL_AUTH_ID IS NULL OR EXTERNAL_AUTH_ID='') ";
        $DB->query($query, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);

        return $checkword;
    }

    /**
     * Checks user password
     *
     * @param integer $userId
     * @param string $password
     * @return boolean
     */
    public static function checkPassword($userId, $password)
    {
        $userData = CUser::GetByID($userId)->Fetch();

        $salt = substr($userData['PASSWORD'], 0, (strlen($userData['PASSWORD']) - 32));

        $realPassword = substr($userData['PASSWORD'], -32);
        $password = md5($salt.$password);

        return ($password == $realPassword);
    }
}
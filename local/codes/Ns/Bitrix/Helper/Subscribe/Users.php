<?php
namespace Ns\Bitrix\Helper\Subscribe;

use Ns\Bitrix\Helper\User\CRUDException;

/**
 *
 */
class Users extends \Ns\Bitrix\Helper\HelperCore
{
    const NEW_USER_SUBSCRIBE_GROUP = 7;
    const DATA_SALT = "mysaltalfa";
    const EVENT_TYPE = "SUBSCRIBE_LOGIN_LINK";


    private $user = array();

    public function Add($email='', $name='') {
        global $USER;
        if ($USER->isAuthorized()) {
            return $USER->getID();
        } else {
            try {
                $validator = \Ns\Bitrix\Helper::Create('iblock')->useVariant('validator');
            } catch (\Exception $e) {
                prentExpection($e->getMessage());
            }
            $this->user["email"] = $validator->email($email);
            $this->user["name"] = $validator->xss($name);
            if (!$this->user["email"]) {
                $e["ERROR"]["EMAIL"] = "Y";
            }
            if (!$this->user["name"]) {
                $e["ERROR"]["NAME"] = "Y";
            }
            if (is_array($e)) {
                return $e;
            } else {
                $user = \CUser::GetList($by = "ID", $order = "desc", array("EMAIL" => $this->user["email"]));
                if ($user->SelectedRowsCount() > 0) {
                    $this->user = $user->Fetch();
                    \ChromePhp::log($this->user);
                    try {
                        global $APPLICATION;
                        $this->sendEmailToLogin();
                    } catch (\Exception $e) {
                        prentExpection($e->getMessage());
                    }
                    return True;
                } else {
                    $arFields = array(
                        "EMAIL" => $this->user["email"],
                        "NAME" => $this->user["name"]
                    );
                    try {
                        return \Ns\Bitrix\Helper::Create('user')->useVariant('crud')->withFields($arFields)->withGroupId(self::NEW_USER_SUBSCRIBE_GROUP)->Add();
                    } catch (CRUDException $e) {
                        prentExpection($e->getMessage());
                    } catch (\Exception $e1) {
                        prentExpection($e1->getMessage());
                    }
                }
            }
        }
    }

    public function DeleteByLogin($login) {
        if (!$login) {
            throw new \Exception("Unexpected login", 1);
        } else {
            $user = \CUser::GetByLogin($login)->Fetch();
            $subscriber = \CSubscription::GetByEmail($user["EMAIL"])->Fetch();
            \CSubscription::Delete($subscriber["ID"]);
            return True;
        }
    }

    public function checkAuthLink($login, $hash) {
        if ($this->getHashByLogin($login) == $hash) {
            return True;
        } else {
            return Fasle;
        }
    }

    private function getHashByLogin($login) {
        return md5($login . self::DATA_SALT);
    }

    private function sendEmailToLogin() {
        $event = \CEvent::Send(
            self::EVENT_TYPE,
            SITE_ID,
            array(
                "AUTH_LINK" => $this->authLink(),
                "NAME" => $this->user["NAME"],
                "EMAIL" => $this->user["EMAIL"],
                "UNSUBSCRIBE_LINK" => $this->unsubscribeLink()
            )
        );
        \ChromePhp::log($event);
    }

    private function authLink() {
        //\ChromePhp::log($this->user);
        //\ChromePhp::log('adasd');
        return "http://".$_SERVER['HTTP_HOST']."/o_banke/press-center/?hash=" . $this->getHashByLogin($this->user["LOGIN"]) . "&login=" . $this->user["LOGIN"];
//        return \CMain::GetCurPageParam("hash=" . \CUser::GetPasswordHash($this->user["PASSWORD"]) . "&login=" . $this->user["LOGIN"], array("clear_cache"));
    }

    private function unsubscribeLink() {
        return "http://".$_SERVER['HTTP_HOST']."/o_banke/press-center/?hash=" . $this->getHashByLogin($this->user["LOGIN"]) . "&login=" . $this->user["LOGIN"] . "&unsubscribe=Y";
//        return \CMain::GetCurPageParam("hash=" . \CUser::GetPasswordHash($this->user["PASSWORD"]) . "&login=" . $this->user["LOGIN"] . "&unsubscribe=Y", array("clear_cache"));
    }
}
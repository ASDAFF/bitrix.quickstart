<?php
namespace Ns\Bitrix\Helper\Subscribe;


class CRUD extends  \Ns\Bitrix\Helper\HelperCore
{
    private $user = array();
    private $objSubscribtion = False;

    public function Add() {
        \ChromePhp::log("Subscr CRUD Add");
        if ($subscriber = $this->getSubscriber()) {
            $this->user = \CUser::GetByID($subscriber)->Fetch();
            \CUser::Authorize($this->user["ID"]);
            if ($subscribe = \CSubscription::GetList(False, array("ACTIVE" => "Y", "USER_ID" => $this->user["ID"]))->Fetch()) {
                return $subscribe["ID"];
            } else {
                try {
                    return $this->addSubscribe();
                } catch (SubscribeException $e) {
                    prentExpection($e->getMessage());
                }
            }
        } else {
            throw new SubscribeUserException("No user for add to subscribers" . ". Line: " . __LINE__);
        }
    }

    public function Update($subscribe=False) {
        if (!$subscribe) {
            throw new SubscribeException("Unexpected ID of subscribe." . __LINE__);
        } else {
            if ($this->objSubscribtion === False) {
                $this->objSubscribtion = new \CSubscription;
            }
            if ($this->objSubscribtion->Update($subscribe, array("RUB_ID" => $this->getRubrics()))) {
                return True;
            } else {
                throw new SubscribeException("An error wa occured: " . $this->objSubscribtion->LAST_ERROR . ". " . __LINE__);
            }
        }
    }

    private function addSubscribe() {
        if ($this->objSubscribtion === False) {
            $this->objSubscribtion = new \CSubscription;
        }
        $arFields = array(
            "USER_ID" => $this->user["ID"],
            "EMAIL" => $this->user["EMAIL"],
            "ACTIVE" => "Y",
            "CONFIRMED" => "Y",
            "SEND_CONFIRM" => "N"
        );
        if ($subscribe = $this->objSubscribtion->Add($arFields)) {
            return $subscribe;
        } else {
            throw new SubscribeException("An error was occured when add subscriber: " . $this->objSubscribtion->LAST_ERROR . ". Line: " . __LINE__);
        }
    }
}
<?php

class Import
{

    protected $fileFolder, $items = array();

    function getFromFile($name)
    {
        $path = $this->getTemplatesPath();
        include("$path/{$this->fileFolder}/{$name}");
        if (isset($arFields)) {
            return $arFields;
        }
    }

    function getFileList()
    {
        $items = array();
        $path = $this->getTemplatesPath();
        $dir = "$path/{$this->fileFolder}";
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $items[] = $this->getFromFile($file);
        }
        return $items;
    }

    function getTemplatesPath()
    {
        return $path = str_replace("//", "/", WIZARD_ABSOLUTE_PATH."/site/public/".LANGUAGE_ID."/bitrix_messages/views");
    }

    function existsRecord($message)
    {
        foreach ($this->items as $item) {
            if ($item['EVENT_NAME'] == $message['EVENT_NAME']) return $item['ID'];
        }
        return false;
    }
}

class ImportMessages extends Import
{

    protected $fileFolder = "message";

    function __construct()
    {
        $eventMessage = new CEventMessage();
        $getList = $eventMessage->GetList($by, $order);
        while ($data = $getList->Fetch()) {
            $this->items[] = $data;
        }
    }

    function insertMessage($message)
    {
        $emess = new CEventMessage;
        return $emess->Add(array(
            "EVENT_NAME" => $message['EVENT_NAME'],
            "ACTIVE" => $message['ACTIVE'],
            "LID" => WIZARD_SITE_ID,
            "EMAIL_FROM" => $message['EMAIL_FROM'],
            "EMAIL_TO" => $message['EMAIL_TO'],
            "SUBJECT" => $message['SUBJECT'],
            "MESSAGE" => $message['MESSAGE'],
            "BODY_TYPE" => $message['BODY_TYPE'],
        ));
    }

    function getSitesByMessage($ID)
    {
        $sites = array();
        $eventMessage = new CEventMessage();
        $getSites = $eventMessage->GetSite($ID);
        while ($site = $getSites->Fetch()) {
            $sites[] = $site['WIZARD_SITE_ID'];
        }
        return $sites;
    }

    function appendMessage($ID)
    {
        $LID = $this->getSitesByMessage($ID);
        $LID[] = WIZARD_SITE_ID;
        $em = new CEventMessage;
        $arFields = Array(
            "LID" => $LID,
        );
        return $em->Update($ID, $arFields);
    }

    function importList()
    {
        $getMessages = $this->getFileList();
        foreach ($getMessages as $message) {
            $ID = $this->existsRecord($message);
            if ($ID) {
                $getSites = $this->getSitesByMessage($ID);
                if (!in_array(WIZARD_SITE_ID, $getSites)) {
                    $this->appendMessage($ID);
                }
            } else {
                $this->insertMessage($message);
            }
        }
    }
}


class ImportMessagesType extends Import
{
    protected $fileFolder = "type";

    function __construct()
    {
        $eventMessage = new CEventType();
        $getList = $eventMessage->GetList();
        while ($data = $getList->Fetch()) {
            $this->items[] = $data;
        }
    }

    function insertType($message)
    {
        $et = new CEventType;
        return $et->Add(array(
            "LID" => $message['LID'],
            "EVENT_NAME" => $message['EVENT_NAME'],
            "NAME" => $message['NAME'],
            "DESCRIPTION" => $message['DESCRIPTION'],
        ));
    }

    function importList()
    {
        $getMessages = $this->getFileList();
        foreach ($getMessages as $message) {
            if ($this->existsRecord($message)) {
                /*nothing*/
            } else {
                $this->insertType($message);
            }
        }
    }
}
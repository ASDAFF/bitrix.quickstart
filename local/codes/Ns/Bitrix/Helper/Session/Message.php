<?php
namespace Ns\Bitrix\Helper\Session;

/**
 *
 */
class Message extends \Ns\Bitrix\Helper\HelperCore
{
    public function setFlashMessage($text)
    {
        $_SESSION["FLASHMESSAGES"][] = $text;
        return False;
    }

    public function getFlashMessage($delete = True) {
        $result = '';
        foreach ($_SESSION["FLASHMESSAGES"] as $msg) {
            $result .= $msg . "<br/>";
        }
        if ($delete) {
            unset($_SESSION["FLASHMESSAGES"]);
        }
        return $result;
    }

}


?>
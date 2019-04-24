<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Form\ResultAnswerTable;

/**
 * Представляет обработчик команды, для проверки наличия подписки пользователя.
 */
class CheckSubscriptionAvailabilityHandler extends BaseHandler {

    private $_email;

    /**
     * Инициализирует объект класса CheckSubscriptionAvailabilityHandler.
     * @param $request
     */
    function __construct($request) {
        $this->_email = $request->getPost("email");
    }

    /**
     * Обрабатывает запрос.
     * @return bool
     */
    public function Execute() {
        
        if ($this->_email === false) {
            return false;
        }

        require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/form/lib/resultanswer.php";

        $result = ResultAnswerTable::getList([
            "select" => ["*"],
            "filter"  => ["FORM_ID" => 2, 'FIELD_ID' => 4, 'USER_TEXT' => $this->_email]
        ])->fetchAll();

        echo json_encode(["subscriptionAvailability" => count($result) > 0]);

        return true;
    }
}
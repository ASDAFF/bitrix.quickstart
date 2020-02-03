<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Server;
use Bitrix\Main\HttpRequest;

/**
 * Представляет обработчик запроса, на полчуня html кода компонента.
 */
class ComponentReloadHandler extends BaseHandler
{
    private $name;
    private $template;
    private $parameters;
    private $parentComponent;
    private $arFunctionParams;
    private $requestUri;
    private $page;

    /**
     * Инициализирует обработчик ComponentReloadHandler.
     *
     * @param Bitrix\Main\HttpRequest $request
     */
    public function __construct($request)
    {
        $this->name = $request->getPost("name");
        $this->template = $request->getPost("template");
        $this->parameters = $request->getPost("parameters");
        $this->parentComponent = (object)$request->getPost("parentComponent");
        $this->requestUri = $request->getPost("requestUri");
        $this->page = $request->getPost("PAGEN_1");
    }

    /**
     * Выполняет запрос, на получение html кода компонента.
     *
     * @return void
     */
    public function Execute() {
        global $APPLICATION;

        $_SERVER["REQUEST_URI"] = $this->requestUri ? $this->requestUri : $_SERVER["REQUEST_URI"];
        if (substr($_SERVER["REQUEST_URI"], -4) === ".php") {
            $_SERVER["SCRIPT_NAME"] = $_SERVER["REQUEST_URI"];
        } else {
            $_SERVER["SCRIPT_NAME"] = $_SERVER["REQUEST_URI"] . "index.php";
        }
        
        // Для старого ядра.
        $APPLICATION = new CMain();

        // Для D7.
        $_POST["PAGEN_1"] = $this->page;
        $_GET["PAGEN_1"] = $this->page;
        $server = new Server($_SERVER);
        $request = new HttpRequest($server, $_GET, $_POST, $_FILES, $_COOKIE);
        Bitrix\Main\Context::getCurrent()->initialize($request, null, $server);

        try {
            $APPLICATION->IncludeComponent(
                $this->name,
                $this->template,
                $this->parameters,
                $this->parentComponent,
                $this->arFunctionParams
            );
        } catch (Exception $e) {
            return false;
        }
        
        return true;
    }
}
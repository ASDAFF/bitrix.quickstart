<?php
namespace Helper;

/**
 * Представляет базовый класс для обработчичков.
 */
abstract class BaseHandler {

    /**
     * BaseHandler constructor.
     * @param $request
     */
    abstract function __construct($request);

    /**
     * Обрабатывает запрос.
     * @return bool
     */
    abstract function Execute();
}
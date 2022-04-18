<?php

namespace DigitalWand\MVC;

/**
 * Trait ActionTrait
 * @package DigitalWand\MVC
 *
 * @property array $arResult;
 * @property array $arParams;
 *
 * Трейт предназначен для использования в Actions. Он позволяет внутри экшена обращаться к методам и переменным компонента,
 * как к своим собственным.
 * Формат трейта выбран для того, чтобы не ограничивать программиста в возможностях во время проектирования. Т.е. разработчик
 * может превратить в action любой класс, независимо от того. какие интерфейсы он реализует и от каких классов наследуется.
 * Чтобы action работал. он должен содержать этот трейт.
 */
trait ActionTrait
{
    /** @var \CBitrixComponent $component */
    private $component;

    /**
     * @param \CBitrixComponent $component
     * @deprecated
     * Сохраняем текущий компонент внутри action, чтобы потом обращаться к его методам.
     * Не нужно использовать эту функцию в своём коде, связь с компонентом устанавливается автоматически при создании объекта.
     */
    public function setComponent(\CBitrixComponent &$component)
    {
        $this->component = $component;
    }

    function __call($name, $arguments)
    {
        return call_user_func_array(array($this->component, $name), $arguments);
    }

    function &__get($name)
    {
        return $this->component->$name;
    }

    function __set($name, $value)
    {
        $this->component->$name = $value;
    }

}
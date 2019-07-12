<?php

class Novagroup_Classes_General_Handler extends Novagroup_Classes_Abstract_Handler {

    function OnEpilogEventAddHandler()
    {
        /*
         * тут можно дополнительно вставить условие, на каких страницах подключать скрипты
         */
        CBitrixComponent::includeComponentClass("novagr.shop:catalog.element");
        $component = new NovagroupCatalogElement();
        $component->AddHeadScripts();

        CBitrixComponent::includeComponentClass("novagroup:catalog.timetobuy");
        $component = new NovagroupCatalogTimeToBuy();
        $component->AddHeadScripts();

        parent::OnEpilogEventAddHandler();
    }
}
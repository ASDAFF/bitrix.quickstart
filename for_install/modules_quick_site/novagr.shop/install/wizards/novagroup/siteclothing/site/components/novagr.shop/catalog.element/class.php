<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 25.01.14
 * Time: 0:23
 */

class NovagroupCatalogElement extends CBitrixComponent {

    function __construct($component = null)
    {
        self::AddHeadScripts();
        return parent::__construct($component);
    }

    public function AddHeadScripts()
    {
        global $APPLICATION;
        $APPLICATION->AddHeadScript('/local/components/novagr.shop/catalog.element/js/pSubscribe.js');
    }
} 
<?php

abstract class Novagroup_Classes_Abstract_CatalogRecommend extends Novagroup_Classes_Abstract_CatalogOffers
{
    protected $arSelect = array(
        'IBLOCK_ID',
        "SECTION_ID",
        'ID',
        'NAME',
        'DETAIL_PAGE_URL',
        'PROPERTY_VENDOR.NAME',
    );
}
<?

$getOptGroup = Novagroup_Classes_General_CatalogPrice::getOptGroup();
$getOptUserGroup = Novagroup_Classes_General_CatalogPrice::getOptUserGroup();
$getBaseGroup = Novagroup_Classes_General_CatalogPrice::__getBaseGroup();

// price for default
if(is_array($getBaseGroup)){
    $arParams['CATALOG_PRICE'] = $arParams['GROUP_PRICE']['default']['PRICE'] = "CATALOG_PRICE_".(int)$getBaseGroup['ID'];
    $arParams['CATALOG_GROUP'] = $arParams['GROUP_PRICE']['default']['GROUP'] = "CATALOG_GROUP_".(int)$getBaseGroup['ID'];
}
// example special price for usergoup 8 (opt price)
if(is_array($getOptGroup) and is_array($getOptUserGroup)){
    $optUserGroupID = (int)$getOptUserGroup['ID'];
    $arParams['GROUP_PRICE'][$optUserGroupID]['PRICE'] = "CATALOG_PRICE_".(int)$getOptGroup['ID'];
    $arParams['GROUP_PRICE'][$optUserGroupID]['GROUP'] = "CATALOG_GROUP_".(int)$getOptGroup['ID'];
}

if(is_array($arParams['GROUP_PRICE'] ))
{
    global $USER;
    $UserGroups = $USER -> GetUserGroup($USER -> GetId() );
    // simple set price for usergroup
    foreach($arParams['GROUP_PRICE'] as $key => $val)
    {
        // simple
        if( in_array( $key, $UserGroups ) )
        {
            $arParams['CATALOG_PRICE'] = $val['PRICE'];
            $arParams['CATALOG_GROUP'] = $val['GROUP'];
        }
    }
}

?>
<?php
// Инициализация JS-объекта для Яндекс.Карты
$arResult["YAMAPS_DATA"] = array();
if (!empty($arResult["SECTIONS"])) {
    foreach ($arResult["SECTIONS"] as $arSection) {
        if ($arSection["ID"] == 132) {
            continue;
        }
        
        if (!empty($arSection["ITEMS"])) {
            foreach ($arSection["ITEMS"] as $arItem) {
                $arResult["YAMAPS_DATA"][] = array(
                    "id" => (int)$arItem["ID"],
                    "name" => $arItem["NAME"],
                    "address" => $arItem["PROPERTIES"]["ADDRESS"]["VALUE"],
                    "phone" => $arItem["PROPERTIES"]["PHONE"]["VALUE"],
                    "fax" => $arItem["PROPERTIES"]["FAX"]["VALUE"],
                    "email" => $arItem["PROPERTIES"]["EMAIL"]["VALUE"],
                    "site" => $arItem["PROPERTIES"]["SITE"]["VALUE"],
                    "coords" => array(
                        "x" => round((float)$arItem["PROPERTIES"]["X"]["VALUE"], 2),
                        "y" => round((float)$arItem["PROPERTIES"]["Y"]["VALUE"], 2),
                    ),
                    "link" => $arItem["PROPERTIES"]["LINKY"]["VALUE"],
                );
            }
        }
    }
}
?>

<script>var obYAMap = <?php echo json_encode($arResult["YAMAPS_DATA"]); ?>;</script>
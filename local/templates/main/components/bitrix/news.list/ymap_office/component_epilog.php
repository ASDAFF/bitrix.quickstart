<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
?>
<!--<script src="https://api-maps.yandex.ru/2.1/?load=package.full&amp;lang=ru-RU" type="text/javascript"></script>-->
<script type="text/javascript">
    ymaps.ready(init);

    function init() {

        // Создадим карту, на которой необходимо кластеризовать геообъекты.
        var map = new ymaps.Map("map", {
            center: [55.753215, 37.622504],
            zoom: 10,
            controls: []
        });
        map.behaviors.disable('scrollZoom');

        map.controls.add("zoomControl", {
            position: {top: 245, right: 15}
        });

        // Создадим массив геообъектов.
        myGeoObjects = [];
        <?
        $i = 0;
        foreach($arResult["ITEMS"] as $arItem){ ?>
                myGeoObjects[<?=$i?>] = new ymaps.GeoObject({
                        geometry: {
                            type: "Point",
                            coordinates: [<?=$arItem['PROPERTIES']['X']['~VALUE']?>, <?=$arItem['PROPERTIES']['Y']['~VALUE']?>]
                        },
                        properties: {
                            iconContent: 'Кузов',
                            balloonContentBody: '<div style = "" ><?if(strlen($arItem['PROPERTIES']['ADDRESS']['~VALUE']) > 0){?> <?=$arItem['PROPERTIES']['ADDRESS']['~VALUE']?>  <br/><?}?><?/*if(strlen($arItem['PROPERTIES']['PHONE']['~VALUE']) > 0){*/?>Телефон: <?= implode(",", $arItem['PROPERTIES']['PHONE']['~VALUE'])?> <br/><?/*}*/?> <?if(strlen($arItem['PROPERTIES']['FAX']['~VALUE']) > 0){?>Факс: <?=$arItem['PROPERTIES']['FAX']['~VALUE']?> <?}?></div>'
                        }
                    },
                    {
                        iconLayout: 'default#image',
                        iconImageHref: '<?=$templateFolder . '/images/map.png'?>',
                        iconImageSize: [58, 80],
                        iconImageOffset: [-24, -80]
                    }
                );
                map.geoObjects.add(myGeoObjects[<?=$i?>]);
                <?
                $i++;

        }
        ?>
        //console.log(myGeoObjects);
        //map.geoObjects.add(myGeoObjects);
    }
</script>
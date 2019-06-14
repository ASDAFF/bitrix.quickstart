<?php
$moduleName = "altasib.starterkit";
CJSCore::RegisterExt("as_colorbox",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/colorbox/jquery.colorbox-min.js",
        "css" => "/bitrix/js/" . $moduleName . "/colorbox/colorbox.css",
    )
);

CJSCore::RegisterExt("as_fancybox",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/fancybox/jquery.fancybox.pack.js",
        "css" => "/bitrix/js/" . $moduleName . "/fancybox/jquery.fancybox.css",
    )
);

CJSCore::RegisterExt("as_flexslider",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/flexslider/jquery.flexslider-min.js",
        "css" => "/bitrix/js/" . $moduleName . "/flexslider/flexslider.css",
    )
);

CJSCore::RegisterExt("as_formstyler",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/formstyler/jquery.formstyler.min.js",
        "css" => "/bitrix/js/" . $moduleName . "/formstyler/jquery.formstyler.css",
    )
);

CJSCore::RegisterExt("as_maskedinput",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/maskedinput/jquery.maskedinput.min.js",
    )
);

CJSCore::RegisterExt("as_owlcarousel",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/owl-carousel/owl.carousel.min.js",
        "css" => "/bitrix/js/" . $moduleName . "/owl-carousel/owl.carousel.css",
    )
);

CJSCore::RegisterExt("as_waitwindow",
    Array(
        "js" => "/bitrix/js/" . $moduleName . "/waitwindow/waitwindow.js",
        "css" => "/bitrix/js/" . $moduleName . "/waitwindow/waitwindow.css",
    )
);
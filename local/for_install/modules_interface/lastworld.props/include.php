<?php
CModule::AddAutoloadClasses(
    "lastworld.props",
    array(
        //Helpers
        'LastWorld\\Helper\\CLWLinkHelper' => 'lib/helper/CLinkHelper.php',
        //Props
        'LastWorld\\Property\\CYouTubeProperty' => 'lib/property/CYouTubeProperty.php',
        'LastWorld\\Property\\CColorProperty' => 'lib/property/CColorProperty.php'
        //Clouds
        //'LastWorld\\Cloud\\CYandexDiskCloud' => 'lib/cloud/CYandexDiskCloud.php'
    )
);

CJSCore::RegisterExt('lwcolor', array(
    'js' => '/bitrix/js/lastworld.props/LWColor.js',
    'css' => '/bitrix/css/lastworld.props/LWColor.css',
    'rel' => array("jquery"),
    'skip_core' => true
));
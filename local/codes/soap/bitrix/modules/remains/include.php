<?php
CModule::AddAutoloadClasses(
        "remains", array(
            "availability"  => "classes/general/availability.php",
            "matching"      => "classes/general/matching.php", 
            "mytpl"         => "classes/general/mytemplates.php",
            "remainUpdater" => "classes/general/updater.php",
            "remainsLog"    => "classes/general/log.php",
            "remainsHelper"    => "classes/general/remains.php"
        ) 
);
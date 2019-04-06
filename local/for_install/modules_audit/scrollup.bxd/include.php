<?php CModule::AddAutoloadClasses('scrollup.bxd', array('Cbxd' => 'classes/Cbxd.php'));


function __($__, $inPlace = false, $saveToFile = false) {
    $debugGroups = explode(",", COption::GetOptionString("scrollup.bxd", "SBXD_GROUPS", ""));
    if(CSite::InGroup($debugGroups)){
        return call_user_func_array(
            array('Cbxd', 'debug'), array($__, $inPlace, $saveToFile)
        );
    }
}

function _l() {
    $debugGroups = explode(",", COption::GetOptionString("scrollup.bxd", "SBXD_GROUPS", ""));
    if(CSite::InGroup($debugGroups)){
        $_ = func_get_args();
        return call_user_func_array(
            array('Cbxd', 'l'), $_
        );
    }
}

function _c() {
    $debugGroups = explode(",", COption::GetOptionString("scrollup.bxd", "SBXD_GROUPS", ""));
    if(CSite::InGroup($debugGroups)){
        $_ = func_get_args();
        return call_user_func_array(
            array('Cbxd', 'c'), $_
        );
    }
}
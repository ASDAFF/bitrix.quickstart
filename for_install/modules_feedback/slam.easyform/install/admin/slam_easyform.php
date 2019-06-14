<?
if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/slam.easyform/admin/slam_easyform.php")) {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/modules/slam.easyform/admin/slam_easyform.php");
} else {
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/slam.easyform/admin/slam_easyform.php");
}
?>
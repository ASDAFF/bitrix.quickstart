<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $sUploadDirectory = $_SERVER['DOCUMENT_ROOT'].'/upload/startshop';

    $sFileName = $_GET['filename'];
    $sFileData = file_get_contents('php://input');
    $sFilePath = $sUploadDirectory.'/'.$sFileName;
    $sFileDirectory = dirname($sFilePath);

    if (!empty($sFileName) && !empty($sFileData) && !empty($sCookieValue)) {
        if (!is_dir($sFileDirectory))
            mkdir($sFileDirectory, 0777, true);

        if (is_dir($sFileDirectory)) {
            file_put_contents($sFilePath, $sFileData);

            if (is_file($sFilePath)) {
                echo "success\n";
            } else {
                echo "failure\n";
            }
        } else {
            echo "failure\n";
        }
    } else {
        echo "failure\n";
    }
?>
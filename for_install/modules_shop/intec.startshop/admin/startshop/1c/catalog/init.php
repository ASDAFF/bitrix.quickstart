<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
    $bUseZip = false;
    $sFileSize = CStartShopVariables::Get("1C_EXCHANGE_FILE_SIZE", null);

    if (!empty($sFileSize) && is_numeric($sFileSize)) {
        echo "zip=".($bUseZip ? "yes" : "no")."\n";
        echo "file_limit=".$sFileSize."\n";
    } else {
        echo "failure\n";
    }
?>
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
function getImageUploaderId()
{
    static $iIndexOnPage = 0;
    $iIndexOnPage++;
    return 'bx_feedback_'.$iIndexOnPage;
}

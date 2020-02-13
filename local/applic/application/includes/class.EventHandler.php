<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/* ------------------------------------------------------------------------------------------
    ADD EVENTS HANDLERS
------------------------------------------------------------------------------------------ */

/**
 * Generates 404 Page
 */
AddEventHandler("main", "OnEpilog", array("EventHandler", "OnEpilogHandler"));

/* ------------------------------------------------------------------------------------------
    HANDLERS
------------------------------------------------------------------------------------------ */

class EventHandler
{
    function OnEpilogHandler()
    {
        if (!defined('ADMIN_SECTION') && defined("ERROR_404") &&
            defined("PATH_TO_404") && file_exists($_SERVER["DOCUMENT_ROOT"] . PATH_TO_404)) {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            CHTTP::SetStatus("404 Not Found");

            include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/header.php");
            include($_SERVER["DOCUMENT_ROOT"] . PATH_TO_404);
            include($_SERVER["DOCUMENT_ROOT"] . SITE_TEMPLATE_PATH . "/footer.php");
        }
    }
}
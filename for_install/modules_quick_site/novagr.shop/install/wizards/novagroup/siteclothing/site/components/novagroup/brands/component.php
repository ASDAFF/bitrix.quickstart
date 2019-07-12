<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$template = "list";
if ($_REQUEST['abc'] == "1")
    $template = "abc";
if ($_REQUEST['id'] > 0)
    $template = "detail";

$this->IncludeComponentTemplate($template);
?>
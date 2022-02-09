<?   
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    
CUser::Authorize(1);

LocalRedirect('/bitrix/');
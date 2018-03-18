<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?><?
global $MESS;
$MESS["SALE_CHR_REC_ORDER"] = "ID ".GetMessage("PAYU_PAYU_ZAKAZA_NEIZVESTEN");
$MESS["SALE_CHR_REC_SUMM"] = GetMessage("PAYU_PAYU_NEVERNYE_DANNYE_SUM");
$MESS["SALE_CHR_REC_TRANS"] = GetMessage("PAYU_PAYU_NEVERNYE_DANNYE_NEV");
$MESS["SALE_CHR_REC_SIGN"] = GetMessage("PAYU_PAYU_NEVERNYE_DANNYE_POD");
$MESS["SALE_CHR_REC_PRODUCT"] = GetMessage("PAYU_PAYU_NEVERNYE_DANNYE_PRO");
?>
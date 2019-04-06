<?
$MESS["IBLOCK_PROP_SOCIALMEDIAPOSTER_DESC"] = "Defa: Publish to social networks";
$MESS["SOCIALMEDIAPOSTER_SETTING_TITLE"] = "Social networks publication properties";
$MESS["SOCIALMEDIAPOSTER_ENTITIES_SETTING_TITLE"] = "Social networks properties";
$MESS["ERROR_IBLOCK_PROP_SOCIALMEDIAPOSTER_IS_MULTIPLE"] = "Property with type &laquo;#TYPE#&raquo; can not be multiple";
$MESS["ERROR_IBLOCK_PROP_SOCIALMEDIAPOSTER_PROPERTY_EXISTS"] = "Property with type &laquo;#TYPE#&raquo; also exists in this iblock";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_LOGIN"] = "User login";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PASSWORD"] = "User password";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID"] = "Group/page ID";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PHONE_4DIGITS"] = "Last 4 digits of phone";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_LOGIN_DESC"] = "User login (group/page owner)";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PASSWORD_DESC"] = "User password (group/page owner)";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC"] = "Group/page ID";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_VKONTAKTE"] = "Group/page ID.<br />Example: <em>31234567</em>.<br />To create group/page click this <a href=\"http://vkontakte.ru/public.php?act=new\" target=\"_blank\">link</a> and follow wizard.";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PHONE_4DIGITS_DESC_VKONTAKTE"] = "Last 4 digits of your account tied to your phone.<br />May be required to verify the user when they log in soc. network.<br />Example: <em>1234</em>.";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_ODNOKLASSNIKI"] = "Group ID.<br />Example: <em>51234567890123</em>.<br />To create group click this <a href=\"http://odnoklassniki.ru/groups\" target=\"_blank\">link</a>, press the button <strong>&laquo;Create group&raquo;</strong> and follow wizard.";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_FACEBOOK"] = "Group/page ID.<br />Example: <em>123456789012345</em>.<br />To create group/page click this <a href=\"http://www.facebook.com/pages/create.php\" target=\"_blank\">link</a> and follow wizard.";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_FACEBOOK_PAGE_OR_GROUP"] = "Community type";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_DESC_FACEBOOK_PAGE_OR_GROUP"] = "Specify community type";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_FACEBOOK_GROUP"] = "Group";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_FACEBOOK_PUBLIC_PAGE"] = "Public page";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_LIVEJOURNAL"] = "Community name";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_LIVEJOURNAL"] = "Community account name.<br />Example: <em>mycommunity</em>.<br />To create community click this <a href=\"http://www.livejournal.com/community/create.bml\" target=\"_blank\">link</a> and follow wizard.<br><br><strong>Set empty to publish to personal blog.</strong>";


$MESS["SOCIALMEDIAPOSTER_SETTING_TEMPLATES_TITLE"] = "Template settings of published material";

$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME"] = "Name";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_PREVIEW_TEXT"] = "Preview text";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_DETAIL_TEXT"] = "Detail text";

$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"] = "<br><br>Also available <a href=\"http://dev.1c-bitrix.ru/api_help/main/general/constants.php\" target=\"_blank\">special constants</a>, <a href=\"http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getlist.php\" target=\"_blank\">iblock element fields</a> and all element properties like: <br><br><strong>PROPERTY_AUTHOR_NAME</strong> &mdash; property name, <br><strong>PROPERTY_AUTHOR_VALUE</strong> &mdash; property value. <br><br>Instead of character property codes, you can use the of their numeric IDs.";

$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME_DESC"] = "Publishing material name template. <br>Example: <br><strong>New article added: #NAME#</strong>.";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_PREVIEW_TEXT_DESC"] = "Publishing material preview text template. <br>Example: <br><strong>New article added into iblock &laquo;#IBLOCK_NAME#&raquo;: #PREVIEW_TEXT#</strong>.";
$MESS["SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_DETAIL_TEXT_DESC"] = "Publishing material detail text template. <br>Example: <br><strong>New article added into iblock &laquo;#IBLOCK_NAME#&raquo; on site #SITE_SERVER_NAME# (#HOST#): #ACTIVE_FROM# #DETAIL_TEXT#</strong>.";

$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_CHECK_DATES"] = "Publish only the currently active elements";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_CHECK_DATES_DESC"] = "If the check is set, the publication will be tested fields <em> «date aktivnoesti»</em> and <em>«Deadline Activity»</em>. Will be published only in the active elements of dates. <br> <br> <strong> For Developers: </strong><br> analog field <em>ACTIVE_DATE</em>";

$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE"] = "Data type";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE_TEXT"] = "Article";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE_PHOTO"] = "Photo";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_POST_TYPE_VIDEO"] = "Video";

$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_SEND_NOW"] = "Publish now";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_SEND_ALL"] = "Publish selected";

$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_CURL_TITLE"] = "PHP cURL module";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_CURL_GREEN"] = "installed";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_CURL_RED"] = "not installed";

$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_BX_CRONTAB_TITLE"] = "BX_CRONTAB constant";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_BX_CRONTAB_GREEN"] = "not defined";
$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_BX_CRONTAB_RED"] = "is defined";

$MESS["SOCIALMEDIAPOSTER_SETTING_PROPERTY_DIAGNOSTICS_MANUAL_LINK"] = "More info";


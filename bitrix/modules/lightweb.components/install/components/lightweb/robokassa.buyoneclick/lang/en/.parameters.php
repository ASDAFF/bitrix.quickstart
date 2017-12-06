<?
// Configure store ROBOKASSA
$MESS ['RK_BOC_GROUP_STORAGE_OPTION']='Shop';
$MESS ['RK_BOC_PAYMENT_OPTIONS']='Settings podkluchenie to the store payment system "Robokassa"';
$MESS ['RK_BOC_PAYMENT_TEST_MODE']='test Mode';
$MESS ['RK_BOC_STORAGE_LOGIN']='Login';
$MESS ['RK_BOC_STORAGE_PAYMENT_PASSWORD']='Payment password';
$MESS ['RK_BOC_STORAGE_PAYMENT_PASSWORD_2']='Payment password 2';
$MESS ['RK_BOC_HIDDEN']='Hidden';
$MESS ['RK_BOC_SAVE_BUTTON']='Save changes';
$MESS ['RK_BOC_SAVE_MESSAGE']='Settings have been saved and hidden for security reasons';
$MESS ['RK_BOC_ONSUCCESS']='connection Settings saved';
$MESS ['RK_BOC_ONFAILURE']='Error while saving. The connection settings are not saved';


// Adjust store orders
$MESS ['RK_BOC_GROUP_STORAGE_PRODUCTS_OPTION'] = "Catalog";
$MESS ['RK_BOC_PROPERTY_PRODUCTS_IBLOCK_TYPE_ID']='Type is "Catalog"';
$MESS ['RK_BOC_PROPERTY_PRODUCTS_IBLOCK_ID']='IB "Catalog"';
$MESS ['RK_BOC_PROPERTY_PRODUCTS_COST']='Property: "product Price"';
$MESS ['RK_BOC_PROPERTY_PRODUCTS_DESCRIPTION']='Property for a description of the goods, which is sent to the user after purchase';

// Adjust store orders
$MESS ['RK_BOC_GROUP_STORAGE_ORDER_OPTION'] = "Store order";
$MESS ['RK_BOC_PROPERTY_ORDER_IBLOCK_TYPE_ID']='Type IB for the registration of orders';
$MESS ['RK_BOC_PROPERTY_ORDER_IBLOCK_ID']='IB for registration of the order';
$MESS ['RK_BOC_PROPERTY_ORDER_PRODUCT_ID']='Property to store: ID of the article';
$MESS ['RK_BOC_PROPERTY_ORDER_SUM']='Property to store: order amount';
$MESS['RK_BOC_PAID_PROP_NAME'] = 'Property to store: the payment status of the order';
$MESS['RK_BOC_CUSTOMER_PROP_NAME'] = 'Property to store: the name of the purchaser';
$MESS['RK_BOC_CUSTOMER_PHONE_PROP_NAME'] = 'Property to store phone buyer';
$MESS['RK_BOC_CUSTOMER_EMAIL_PROP_NAME'] = 'Property to store email.mail buyer';
$MESS['RK_BOC_CUSTOMER_MESSAGE_PROP_NAME'] = 'a Property to store the messages of the buyer';
$MESS['RK_BOC_ORDER_PASSWORD_PROP_NAME'] = 'a Property to store the one-time password';

// Setup window ordering
$MESS ['RK_BOC_GROUP_ORDERING_OPTION']='checkout';
$MESS ['RK_BOC_USED_FIELDS'] = "Fields";
$MESS ['RK_BOC_REQUIRED_FIELDS'] = "required fields";
$MESS ['RK_BOC_NAME'] = "Name";
$MESS ['RK_BOC_PHONE'] = "Phone number";
$MESS ['RK_BOC_EMAIL'] = "El. mail";
$MESS ['RK_BOC_MESSAGE'] = "Message";
$MESS ['RK_BOC_FORM_NAME'] = "form Name";
$MESS ['RK_BOC_FORM_ID'] = "form ID";
$MESS ['RK_BOC_DEFAULT_BUTTON_NAME'] = "Buy";
$MESS ['RK_BOC_BUTTON_NAME'] = "Name of button";

// Setup the notification about the order
$MESS ['RK_BOC_GROUP_NOTIFICATION_OPTION']='order Notification';
$MESS ['RK_BOC_EMAIL_ADMINISTRATOR'] = "El. mail administrator";
$MESS ['RK_BOC_EMAIL_TEMPLATES_FOR_ADMINISTRATOR'] = "Email templates for notifying the administrator";
$MESS ['RK_BOC_EMAIL_TEMPLATES_FOR_CUSTOMER'] = "Email templates for the notification of the buyer";

// Post event (additional fields)
$MESS ['RK_BOC_EMAIL_CUSTOMER'] = "El. mail buyer";
$MESS ['RK_BOC_ORDER_ID'] = "Order ID";
$MESS ['RK_BOC_PRODUCT_ID'] = "Product ID";
$MESS ['RK_BOC_PRODUCT_DESCRIPTION'] = "product Description";
$MESS ['RK_BOC_ORDER_SUM'] = "order Amount";
$MESS ['RK_BOC_ORDER_NAME'] = "Name of order";
$MESS ['RK_BOC_EVENT_TYPE_NAME'] = "Notification of order of goods (RoboKassa. Buy in one click)";
$MESS ['RK_BOC_DEFAULT_EMAIL_FROM'] = "E-Mail address by default (set in settings)";
$MESS ['RK_BOC_SITE_NAME'] = "site Name (set in settings)";
$MESS ['RK_BOC_SERVER_NAME'] = "server URL (set in settings)";

$MESS ['RK_BOC_ORDER_DATE'] = "order date";
$MESS ['RK_BOC_ORDER_PASSWORD'] = "the Password to the receipt of the order";
$MESS ['RK_BOC_PRODUCT_NAME'] = "product Name";

// SMS.RU settings
$MESS['RK_BOC_ABOUT_FORM'] = 'form Options';
$MESS['RK_BOC_SMS_RU'] = 'Settings SMS.RU';
$MESS['RK_BOC_SMS_RU_FROM'] = 'Sender';
$MESS['RK_BOC_SMS_RU_STATE'] = 'Activity';
$MESS['RK_BOC_STATE_ACTIVE'] = 'Enabled';
$MESS['RK_BOC_STATE_DISABLED'] = 'Off';
$MESS['RK_BOC_SMS_RU_STATE_TESTING'] = 'Test (SMS not send. and money is not deducted)';
$MESS['RK_BOC_SMS_RU_API_KEY'] = 'API Key';
$MESS['RK_BOC_SMS_RU_ADMIN_NUMBER'] = 'the phone number of the administrator';
$MESS['RK_BOC_SMS_RU_TEMPLATE_SUCCESS'] = 'Template for SMS text messages when uspesni payment';
$MESS['RK_BOC_SMS_RU_TEMPLATE_SUCCESS_DEFAULT'] = 'Payment for order "msgstr ""#ORDER_NAME# on" # #ORDER_ID# executed successfully. A receipt password: #PASSWORD#';
?>
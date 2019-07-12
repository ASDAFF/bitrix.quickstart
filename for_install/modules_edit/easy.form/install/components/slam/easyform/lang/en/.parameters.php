<?
// DEFAULT_FIELDS
$MESS['SLAM_EASYFORM_FIELD_TITLE'] = 'Your name';
$MESS['SLAM_EASYFORM_FIELD_EMAIL'] = 'Your E-mail';
$MESS['SLAM_EASYFORM_FIELD_PHONE'] = 'Mobile phone';
$MESS['SLAM_EASYFORM_FIELD_MALE'] = 'Your sex';
$MESS['SLAM_EASYFORM_FIELD_MALE_VAL_1'] = 'Male';
$MESS['SLAM_EASYFORM_FIELD_MALE_VAL_2'] = 'Female';
$MESS['SLAM_EASYFORM_FIELD_BUDGET'] = 'Budget';
$MESS['SLAM_EASYFORM_FIELD_BUDGET_VAL_1'] = 'up to 50,000 rubles';
$MESS['SLAM_EASYFORM_FIELD_BUDGET_VAL_2'] = 'from 50,000 to 200,000 rubles';
$MESS['SLAM_EASYFORM_FIELD_SERVICES'] = 'Service';
$MESS['SLAM_EASYFORM_FIELD_SERVICES_VAL_1'] = 'Website development';
$MESS['SLAM_EASYFORM_FIELD_SERVICES_VAL_2'] = 'Site Support';
$MESS['SLAM_EASYFORM_FIELD_ACCEPT'] = 'Consent to processing data';
$MESS['SLAM_EASYFORM_FIELD_ACCEPT_VAL'] = 'I agree to process <a href="#" target="_blank"> personal data </a>';
$MESS['SLAM_EASYFORM_FIELD_MESSAGE'] = 'Message';
$MESS['SLAM_EASYFORM_FIELD_DOCS'] = 'Document';
$MESS['SLAM_EASYFORM_FIELD_HIDDEN'] = 'Hidden Field';

// VISUAL
$MESS['SLAM_EASYFORM_UNIQUE_FORM_ID'] = 'Form ID';
$MESS['SLAM_EASYFORM_FORM_NAME'] = 'Form name';
$MESS['SLAM_EASYFORM_FORM_NAME_DEFAULT'] = 'Feedback Form';
$MESS['SLAM_EASYFORM_WIDTH_FORM'] = 'Width of the form';
$MESS['SLAM_EASYFORM_DISPLAY_FIELDS'] = 'Fields';
$MESS['SLAM_EASYFORM_REQUIRED_FIELDS'] = 'Required fields';
$MESS['SLAM_EASYFORM_FIELDS_ORDER'] = 'Location of form fields';
$MESS['SLAM_EASYFORM_HIDE_FIELD_NAME'] = 'Hide form field names';
$MESS['SLAM_EASYFORM_HIDE_ASTERISK'] = 'Remove colons and asterisks';
$MESS['SLAM_EASYFORM_FORM_AUTOCOMPLETE'] = 'AutoComplete form field values';
$MESS['SLAM_EASYFORM_FORM_SUBMIT_VALUE'] = 'Button Name';
$MESS['SLAM_EASYFORM_FORM_SUBMIT_VALUE_DEFAULT'] = 'Send';

// SUBMIT
$MESS['SLAM_EASYFORM_GROUP_SUBMIT'] = 'Send form';
$MESS['SLAM_EASYFORM_SEND_AJAX'] = 'Send form using AJAX?';
$MESS['SLAM_EASYFORM_SHOW_MODAL'] = 'Show result in modal window';
$MESS['SLAM_EASYFORM_FUNCTION_CALLBACKS_SUCCESS'] = 'Function name on successful sending ("_callbacks")';
$MESS['SLAM_EASYFORM_OK_MESSAGE'] = 'The message about the successful sending';
$MESS['SLAM_EASYFORM_OK_TEXT'] = 'Your message has been sent. We will contact you within 2 hours';
$MESS['SLAM_EASYFORM_ERROR_MESSAGE'] = 'Error message';
$MESS['SLAM_EASYFORM_ERROR_TEXT'] = 'An error occurred. Message not sent.';
$MESS['SLAM_EASYFORM_TITLE_SHOW_MODAL'] = 'Window title';
$MESS['SLAM_EASYFORM_DEFAULT_TITLE_SHOW_MODAL'] = 'Thank you!';

// MAIL
$MESS['SLAM_EASYFORM_GROUP_MAIL'] = 'Sending settings for messages';
$MESS['SLAM_EASYFORM_ENABLE_SEND_MAIL'] = 'Enable sending emails';
$MESS['SLAM_EASYFORM_CREATE_SEND_MAIL'] = 'Create a new mail template';
$MESS['SLAM_EASYFORM_EMAIL_TEMPLATES'] = 'Letter template';
$MESS['SLAM_EASYFORM_REPLACE_FIELD_FROM'] = "Replace the letter \"From\" with the visitor's e-mail address";
$MESS['SLAM_EASYFORM_EMAIL_FROM'] = '# EMAIL_FROM #';
$MESS['SLAM_EASYFORM_EVEN_EMAIL_TO'] = '# EMAIL_TO #';
$MESS['SLAM_EASYFORM_BCC'] = '# EMAIL_BCC #';
$MESS['SLAM_EASYFORM_MAIL_SUBJECT_ADMIN'] = 'The subject of the message for the administrator';
$MESS['SLAM_EASYFORM_MAIL_SUBJECT_ADMIN_DEFAULT'] = '# SITE_NAME #: Message from feedback form';
$MESS['SLAM_EASYFORM_WRITE_MESS_FILDES_TABLE'] = 'Record fields in the mail template with a table';
$MESS['SLAM_EASYFORM_EMAIL_TO'] = 'E-mail to which the message will be sent (by default it is used from module settings)';
$MESS['SLAM_EASYFORM_BCC'] = 'Bcc';
$MESS['SLAM_EASYFORM_EVEN_BCC'] = '# BCC #';
$MESS['SLAM_EASYFORM_RU_NAME'] = 'Sending a message via the SLAM super-form';
$MESS['SLAM_EASYFORM_RU_DESCRIPTION'] = '=== Service Macros ===
# AUTHOR_NAME # - Author of the message
# SUBJECT # - Subject of the letter
# FORM_NAME # - Form name
# FORM_FIELDS # - The contents of all fields in tabular or line form (depending on the settings of the form component)
# EMAIL_FROM # - Email of the sender of the message (E-mail by default, or value of the form field "E-mail", depending on the settings)
# EMAIL_TO # - Email of the message recipient (it is set in the settings of the comonent)
# EMAIL_BCC # - Email a hidden copy (it is set in the settings of the comonent)

=== The default form field macros ===
# TITLE # - Your Name
# WORK_POSITION # - Position
# WORK_COMPANY # - Company
# EMAIL # - E-mail
# PHONE # - Mobile phone
# ADDRESS # - Address
# SERVICES # - Service
# MESSAGE # - Message

=== Any form fields ===
The value of the character code of any field, for example:
# EMAIL #

=== System macros ===
';
$MESS['SLAM_EASYFORM_SUBJECT'] = '# SUBJECT #';
$MESS['SLAM_EASYFORM_MESSAGE'] = 'Information message of the site # SITE_NAME # <br>
------------------------------------------ <br>
The <br>
You have been sent a message using form # FORM_NAME # <br>
The <br>
Message text: <br>
# FORM_FIELDS # <br>
The <br>
The message is generated automatically.
';
// IBLOCK
$MESS['SLAM_EASYFORM_GROUP_WRITE_IB'] = 'Record results in the information block';
$MESS['SLAM_EASYFORM_USE_IBLOCK_WRITE'] = 'Write results to IS';
$MESS['SLAM_EASYFORM_IBLOCK_PROP_ADD_NAME'] = 'Create a new IB';
$MESS["SLAM_EASYFORM_IBLOCK_DESC_LIST_TYPE"] = "Information block type (only used for verification)";
$MESS["SLAM_EASYFORM_IBLOCK_DESC_LIST_ID"] = "Information block code for storing the result";
$MESS['SLAM_EASYFORM_ACTIVE_ELEMENT'] = "Deactivate an element when adding?";
$MESS['SLAM_EASYFORM_CATEGORY_IBLOCK_FIELD'] = "Property of the information block to which data will be written";
$MESS['SLAM_EASYFORM_IBLOCK_FIELD_NO_WRITE'] = "Do not write";
$MESS['SLAM_EASYFORM_IBLOCK_FIELD_NAME'] = "Name";
$MESS['SLAM_EASYFORM_IBLOCK_FIELD_DETAIL_TEXT'] = "Detailed Description";
$MESS['SLAM_EASYFORM_IBLOCK_FIELD_PREVIEW_TEXT'] = "Description for announcement";
$MESS['SLAM_EASYFORM_IBLOCK_FIELD_FORM'] = "Create automatically";
$MESS['SLAM_EASYFORM_IBLOCK_LANG_RU_NAME'] = "Form Results";
$MESS['SLAM_EASYFORM_IBLOCK_LANG_EN_NAME'] = "Form result";
$MESS['SLAM_EASYFORM_IBLOCK_PROP_RU_NAME'] = "Create a new IB?";

// CAPTCHA
$MESS['SLAM_EASYFORM_CAPTCHA'] = 'Captcha';
$MESS['SLAM_EASYFORM_USE_CAPTCHA'] = 'Use captcha reCAPTCHA';
$MESS['SLAM_EASYFORM_USE_CAPTCHA_TIP'] = 'Smart CAPTCHA by Google';
$MESS['SLAM_EASYFORM_CAPTCHA_BUTTON_NAME'] = 'Customize captcha';
$MESS['SLAM_EASYFORM_CAPTCHA_KEY'] = 'ReCAPTCHA key';
$MESS['SLAM_EASYFORM_CAPTCHA_KEY_TIP'] = 'You can get the key at https://www.google.com/recaptcha/admin';
$MESS['SLAM_EASYFORM_CAPTCHA_SECRET_KEY'] = 'ReCAPTCHA secret key';
$MESS['SLAM_EASYFORM_CAPTCHA_SECRET_KEY_TIP'] = 'You can get the private key at https://www.google.com/recaptcha/admin';
$MESS['SLAM_EASYFORM_FIELD_CAPTCHA_TITLE'] = 'Title';

// SUBMIT WARNING
$MESS['SLAM_EASYFORM_GROUP_PERSONAL_DATA'] = 'Processing of personal data';
$MESS['SLAM_EASYFORM_USE_MODULE_VARNING'] = 'Use message from module settings';
$MESS['SLAM_EASYFORM_FORM_SUBMIT_VARNING'] = 'The message displayed before the button';
$MESS['SLAM_EASYFORM_FORM_SUBMIT_VARNING_TIP'] = "You can use the template #BUTTON# instead of the button name";
$MESS['SLAM_EASYFORM_FORM_SUBMIT_VARNING_DEFAULT'] = 'By clicking on the "#BUTTON#" button, you consent to the processing of <a target="_blank" href="#"> personal data </a>';

//GROUP_JS_VALIDATE_SETTINGS
$MESS['SLAM_EASYFORM_GROUPS_JS_VALIDATE_SETTINGS'] = "JS Bootstrap Validators";
$MESS['SLAM_EASYFORM_USE_FORMVALIDATION_JS'] = 'Scan fields via JS Bootstrap Validators';
$MESS['SLAM_EASYFORM_HIDE_FORMVALIDATION_TEXT'] = 'Hide error messages';
$MESS['SLAM_EASYFORM_INCLUDE_FORMVALIDATION_LIBS'] = 'Include JS Bootstrap Validators';


// GROUP_JS_VALIDATE_SETTINGS
$MESS['SLAM_EASYFORM_GROUPS_JS_LIB_SETTINGS'] = "JS plug-ins";
$MESS['SLAM_EASYFORM_INCLUDE_JQUERY'] = 'Connect jQuery-1.12.4';
$MESS['SLAM_EASYFORM_USE_BOOTSRAP_CSS'] = 'Connect Standard Bootstrap Styles 3';
$MESS['SLAM_EASYFORM_USE_BOOTSRAP_JS'] = 'Connect standard JS Bootstrap 3';
$MESS['SLAM_EASYFORM_USE_DROPZONE_JS'] = 'Connect boot loader DragnDrop';
$MESS['SLAM_EASYFORM_USE_BOOTSRAP_JS_TIP'] = 'Required for the modal window';
$MESS['SLAM_EASYFORM_USE_INPUTMASK_JS'] = 'Connect JS Inputmask';
$MESS['SLAM_EASYFORM_USE_INPUTMASK'] = 'Enable mask';

// GROUP FIELDS
$MESS['SLAM_EASYFORM_GROUP_FIELD_TITLE'] = '- field settings';
$MESS['SLAM_EASYFORM_GROUP_FIELD_NAME'] = 'Name';
$MESS['SLAM_EASYFORM_TYPE_FIELD'] = 'Field Type';
$MESS['SLAM_EASYFORM_TYPE_FIELD_ACCEPT'] = '(Consent)';
$MESS['SLAM_EASYFORM_GROUP_FIELD_REQ'] = 'Additional validation rules';
$MESS['SLAM_EASYFORM_GROUP_FIELD_VALUE'] = 'Value';
$MESS['SLAM_EASYFORM_GROUP_FIELD_SELECT_ADD'] = 'Additional value (entered manually)';
$MESS['SLAM_EASYFORM_GROUP_FIELD_SELECT_ADD_DEF'] = 'Other (write your own)';
$MESS['SLAM_EASYFORM_GROUP_FIELD_FILE_EXTENSION'] = 'Valid file extensions (separated by commas)';
$MESS['SLAM_EASYFORM_GROUP_FIELD_FILE_MAX_SIZE'] = 'Maximum file size (in Kb)';
$MESS['SLAM_EASYFORM_GROUP_FIELD_INPUTMASK_TEMP'] = 'Mask template';
$MESS['SLAM_EASYFORM_GROUP_FIELD_VIEW'] = 'Horizontal display of values';

// VALIDATION_MESSAGES
$MESS['SLAM_EASYFORM_FIELD_VALIDATION_MESSAGE'] = 'Text on error';
$MESS['SLAM_EASYFORM_FIELD_VALIDATION_ADDITIONALLY_MESSAGE'] = 'Additional validation parameters';
$MESS['SLAM_EASYFORM_FIELD_VALIDATION_MESSAGE_DEFAULT'] = 'Required field';
$MESS['SLAM_EASYFORM_FIELD_VALIDATION_MESSAGE_EMAIL_DEFAULT'] = 'E-mail entered incorrectly';
?>

<?php
/**
 * Файл со всеми языковыми константами.
 *
 * Выдержка из помощи 1C-Bitrix:
 * 		"Подключаемый языковой файл должен иметь то же имя, что и подключающий файл, и быть расположен на диске в каталоге:
 * 		/bitrix/modules/ID модуля/lang/ID языка/путь к файлу относительно корня модуля."
 * Было принято решение разместить все языковые константы в один общий файл, чтобы не создавать множество различных маленьких файлов.   
 * @author r.smoliarenko
 * @author r.sarazhyn
 */

global $MESS;
// uniteller.sale/install/index.php
$MESS['UNITELLER.SALE_INSTALL_NAME'] = '«Uniteller payment system»';
$MESS['UNITELLER.SALE_INSTALL_DESCRIPTION'] = 'Description of the «Uniteller payment system» module <a href="/bitrix/admin/settings.php?lang=en&mid=uniteller.sale&mid_menu=1" target="_blank">Go to the Help page</a>';
$MESS['UNITELLER.SALE_PREINSTALL_DESCRIPTION'] = 'Description of the «Uniteller payment system» module <a href="#" onclick="alert(\'Install the «Uniteller payment system» module, please!\')">Go to the Help page</a>';
$MESS['UNITELLER.SALE_INSTALL_ERROR'] = 'An error occurred while copying the files during the «Uniteller payment system» module installation.';

// uniteller.sale/install/step_ok.php
$MESS['UNITELLER.SALE_INSTALL_MESSAGE'] = '«Uniteller payment system» module has been successfully installed.';

// uniteller.sale/install/unstep_ok.php
$MESS['UNITELLER.SALE_BTN_CANCEL'] = 'Cancel';
$MESS['UNITELLER.SALE_SAVE_TABLES'] = 'Do you want to save the «Uniteller payment system» module table? ';

// uniteller.sale/options.php & uniteller.sale/install/step_ok.php & uniteller.sale/admin/uniteller_agent_log.php
$MESS['UNITELLER.SALE_BTN_HELP'] = 'Help';

// admin/uniteller_agent_log.php
$MESS['UNITELLER.AGENT_ORDER_ID'] = 'Order ID';
$MESS['UNITELLER.AGENT_INSERT_DATATIME'] = 'Error adding time';
$MESS['UNITELLER.AGENT_TYPE_ERROR'] = 'Error type';
$MESS['UNITELLER.AGENT_TEXT_ERROR'] = 'Error text';
$MESS['UNITELLER.AGENT_LOGS_TITLE'] = '«Uniteller payment system» module logs';
$MESS['UNITELLER.AGENT_LOGS_DEL'] = 'Delete';
$MESS['UNITELLER.AGENT_DEL_ERROR'] = 'The record cannot be removed.';
$MESS['UNITELLER.AGENT_NAV'] = 'Error logs';
$MESS['UNITELLER.AGENT_DEL_CONF'] = 'Do you want to remove the record?';
$MESS['UNITELLER.AGENT_F_ORDER_ID'] = 'Order ID';
$MESS['UNITELLER.AGENT_F_INSERT_DATATIME'] = 'Error adding time';
$MESS['UNITELLER.AGENT_F_TYPE_ERROR'] = 'Error type';
$MESS['UNITELLER.AGENT_F_TEXT_ERROR'] = 'Error text';
$MESS['UNITELLER.AGENT_FIND'] = 'Find';
$MESS['UNITELLER.AGENT_F_FIND_TYTLE'] = 'Enter the search line';
$MESS['POST_ALL'] = 'All';

// uniteller.sale/options.php
$MESS['UNITELLER.AGENT_LOGS'] = 'Logs';
$MESS['UNITELLER.SALE_HELP_TEXT'] = '
<h3>Installation and Setup Guide for Uniteller payment system modules in CMS 1C-Bitrix</h3>
<b>Uniteller system settings </b>
<p>
<div>1. Log in to the Uniteller Back Office.</div>
<div>2. In the Back Office go to «Договоры и магазины» page and choose «Настройки» for the required e-shop. The settings page will be displayed.</div>
<div>3. Into the «URL-адрес магазина» enter «http://e-shop_address/».</div>
<div>4. Into the «E-Mail службы поддержки магазина» enter e-mail.</div>
<div>5. Into the «URL для уведомления сервера интернет-магазина об изменившемся статусе счёта/оплаты» enter «http://e-shop_address/personal/ordercheck/result_rec.php». Save changes.</div>
</p>
<b>Module installation</b>
<p>
<div>1. Log in to the «Control panel» on the site with the administrator credentials</div>
<div>2. Go to «Marketpalce» section (Settings–>Marketplace). Find «Uniteller payment system» module in the list, double-click it and click «Download». </div>
<div>3. When Uniteller payment system module is downloaded, click «Install» button near it. When the installation process is finished the «Uniteller payment system module has been successfully installed» message will appear.</div>
<div>4. Click «Back to list» button.  Uniteller payment system will appear with «Installed» status in the modules list.</div>
</p>
<h3>Module setup</h3>
<b>Site setup</b>
<p>
<div>1. Log in to the «Control panel» with the administrator credentials.</div>
<div>2. Go to «List of Sites» section (Settings –>System Settings–>Sites–> List of Sites). From the list of sites, choose the required e-store and double-click it. Into the «Server URL (without http://)» field, enter the real URL of the e-store (without http://). Into the «Default Email address» field, enter the real e-mail address of the e-store.</div>
<div>3. Go to «Statuses» section (e-Store–>e-Store Settings–>Statuses). To create a new status, click the «New Status» button. With the section displayed, enter the status code, the English letter B, next to the Code field. Within the Russian section, enter «Средства заблокированы» (Funds blocked) into the Name field. Within the English section, enter «Funds blocked» into the Name field. Click the «Save» button. In the status list, you can see a new status «B» – «Funds blocked».</div>
</p>
<b>Uniteller payment system setup</b>
<p>
<div>1. Go to «Payment systems» section (e-Store–>e-Store settings–>Payment systems)</div>
<div>2. Click «New payment system» button above the payment systems table and in the drop-down list choose the site the payment system will be installed for. A section for a new payment system will appear.</div>
<div>3. To add a new payment system, go to «Payment system» tab and fill all required fields in. Into the «Currency» field, enter the payment system currency. Into the «Name» field, enter «Uniteller». Tick off the «Active» field.  Into the «Sorting index» field, enter value 1. Into the «Description» field, enter a short description on the payment system.</div>
<div>4. Go to «Физическое лицо» (Individuals) tab.</div>
<div>&nbsp;&nbsp;a. If the chosen payment system should work with «Физическое лицо» (Individuals), tick off the «Applied to this payer type» field. Specify the name of the handler for the payment system in the «Name» field.  In the «Handler» field, choose Uniteller from the drop-down list. Fill in the additional fields.</div>
<div>&nbsp;&nbsp;b. Fill in the «Description of the payment system» field. </div>
<div>&nbsp;&nbsp;c. Into the «Shop ID» field, enter Uniteller Point ID specified on the «Agreement list» page of the Back Office.</div>
<div>&nbsp;&nbsp;d. Into the «Login» field, enter the Login specified on the «Authorization parameters» page of the Back Office.</div>
<div>&nbsp;&nbsp;e. Into the «Password» field, enter the Password specified on the «Authorization parameters» page of the Back Office.</div>
<div>&nbsp;&nbsp;f. Into the «e-Shop name in Latin, assigned by Unireller» field, enter «e-Shop name» specified on the «Company Agreements list» page of the Back Office. </div>
<div>&nbsp;&nbsp;g. Необязательное поле «Время жизни формы оплаты в секундах». Должно быть целым положительным числом. Если покупатель проведет на форме дольше, чем указанное время, то форма оплаты будет считаться устаревшей, и платеж не будет принят. Покупателю в таком случае будет предложено вернуться на сайт.</div>
<div>&nbsp;&nbsp;h. Необязательное поле «Время, в течение которого статус платежа "paid" может быть отменён». Если не ввести ничего, то в качестве значения по умолчанию будет использоваться значение 14 (дней). Чем меньше этот период, тем меньше запросов при синхронизации статусов будет генерировать модуль к системе Uniteller.</div>
<div>&nbsp;&nbsp;i. Необязательное поле «Время, в течение которого статус платежа будет синхронизироваться». Если не ввести ничего, то в качестве значения по умолчанию будет использоваться значение 30 (дней). Чем меньше этот период, тем меньше запросов при синхронизации статусов будет генерировать модуль к системе Uniteller.</div>
<div>&nbsp;&nbsp;j. Into the «URL for successful transaction (URL_RETURN_OK)» field, enter «http://e-Shop_address/personal/ordercheck/check/».</div>
<div>&nbsp;&nbsp;k. Into the «URL for unsuccessful transaction (URL_RETURN_NO)» field, enter «http://e-Shop_address/personal/ordercheck/detail/»./div>
<div>&nbsp;&nbsp;l. В поле «Тестовый режим» ввести «Да» для включения тестового режима или очистить его для проведения реальной оплаты.</div>
<div>5. Apply all the above mentioned steps (for «Физическое лицо» (Individual) tab) for «Юридическое лицо» (Legal entity) tab.</div>
<div>6. Click «Save» button.</div>
<div>7. Uniteller payment system will appear in the list.</div>
</p>
<b>Adding the receipt list</b>
<p>
<div>1. Log in to the «Site» panel with the administrator credentials.</div>
<div>2. Chose «Edit левое меню» from the drop-down menu (Menu–>Edit левое меню).</div>
<div>3. Change link for «Мои заказы» from «order/» to «ordercheck/».</div>
<div>4. If such link does not exist, it is necessary to add it. If site design does not contain left menu, this link should be added to any menu.</div>
<div>&nbsp;&nbsp;<b>Additionally:</b></div>
<div>&nbsp;&nbsp;The link to a receipt page is in the template by default - «bitrix:sale.personal.ordercheck.list» component. If it is necessary to add this link into another template of the component, the following should be added into the corresponding template file and place:</div>
<div>&nbsp;&nbsp;&lt;!-- UnitellerPlugin add --&gt;</div>
<div>&nbsp;&nbsp;&lt;?if ($vval["ORDER"]["NEED_CHECK"] == "Y"):?&gt;</div>
<div>&nbsp;&nbsp;&lt;a title="&lt;?= GetMessage(\STPOL_CHECK\'); ?&gt;" href="&lt;?= $vval[\'ORDER\'][\'URL_TO_CHECK\']; ?&gt;"&gt;&lt;?= GetMessage(\'STPOL_CHECK\'); ?&gt;&lt;/a&gt;</div>
<div>&nbsp;&nbsp;&lt;?endif;?&gt;</div>
<div>&nbsp;&nbsp;&lt;!-- /UnitellerPlugin add --&gt;</div>
<div>5. Go to the «Site» section. Switch «Edit mode» on. Choose «My orders» link at the site upper menu. Choose this  box and click «Edit area as text». In the appeared window, change  «&lt;li&gt;&lt;a href="/personal/order/"&gt;Мои заказы&lt;/a&gt;&lt;/li&gt;» to «&lt;li&gt;&lt;a href="/personal/ordercheck/"&gt;Мои заказы&lt;/a&gt;&lt;/li&gt;». Click «Save». «The page has been successfully updated. Undo Changes» message will appear. Switch «Edit Mode» off.</div>
</p>
<b>Remove the module</b>
<p>
<div>1. Go to «Modules» section (Settings–>Modules). Choose «Uniteller payment system» in the list and press «Remove». «Attention! The module will be removed from the system with all settings.» message will appear.</div>
<div>2. To save the module tables in the database, tick off the «Do you want to save the Uniteller payment system module table?» field.</div>
<div>3. Click «Remove». Uniteller payment system module will appear with «Not installed» status in the list of modules.</div>
<div>&nbsp;&nbsp;<b>Additionally:</b></div>
<div>&nbsp;&nbsp;1. If to remove the tick from «Do you want to save the Uniteller payment system module table?», the table with error logs will be removed from the database. If the tick is kept for this field, the table will remain in the database with the saved and available data.</div>
<div>&nbsp;&nbsp;2. If the module is removed, «Folder_with_bitrix/bitrix/modules/uniteller.sale» folder with the installation package will be kept in the system. All other files will be removed.</div>
</p>
<b>Update the module</b>
<p>
<div>1. Go to «Partner update» section (Settings–>Marketplace–>Partner update).</div>
<div>2. Go to «Updates» tab. Select all updates for «Uniteller payment system», if any available. Click «Install updates». When the updates installation is finished, «Successfully installed updates: (number)» will be displayed.</div>
</p>
<b>Settings cron</b><br/><br/>
<b>Windows</b>
<p>
<div>1. Open «cron.bat» file (from “folder_with_bitrix/” folder) with the file manager.</div>
<div>2. In the opened file, specify the path to the required files.</div>
<div>&nbsp;&nbsp;a. For phpexe_path variable, specify the full path to php.exe file. </div>
<div>&nbsp;&nbsp;b. For phpini_path variable, specify the full path to php.ini file.</div>
<div>&nbsp;&nbsp;c. For bitrix_path variable, specify «folder_with_bitrix\personal\ordercheck\cron.php».</div>
<div>&nbsp;&nbsp;d. d.	Save.</div>
<div>3. Enable cron.bat in OS Windows tasks scheduler to be processed every minute.</div>
</p>
<b>Linux</b>
<p>
<div>1. Make the following actions on behalf of administrator:</div>
<div>&nbsp;&nbsp;a. Open console.</div>
<div>&nbsp;&nbsp;b. Execute «crontab -e» command.</div>
<div>&nbsp;&nbsp;c. Enter and save: «*/1 * * * * /path_from_the_disk_root_to_the_script/php -f /path_from_the_disk_root_to_the_file/cron.php».</div>
</p>
<b>Additionally</b>
<p>
<div>All injections of the Uniteller plugin code made into 1C-Bitrix files are marked with special comments:</<div>
<div>1. Start of the code added: «UnitellerPlugin add».</<div>
<div>2. End of the code added: «/UnitellerPlugin add».</div>
<div>3. Start of the code changed: «UnitellerPlugin change».</div>
<div>4. End of the code changed: «/UnitellerPlugin change».</div>
</p>';
?>
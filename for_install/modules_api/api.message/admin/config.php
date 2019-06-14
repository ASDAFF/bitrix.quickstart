<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;

//use Bitrix\Main\Mail\Internal\EventMessageTable;

define("ADMIN_MODULE_NAME", "api.message");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

$ASM_RIGHT = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($ASM_RIGHT == 'D')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

$APPLICATION->SetTitle(Loc::getMessage('ASM_CONFIG_PAGE_TITLE'));
CJSCore::Init(array('jquery'));

$errorMsgs = null;

Loader::includeModule(ADMIN_MODULE_NAME);
use \Api\Message\ConfigTable;


$context = Application::getInstance()->getContext();
$request = $context->getRequest();


//---------- Сайты ----------//
$arSites = SiteTable::getList(array(
	'select' => array('LID', 'SITE_NAME', 'EMAIL'),
	'filter' => array('ACTIVE' => 'Y'),
))->fetchAll();


//---------- Табы ----------//
$formId = 'asm_config';
$aTabs  = array();
foreach($arSites as $val)
{
	$aTabs[] = Array(
		'DIV'   => 'tab_site_' . $val['LID'],
		'TAB'   => '[' . $val['LID'] . '] ' . $val['SITE_NAME'],
		'TITLE' => Loc::getMessage('ASM_CONFIG_TAB_SITE_TITLE') . ' "' . $val['SITE_NAME'] . '"',
	);
}
$tabControl = new CAdminTabControl($formId, $aTabs, true, true);


//---------- Сохраняем настройки ----------//
if($request->isPost() && strlen($save) > 0 && check_bitrix_sessid())
{
	if($arOptions = $request->getPost('FIELDS'))
	{
		foreach($arOptions as $key => $arOption)
		{
			if(is_array($arOption))
			{
				foreach($arOption as $siteId => $option)
				{
					if(is_array($option))
					{
						$arValues = array();
						foreach($option as $eventTypeName => $value)
						{
							if($value == 1)
								$arValues[] = $eventTypeName;
						}

						$option = implode(',', $arValues);
					}

					$arData = array(
						'NAME'    => $key,
						'VALUE'   => $option,
						'SITE_ID' => $siteId,
					);
					ConfigTable::addEx($arData);
				}
			}
		}

		unset($arOptions, $arOption, $option, $arData);
	}


	//Clear cache
	ConfigTable::clearCache();

	if(!$errorMsgs)
		LocalRedirect('/bitrix/admin/api_message_config.php?lang=' . LANGUAGE_ID . '&' . $tabControl->ActiveTabParam());
}



//Подготовим все настройки модуля для вывода
$arOptions = array();
$rsConfig  = ConfigTable::getList();
while($option = $rsConfig->fetch())
{
	$arOptions[ $option['NAME'] . '_' . $option['SITE_ID'] ] = $option['VALUE'];
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

//START VIEW

//Выводим сообщения
if($errorMsgs)
{
	$m = new CAdminMessage(array(
		'TYPE'    => 'ERROR',
		'MESSAGE' => implode('<br>\n', $errorMsgs),
		'HTML'    => true,
	));

	echo $m->Show();
}

?>
	<form method="POST" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?
		$tabControl->Begin();

		$sectionStyle = 'height: 30px;text-transform: uppercase;font-size: 16px';
		$hintStyle    = 'border-radius: 3px;background: #fbfae2;box-shadow: 0 0 0 1px #d4d5d6;color: #000;padding:3px 5px;margin:5px 0';

		foreach($arSites as $arSite)
		{
			$siteId = $arSite['LID'];

			$tabControl->BeginNextTab();
			?>
			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<?=Loc::getMessage('ASM_CONFIG_USE_JQUERY');?>
				</td>
				<td class="adm-detail-content-cell-r">
					<?
					$curValue = $arOptions[ 'USE_JQUERY_' . $siteId ];
					$arjQuery = Loc::getMessage('ASM_CONFIG_USE_JQUERY_VALUES');
					?>
					<select name="FIELDS[USE_JQUERY][<?=$siteId?>]">
						<? foreach($arjQuery as $key => $val): ?>
							<option value="<?=$key?>"<?=($key == $curValue ? ' selected' : '')?>><?=$val?></option>
						<? endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<?=Loc::getMessage('ASM_CONFIG_CACHE_TTL');?>
				</td>
				<td class="adm-detail-content-cell-r">
					<?
					$curValue = $arOptions[ 'CACHE_TTL_' . $siteId ];
					?>
					<input type="text" name="FIELDS[CACHE_TTL][<?=$siteId?>]" value="<?=intval($curValue)?>">
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l" width="50%">
					<?=Loc::getMessage('ASM_CONFIG_COOKIE_NAME');?>
				</td>
				<td class="adm-detail-content-cell-r">
					<?
					$cookie_name = $arOptions[ 'COOKIE_NAME_' . $siteId ];
					if(!$cookie_name)
						$cookie_name = Option::get("main", "cookie_name", "BITRIX_SM") . '_' . $siteId;
					?>
					<input type="text" name="FIELDS[COOKIE_NAME][<?=$siteId?>]" value="<?=ToUpper(trim($cookie_name))?>">
				</td>
			</tr>
			<?
		}
		?>
		<?
		//Выводим кнопки
		$tabControl->Buttons(
			array(
				"disabled"      => ($ASM_RIGHT < "W"),
				'btnSave'       => true,
				'btnApply'      => false,
				'btnCancel'     => false,
				'btnSaveAndAdd' => false,
			)
		);

		$tabControl->End();
		?>
	</form>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>
<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Internal\EventTypeTable;

//use Bitrix\Main\Mail\Internal\EventMessageTable;

define('ADMIN_MODULE_NAME', 'api.orderstatus');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D') {
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$APPLICATION->SetTitle(Loc::getMessage('AOS_OPTIONS_PAGE_TITLE'));
CJSCore::Init(array('jquery'));

$errorMsgs = null;

Loader::includeModule(ADMIN_MODULE_NAME);
use Api\OrderStatus\OptionTable;


$context = Application::getInstance()->getContext();
$request = $context->getRequest();


//Получим все табы
$formId     = 'aos_options';
$aTabs      = Loc::getMessage('AOS_OPTIONS_TABS');
$tabControl = new CAdminTabControl($formId, $aTabs);

//Получим все сайты
$arSites = SiteTable::getList(array(
	 'select' => array('LID', 'SITE_NAME', 'EMAIL'),
	 'filter' => array('ACTIVE' => 'Y'),
))->fetchAll();

//Полуим все типы почтовых событий
$arEventTypes = EventTypeTable::getList(array(
	 'order'  => array('EVENT_NAME' => 'ASC'),
	 'filter' => array('LID' => LANGUAGE_ID),
))->fetchAll();



//Сохраняем настройки модуля
if($request->isPost() && strlen($save) > 0 && check_bitrix_sessid()) {

	//Подготовим для сохранения настроек значения визуального редактора
	if($arSites) {
		foreach($arSites as $arSite) {
			//MAIL_SALE_NEW_ORDER - оформление заказа
			$_REQUEST['OPTION']['MAIL_SALE_NEW_ORDER'][ $arSite['LID'] ]      = $_REQUEST[ 'OPTION_MAIL_SALE_NEW_ORDER_' . $arSite['LID'] ];
			$_REQUEST['OPTION']['MAIL_SALE_NEW_ORDER_TYPE'][ $arSite['LID'] ] = $_REQUEST[ 'OPTION_MAIL_SALE_NEW_ORDER_TYPE_' . $arSite['LID'] ];

			$_REQUEST['OPTION']['MAIL_SALE_NEW_ORDER_HEADER'][ $arSite['LID'] ]      = $_REQUEST[ 'OPTION_MAIL_SALE_NEW_ORDER_HEADER_' . $arSite['LID'] ];
			$_REQUEST['OPTION']['MAIL_SALE_NEW_ORDER_HEADER_TYPE'][ $arSite['LID'] ] = $_REQUEST[ 'OPTION_MAIL_SALE_NEW_ORDER_HEADER_TYPE_' . $arSite['LID'] ];

			$_REQUEST['OPTION']['MAIL_SALE_NEW_ORDER_FOOTER'][ $arSite['LID'] ]      = $_REQUEST[ 'OPTION_MAIL_SALE_NEW_ORDER_FOOTER_' . $arSite['LID'] ];
			$_REQUEST['OPTION']['MAIL_SALE_NEW_ORDER_FOOTER_TYPE'][ $arSite['LID'] ] = $_REQUEST[ 'OPTION_MAIL_SALE_NEW_ORDER_FOOTER_TYPE_' . $arSite['LID'] ];
		}
	}


	//1. Сохраняем настройки магазинов
	if($arRequestOptions = $_REQUEST['OPTION']) {
		foreach($arRequestOptions as $key => $arOption) {
			if(is_array($arOption)) {
				foreach($arOption as $siteId => $option) {

					//EVENT_TYPE to string
					if(is_array($option)) {
						$arValues = array();
						foreach($option as $eventTypeName => $value) {
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
					OptionTable::addEx($arData);
				}
			}
		}

		unset($arRequestOptions, $arOption, $option, $arData);
	}


	//2. Сохраняем настройки каталогов
	if($arCatalogs = $_REQUEST['CATALOG']) {
		foreach($arCatalogs as $key => $val) {
			if(is_array($val) && $val) {
				$arCatalogs[ $key ] = (in_array('all', $val) ? 'all' : join(',', $val));
			}	else {
				unset($arCatalogs[ $key ]);
			}
		}

		$arData = array(
			 'NAME'  => 'CATALOG',
			 'VALUE' => serialize($arCatalogs),
			 //'VALUE' => ($arCatalogs ? serialize($arCatalogs) : null),
		);
		OptionTable::addEx($arData);
	}

	unset($arRequestOptions, $arOption, $option, $arData);


	//3. Загружаем логотипы и сохраняем настройки
	if($arSites) {
		foreach($arSites as $arSite) {
			$arFile = $_FILES[ 'FILE_SALE_LOGO_' . $arSite['LID'] ];

			if($arFile['type'] == null)
				continue;

			if(CFile::CheckFile($arFile, 1024 * 1024, false, "jpg,jpeg,png,gif")) {
				$errorMsgs[] = Loc::getMessage('AOS_OPTIONS_ERROR_WRONG_FILE_EXT');
			}
			else {

				$filePath    = '/upload/api_orderstatus/' . $arSite['LID'] . '_' . $arFile['name'];
				$destination = $_SERVER['DOCUMENT_ROOT'] . $filePath;

				$bCanLoadFile = true;
				$dirnameFile  = dirname($destination);
				if(!is_dir($dirnameFile)) {
					if(!mkdir($dirnameFile, 0755, true)) {
						$bCanLoadFile = false;
						$errorMsgs[]  = Loc::getMessage('AOS_OPTIONS_ERROR_WRONG_FILE_DIR');
					}
				}

				if($bCanLoadFile && move_uploaded_file($arFile['tmp_name'], $destination)) {
					$arData = array(
						 'NAME'    => 'SALE_LOGO',
						 'VALUE'   => $filePath,
						 'SITE_ID' => $arSite['LID'],
					);
					OptionTable::addEx($arData);
				}
			}
		}
		unset($arSite, $arFile, $arData);
	}

	if(!$errorMsgs)
		LocalRedirect('/bitrix/admin/api_orderstatus_options.php?lang=' . LANGUAGE_ID . '&' . $tabControl->ActiveTabParam());
}



//Подготовим все настройки модуля для вывода
$arOptions = array();
$res       = OptionTable::getList();
while($option = $res->fetch()) {
	if($option['SITE_ID'])
		$arOptions[ $option['NAME'] . '_' . $option['SITE_ID'] ] = $option['VALUE'];
	else
		$arOptions[ $option['NAME'] ] = unserialize($option['VALUE']);
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

//START VIEW


//Выводим сообщения
if($errorMsgs) {
	$m = new CAdminMessage(array(
		 'TYPE'    => 'ERROR',
		 'MESSAGE' => implode('<br>\n', $errorMsgs),
		 'HTML'    => true,
	));

	echo $m->Show();
}

//Выводим табы
$tabControl->Begin();
?>
	<style>
		.mail-headers textarea{ width: 100%; min-width: 100%; max-width: 100%; min-height: 150px; }
		.preview-logo img{ margin-top: 5px; max-height: 100px }
	</style>
	<script>
		jQuery(document).ready(function ($) {
			$(".check-all").on('change', function () {
				if ($(this).is(':checked'))
					$(this).parents('.event-type-table').find('.event-type-list input').prop('checked', true);
				else
					$(this).parents('.event-type-table').find('.event-type-list input').prop('checked', false);
			});
		});

		function AOS_RestoreDefaults(siteId) {

			if (!siteId.length) {
				alert('siteId=false');
				return false;
			}

			if (confirm('<?=Loc::getMessage('AOS_OPTIONS_DEFAULTS_APPLY_CONFIRM')?>')) {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: '/bitrix/admin/api_orderstatus_defaults.php',
					data: {
						sessid: BX.bitrix_sessid()
					},
					timeout: 20000,
					error: function (request, error) {
						if (error.length)
							alert('Error! ' + error);
					},
					success: function (data) {
						if (data.result == 'ok') {
							var defaults = data.defaults || '';
							if (defaults) {
								for (var i in defaults) {
									var fieldName1 = '#aos_table_' + siteId + ' [name="OPTION[' + i + '][' + siteId + ']"]';
									var fieldName2 = '#aos_table_' + siteId + ' [name="OPTION_' + i + '_' + siteId + '"]';
									var fieldHtml  = '#aos_table_' + siteId + ' input[id="bxed_OPTION_' + i + '_' + siteId + '_html"]';

									if ($(fieldHtml).length)
										$(fieldHtml).click();

									if ($(fieldName1).length)
										$(fieldName1).val(defaults[i]);

									if ($(fieldName2).length)
										$(fieldName2).val(defaults[i]);
								}

								setTimeout(function () {
									alert('<?=Loc::getMessage('AOS_OPTIONS_DEFAULTS_APPLY_OK')?>');
								}, 500);

							}
						}
						else {
							alert('<?=Loc::getMessage('AOS_OPTIONS_DEFAULTS_APPLY_ERROR')?>');
						}
					}
				});
			}
		}
	</script>

	<form method="POST" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?
		///////////////////////////////////////////////////////////////////////////
		//                      Настройка магазинов/сайтов
		///////////////////////////////////////////////////////////////////////////
		$tabControl->BeginNextTab();
		?>
		<tr>
			<td colspan="2" valign="top">
				<?
				$aTabs2 = Array();
				foreach($arSites as $val) {
					$aTabs2[] = Array(
						 'DIV'   => 'tab_site_' . $val['LID'],
						 'TAB'   => '[' . $val['LID'] . '] ' . $val['SITE_NAME'],
						 'TITLE' => Loc::getMessage('AOS_OPTIONS_TAB_SITE_TITLE') . ' "' . $val['SITE_NAME'] . '"',
					);
				}
				$tabControl2 = new CAdminViewTabControl($formId . '_sites', $aTabs2);
				$tabControl2->Begin();


				$sectionStyle = 'height: 30px;text-transform: uppercase;font-size: 16px';
				$hintStyle    = 'border-radius: 3px;background: #fbfae2;box-shadow: 0 0 0 1px #d4d5d6;color: #000;padding:3px 5px;margin:5px 0';

				foreach($arSites as $arSite) {
					$siteId = $arSite['LID'];
					$tabControl2->BeginNextTab();
					?>
					<table cellpadding="2" cellspacing="2" border="0" width="100%" align="center" id="aos_table_<?=$siteId?>">

						<tr class="heading">
							<td colspan="2" style="<?=$sectionStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_CONTACTS')?></td>
						</tr>
						<tr>
							<td valign="middle" align="right" width="50%">
								<?=Loc::getMessage('AOS_OPTIONS_SITE_SALE_LOGO');?>
								<span style="<?=$hintStyle?>">#SALE_LOGO#</span>
							</td>
							<td valign="middle">
								<input type="file" name="FILE_SALE_LOGO_<?=$siteId?>" value="">
								<? if($imgSrc = $arOptions[ 'SALE_URL_' . $siteId ] . $arOptions[ 'SALE_LOGO_' . $siteId ]): ?>
									<div class="preview-logo"><img src="<?=$imgSrc?>" alt="SALE_LOGO_<?=$siteId?>"></div>
									<?
								else:?>
									<input type="hidden" name="OPTION[SALE_LOGO][<?=$siteId?>]" value="">
								<? endif ?>
							</td>
						</tr>

						<tr>
							<td valign="middle" align="right" width="50%">
								<?=Loc::getMessage('AOS_OPTIONS_SITE_SALE_NAME');?>
								<span style="<?=$hintStyle?>">#SALE_NAME#</span>
							</td>
							<td valign="middle">
								<input type="text" name="OPTION[SALE_NAME][<?=$siteId?>]" value="<?=$arOptions[ 'SALE_NAME_' . $siteId ]?>" size="60">
							</td>
						</tr>

						<tr>
							<td valign="middle" align="right" width="50%">
								<?=Loc::getMessage('AOS_OPTIONS_SITE_SALE_URL');?>
								<span style="<?=$hintStyle?>">#SALE_URL#</span>
							</td>
							<td valign="middle">
								<input type="text" name="OPTION[SALE_URL][<?=$siteId?>]" value="<?=$arOptions[ 'SALE_URL_' . $siteId ]?>" size="60">
							</td>
						</tr>

						<tr>
							<td valign="middle" align="right" width="50%">
								<?=Loc::getMessage('AOS_OPTIONS_SITE_SALE_EMAIL');?>
								<span style="<?=$hintStyle?>">#SALE_EMAIL#</span>
							</td>
							<td valign="middle">
								<input type="text" name="OPTION[SALE_EMAIL][<?=$siteId?>]" value="<?=$arOptions[ 'SALE_EMAIL_' . $siteId ]?>" size="60">
							</td>
						</tr>

						<tr>
							<td valign="middle" align="right" width="50%">
								<?=Loc::getMessage('AOS_OPTIONS_SITE_SALE_PHONE');?>
								<span style="<?=$hintStyle?>">#SALE_PHONE#</span>
							</td>
							<td valign="middle">
								<input type="text" name="OPTION[SALE_PHONE][<?=$siteId?>]" value="<?=$arOptions[ 'SALE_PHONE_' . $siteId ]?>" size="60">
							</td>
						</tr>
						<tr>
							<td valign="middle" align="right" width="50%">
								<?=Loc::getMessage('AOS_OPTIONS_SITE_SALE_ADDRESS');?>
								<span style="<?=$hintStyle?>">#SALE_ADDRESS#</span>
							</td>
							<td valign="middle">
								<input type="text" name="OPTION[SALE_ADDRESS][<?=$siteId?>]" value="<?=$arOptions[ 'SALE_ADDRESS_' . $siteId ]?>" size="60">
							</td>
						</tr>

						<tr class="heading">
							<td colspan="2" style="<?=$sectionStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_HEADERS')?></td>
						</tr>
						<tr>
							<td class="adm-detail-content-cell-l"><?=Loc::getMessage('AOS_OPTIONS_MAIL_REPLACE');?></td>
							<td class="adm-detail-content-cell-r">
								<input type="hidden" name="OPTION[MAIL_REPLACE][<?=$siteId?>]" value="N">
								<input type="checkbox" name="OPTION[MAIL_REPLACE][<?=$siteId?>]" value="Y" <?=$arOptions[ 'MAIL_REPLACE_' . $siteId ] == 'Y' ? 'checked' : '';?>>
							</td>
						</tr>
						<tr>
							<td valign="middle" colspan="2">
								<div style="<?=$hintStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_MAIL_HEADER')?></div>
								<div id="mail_headers_<?=$siteId?>" class="mail-headers">
									<textarea name="OPTION[MAIL_HEADER][<?=$siteId?>]" style="height:350px"><?=$arOptions[ 'MAIL_HEADER_' . $siteId ]?></textarea>

									<div style="<?=$hintStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_MAIL_CONTENT')?></div>
									<textarea name="OPTION[MAIL_CONTENT][<?=$siteId?>]" style="height:350px"><?=$arOptions[ 'MAIL_CONTENT_' . $siteId ]?></textarea>

									<div style="<?=$hintStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_MAIL_FOOTER')?></div>
									<textarea name="OPTION[MAIL_FOOTER][<?=$siteId?>]" style="height:350px"><?=$arOptions[ 'MAIL_FOOTER_' . $siteId ]?></textarea>
								</div>
							</td>
						</tr>

						<tr class="heading">
							<td colspan="2" style="<?=$sectionStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_ORDER_MAKE')?></td>
						</tr>

						<tr>
							<td valign="middle"><?=Loc::getMessage('AOS_OPTIONS_SITE_ORDER_MAKE_SUBJECT')?>:</td>
							<td valign="middle">
								<input type="text" name="OPTION[MAIL_SALE_NEW_ORDER_SUBJECT][<?=$siteId?>]" value="<?=$arOptions[ 'MAIL_SALE_NEW_ORDER_SUBJECT_' . $siteId ]?>" size="60">
							</td>
						</tr>

						<tr>
							<td valign="middle" colspan="2">
								<div style="<?=$hintStyle?>"><?=nl2br(Loc::getMessage('AOS_OPTIONS_SITE_ORDER_MAKE_HEADER'))?></div>
								<div id="MAIL_SALE_NEW_ORDER_HEADER_<?=$siteId?>" class="mail-headers">
									<? CFileMan::AddHTMLEditorFrame(
										 "OPTION_MAIL_SALE_NEW_ORDER_HEADER_" . $siteId,
										 $arOptions[ 'MAIL_SALE_NEW_ORDER_HEADER_' . $siteId ],
										 "OPTION_MAIL_SALE_NEW_ORDER_HEADER_TYPE_" . $siteId,
										 (isset($arOptions[ 'MAIL_SALE_NEW_ORDER_HEADER_TYPE_' . $siteId ]) ? $arOptions[ 'MAIL_SALE_NEW_ORDER_HEADER_TYPE_' . $siteId ] : 'html'),
										 array(
												'height' => (strlen($arOptions[ 'MAIL_SALE_NEW_ORDER_HEADER_' . $siteId ]) > 0 ? 350 : 150),
												'width'  => '100%',
										 ),
										 "N",
										 0,
										 "",
										 "",
										 $siteId,
										 true,
										 false,
										 array(
												'componentFilter' => array('TYPE' => 'mail'),
										 )
									); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="middle" colspan="2">
								<div style="<?=$hintStyle?>"><?=nl2br(Loc::getMessage('AOS_OPTIONS_SITE_ORDER_MAKE_CONTENT'))?></div>
								<div id="MAIL_SALE_NEW_ORDER_<?=$siteId?>" class="mail-headers">
									<? CFileMan::AddHTMLEditorFrame(
										 "OPTION_MAIL_SALE_NEW_ORDER_" . $siteId,
										 $arOptions[ 'MAIL_SALE_NEW_ORDER_' . $siteId ],
										 "OPTION_MAIL_SALE_NEW_ORDER_TYPE_" . $siteId,
										 (isset($arOptions[ 'MAIL_SALE_NEW_ORDER_TYPE_' . $siteId ]) ? $arOptions[ 'MAIL_SALE_NEW_ORDER_TYPE_' . $siteId ] : 'html'),
										 array(
												'height' => (strlen($arOptions[ 'MAIL_SALE_NEW_ORDER_' . $siteId ]) > 0 ? 350 : 150),
												'width'  => '100%',
										 ),
										 "N",
										 0,
										 "",
										 "",
										 $siteId,
										 true,
										 false,
										 array(
												'componentFilter' => array('TYPE' => 'mail'),
										 )
									); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td valign="middle" colspan="2">
								<div style="<?=$hintStyle?>"><?=nl2br(Loc::getMessage('AOS_OPTIONS_SITE_ORDER_MAKE_FOOTER'))?></div>
								<div id="MAIL_SALE_NEW_ORDER_FOOTER_<?=$siteId?>" class="mail-headers">
									<? CFileMan::AddHTMLEditorFrame(
										 "OPTION_MAIL_SALE_NEW_ORDER_FOOTER_" . $siteId,
										 $arOptions[ 'MAIL_SALE_NEW_ORDER_FOOTER_' . $siteId ],
										 "OPTION_MAIL_SALE_NEW_ORDER_FOOTER_TYPE_" . $siteId,
										 (isset($arOptions[ 'MAIL_SALE_NEW_ORDER_FOOTER_TYPE_' . $siteId ]) ? $arOptions[ 'MAIL_SALE_NEW_ORDER_FOOTER_TYPE_' . $siteId ] : 'html'),
										 array(
												'height' => (strlen($arOptions[ 'MAIL_SALE_NEW_ORDER_FOOTER_' . $siteId ]) > 0 ? 350 : 150),
												'width'  => '100%',
										 ),
										 "N",
										 0,
										 "",
										 "",
										 $siteId,
										 true,
										 false,
										 array(
												'componentFilter' => array('TYPE' => 'mail'),
										 )
									); ?>
								</div>
							</td>
						</tr>

						<tr class="heading">
							<td colspan="2" style="<?=$sectionStyle?>"><?=Loc::getMessage('AOS_OPTIONS_SITE_MAIL_TYPES')?></td>
						</tr>
						<tr>
							<td valign="middle" colspan="2">
								<div style="<?=$hintStyle?>"><?=nl2br(Loc::getMessage('AOS_OPTIONS_SITE_MAIL_TYPES_HINT'))?></div>
								<table cellpadding="2" cellspacing="2" border="0" width="100%" class="event-type-table">
									<thead>
									<tr class="heading">
										<td align="center">
											<input type="checkbox" class="check-all">
										</td>
										<td>EVENT_TYPE</td>
										<td>EVENT_NAME</td>
									</tr>
									</thead>
									<tbody class="event-type-list">
									<? foreach($arEventTypes as $arEventType): ?>
										<?
										$arETOValues = explode(',', $arOptions[ 'EVENT_TYPE_' . $siteId ]);
										?>
										<tr>
											<td align="center" valign="middle">
												<input type="hidden" name="OPTION[EVENT_TYPE][<?=$siteId?>][<?=$arEventType['EVENT_NAME']?>]" value="0">
												<input type="checkbox" name="OPTION[EVENT_TYPE][<?=$siteId?>][<?=$arEventType['EVENT_NAME']?>]" value="1" <? if(in_array($arEventType['EVENT_NAME'], $arETOValues)): ?> checked="checked"<? endif ?>>
											</td>
											<td valign="middle"><?=$arEventType['EVENT_NAME']?></td>
											<td valign="middle"><?=$arEventType['NAME']?></td>
										</tr>
									<? endforeach; ?>
									</tbody>
								</table>
							</td>
						</tr>

					</table>
					<br>
					<div style="<?=$hintStyle?>"><?=nl2br(Loc::getMessage('AOS_OPTIONS_SITE_RESTORE_HINT'))?></div>
					<a href="" onclick="AOS_RestoreDefaults('<?=$siteId?>'); return false;"><?=Loc::getMessage('AOS_OPTIONS_SITE_BTN_RESTORE')?></a>
					<?
				}
				$tabControl2->End();
				?>
			</td>
		</tr>

		<?
		///////////////////////////////////////////////////////////////////////////
		//                      Настройка свойств корзины
		///////////////////////////////////////////////////////////////////////////
		$tabControl->BeginNextTab();
		?>
		<div style="<?=$hintStyle?>"><?=nl2br(Loc::getMessage('AOS_OPTIONS_CATALOG_HEADER'))?></div>
		<?
		if(CModule::IncludeModule('catalog')) {
			$arIblockIDs   = array();
			$arIblockNames = array();
			$arIblockProps = array();

			$dbCatalog = CCatalog::GetList(array(), array('IBLOCK_ACTIVE' => 'Y'));
			while($arCatalog = $dbCatalog->GetNext()) {
				$arIblockIDs[]                            = $arCatalog["IBLOCK_ID"];
				$arIblockNames[ $arCatalog["IBLOCK_ID"] ] = $arCatalog["NAME"];
			}

			// iblock props
			$arPropNameCodeCount = array();
			foreach($arIblockIDs as $iblockID) {
				$arProps = array();
				$dbProps = CIBlockProperty::GetList(
					 array(
							"SORT" => "ASC",
							"NAME" => "ASC",
					 ),
					 array(
							"IBLOCK_ID"         => $iblockID,
							"ACTIVE"            => "Y",
							"CHECK_PERMISSIONS" => "N",
					 )
				);

				while($prop = $dbProps->GetNext()) {
					$arProps[] = $prop;
				}

				$arIblockProps[ $iblockID ] = $arProps;
			}

			if($arIblockProps) {
				foreach($arIblockProps as $iblockID => $arProps) {
					$arCatOptions = $arOptions['CATALOG'][$iblockID];
					?>
					<tr class="heading">
						<td colspan="2" style="height: 30px;text-transform: uppercase;font-size: 16px"><?=$arIblockNames[ $iblockID ]?> [<?=$iblockID?>]</td>
					</tr>
					<tr>
						<td width="50%"><?=Loc::getMessage('AOS_OPTIONS_CATALOG_ALL')?></td>
						<td width="50%">
							<input type="hidden" name="CATALOG[<?=$iblockID?>]" value="0">
							<input type="checkbox" name="CATALOG[<?=$iblockID?>][]" value="all" <?=($arCatOptions == 'all' ? 'checked=""' : '')?>>
						</td>
					</tr>
					<?
					if($arProps) {
						foreach($arProps as $arProp) {
							?>
							<tr>
								<td width="50%"><?=$arProp['NAME']?> [<?=$arProp['ID']?>]</td>
								<td width="50%">
									<input type="checkbox" name="CATALOG[<?=$iblockID?>][]" value="<?=$arProp['ID']?>" <?=($arCatOptions && preg_match('/'.$arProp['ID'].'/',$arCatOptions) ? 'checked=""' : '')?>>
								</td>
							</tr>
							<?
						}
					}
				}
			}
		}
		?>

		<?
		///////////////////////////////////////////////////////////////////////////
		//                                Помощь
		///////////////////////////////////////////////////////////////////////////
		$tabControl->BeginNextTab();
		echo nl2br(Loc::getMessage('AOS_OPTIONS_TAB_HELP_DESC'));
		?>

		<?
		//Выводим кнопки
		$tabControl->Buttons(
			 array(
				 //"disabled"      => ($MODULE_SALE_RIGHT < "W"),
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
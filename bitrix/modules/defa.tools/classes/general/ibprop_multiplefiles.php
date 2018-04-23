<?

IncludeModuleLangFile(__FILE__);

if (!class_exists("DefaTools_IBProp_MultipleFiles"))
{

	class DefaTools_IBProp_MultipleFiles
	{
		function OnAfterIBlockPropertyHandler(&$arFields)
		{
			if($arFields['USER_TYPE'] == 'DefaToolsMultipleFiles') {
				$arFields['MULTIPLE'] = 'Y';

				if($arFields['MULTIPLE_CNT'] < 2)
					$arFields['MULTIPLE_CNT'] = 2;
			}
		}

		function GetUserTypeDescription()
		{
			return array(
				'PROPERTY_TYPE'			=> 'F',
				'USER_TYPE'				=> 'DefaToolsMultipleFiles',
				'DESCRIPTION'			=> GetMessage("DEFATOOLS_PROP_NAME"),
				'GetPropertyFieldHtml'	=> array('DefaTools_IBProp_MultipleFiles','GetPropertyFieldHtml'),
				'ConvertToDB'			=> array('DefaTools_IBProp_MultipleFiles','ConvertToDB'),
				'ConvertFromDB'			=> array('DefaTools_IBProp_MultipleFiles','ConvertFromDB'),
				'GetSettingsHTML'		=> array('DefaTools_IBProp_MultipleFiles','GetSettingsHTML'),
				'PrepareSettings'		=> array('DefaTools_IBProp_MultipleFiles','PrepareSettings')
			  );
		}

		function PrepareSettings($arFields)
		{
			if(empty($arFields["USER_TYPE_SETTINGS"]["PLUGIN_TYPE_EDIT"]))
				$arFields["USER_TYPE_SETTINGS"]["PLUGIN_TYPE_EDIT"] = 'java';

			return array('PLUGIN_TYPE_EDIT' => trim($arFields["USER_TYPE_SETTINGS"]["PLUGIN_TYPE_EDIT"]));
		}

		function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
		{
			$arPropertyFields = array("USER_TYPE_SETTINGS_TITLE" => GetMessage("DEFATOOLS_USER_TYPE_SETTINGS_TITLE"));

			$html  = '<tr>
						<td>'.GetMessage("DEFATOOLS_PLUGIN_TYPE").':</td>
						<td>
							<select name="'.$strHTMLControlName["NAME"].'[PLUGIN_TYPE_EDIT]">';
			if($arProperty['USER_TYPE_SETTINGS']['PLUGIN_TYPE_EDIT'] == 'java'):
				$html .= ' <option	value="flash">Flash</option>
							<option  value="html5">HTML5</option>
						   <option  selected="selected" value="java">Java applet</option>
				';
			elseif($arProperty['USER_TYPE_SETTINGS']['PLUGIN_TYPE_EDIT'] == 'html5'):
				$html .= ' <option  value="flash">Flash</option>
						   <option	selected="selected" value="html5">HTML5</option>
						   <option  value="java">Java applet</option>
				';
			else:
				$html .= ' <option	selected="selected" value="flash">Flash</option>
						   <option  value="html5">HTML5</option>
						   <option  value="java">Java applet</option>
				';
			endif;
			$html .= '  	</select>
						</td>
					</tr>';

			return $html;
		}

		function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
		{

			global $APPLICATION;
			$plugin_type_edit = $arProperty['USER_TYPE_SETTINGS']['PLUGIN_TYPE_EDIT'];
			$is_public = (is_set($_REQUEST, 'bxpublic') && $_REQUEST['bxpublic'] == 'Y') ? true : false;

			$bFileman = CModule::IncludeModule('fileman');

			ob_start();

			echo '<table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb'.md5('PROP['.$arProperty["ID"].']').'">';

			$cols = $arProperty["COL_COUNT"];

			echo '<tr><td>';

			$val = $value["VALUE"];
			$val_description = $value["DESCRIPTION"];

			if($bFileman)
			{
				// LANG_SHOW_MESS
				if((!isset($_REQUEST['ID']) || $_REQUEST['ID'] <= 0) && empty($GLOBALS['NOTE_ELID_IS_ON_PAGE'. $arProperty['ID']]))
				{
					$GLOBALS['NOTE_ELID_IS_ON_PAGE'. $arProperty['ID']] = 'Y';
					echo BeginNote() . GetMessage("DEFATOOLS_BEFORE_ELEM_SAVE") . EndNote();
				}
				else
				{
					///////////////////////////////////////////
					/// UPLOAD  // HTML5
					///////////////////////////////////////////
					if(isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0 && empty($GLOBALS['HTML5_IS_ON_PAGE'. $arProperty['ID']]) &&  $plugin_type_edit == 'html5') 
					{
						$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/tools/defatools/uploadify/css/file_upload.css">',true);
						?>
						<script type="text/javascript">
							// JS HACKS
							BX.addCustomEvent('BFileDLoadFormControllerInit', BX.delegate(function (data) {
								data.uploadFileUrl += '&prop_id=' + parseInt(data.id.substr(16));
								data.uploadFileUrl += '&defa_html5_upload=Y';
							}));
						</script>
						<?
						$GLOBALS['HTML5_IS_ON_PAGE'. $arProperty['ID']] = 'Y';

						// PHP HACKS
						ob_start();

						?>
						<div class="defa-file-upload">
						<?$APPLICATION->IncludeComponent('bitrix:main.file.input', 'drag_n_drop', array(
							'INPUT_NAME' => 'FILE_NEW'. $arProperty['ID'],
							'MODULE_ID' => 'iblock',
							'MULTIPLE' => 'Y',
							'CONTROL_ID' => 'MULTIPLE_UPLOAD_'. $arProperty['ID'],

						));?>
						</div>
						<?
						$data = ob_get_clean();
						$data = str_replace('input class="file-fileUploader', 'input class="adm-designed-file file-fileUploader', $data);
						echo $data;
					}

					///////////////////////////////////////////
					/// UPLOAD  // FLASH
					///////////////////////////////////////////
					if(isset($_REQUEST['ID']) && $_REQUEST['ID'] > 0 && empty($GLOBALS['FLASH_IS_ON_PAGE'. $arProperty['ID']]) &&  $plugin_type_edit == 'flash'):
						$GLOBALS['FLASH_IS_ON_PAGE'. $arProperty['ID']] = 'Y';

						$tools_dir = '/bitrix/tools/defatools';
						CJSCore::Init('jquery');
						$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="'.$tools_dir.'/uploadify/css/uploadify.css">');
						$APPLICATION->AddHeadString('<script type="text/javascript" src="'.$tools_dir.'/uploadify/js/jquery.uploadify.min.js"></script>');
						?>
						<script type="text/javascript">
							$(function() {
								$('#fileinput_<?= $arProperty['ID'] ?>').uploadify({
									'buttonText' : '<?=GetMessage('DEFATOOLS_MULTIPLE_UPLOAD_BROWSE')?>',
									'swf': '<?=$tools_dir?>/uploadify/swf/uploadify.swf',
									'uploader': '<?=$tools_dir?>/uploadify/uploadify.php',
									'folder': '/bitrix/tmp',
									'auto' : false,
									'cancelImg': '<?=$tools_dir?>/uploadify/toolimages/cancel.png',
									'width'         : '205',
									'formData': {
										'el_id':<?=$_REQUEST['ID']?>,
										'iblock_id':<?=$_REQUEST['IBLOCK_ID']?>,
										'prop_id':<?=$arProperty["ID"]?>,
										'sessid': '<?=bitrix_sessid()?>',
										"<?=session_name()?>": "<?=session_id()?>"
									},
									'multi' : true,

									<?if(!empty($arProperty['FILE_TYPE'])):?>
									<?
									  $str_exts = $arProperty['FILE_TYPE'];
									  if(strpos($arProperty['FILE_TYPE'], ',')):
										$str_exts =	'*.'.str_replace(', ', ';*.', trim($arProperty['FILE_TYPE']));
									  endif;
									?>
									'fileTypeExts' : '<?=$str_exts?>',
									'fileTypeDesc' : '<?=$str_exts?>',
									<?endif;?>

									'onQueueComplete' : function(event, data) {
										<? if ($is_public && method_exists('CIBlock', 'GetAdminElementEditLink')) : ?>
										alert('<?=GetMessage("DEFATOOLS_MULTIPLE_UPLOAD_FLASH_OK")?>');
										top.BX.showWait();
										top.BX.ajax.get(
											'/bitrix/admin/<?echo CUtil::JSEscape(
											CIBlock::GetAdminElementEditLink(intval($_REQUEST['IBLOCK_ID']), intval($_REQUEST['ID']), array(
												"from_module" => "iblock",
												"bxpublic" => "Y",
												"nobuttons" => "Y",
											)
											))?>',
											function (result) {
												top.BX.closeWait();
												top.window.reloadAfterClose = true;
												top.BX.WindowManager.Get().SetContent(result);
											}
										);
										<? elseif ($is_public) : ?>
										<? else: ?>
										alert('<?=GetMessage("DEFATOOLS_MULTIPLE_UPLOAD_FLASH_OK")?>');
										window.location.href = '<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get(), array("sessid"))?>';
										<? endif; ?>
									},
									'onInit' : function() {
										if(IfFlashIsAvailable())
										{
											$('#file_input_outer')
												.css({'width': '520px'})
												.find('.adm-input-file')
												.css({'height': 'auto'})
												.end().find('.adm-input-file').children('span').css({'padding-bottom': '10px', 'display': 'block'});
										}
										else
										{
											$('#file_input_outer').hide();
											$('#dt_flash_unav').show();
										}
									},
									'onUploadError' : function(file, errorCode, errorMsg, errorString) {
										if (errorCode !== -280) {
											alert('<?=GetMessage("DEFATOOLS_MULTIPLE_UPLOAD_FLASH_ERROR")?>');
										}
									}

								});
							});

							function IfFlashIsAvailable () {
								try {
									var flashVersion = '0,0,0',
									Plugin = navigator.plugins['Shockwave Flash'] || ActiveXObject;
									flashVersion = Plugin.description || (function () {
										try {
											return (new Plugin('ShockwaveFlash.ShockwaveFlash')).GetVariable('$version');
										}
										catch (eIE) {}
									}());
								} catch(e) {}
								flashVersion = flashVersion.match(/^[A-Za-z\s]*?(\d+)[\.|,](\d+)(?:\s+[d|r]|,)(\d+)/);

								return flashVersion[1] > 0;

							}

						</script>

					<div id="file_input_outer">
						<div id="fileinput_<?= $arProperty['ID'] ?>" name="fileinput_<?= $arProperty['ID'] ?>" type="file" /></div>
						<a class="uploadify-button download" href="javascript:$('#fileinput_<?= $arProperty['ID'] ?>').uploadify('upload', '*');">
							<span class="uploadify-button-text"><?=GetMessage("DEFATOOLS_UPLOAD_FILES")?></span>
						</a>
						<a class="uploadify-button cancel" href="javascript:$('#fileinput_<?= $arProperty['ID'] ?>').uploadify('cancel', '*');">
							<span class="uploadify-button-text"><?=GetMessage("DEFATOOLS_CLEAR_QUEUE")?></span>
						</a>
					</div>

					<div id="dt_flash_unav" style="display:none;">
						<?=BeginNote().  GetMessage("DEFATOOLS_NOFLASH") .EndNote().'<br />'?>
					</div>

					<br clear="all" /><br clear="all" />

					<?
					endif;

					///////////////////////////////////////////
					/// UPLOAD  // JAVA
					///////////////////////////////////////////

					//  УСЛОВИЕ ВЫВОДА АППЛЕТА - он должен быть только один на странице
					if(
						isset($_REQUEST['ID'])
						&& $_REQUEST['ID'] > 0
						&& empty($GLOBALS['APPLET_IS_ON_PAGE'])
//						&& empty($GLOBALS['APPLET_IS_ON_PAGE'. $arProperty['ID']])
//						&& empty($GLOBALS['APPLET_NOTIFY_IS_ON_PAGE' . $arProperty['ID']])
						&& $plugin_type_edit == 'java'
					):

						$GLOBALS['APPLET_IS_ON_PAGE'] = 'Y'; // Установка флага, что один апплет уже выведен
						$GLOBALS['APPLET_IS_ON_PAGE'. $arProperty['ID']] = 'Y'; // Установка флага, что один апплет уже выведен

						?><script type="text/javascript" src="/bitrix/image_uploader/iuembed.js"></script><?
						include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/image_uploader/version.php");
						include_once($_SERVER['DOCUMENT_ROOT']."/bitrix/image_uploader/localization.php");
						?>

					<div style="border: 1px solid #94918C; float: left; padding: 5px;">
					<div id="defa-java-upload-wrapper"></div>
					<script type="text/javascript">

					function itemColsSelChange2(pEl, e) {
						return true;
					}

					<?
					$strFileMask = '*';
					if(strpos($arProperty['FILE_TYPE'], ',')):
						$strFileMask =	'*.'.str_replace(', ', ';*.', trim($arProperty['FILE_TYPE']));
					endif;

					$arCookie = array();
					foreach ($_COOKIE as $key => $val)
						$arCookie[] = $key."=".$val."; ";

					?>

					window.oColAccess = {};

					//Create JavaScript object that will embed Image Uploader to the page.
					var iu = new ImageUploaderWriter("ImageUploaderML", 570, 300);
					iu.activeXControlCodeBase = "<?=$arAppletVersion["activeXControlCodeBase"]?>";
					iu.activeXClassId = "<?=$arAppletVersion["IuActiveXClassId"]?>";
					iu.activeXControlVersion = "<?=$arAppletVersion["IuActiveXControlVersion"]?>";
					//For Java applet only path to directory with JAR files should be specified (without file name).
					iu.javaAppletCodeBase = "<?=$arAppletVersion["javaAppletCodeBase"]?>";
					iu.javaAppletClassName = "<?=$arAppletVersion["javaAppletClassName"]?>";
					iu.javaAppletJarFileName = "<?=$arAppletVersion["javaAppletJarFileName"]?>";
					iu.javaAppletCached = false;
					iu.javaAppletVersion = "<?=$arAppletVersion["IuJavaAppletVersion"]?>";
					iu.addParam("LicenseKey", "Bitrix");
					iu.addParam("ShowDescriptions", "false");
					iu.addParam("AllowLargePreview", "true");

					//iu.showNonemptyResponse = "on"; // debug
					iu.showNonemptyResponse = "off";
					//Configure appearance.
					iu.addParam("PaneLayout", "TwoPanes");
					iu.addParam("ShowDebugWindow", "true");
					iu.addParam("AllowRotate", "true");
					iu.addParam("BackgroundColor", "#ffffff");
					//Configure URL files are uploaded to.
					//iu.addParam("AdditionalFormName", "ml_upload");
					iu.addParam("Action", "<?=$APPLICATION->GetCurPageParam('action=upload&'.bitrix_sessid_get(), array("action", "sessid"))?>");
					iu.addParam("RedirectUrl", "");
					iu.addParam("FileMask", "<?= $strFileMask?>");
					language_resources.addParams(iu);

					function ImageUploaderML_AfterUpload(Html)
					{
						<? if ($is_public && method_exists('CIBlock', 'GetAdminElementEditLink')) : ?>
						top.BX.showWait();
						top.BX.ajax.get(
							'/bitrix/admin/<?echo CUtil::JSEscape(
											CIBlock::GetAdminElementEditLink(intval($_REQUEST['IBLOCK_ID']), intval($_REQUEST['ID']), array(
												"from_module" => "iblock",
												"bxpublic" => "Y",
												"nobuttons" => "Y",
											)
											))?>',
							function (result) {
								top.BX.closeWait();
								top.window.reloadAfterClose = true;
								top.BX.WindowManager.Get().SetContent(result);
							}
						);
						<? elseif ($is_public) : ?>
						<? else : ?>
						try
						{
							var i1 = Html.indexOf('#JS#') + 4,
								i2 = Html.lastIndexOf('#JS#'),
								sGet = (i1 != -1 && i2 != i1) ? Html.substring(i1, i2) : '';
							return jsUtils.Redirect([], "<?=$APPLICATION->GetCurPageParam('action=redirect&'.bitrix_sessid_get(), array("action", "sessid"))?>" + sGet);

						}
						catch(e)
						{
							return jsUtils.Redirect([], "<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get(), array("sessid"))?>");
						}
						<? endif; ?>

					}
					iu.addEventListener("AfterUpload", "ImageUploaderML_AfterUpload");

					function ImageUploaderML_BeforeUpload()
					{
						if (iu.getControlType() == "Java")
					  		getImageUploader("ImageUploaderML").AddCookie('<?=CUtil::JSEscape(implode("", $arCookie))?>');
					}
					iu.addEventListener("BeforeUpload", "ImageUploaderML_BeforeUpload");

					//Tell Image Uploader writer object to generate all necessary HTML code to embed Image Uploader to the page.
					document.getElementById('defa-java-upload-wrapper').innerHTML = iu.getHtml();
					</script>
					</div>
					<br clear="all" /><br clear="all" />

					<?
					//  УСЛОВИЕ ВЫВОДА АППЛЕТА - он должен быть только один на странице
					elseif(
						isset($_REQUEST['ID'])
						&& $_REQUEST['ID'] > 0
						&& !empty($GLOBALS['APPLET_IS_ON_PAGE'])
						&& empty($GLOBALS['APPLET_IS_ON_PAGE'. $arProperty['ID']])
						&& empty($GLOBALS['APPLET_NOTIFY_IS_ON_PAGE' . $arProperty['ID']])
						&& $plugin_type_edit == 'java'
					):
						$GLOBALS['APPLET_NOTIFY_IS_ON_PAGE' . $arProperty['ID']] = "Y";
						echo BeginNote() . GetMessage('DEFATOOLS_MULTIPLE_UPLOAD_MULTIPLE_APPLETS_ERROR') . EndNote();

					elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'upload'):

						$fileCount = intval($_POST['FileCount']);
						if ($fileCount > 0):

							for ($i = 1; $i <= $fileCount; $i++):
								$arFile = $_FILES['SourceFile_'.$i];
								$arFile["MODULE_ID"] = "iblock";
								self::SaveFile($_REQUEST['ID'], $_REQUEST['IBLOCK_ID'], $arProperty['ID'], $arFile);

							endfor;
						endif;
						die('#JS#&files_up='. ($i - 1) .'#JS#');

					endif; // APPLET_IS_ON_PAGE

					echo CMedialib::InputFile(	str_replace('[VALUE]', '', $strHTMLControlName["VALUE"]), $val,
						array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y",
						"IMAGE_POPUP"=>"Y", "MAX_SIZE" => array("W" => 200, "H"=>200)), //info
						array("SIZE"=>$cols), //file
						array(), //server
						array(), //media lib
						($arProperty["WITH_DESCRIPTION"]=="Y"?
							array("NAME" => "DESCRIPTION_".$strHTMLControlName["VALUE"]
								, "VALUE" => $val_description
							):
							false
						), //descr
						array() //delete
					);
				}
			} //if($bFileman)
			else
			{
				echo CFile::InputFile($strHTMLControlName["VALUE"], $cols, $val, false, 0, "")."<br>";
				echo CFile::ShowFile($val, $max_file_size_show, 400, 400, true)."<br>";

				if($arProperty["WITH_DESCRIPTION"]=="Y")
					echo ' <span title="'.GetMessage("DEFATOOLS_ELEMENT_EDIT_PROP_DESC").'">'.GetMessage("DEFATOOLS_ELEMENT_EDIT_PROP_DESC_1").'<input name="DESCRIPTION_'.$strHTMLControlName["VALUE"].'" value="'.htmlspecialcharsex($val_description).'" size="18" type="text"></span>';

			}
			echo '<br></td></tr>';
			echo '</table>';

			$return = ob_get_contents();
			ob_end_clean();

			return $return;
		}

		function ConvertToDB($arProperty, $value){
			$return = array();

			// setting description
			if ($arProperty['WITH_DESCRIPTION'] == "Y" && is_array($value["DESCRIPTION"])) {
				$description = trim($value["DESCRIPTION"]["VALUE"]);
			} else {
				$description = "";
			}

			if ($value['VALUE']['del'] === "Y") {
				$new_del = true;
			}

			if (is_set($value, 'del')) {
				$del = $value['del'];
			} else {
				$del = null;
			}
			// assume that $value contains value only for 1 item
			$value = $value['VALUE'];

			// and again)
			if (isset($value['VALUE'])) {
				if (is_set($value, 'del')) {
					$del = $value['del'];
				} else {
					$del = null;
				}

				$value = $value['VALUE'];
			}

			// save file
			$return['DESCRIPTION'] = $description;

			if (isset($value["size"]) && $value["size"] > 0){

				$arFile              = $value;
				$arFile["MODULE_ID"] = "iblock";

				$file_id = CFile::SaveFile($arFile, "iblock");

				if (intval($file_id) > 0){
					$return['VALUE'] = $file_id;
				}
				// remove file
			} elseif ($new_del || ($del && !empty($del['VALUE']))) {
				$arFile["MODULE_ID"] = "iblock";
				$arFile["del"]       = "Y";

				$return['VALUE'] = $arFile;
			} else {
				$return['VALUE'] = $value;
			}

			return $return;
		}

		function ConvertFromDB($arProperty, $value)
		{
			return $value;
		}

		function OnMainFileInputUploadHandler($tmp)
		{
			if (isset($_REQUEST['defa_html5_upload']) && $_REQUEST["defa_html5_upload"] == "Y") {
				self::SaveFile($_REQUEST["ID"], $_REQUEST["IBLOCK_ID"], $_REQUEST["prop_id"], $tmp);
			}
		}

		function SaveFile($ELEMENT_ID, $IBLOCK_ID, $PROP_ID, $tmp_file)
		{
			$ELEMENT_ID = intval($ELEMENT_ID);
			$IBLOCK_ID = intval($IBLOCK_ID);
			$PROP_ID = intval($PROP_ID);

			if (!check_bitrix_sessid() && empty($tmp_file)) {
				return false;
			}

			if (!is_set($tmp_file, "fileID")) {
				$tmp_file = array('fileID' => $tmp_file);
				CFile::SaveForDB($tmp_file, 'fileID', "iblock");
			}

			if (
				$tmp_file['fileID'] > 0
				&& $ELEMENT_ID > 0 && $IBLOCK_ID > 0 && $PROP_ID > 0
				&& CModule::IncludeModule('iblock')
				&& ($resElement= CIBlockElement::GetList(array(), array("IBLOCK_ID" => $IBLOCK_ID, "ID" => $ELEMENT_ID), false, false, array("ID"))->Fetch())
				&& CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $ELEMENT_ID, "element_edit")
			) {
				CIBlockElement::SetPropertyValues($ELEMENT_ID, $IBLOCK_ID, $tmp_file['fileID'], $PROP_ID);
				return true;
			}

			return false;
		}
	}
} // class exists

?>

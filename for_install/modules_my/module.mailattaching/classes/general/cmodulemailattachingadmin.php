<?
class CModuleMailAttachingAdmin {
	// OnAdminTabControlBegin handler
	public static function AddEditFormTab(&$obTabControl) {

		if(MODULE_MAILATTACHING_USE_CUSTOM_MAIL && $GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/message_edit.php') {
			
			$sFileFieldId = 'module_mailattaching_tmp_file';
			$sFileStrJs = '';
			$sFileStrJs .= '<div style="margin: 0 0 3px 0;" class="module-mailattaching-file-box">';
			$sFileStrJs .= '<input class="module-mailattaching-file-path" size="30" style="width: 300px;" onchange="return module_mailattaching_updateVals(this);" ondblclick="return module_mailattaching_showFileDialog(this)" type="text" />';
			$sFileStrJs .= '<input class="module-mailattaching-file-button" size="10" style="width: 30px; margin-left: 2px;" value="..." type="button" onclick="return module_mailattaching_showFileDialog(this);" />';
			$sFileStrJs .= '<span class="module-mailattaching-file-box-add" onclick="return module_mailattaching_AddFileBox(this);" title="'.GetMessage('MODULE_MAILATTACHING_MORE_FILE').'" style="cursor: pointer; font-family: Tahoma, Arial; font-size: 20px; font-weight: bold; line-height: 20px; color: green; margin: 0 5px 0 10px;">+</span>';
			$sFileStrJs .= '<span class="module-mailattaching-file-box-del" onclick="return module_mailattaching_DelFileBox(this);" title="'.GetMessage('MODULE_MAILATTACHING_DEL_FIELD').'" style="cursor: pointer; font-family: Tahoma, Arial; font-size: 20px; font-weight: bold; line-height: 20px; color: red;">&ndash;</span>';
			$sFileStrJs .= '</div>';
			ob_start();

			CAdminFileDialog::ShowScript(
				array(
					'event' => 'BX_FD_'.$sFileFieldId,
					'arResultDest' => array(
						'FUNCTION_NAME' => 'BX_FD_ONRESULT_'.$sFileFieldId
					),
					'arPath' => array(),
					'select' => 'F',
					'operation' => 'O',
					'showUploadTab' => true,
					'showAddToMenuTab' => false,
					'fileFilter' => '',
					'allowAllFiles' => true,
					'SaveConfig' => true
				)
			);

			?><style type="text/css">
			td.adm-detail-content-cell-l {
				vertical-align: top;
			}
			td.adm-detail-content-cell-l input {
				margin-right: 5px;
			}</style>
			<script type="text/javascript">
				var obModuleMailattachingButton = null;
				var module_mailattaching_showFileDialog = function(obElement) {
					obModuleMailattachingButton = null;
					if(BX.hasClass(obElement, 'module-mailattaching-file-button')) {
						obModuleMailattachingButton = obElement;
					} else if(BX.hasClass(obElement, 'module-mailattaching-file-path')) {
						obModuleMailattachingButton = obElement.nextSibling;
						if(!BX.hasClass(obModuleMailattachingButton, 'module-mailattaching-file-button')) {
							obModuleMailattachingButton = null;
						}
					}
					window.BX_FD_<?=$sFileFieldId?>();
				};

				var module_mailattaching_getCont = function(obElement) {
					var obCont = BX.findParent(obElement, {'tag': 'div', 'className': 'module-mailattaching-file-cont'});
					return obCont;
				};
				
				var BX_FD_ONRESULT_<?=$sFileFieldId?> = function(sFilename, sFilepath) {
					if(obModuleMailattachingButton) {
						sFilepath = sFilepath == '/' ? '' : sFilepath;
						var obFilesBox = BX.findParent(obModuleMailattachingButton, {'tag': 'div', 'className': 'module-mailattaching-file-box'});
						if(obFilesBox) {
							var obField = BX.findChild(obFilesBox, {'tag': 'input', 'type': 'text', 'className': 'module-mailattaching-file-path'}, true, false);
							if(obField) {
								obField.value = sFilepath+'/'+sFilename;
								module_mailattaching_updateVals(obField);
							}
						}
						obModuleMailattachingButton.previousSibling;
					}
				};

				var module_mailattaching_updateVals = function(obElement) {
					if(obElement) {
						var obCont = module_mailattaching_getCont(obElement);
						var sResultVar = '';
						var sGlueStr = '';
						var obFilesBlock = BX.findChild(obCont, {'tag': 'div', 'className': 'module-mailattaching-file-block'}, true, false);
						var obInputCollecttion = BX.findChild(obFilesBlock, {'tag': 'input', 'type': 'text', 'className': 'module-mailattaching-file-path'}, true, true);
						if(obInputCollecttion) {
							for(var mIdx in obInputCollecttion) {
								var sVal = BX.util.trim(obInputCollecttion[mIdx].value);
								if(sVal.length) {
									sResultVar += sGlueStr+sVal;
									sGlueStr = ';';
								}
							}
						}
						var obCell = BX.findParent(obCont, {'tag': 'td'});
						if(obCell) {
							var obInput = BX.findChild(obCell, {'tag': 'input', 'type': 'text', 'readOnly': true}, true, false);
							if(obInput) {
								obInput.value = sResultVar;
							}
						}
					}
				};

				var module_mailattaching_getCloneFileBox = function(obCloneElement) {
					obClone = obCloneElement.cloneNode(true);
					if(obClone) {
						var obChilds = obClone.childNodes;
						for(var iIdx in obChilds) {
							if(obChilds[iIdx].type && obChilds[iIdx].type == 'text') {
								obChilds[iIdx].value = '';
							}
						}
					}
					return obClone;
				}

				var module_mailattaching_AddFileBox = function(obElement) {
					var obCont = module_mailattaching_getCont(obElement);

					var obFileBox = BX.findChild(obCont, {'tag': 'div', 'className': 'module-mailattaching-file-box'}, true, false);
					var obFilesBlock = BX.findChild(obCont, {'tag': 'div', 'className': 'module-mailattaching-file-block'}, true, false);
					var obFileBoxClone = null;
					if(obFileBox && obFilesBlock) {
						var obFileBoxClone = module_mailattaching_getCloneFileBox(obFileBox);
						if(obFileBoxClone) {
							obFilesBlock.appendChild(obFileBoxClone);
						}
					}
				};

				var module_mailattaching_DelFileBox = function(obElement) {
					var obFileBox = BX.findParent(obElement, {'tag': 'div', 'className': 'module-mailattaching-file-box'});

					if(obFileBox) {
						var obCont = module_mailattaching_getCont(obElement);
						var obFileBoxCollection = BX.findChild(obCont, {'tag': 'div', 'className': 'module-mailattaching-file-box'}, true, true);
						if(obFileBoxCollection && obFileBoxCollection.length > 1) {
							var obFileBlock = BX.findParent(obFileBox, {'tag': 'div', 'className': 'module-mailattaching-file-block'});
							var obField = BX.findChild(obFileBox, {'tag': 'input', 'className': 'module-mailattaching-file-path'}, true, false);
							var bConfirmed = true;
							if(obField && obField.value.length) {
								bConfirmed = confirm("<?=GetMessage('MODULE_MAILATTACHING_DEL_CONFIRM')?>");
							}
							if(bConfirmed) {
								obFileBlock.removeChild(obFileBox);
							}
							module_mailattaching_updateVals(obFileBlock);
						}
					}
				};

				(function() {
					BX.ready(
						function() {
							var bGetNext = true;
							var obFieldRow = null;

							var addFileAttachesCtrl = function(obFieldRow, sNameFieldName, sValFieldName, sCheckboxId, sBlockId) {
								var obNameField = BX.findChild(obFieldRow, {'tag': 'input', 'attr': {'name': sNameFieldName}}, true, false);
								var obValField = BX.findChild(obFieldRow, {'tag': 'input', 'attr': {'name': sValFieldName}}, true, false);
								if(!obNameField || !obValField) {
									return false;
								}

								var checkboxHandler = function(obCheckbox, sFileBoxId) {
									var bReadonly = false;
									var bContinue = true;
									var sVal_1 = '';
									var sVal_2 = '';

									if(obCheckbox.checked) {
										sVal_1 = BX.util.trim(obNameField.value);
										sVal_2 = BX.util.trim(obValField.value);
										if((sVal_1.length && sVal_1 != 'ATTACHED-FILES') || (sVal_1 != 'ATTACHED-FILES' && sVal_2.length)) {
											var bConfirmed = confirm("<?=GetMessage('MODULE_MAILATTACHING_CHECKED_CONFIRM')?>");
											bContinue = !bConfirmed ? false : bContinue;
											if(!bConfirmed) {
												obCheckbox.checked = false;
												return false;
											}
										}
									}
									if(bContinue) {
										if(obCheckbox.checked) {
											obNameField.value = 'ATTACHED-FILES';
											bReadonly = true;
											if(sVal_1 != 'ATTACHED-FILES' && sVal_2.length) {
												obValField.value = '';
											}

											var obFilesBlock = document.getElementById(sBlockId);
											var obFileBoxClone = null;
											var obFileBoxCollect = null;
											if(obFilesBlock) {
												obFileBoxCollect = BX.findChild(obFilesBlock, {'tag': 'div', 'className': 'module-mailattaching-file-box'}, true, true);
												if(obFileBoxCollect) {
													var iCnt = obFileBoxCollect.length;
													for(var mIdx in obFileBoxCollect) {
														if(!obFileBoxClone) {
															obFileBoxClone = module_mailattaching_getCloneFileBox(obFileBoxCollect[mIdx]);
														}
														obFilesBlock.removeChild(obFileBoxCollect[mIdx]);
													}
												}
											}
											if(obFilesBlock && obFileBoxClone) {
												var arVals = [];
												if(sVal_1 == 'ATTACHED-FILES' && sVal_2.length) {
													arVals = sVal_2.split(/,|;/);
												}

												if(arVals.length) {
													for(var mIdx in arVals) {
														var sValue = BX.util.trim(arVals[mIdx]);
														if(sValue.length) {
															var obFileField = BX.findChild(obFileBoxClone, {'tag': 'input', 'className': 'module-mailattaching-file-path'}, true, false);
															var obChilds = obFileBoxClone.childNodes;
															for(var iIdx in obChilds) {
																if(obChilds[iIdx].type && obChilds[iIdx].type == 'text') {
																	obChilds[iIdx].value = sValue;
																}
															}
															obFilesBlock.appendChild(obFileBoxClone);
															obFileBoxClone = module_mailattaching_getCloneFileBox(obFileBoxClone);
														}
													}
												}
												obFilesBlock.appendChild(obFileBoxClone);
											}
										}
										obNameField.readOnly = bReadonly;
										obValField.readOnly = bReadonly;

										if(sFileBoxId) {
											var obFileBox = document.getElementById(sFileBoxId);
											if(obFileBox) {
												obFileBox.style.display = bReadonly ? 'block' : 'none';
												module_mailattaching_updateVals(obFileBox);
											}
										}
									}
								};

								try {
									obFieldRow.style.display = 'table-row';
								} catch(e) {
									obFieldRow.style.display = 'block';
								}
								var obCells = BX.findChild(obFieldRow, {'tag': 'td'}, true, true);
								var obCell_2 = null;
								if(obCells && (obCell_2 = obCells[1])) {
									obCell_2.appendChild(
										BX.create('div', {
											props: {'className': 'module-mailattaching-file-cont'},
											style: {},
											html: '<div style="margin: 0 0 5px 0;"><label style="vertical-align: middle;"><input style="vertical-align: middle;" id="'+sCheckboxId+'" type="checkbox" /><?=GetMessage('MODULE_MAILATTACHING_CHECKBOX')?></label></div>'
											+'<div id="'+sBlockId+'" class="module-mailattaching-file-block" style="display: none; margin: 0 0 10px 0; padding: 6px 7px; border: dashed 1px #cccccc;">'
												+'<div class="module-mailattaching-file-title" style="margin: 0 0 5px 0;"><?=GetMessage('MODULE_MAILATTACHING_CHECKBOX_TEXT')?></div>'
												+'<?=$sFileStrJs?>'
											+'</div>'
										})
									);
									var obCheckbox = document.getElementById(sCheckboxId);
									BX.bind(
										obCheckbox, 
										'click', 
										function() {
											checkboxHandler(this, sBlockId);
										}
									);

									if(obNameField && obValField) {
										if(obNameField.value == 'ATTACHED-FILES' && obValField.value.length) {
											obCheckbox.click();
										}
									}
								}
							};

							var bTmpRes = false;
							if(bGetNext && (obFieldRow = document.getElementById('msg_ext6'))) {
								bTmpRes = addFileAttachesCtrl(obFieldRow, 'FIELD1_NAME', 'FIELD1_VALUE', 'module-mailattaching-checkbox-1', 'module-mailattaching-files-block-1');
								if(!bTmpRes) {
									bTmpRes = addFileAttachesCtrl(obFieldRow, 'ADDITIONAL_FIELD[NAME][]', 'ADDITIONAL_FIELD[VALUE][]', 'module-mailattaching-checkbox-1', 'module-mailattaching-files-block-1');
								}
							}
							if(bGetNext && (obFieldRow = document.getElementById('msg_ext7'))) {
								bTmpRes = addFileAttachesCtrl(obFieldRow, 'FIELD2_NAME', 'FIELD2_VALUE', 'module-mailattaching-checkbox-2', 'module-mailattaching-files-block-2');
								if(!bTmpRes) {
									bTmpRes = addFileAttachesCtrl(obFieldRow, 'ADDITIONAL_FIELD[NAME][]', 'ADDITIONAL_FIELD[VALUE][]', 'module-mailattaching-checkbox-2', 'module-mailattaching-files-block-2');
								}
							}
						}
					);
				}());
			</script><?
			$sContent = ob_get_clean();
			$GLOBALS['APPLICATION']->AddHeadString($sContent);
		}
	}
}

<?php

IncludeModuleLangFile(__FILE__);

class CASDiblockAction {

	public static function OnBeforePrologHandler() {

		global $USER_FIELD_MANAGER;

		if (isset($_REQUEST['action_button']) && !isset($_REQUEST['action'])) {
			$_REQUEST['action'] = $_REQUEST['action_button'];
		}
		if (!isset($_REQUEST['action'])) {
			return;
		}
		$BID = (isset($_REQUEST['ID']) ? (int)$_REQUEST['ID'] : 0);

		if ($_REQUEST['action']=='asd_prop_export' && $BID>0 && check_bitrix_sessid() &&
			CModule::IncludeModule('iblock') && CASDIblockRights::IsIBlockEdit($BID)
		) {
			$strPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/tmp/asd.iblock/';
			$strName = 'asd_props_export_'.$BID.'_'.md5(LICENSE_KEY).'.xml';
			CheckDirPath($strPath);
			if ($hdlOutput = fopen($strPath.$strName, 'wb')) {
				fwrite($hdlOutput, '<?xml version="1.0" encoding="'.SITE_CHARSET.'"?>'."\n");
				fwrite($hdlOutput, '<asd_iblock_props>'."\n");
				fwrite($hdlOutput, CASDiblockTools::ExportPropsToXML($BID, $_REQUEST['p']));
				if ($_REQUEST['forms'] == 'Y') {
					fwrite($hdlOutput, CASDiblockTools::ExportSettingsToXML($BID, array('forms')));
				}
				fwrite($hdlOutput, '</asd_iblock_props>'."\n");
				fclose($hdlOutput);
			}
			?><script type="text/javascript">
				top.BX.closeWait(); top.BX.WindowManager.Get().AllowClose(); top.BX.WindowManager.Get().Close();
				window.location.href = '/bitrix/tools/asd.iblock/props_export.php?ID=<? echo $BID; ?>';
			</script><?
			die();
		}

		if ($_REQUEST['action']=='asd_prop_import' && $BID>0 && !$_FILES['xml_file']['error'] &&
			check_bitrix_sessid() && CModule::IncludeModule('iblock') && CASDIblockRights::IsIBlockEdit($BID)
		) {
			CASDiblockTools::ImportPropsFromXML($BID, $_FILES['xml_file']['tmp_name'], $arOldNewID);
			CASDiblockTools::ImportFormsFromXML($BID, $_FILES['xml_file']['tmp_name'], $arOldNewID);
			LocalRedirect('/bitrix/admin/iblock_edit.php?type='.$_REQUEST['type'].'&tabControl_active_tab=edit2&lang='.LANGUAGE_ID.'&ID='.$BID.'&admin=Y');
		}

		$IBLOCK_ID = 0;
		if (isset($_REQUEST['IBLOCK_ID'])) {
			$IBLOCK_ID = (int)$_REQUEST['IBLOCK_ID'];
			if ($IBLOCK_ID < 0) {
				$IBLOCK_ID = 0;
			}
		}

		if ($_REQUEST['action']=='asd_reverse' && $IBLOCK_ID>0 && check_bitrix_sessid() &&
			CModule::IncludeModule('iblock') && CASDIblockRights::IsIBlockEdit($IBLOCK_ID)
		) {
			$LIST_MODE = CIBlock::GetArrayByID($IBLOCK_ID, 'LIST_MODE');
			if (!strlen($LIST_MODE)) {
				$LIST_MODE = COption::GetOptionString('iblock', 'combined_list_mode', 'N')=='Y' ? 'C' : 'S';
			}
			$LIST_MODE = $LIST_MODE=='C' ? 'S' : 'C';
			$ib = new CIBlock();
			$ib->Update($IBLOCK_ID, array('LIST_MODE' => $LIST_MODE));
			LocalRedirect('/bitrix/admin/'.($LIST_MODE == 'S' ? 'iblock_element_admin' : 'iblock_list_admin').'.php?IBLOCK_ID='.$IBLOCK_ID.
																'&type='.htmlspecialcharsbx($_REQUEST['type']).
																'&find_section_section='.intval($_REQUEST['find_section_section']).
																'&lang='.LANGUAGE_ID);
		}

		$strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
		$bElemPage = ($strCurPage=='/bitrix/admin/iblock_element_admin.php' ||
						$strCurPage=='/bitrix/admin/cat_product_admin.php'
					);
		$bSectPage = ($strCurPage=='/bitrix/admin/iblock_section_admin.php' ||
						$strCurPage=='/bitrix/admin/cat_section_admin.php'
					);
		$bMixPage = ($strCurPage=='/bitrix/admin/iblock_list_admin.php');
		$bRightPage = ($bElemPage || $bSectPage || $bMixPage);
		$successRedirect = false;

		if ($bRightPage && $_REQUEST['action']=='asd_copy_in_list' && strlen($_REQUEST['ID'])>0) {
			$bDoAction = true;
			$_REQUEST['action'] = 'asd_copy';
			$_REQUEST['asd_ib_dest'] = $IBLOCK_ID;
			$_REQUEST['ID'] = array($_REQUEST['ID']);
		} else {
			$bDoAction = false;
		}

		if ($bRightPage && check_bitrix_sessid() && !empty($_REQUEST['ID']) &&
			($_SERVER['REQUEST_METHOD']=='POST' || $bDoAction) && CModule::IncludeModule('iblock') &&
			($_REQUEST['action']=='asd_copy' || $_REQUEST['action']=='asd_move') &&
			isset($_REQUEST['asd_ib_dest']) && (int)$_REQUEST['asd_ib_dest'] > 0 &&
			CASDIblockRights::IsIBlockDisplay($_REQUEST['asd_ib_dest'])
		) {
			$intSrcIBlockID = $IBLOCK_ID;
			$intDestIBlockID = (int)$_REQUEST['asd_ib_dest'];

			$intSetSectID = 0;
			if (isset($_REQUEST['asd_sect_dest'])) {
				$intSetSectID = (int)$_REQUEST['asd_sect_dest'];
				if ($intSetSectID < 0) {
					$intSetSectID = 0;
				}
			}

			$boolCreateElement = false;
			$boolCreateSection = false;

			if ($bElemPage || $bMixPage) {
				$boolCreateElement = CASDIblockRights::IsSectionElementCreate($intDestIBlockID, $intSetSectID);
			}
			if ($bSectPage || $bMixPage) {
				$boolCreateSection = CASDIblockRights::IsSectionSectionCreate($intDestIBlockID, $intSetSectID);
			}

			if ($boolCreateElement || $boolCreateSection) {
				$arPropListCache = array();
				$arOldPropListCache = array();
				$arNamePropListCache = array();
				$arOldNamePropListCache = array();

				$boolUFListCache = false;
				$arUFListCache = array();
				$arOldUFListCache = array();
				$arUFEnumCache = array();
				$arOldUFEnumCache = array();
				$arUFNameEnumCache = array();
				$arOldUFNameEnumCache = array();

				$arDestIBlock = CIBlock::GetArrayByID($intDestIBlockID);
				$arDestIBFields = $arDestIBlock['FIELDS'];
				$boolCodeUnique = false;
				if ($arDestIBFields['CODE']['DEFAULT_VALUE']['UNIQUE'] == 'Y') {
					$boolCodeUnique = ($intSrcIBlockID == $intDestIBlockID);
				}
				$boolSectCodeUnique = false;
				if ($arDestIBFields['SECTION_CODE']['DEFAULT_VALUE']['UNIQUE'] == 'Y') {
					$boolSectCodeUnique = ($intSrcIBlockID == $intDestIBlockID);
				}

				$boolCatalog = CModule::IncludeModule('catalog');
				$boolCopyCatalog = false;
				$boolNewCatalog = false;
				if ($boolCatalog) {
					$boolCopyCatalog = (is_array(CCatalog::GetByID($intDestIBlockID)));
					$boolNewCatalog = $boolCopyCatalog;
					if ($boolCopyCatalog) {
						$boolCopyCatalog = (is_array(CCatalog::GetByID($intSrcIBlockID)));
					}
				}

				$el = new CIBlockElement();
				$sc = new CIBlockSection();
				$obEnum = new CUserFieldEnum();
				foreach ($_REQUEST['ID'] as $eID) {
					$boolCopyElem = false;
					$boolCopySect = false;
					if ($bMixPage) {
						if (substr($eID, 0, 1) != 'E') {
							$boolCopySect = true;
						} else {
							$boolCopyElem = true;
						}
						$ID = (int)substr($eID, 1);
					} else {
						$boolCopyElem = $bElemPage;
						$boolCopySect = $bSectPage;
						$ID = (int)$eID;
					}
					if ($boolCreateElement && $boolCopyElem) {
						if ($obSrc = CIBlockElement::GetByID($ID)->GetNextElement()) {
							$arSrc = $obSrc->GetFields();
							$arSrcPr = $obSrc->GetProperties(false, array('EMPTY' => 'N'));
							$arSrc['PREVIEW_PICTURE'] = (int)$arSrc['PREVIEW_PICTURE'];
							if ($arSrc['PREVIEW_PICTURE'] > 0) {
								$arSrc['PREVIEW_PICTURE'] = CFile::MakeFileArray($arSrc['PREVIEW_PICTURE']);
								if (empty($arSrc['PREVIEW_PICTURE'])) {
									$arSrc['PREVIEW_PICTURE'] = false;
								} else {
									$arSrc['PREVIEW_PICTURE']['COPY_FILE'] = 'Y';
								}
							} else {
								$arSrc['PREVIEW_PICTURE'] = false;
							}
							$arSrc['DETAIL_PICTURE'] = (int)$arSrc['DETAIL_PICTURE'];
							if ($arSrc['DETAIL_PICTURE'] > 0) {
								$arSrc['DETAIL_PICTURE'] = CFile::MakeFileArray($arSrc['DETAIL_PICTURE']);
								if (empty($arSrc['DETAIL_PICTURE'])) {
									$arSrc['DETAIL_PICTURE'] = false;
								}
								else {
									$arSrc['DETAIL_PICTURE']['COPY_FILE'] = 'Y';
								}
							}
							else {
								$arSrc['DETAIL_PICTURE'] = false;
							}
							$arSrc = array(
								'IBLOCK_ID' => $intDestIBlockID,
								'ACTIVE' => $arSrc['ACTIVE'],
								'ACTIVE_FROM' => $arSrc['ACTIVE_FROM'],
								'ACTIVE_TO' => $arSrc['ACTIVE_TO'],
								'SORT' => $arSrc['SORT'],
								'NAME' => $arSrc['~NAME'],
								'PREVIEW_PICTURE' => $arSrc['PREVIEW_PICTURE'],
								'PREVIEW_TEXT' => $arSrc['~PREVIEW_TEXT'],
								'PREVIEW_TEXT_TYPE' => $arSrc['PREVIEW_TEXT_TYPE'],
								'DETAIL_TEXT' => $arSrc['~DETAIL_TEXT'],
								'DETAIL_TEXT_TYPE' => $arSrc['DETAIL_TEXT_TYPE'],
								'DETAIL_PICTURE' => $arSrc['DETAIL_PICTURE'],
								'WF_STATUS_ID' => $arSrc['WF_STATUS_ID'],
								'CODE' => $arSrc['~CODE'],
								'TAGS' => $arSrc['~TAGS'],
								'XML_ID' => $arSrc['~XML_ID'],
								'PROPERTY_VALUES' => array(),
							);
							if ($arDestIBFields['CODE']['IS_REQUIRED'] == 'Y') {
								if (!strlen($arSrc['CODE'])) {
									$arSrc['CODE'] = mt_rand(100000, 1000000);
								}
							}
							if ($arDestIBFields['CODE']['DEFAULT_VALUE']['UNIQUE'] == 'Y') {
								$boolElCodeUnique = $boolCodeUnique;
								if (!$boolCodeUnique) {
									$rsCheckItems  = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intDestIBlockID, '=CODE' => $arSrc['CODE'], 'CHECK_PERMISSIONS' => 'N'),
																	false, array('nTopCount' => 1), array('ID', 'IBLOCK_ID'));
									if ($arCheck = $rsCheckItems->Fetch()) {
										$boolElCodeUnique = true;
									}
								}
								if ($boolElCodeUnique) {
									$arSrc['CODE'] .= mt_rand(100, 10000);
								}
							}
							if ($intSetSectID > 0) {
								$arSrc['IBLOCK_SECTION_ID'] = $intSetSectID;
							} elseif ($intSrcIBlockID == $intDestIBlockID) {
								$arSectionList = array();
								$rsSections = CIBlockElement::GetElementGroups($ID, true);
								while ($arSection = $rsSections->Fetch()) {
									$arSectionList[] = $arSection['ID'];
								}
								$arSrc['IBLOCK_SECTION'] = $arSectionList;
							}
							if ($intSrcIBlockID != $intDestIBlockID) {
								if (empty($arPropListCache)) {
									$rsProps = CIBlockProperty::GetList(
										array(),
										array('IBLOCK_ID' => $intDestIBlockID, 'PROPERTY_TYPE' => 'L', 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
									);
									while ($arProp = $rsProps->Fetch()) {
										$arValueList = array();
										$arNameList = array();
										$rsValues = CIBlockProperty::GetPropertyEnum($arProp['ID']);
										while ($arValue = $rsValues->Fetch()) {
											$arValueList[$arValue['XML_ID']] = $arValue['ID'];
											$arNameList[$arValue['ID']] = trim($arValue['VALUE']);
										}
										if (!empty($arValueList)) {
											$arPropListCache[$arProp['CODE']] = $arValueList;
										}
										if (!empty($arNameList)) {
											$arNamePropListCache[$arProp['CODE']] = $arNameList;
										}
									}
								}
								if (empty($arOldPropListCache)) {
									$rsProps = CIBlockProperty::GetList(
										array(),
										array('IBLOCK_ID' => $intSrcIBlockID, 'PROPERTY_TYPE' => 'L', 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
									);
									while ($arProp = $rsProps->Fetch()) {
										$arValueList = array();
										$arNameList = array();
										$rsValues = CIBlockProperty::GetPropertyEnum($arProp['ID']);
										while ($arValue = $rsValues->Fetch()) {
											$arValueList[$arValue['ID']] = $arValue['XML_ID'];
											$arNameList[$arValue['ID']] = trim($arValue['VALUE']);
										}
										if (!empty($arValueList)) {
											$arOldPropListCache[$arProp['CODE']] = $arValueList;
										}
										if (!empty($arNameList)) {
											$arOldNamePropListCache[$arProp['CODE']] = $arNameList;
										}
									}
								}
							}
							foreach ($arSrcPr as &$arProp) {
								if ($arProp['USER_TYPE'] == 'HTML') {
									if (is_array($arProp['~VALUE'])) {
										if ($arProp['MULTIPLE'] == 'N') {
											$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = array('VALUE' => array('TEXT' => $arProp['~VALUE']['TEXT'], 'TYPE' => $arProp['~VALUE']['TYPE']));
											if ($arProp['WITH_DESCRIPTION'] == 'Y') {
												$arSrc['PROPERTY_VALUES'][$arProp['CODE']]['DESCRIPTION'] = $arProp['~DESCRIPTION'];
											}
										} else {
											if (!empty($arProp['~VALUE'])) {
												$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = array();
												foreach ($arProp['~VALUE'] as $propValueKey => $propValue) {
													$oneNewValue = array('VALUE' => array('TEXT' => $propValue['TEXT'], 'TYPE' => $propValue['TYPE']));
													if ($arProp['WITH_DESCRIPTION'] == 'Y') {
														$oneNewValue['DESCRIPTION'] = $arProp['~DESCRIPTION'][$propValueKey];
													}
													$arSrc['PROPERTY_VALUES'][$arProp['CODE']][] = $oneNewValue;
													unset($oneNewValue);
												}
												unset($propValue, $propValueKey);
											}
										}
									}
								} elseif ($arProp['PROPERTY_TYPE'] == 'F') {
									if (is_array($arProp['VALUE'])) {
										$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = array();
										foreach ($arProp['VALUE'] as $propValueKey => $file) {
											if ($file > 0) {
												$tmpValue = CFile::MakeFileArray($file);
												if (!is_array($tmpValue))
													continue;
												if ($arProp['WITH_DESCRIPTION'] == 'Y') {
													$tmpValue = array(
														'VALUE' => $tmpValue,
														'DESCRIPTION' => $arProp['~DESCRIPTION'][$propValueKey]
													);
												}
												$arSrc['PROPERTY_VALUES'][$arProp['CODE']][] = $tmpValue;
											}
										}
									} elseif ($arProp['VALUE'] > 0) {
										$tmpValue = CFile::MakeFileArray($arProp['VALUE']);
										if (is_array($tmpValue)) {
											if ($arProp['WITH_DESCRIPTION'] == 'Y') {
												$tmpValue = array(
													'VALUE' => $tmpValue,
													'DESCRIPTION' => $arProp['~DESCRIPTION']
												);
											}
											$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = $tmpValue;
										}
									}
								} elseif ($arProp['PROPERTY_TYPE'] == 'L') {
									if (!empty($arProp['VALUE_ENUM_ID'])) {
										if ($intSrcIBlockID == $arSrc['IBLOCK_ID']) {
											$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = $arProp['VALUE_ENUM_ID'];
										} else {
											if (isset($arPropListCache[$arProp['CODE']]) && isset($arOldPropListCache[$arProp['CODE']])) {
												if (is_array($arProp['VALUE_ENUM_ID'])) {
													$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = array();
													foreach ($arProp['VALUE_ENUM_ID'] as &$intValueID) {
														$strValueXmlID = $arOldPropListCache[$arProp['CODE']][$intValueID];
														if (isset($arPropListCache[$arProp['CODE']][$strValueXmlID])) {
															$arSrc['PROPERTY_VALUES'][$arProp['CODE']][] = $arPropListCache[$arProp['CODE']][$strValueXmlID];
														} else {
															$strValueName = $arOldNamePropListCache[$arProp['CODE']][$intValueID];
															$intValueKey = array_search($strValueName, $arNamePropListCache[$arProp['CODE']]);
															if ($intValueKey !== false) {
																$arSrc['PROPERTY_VALUES'][$arProp['CODE']][] = $intValueKey;
															}
														}
													}
													if (isset($intValueID)) {
														unset($intValueID);
													}
													if (empty($arSrc['PROPERTY_VALUES'][$arProp['CODE']])) {
														unset($arSrc['PROPERTY_VALUES'][$arProp['CODE']]);
													}
												} else {
													$strValueXmlID = $arOldPropListCache[$arProp['CODE']][$arProp['VALUE_ENUM_ID']];
													if (isset($arPropListCache[$arProp['CODE']][$strValueXmlID])) {
														$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = $arPropListCache[$arProp['CODE']][$strValueXmlID];
													} else {
														$strValueName = $arOldNamePropListCache[$arProp['CODE']][$arProp['VALUE_ENUM_ID']];
														$intValueKey = array_search($strValueName, $arNamePropListCache[$arProp['CODE']]);
														if ($intValueKey !== false) {
															$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = $intValueKey;
														}
													}
												}
											}
										}
									}
								} elseif ($arProp['PROPERTY_TYPE'] == 'S' || $arProp['PROPERTY_TYPE'] == 'N') {
									if ($arProp['MULTIPLE'] == 'Y') {
										if (is_array($arProp['~VALUE'])) {
											if ($arProp['WITH_DESCRIPTION'] == 'Y') {
												$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = array();
												foreach ($arProp['~VALUE'] as $propValueKey => $propValue) {
													$arSrc['PROPERTY_VALUES'][$arProp['CODE']][] = array(
														'VALUE' => $propValue,
														'DESCRIPTION' => $arProp['~DESCRIPTION'][$propValueKey]
													);
												}
												unset($propValue, $propValueKey);
											} else {
												$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = $arProp['~VALUE'];
											}
										}
									} else {
										$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = (
											$arProp['WITH_DESCRIPTION'] == 'Y'
											? array('VALUE' => $arProp['~VALUE'], 'DESCRIPTION' => $arProp['~DESCRIPTION'])
											: $arProp['~VALUE']
										);
									}
								} else {
									$arSrc['PROPERTY_VALUES'][$arProp['CODE']] = $arProp['~VALUE'];
								}
							}
							if (isset($arProp)) {
								unset($arProp);
							}

							$seoTemplates = CASDIblockElementTools::getSeoFieldTemplates($intSrcIBlockID, $ID);
							if (!empty($seoTemplates)) {
								$arSrc['IPROPERTY_TEMPLATES'] = $seoTemplates;
							}
							unset($seoTemplates);

							$intNewID = $el->Add($arSrc, true, true, true);
							if ($intNewID) {
								if ($boolCatalog && $boolCopyCatalog) {
									$priceRes = CPrice::GetListEx(
										array(),
										array('PRODUCT_ID' => $ID),
										false,
										false,
										array('PRODUCT_ID', 'EXTRA_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY', 'QUANTITY_FROM', 'QUANTITY_TO')
									);
									while ($arPrice = $priceRes->Fetch()){
										$arPrice['PRODUCT_ID'] = $intNewID;
										CPrice::Add($arPrice);
									}
								}
								if ($boolCatalog && $boolNewCatalog) {
									$arProduct = array(
										'ID' => $intNewID
									);
									if ($boolCopyCatalog) {
										$productRes = CCatalogProduct::GetList(
											array(),
											array('ID' => $ID),
											false,
											false,
											array(
												'QUANTITY_TRACE_ORIG',
												'CAN_BUY_ZERO_ORIG',
												'NEGATIVE_AMOUNT_TRACE_ORIG',
												'SUBSCRIBE_ORIG',
												'WEIGHT',
												'PRICE_TYPE',
												'RECUR_SCHEME_TYPE',
												'RECUR_SCHEME_LENGTH',
												'TRIAL_PRICE_ID',
												'WITHOUT_ORDER',
												'SELECT_BEST_PRICE',
												'VAT_ID',
												'VAT_INCLUDED',
												'WIDTH',
												'LENGTH',
												'HEIGHT',
												'PURCHASING_PRICE',
												'PURCHASING_CURRENCY',
												'MEASURE'
											)
										);
										if ($arCurProduct = $productRes->Fetch()){
											$arProduct = $arCurProduct;
											$arProduct['ID'] = $intNewID;
											$arProduct['QUANTITY'] = 0;
											$arProduct['QUANTITY_TRACE'] = $arProduct['QUANTITY_TRACE_ORIG'];
											$arProduct['CAN_BUY_ZERO'] = $arProduct['CAN_BUY_ZERO_ORIG'];
											$arProduct['NEGATIVE_AMOUNT_TRACE'] = $arProduct['NEGATIVE_AMOUNT_TRACE_ORIG'];
											if (isset($arProduct['SUBSCRIBE_ORIG'])) {
												$arProduct['SUBSCRIBE'] = $arProduct['SUBSCRIBE_ORIG'];
											}
											foreach ($arProduct as $productKey => $productValue) {
												if ($productValue === null)
													unset($arProduct[$productKey]);
											}
										}
									}
									CCatalogProduct::Add($arProduct, false);
								}
								if ($_REQUEST['action'] == 'asd_move') {
									if (CASDIblockRights::IsElementDelete($intSrcIBlockID, $ID)) {
										$el->Delete($ID);
									}
									else {
										CASDiblock::$error .= '['.$ID.'] '.GetMessage('ASD_ACTION_ERR_DELETE_ELEMENT_RIGHTS')."\n";
									}
								}
							}
							else {
								CASDiblock::$error .= '['.$ID.'] '.$el->LAST_ERROR."\n";
							}
						}
					}

					if ($boolCreateSection && $boolCopySect) {
						if ($_REQUEST['action'] == 'asd_move') {
							continue;
						}
						$rsSections = CIBlockSection::GetList(
							array(),
							array('ID' => $ID, 'IBLOCK_ID' => $intSrcIBlockID),
							false,
							array('ID', 'NAME', 'XML_ID', 'CODE', 'IBLOCK_SECTION_ID', 'IBLOCK_ID',
								'ACTIVE', 'SORT', 'PICTURE', 'DESCRIPTION', 'DESCRIPTION_TYPE',
								'DETAIL_PICTURE', 'SOCNET_GROUP_ID',
								'UF_*'
							)
						);
						if ($arSrcSect = $rsSections->Fetch())
						{
							$arDestSect = $arSrcSect;
							unset($arDestSect['ID']);
							$arDestSect['IBLOCK_ID'] = $intDestIBlockID;
							if ($arDestIBFields['SECTION_CODE']['IS_REQUIRED'] == 'Y') {
								if (!strlen($arDestSect['CODE'])) {
									$arDestSect['CODE'] = mt_rand(100000, 1000000);
								}
							}
							if ($arDestIBFields['SECTION_CODE']['DEFAULT_VALUE']['UNIQUE'] == 'Y') {
								$boolScCodeUnique = $boolSectCodeUnique;
								if (!$boolSectCodeUnique) {
									$rsCheckItems  = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intDestIBlockID, '=CODE' => $arSrc['CODE'], 'CHECK_PERMISSIONS' => 'N'),
																	false, array('nTopCount' => 1), array('ID', 'IBLOCK_ID'));
									if ($arCheck = $rsCheckItems->Fetch()) {
										$boolScCodeUnique = true;
									}
								}
								if ($boolScCodeUnique) {
									$arDestSect['CODE'] .= mt_rand(100, 10000);
								}
							}

							if ($intSetSectID > 0) {
								$arDestSect['IBLOCK_SECTION_ID'] = $intSetSectID;
							} elseif ($intSrcIBlockID != $intDestIBlockID) {
								$arDestSect['IBLOCK_SECTION_ID'] = 0;
							}

							$arDestSect['PICTURE'] = (int)$arDestSect['PICTURE'];
							if ($arDestSect['PICTURE'] > 0) {
								$arDestSect['PICTURE'] = CFile::MakeFileArray($arDestSect['PICTURE']);
								if (empty($arDestSect['PICTURE'])) {
									$arDestSect['PICTURE'] = false;
								} else {
									$arDestSect['PICTURE']['COPY_FILE'] = 'Y';
								}
							} else {
								$arDestSect['PICTURE'] = false;
							}
							$arDestSect['DETAIL_PICTURE'] = (int)$arDestSect['DETAIL_PICTURE'];
							if ($arDestSect['DETAIL_PICTURE'] > 0) {
								$arDestSect['DETAIL_PICTURE'] = CFile::MakeFileArray($arDestSect['DETAIL_PICTURE']);
								if (empty($arDestSect['DETAIL_PICTURE'])) {
									$arDestSect['DETAIL_PICTURE'] = false;
								} else {
									$arDestSect['DETAIL_PICTURE']['COPY_FILE'] = 'Y';
								}
							} else {
								$arDestSect['DETAIL_PICTURE'] = false;
							}

							if (!$boolUFListCache) {
								$boolUFListCache = true;
								$arUFListCache = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_'.$intDestIBlockID.'_SECTION');
								if (!empty($arUFListCache)) {
									if ($intSrcIBlockID != $intDestIBlockID) {
										$arOldUFListCache = $USER_FIELD_MANAGER->GetUserFields('IBLOCK_'.$intSrcIBlockID.'_SECTION');
										if (empty($arOldUFListCache)) {
											$arUFListCache = array();
										}
									} else {
										$arOldUFListCache = $arUFListCache;
									}
								}
								if (!empty($arUFListCache)) {
									if ($intSrcIBlockID != $intDestIBlockID) {
										foreach ($arUFListCache as &$arOneUserField) {
											if ('enum' == $arOneUserField['USER_TYPE']['BASE_TYPE']) {
												$arUFEnumCache[$arOneUserField['FIELD_NAME']] = array();
												$arUFNameEnumCache[$arOneUserField['FIELD_NAME']] = array();
												$rsEnum = $obEnum->GetList(array(), array('USER_FIELD_ID'=>$arOneUserField['ID']));
												while ($arEnum = $rsEnum->Fetch()) {
													$arUFEnumCache[$arOneUserField['FIELD_NAME']][$arEnum['XML_ID']] = $arEnum['ID'];
													$arUFNameEnumCache[$arOneUserField['FIELD_NAME']][$arEnum['ID']] = trim($arEnum['VALUE']);
												}
											}
										}
										if (isset($arOneUserField)) {
											unset($arOneUserField);
										}
										foreach ($arOldUFListCache as &$arOneUserField) {
											if ($arOneUserField['USER_TYPE']['BASE_TYPE'] == 'enum') {
												$arOldUFEnumCache[$arOneUserField['FIELD_NAME']] = array();
												$arOldUFNameEnumCache[$arOneUserField['FIELD_NAME']] = array();
												$rsEnum = $obEnum->GetList(array(), array('USER_FIELD_ID'=>$arOneUserField['ID']));
												while ($arEnum = $rsEnum->Fetch()) {
													$arOldUFEnumCache[$arOneUserField['FIELD_NAME']][$arEnum['ID']] = $arEnum['XML_ID'];
													$arOldUFNameEnumCache[$arOneUserField['FIELD_NAME']][$arEnum['ID']] = trim($arEnum['VALUE']);
												}
											}
										}
										if (isset($arOneUserField)) {
											unset($arOneUserField);
										}
									}
								}
							}

							if (!empty($arUFListCache)) {
								foreach ($arUFListCache as &$arOneUserField) {
									if (!isset($arDestSect[$arOneUserField['FIELD_NAME']])) {
										continue;
									}
									if ($arOneUserField['USER_TYPE']['BASE_TYPE'] == 'file') {
										if (!empty($arDestSect[$arOneUserField['FIELD_NAME']])) {
											if (is_array($arDestSect[$arOneUserField['FIELD_NAME']])) {
												$arNewFileList = array();
												foreach ($arDestSect[$arOneUserField['FIELD_NAME']] as &$intFileID) {
													$arNewFile = false;
													$intFileID = (int)$intFileID;
													if ($intFileID > 0) {
														$arNewFile = CFile::MakeFileArray($intFileID);
													}
													if (!empty($arNewFile)) {
														$arNewFileList[] = $arNewFile;
													}
												}
												if (isset($intFileID)) {
													unset($intFileID);
												}
												$arDestSect[$arOneUserField['FIELD_NAME']] = (!empty($arNewFileList) ? $arNewFileList : false);
											} else {
												$arNewFile = false;
												$intFileID = (int)$arDestSect[$arOneUserField['FIELD_NAME']];
												if ($intFileID > 0) {
													$arNewFile = CFile::MakeFileArray($intFileID);
												}
												$arDestSect[$arOneUserField['FIELD_NAME']] = (!empty($arNewFile) ? $arNewFile : false);
											}
										} else {
											$arDestSect[$arOneUserField['FIELD_NAME']] = false;
										}
									} elseif ($arOneUserField['USER_TYPE']['BASE_TYPE'] == 'enum') {
										if (!empty($arDestSect[$arOneUserField['FIELD_NAME']])) {
											if ($intSrcIBlockID != $intDestIBlockID) {
												if (array_key_exists($arOneUserField['FIELD_NAME'], $arUFEnumCache) && array_key_exists($arOneUserField['FIELD_NAME'], $arOldUFEnumCache)) {
													if (is_array($arDestSect[$arOneUserField['FIELD_NAME']])) {
														$arNewEnumList = array();
														foreach ($arDestSect[$arOneUserField['FIELD_NAME']] as &$intValueID) {
															$strValueXmlID = $arOldUFEnumCache[$arOneUserField['FIELD_NAME']][$intValueID];
															if (array_key_exists($strValueXmlID, $arUFEnumCache[$arOneUserField['FIELD_NAME']])) {
																$arNewEnumList[] = $arUFEnumCache[$arOneUserField['FIELD_NAME']][$strValueXmlID];
															} else {
																$strValueName = $arOldUFNameEnumCache[$arOneUserField['FIELD_NAME']][$intValueID];
																$intValueKey = array_search($strValueName, $arUFNameEnumCache[$arOneUserField['FIELD_NAME']]);
																if ($intValueKey !== false) {
																	$arNewEnumList[] = $intValueKey;
																}
															}
														}
														if (isset($intValueID)) {
															unset($intValueID);
														}
														if (!empty($arNewEnumList)) {
															$arDestSect[$arOneUserField['FIELD_NAME']] = $arNewEnumList;
														}
													} else {
														$strValueXmlID = $arOldUFEnumCache[$arOneUserField['FIELD_NAME']][$arDestSect[$arOneUserField['FIELD_NAME']]];
														if (array_key_exists($strValueXmlID, $arUFEnumCache[$arOneUserField['FIELD_NAME']])) {
															$arDestSect[$arOneUserField['FIELD_NAME']] = $arUFEnumCache[$arOneUserField['FIELD_NAME']][$strValueXmlID];
														} else {
															$strValueName = $arOldUFNameEnumCache[$arOneUserField['FIELD_NAME']][$arDestSect[$arOneUserField['FIELD_NAME']]];
															$intValueKey = array_search($strValueName, $arUFNameEnumCache[$arOneUserField['FIELD_NAME']]);
															if ($intValueKey !== false) {
																$arDestSect[$arOneUserField['FIELD_NAME']] = $intValueKey;
															}
														}
													}
												}
											}
										} else {
											$arDestSect[$arOneUserField['FIELD_NAME']] = false;
										}
									}
								}
								if (isset($arOneUserField)) {
									unset($arOneUserField);
								}
							}

							$intNewID = $sc->Add($arDestSect);
							if (!$intNewID) {
								CASDiblock::$error .= '['.$ID.'] '.$sc->LAST_ERROR."\n";
							}
						}
					}
				}
				$successRedirect = true;
			}
			unset($_REQUEST['action']);
			if (isset($_REQUEST['action_button'])) {
				unset($_REQUEST['action_button']);
			}
			if ($successRedirect)
				LocalRedirect($GLOBALS['APPLICATION']->GetCurPageParam('', array('action', 'action_button', 'asd_ib_dest', 'asd_sect_dest', 'ID')));
		}

		if (isset($_REQUEST['action']) && $_REQUEST['action']=='asd_remove' && $IBLOCK_ID > 0 && isset($_REQUEST['find_section_section']) &&
			check_bitrix_sessid() && !empty($_REQUEST['ID']) && CASDIblockRights::IsIBlockDisplay($IBLOCK_ID)
		) {
			$intSectionID = (int)$_REQUEST['find_section_section'];
			if ($intSectionID > 0) {
				$strCurPage = $GLOBALS['APPLICATION']->GetCurPage();
				$bElemPage = ($strCurPage=='/bitrix/admin/iblock_element_admin.php' ||
							$strCurPage=='/bitrix/admin/cat_product_admin.php'
				);
				$bMixPage = ($strCurPage=='/bitrix/admin/iblock_list_admin.php');
				if ($bElemPage || $bMixPage) {
					foreach ($_REQUEST['ID'] as $eID) {
						if ($bMixPage) {
							if (substr($eID, 0, 1) != 'E') {
								continue;
							}
							$ID = (int)substr($eID, 1);
						} else {
							$ID = (int)$eID;
						}
						if ($ID <= 0)
							continue;
						if (CASDIblockRights::IsElementEdit($IBLOCK_ID, $ID)) {
							$arSectionList = array();
							$rsSections = CIBlockElement::GetElementGroups($ID, true);
							while ($arSection = $rsSections->Fetch()) {
								$arSection['ID'] = (int)$arSection['ID'];
								if ($arSection['ID'] != $intSectionID) {
									$arSectionList[] = $arSection['ID'];
								}
							}
							CIBlockElement::SetElementSection($ID, $arSectionList, false);
							if (CASDiblockVersion::checkMinVersion('15.0.1')) {
								\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ID);
							}
							$successRedirect = true;
						}
					}
				}
			}
			unset($_REQUEST['action']);
			if (isset($_REQUEST['action_button'])) {
				unset($_REQUEST['action_button']);
			}
			if ($successRedirect)
				LocalRedirect($GLOBALS['APPLICATION']->GetCurPageParam('', array('action', 'action_button')));
		}
	}

	public static function OnAfterIBlockUpdateHandler($arFields) {
		if ($arFields['RESULT'] && CASDIblockRights::IsIBlockEdit($arFields['ID'])) {
			global $USER_FIELD_MANAGER, $HTTP_POST_FILES;
			$PROPERTY_ID = CASDiblock::$UF_IBLOCK;
			$USER_FIELD_MANAGER->EditFormAddFields($PROPERTY_ID, $arFields);
			$USER_FIELD_MANAGER->Update($PROPERTY_ID, $arFields['ID'], $arFields);
		}
	}
}
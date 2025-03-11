<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

$intBlockID = 11;
$intParentBlockID = 22;
$booIsRule = false;

include_once('array.php');

// Обновляет породу у родителей
function UpdateBreedParent() {
	global $intBlockID, $USER, $intRootSection;
	$intAdvertID = intval($_REQUEST['advert_id']);
	$intParentMthID = intval($_REQUEST['parent_mth_id']);
	$intParentFthID = intval($_REQUEST['parent_fth_id']);
	
	if ($intAdvertID && ($intParentMthID || $intParentFthID)) {
		$objE = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intBlockID, 'ID' => $intAdvertID, 'SECTION_ID' => $intRootSection, 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y'));
		if ($arrAdvertA = $objE->GetNext()) {
			if (intval($arrAdvertA['IBLOCK_SECTION_ID']) && ($arrAdvertA['CREATED_BY'] == $USER->GetID() || $USER->IsAdmin())) {
				// Проапдейтим родителей
				if ($intParentMthID) CIBlockElement::SetPropertyValuesEx($intParentMthID, false, array('BREED' => intval($arrAdvertA['IBLOCK_SECTION_ID'])));
				if ($intParentFthID) CIBlockElement::SetPropertyValuesEx($intParentFthID, false, array('BREED' => intval($arrAdvertA['IBLOCK_SECTION_ID'])));
			}//\\ if
		}//\\ if
	}//\\ if
}//\\UpdateBreedParent

$arrResult = array('result' => 'fail');
$intRootSection = intval($_REQUEST['root_section']);
if (isset($_REQUEST['name'])) $strNameField = htmlspecialcharsEx($_REQUEST['name']);
elseif (isset($_FILES) && count($_FILES)) $strNameField = key($_FILES);


if ($strNameField != 'PARENT_MTH_ID' && $strNameField != 'PARENT_FTH_ID' && strpos($strNameField, 'PARENT_') !== false) $strTypeBlock = 'P'; // это родители
else $strTypeBlock = 'A'; // Это объявления

if (check_bitrix_sessid() && $USER->IsAuthorized() && $intRootSection && in_array($intRootSection, array(96, 95)) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && strlen($strNameField)) {
	CModule::IncludeModule('iblock');

	$arrResult['result'] = 'ok';
	
	$intAdvertID = intval($_REQUEST['advert_id']);
	
	// Создадим анкету
	if (!$intAdvertID) {
		$el = new CIBlockElement;
		$intAdvertID = $el->Add(array('IBLOCK_ID' => $intBlockID, 'IBLOCK_SECTION_ID' => $intRootSection, 'NAME' => 'Новое объявление', 'ACTIVE' => 'Y'));
	}//\\ if
	// Проверим права пользователя для этого элемента
	$objE = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intBlockID, 'ID' => $intAdvertID, 'SECTION_ID' => $intRootSection, 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y'));
	if ($arrAdvert = $objE->GetNext()) {
		// Получим свойства
		$res = CIBlockElement::GetProperty($intBlockID, $intAdvertID, 'sort', 'asc', array('ACTIVE' => 'Y'));
    	while ($ob = $res->GetNext()) {
    		if ($ob['PROPERTY_TYPE'] == 'F' && $ob['LIST_TYPE'] == 'L') {
    			if (intval($ob['VALUE'])) $arrAdvert[$ob['CODE']][$ob['PROPERTY_VALUE_ID']] = intval($ob['VALUE']);
   			} else $arrAdvert[$ob['CODE']] = $ob['VALUE'];
   		}//\\ while
		if ($arrAdvert['CREATED_BY'] == $USER->GetID() || $USER->IsAdmin()) $booIsRule = true;
	}//\\ if
	
	
	if ($strTypeBlock == 'P' && $_REQUEST['type'] != 'search') { // Если идет запрос на изменение родителей, проверим права
		$intParentId = 0; 
		if (strpos($strNameField, 'PARENT_MTH_') !== false) $intParentId = intval($_REQUEST['parent_mth_id']);
		else $intParentId = intval($_REQUEST['parent_fth_id']);
		
		// Создадим родителя
		if (!$intParentId) {
			if ($intRootSection == 95) $intType = 373;
			else $intType = 374;
			
			if (strpos($strNameField, '_MTH_')) $strSex = 376; // Женский пол
			else $strSex = 375;

			$el = new CIBlockElement;
			$intParentId = $el->Add(array('IBLOCK_ID' => $intParentBlockID, 'NAME' => 'Новый родитель', 'ACTIVE' => 'Y', 'PROPERTY_VALUES' => array('TYPE' => $intType, 'SEX' => $strSex)));
		}//\\ if
		
		$booIsRule = false;
		// Проверим права пользователя для этого элемента
		$objE = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intParentBlockID, 'ID' => $intParentId, 'ACTIVE' => 'Y'));
		if ($arrParent = $objE->GetNext()) {
			// Получим свойства
			$res = CIBlockElement::GetProperty($intParentBlockID, $intParentId, 'sort', 'asc', array('ACTIVE' => 'Y'));
	    	while ($ob = $res->GetNext()) {
	    		if ($ob['PROPERTY_TYPE'] == 'F' && $ob['LIST_TYPE'] == 'L') {
	    			if (intval($ob['VALUE'])) $arrAdvert['PARENTS'][$intParentId][$ob['CODE']][$ob['PROPERTY_VALUE_ID']] = intval($ob['VALUE']);
	   			} else $arrAdvert['PARENTS'][$intParentId][$ob['CODE']] = $ob['VALUE'];
	   		}//\\ while
			
			if ($arrParent['CREATED_BY'] == $USER->GetID() || $USER->IsAdmin()) $booIsRule = true;
		}//\\ if
	}//\\ if
	
	// Права на этот элемент есть - разрешаем делать операции с базой
	if ($intAdvertID && $booIsRule) {
		// Предадим обратно, ID родителя который изменяли
		if ($strTypeBlock == 'P' && $intParentId) {
			$arrResult['parent_id'] = $intParentId;
			$arrResult['parent_type'] = (strpos($strNameField, '_MTH_') ? 'MTH' : 'FTH');
		}//\\ if
		
		if ($_REQUEST['type'] == 'check_validation') { // Запрос на проверку валидации
		} elseif ($_REQUEST['type'] == 'moderation') { // Запрос на модерацию
			CIBlockElement::SetPropertyValueCode($intAdvertID, 'MODERATION', array('VALUE' => 983));
		} elseif ($_REQUEST['type'] == 'search') { // Поиск по родителям
			$arrResult['search_result'] = array();
			
			if ($intRootSection == 95) $intType = 373;
			else $intType = 374;
			
			if (strpos($strNameField, '_MTH_')) $strSex = 'F'; // Женский пол
			else $strSex = 'M';
			
			$objSearch = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intParentBlockID, 'ACTIVE' => 'Y', 'NAME' => '%'.htmlspecialcharsEx($_REQUEST['value']).'%', 'PROPERTY_TYPE' => $intType, 'PROPERTY_SEX' => $strSex), false, false, array('ID', 'NAME', 'PROPERTY_BREED', 'PROPERTY_OWNER'));
			while ($arrSearch = $objSearch->GetNext()) {
				$arrS = array(
					'id' => $arrSearch['ID'],
					'name' => $arrSearch['NAME'],
					'owner' => $arrSearch['PROPERTY_OWNER_VALUE'],
					'breed' => '',
					'src' => '',
				);
				// Получим породу
				if (intval($arrSearch['PROPERTY_BREED_VALUE'])) {
					$res = CIBlockSection::GetByID(intval($arrSearch['PROPERTY_BREED_VALUE']));
					if ($ar_res = $res->GetNext()) $arrS['breed'] = $ar_res['NAME'];
				}//\\ if
				// Получим первую картинку
				$res = CIBlockElement::GetProperty($intParentBlockID, $arrSearch['ID'], 'id', 'asc', array('ACTIVE' => 'Y', 'CODE' => 'PHOTOS', 'EMPTY' => 'N'));
   				if ($ob = $res->GetNext()) {
					if (intval($ob['VALUE'])) {
						$arrPhoto = CFile::ResizeImageGet(intval($ob['VALUE']), array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
						$arrS['src'] = $arrPhoto['src'];
					}//\\ if
		   		}//\\ if
				
				$arrResult['search_result'][] = $arrS;
			}//\\ while
			
			/*$arrResult['search_result'][] = array(
				'id' => '345',
				'src' => '1.jpg',
				'owner' => 'Смирнова Наталья Ивановна',
				'breed' => 'Лабрадор',
				'name' => 'Шарик',
			);*/
			
		} elseif ($_REQUEST['type'] == 'field') {
	
			$strValueField = $_REQUEST['value'];
	
	
			if (isset($arrField[$strNameField])) {
				$arrUpdate = array($arrField[$strNameField] => $strValueField);
				
				if ($strTypeBlock == 'A') {
				
					if ($arrField[$strNameField] == 'IBLOCK_SECTION_ID' && intval($strValueField)) {
						// Надо переименовать название
						// Получим породы
						$arrBreed = array();
						$rsSections = CIBlockSection::GetList(array('name' => 'asc'), array('IBLOCK_ID' => $intBlockID, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $intRootSection));
						while ($arSection = $rsSections->GetNext()) {
							$arrBreed[$arSection['ID']] = $arSection['NAME'];
						}//\\ while
						$arrUpdate['NAME'] = 'Щенки '.$arrBreed[intval($strValueField)];
					}//\\ if
					// Это поле анкеты
					$el = new CIBlockElement;
					$el->Update($intAdvertID, $arrUpdate);
				} elseif ($strTypeBlock == 'P' && $intParentId) {
					// Это поле родителя
					$el = new CIBlockElement;
					$el->Update($intParentId, $arrUpdate);
				}//\\ if
				UpdateBreedParent();
			} elseif(isset($arrFieldProp[$strNameField])) {
				if ($strTypeBlock == 'A') {
					// Это свойство объявление
					if (strpos($strNameField, 'PUP_SALE_') !== false) CIBlockElement::SetPropertyValueCode($intAdvertID, $arrFieldProp[$strNameField], array('VALUE' => $strValueField));
					else CIBlockElement::SetPropertyValueCode($intAdvertID, $arrFieldProp[$strNameField], $strValueField);
				} elseif ($strTypeBlock == 'P' && $intParentId) {
					// Это свойство родителя
					CIBlockElement::SetPropertyValueCode($intParentId, $arrFieldProp[$strNameField], $strValueField);
				}//\\ if
				UpdateBreedParent();
			} else {
				$arrResult['result'] = 'fail';
			}//\\ if
			CIBlockElement::SetPropertyValueCode($intAdvertID, 'MODERATION', array('VALUE' => 984));
		} elseif ($_REQUEST['type'] == 'photo') {
			if (isset($arrField[$strNameField])) {
				if ($_REQUEST['action'] == 'upload') {
					// Сохраним файл
					$arrFile = $_FILES[$strNameField];
					$arrFile['MODULE_ID'] = 'iblock';

					if (isset($arrAdvert[$arrField[$strNameField]])) {
						$arrFile['old_file'] = $arrAdvert[$arrField[$strNameField]];
						$arrFile['del'] = ${$arrField[$strNameField].'_del'};
					}//\\ if
					$res = CFile::CheckImageFile($arrFile, 20971520, 4000, 4000);
					if (strlen($res)) {
						$arrResult['error'] = $res;
						$arrResult['result'] = 'fail';
					} else {
						$el = new CIBlockElement;
						$res = $el->Update($intAdvertID, array($arrField[$strNameField] => $arrFile));
						if(!$res) {
							$arrResult['error'] = $el->LAST_ERROR;
							$arrResult['result'] = 'fail';
						} else {
							$objE = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intBlockID, 'ID' => $intAdvertID, 'SECTION_ID' => $intRootSection, 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y'));
							if ($arrAdvert = $objE->GetNext()) {
								$arrPhoto = CFile::ResizeImageGet($arrAdvert[$arrField[$strNameField]], array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
								$arrResult['src'] = $arrPhoto['src'];
							}
						}//\\ if
					}//\\ if
				}//\\ if
			} elseif(isset($arrFieldProp[$strNameField])) {
				if ($_REQUEST['action'] == 'upload') {
					// Сохраним файл
					$arrFile = $_FILES[$strNameField];
					$arrFile['MODULE_ID'] = 'iblock';

					$res = CFile::CheckImageFile($arrFile, 20971520, 4000, 4000);
					if (strlen($res)) {
						$arrResult['error'] = $res;
						$arrResult['result'] = 'fail';
					} else {
						if (count($arrAdvert[$arrFieldProp[$strNameField]]) >=5) {
							$arrResult['result'] = 'fail';
							$arrResult['error'] = 'Максимальное количество фотографий - 5 шт.';
						} else {
							if ($strTypeBlock == 'A') {
								// Это свойство объявления
								CIBlockElement::SetPropertyValueCode($intAdvertID, $arrFieldProp[$strNameField], array('VALUE' => $arrFile));
								
								// Получим заново свойство
								$arrAdvert[$arrFieldProp[$strNameField]] = array();
								//$arrFileNew = array();
								$res = CIBlockElement::GetProperty($intBlockID, $intAdvertID, 'sort', 'asc', array('ACTIVE' => 'Y', 'CODE' => $arrFieldProp[$strNameField]));
						    	while ($ob = $res->GetNext()) {
					    			if (intval($ob['VALUE'])) $arrAdvert[$ob['CODE']][] = intval($ob['VALUE']);
					    			//if (intval($ob['VALUE'])) $arrFileNew[] = intval($ob['VALUE']);
						   		}//\\ while
						   		/*$arrFileNewDiff = array_diff($arrFileNew, $arrAdvert[$arrFieldProp[$strNameField]]);
						   		
								if (isset($arrFileNewDiff) && count($arrFileNewDiff)) {
									foreach ($arrFileNewDiff as $intFileID) {
										$arrPhoto = CFile::ResizeImageGet($intFileID, array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
										if (strlen($arrPhoto['src'])) {
											$arrResult['initialPreview'][] = '<img src="'.$arrPhoto['src'].'" class="file-preview-image">';
											$arrResult['initialPreviewConfig'][] = array(
												'width' => '100px',
												'url' => '/site/file-delete',
												'key' => $intFileID,
												'extra' => array(
													'type' => 'photo',
													'action' => 'delete',
													'name' => 'PUP_IMAGES_1',
													'advert_id' => $intAdvertID,
													'root_section' => $intRootSection,
													'file_id' => $intFileID
												)
											); 
										}//\\ if
									}//\\ foreach
									$arrResult['append'] = true;
								}//\\ if*/
								if (isset($arrAdvert[$arrFieldProp[$strNameField]]) && count($arrAdvert[$arrFieldProp[$strNameField]])) {
									foreach ($arrAdvert[$arrFieldProp[$strNameField]] as $intFileID) {
										$arrPhoto = CFile::ResizeImageGet($intFileID, array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
										if (strlen($arrPhoto['src'])) {
											$arrResult['initialPreview'][] = '<img src="'.$arrPhoto['src'].'" class="file-preview-image">';
											$arrResult['initialPreviewConfig'][] = array(
												'width' => '100px',
												'url' => 'save.php',
												'key' => $intFileID,
												'extra' => array(
													'sessid' => bitrix_sessid(),
													'type' => 'photo',
													'action' => 'delete',
													'name' => $strNameField,
													'advert_id' => $intAdvertID,
													'root_section' => $intRootSection,
													'file_id' => $intFileID
												)
											); 
										}//\\ if
									}//\\ foreach
									$arrResult['append'] = true;
								}//\\ if
							} elseif ($strTypeBlock == 'P' && $intParentId) {
								// Это свойство родителя
								CIBlockElement::SetPropertyValueCode($intParentId, $arrFieldProp[$strNameField], array('VALUE' => $arrFile));
								
								// Получим заново свойство
								$arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]] = array();
								//$arrFileNew = array();
								$res = CIBlockElement::GetProperty($intParentBlockID, $intParentId, 'sort', 'asc', array('ACTIVE' => 'Y', 'CODE' => $arrFieldProp[$strNameField]));
						    	while ($ob = $res->GetNext()) {
					    			if (intval($ob['VALUE'])) $arrAdvert['PARENTS'][$intParentId][$ob['CODE']][] = intval($ob['VALUE']);
						   		}//\\ while

								if (isset($arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]]) && count($arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]])) {
									foreach ($arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]] as $intFileID) {
										$arrPhoto = CFile::ResizeImageGet($intFileID, array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
										if (strlen($arrPhoto['src'])) {
											$arrResult['initialPreview'][] = '<img src="'.$arrPhoto['src'].'" class="file-preview-image">';
											$arrResult['initialPreviewConfig'][] = array(
												'width' => '100px',
												'url' => 'save.php',
												'key' => $intFileID,
												'extra' => array(
													'sessid' => bitrix_sessid(),
													'type' => 'photo',
													'action' => 'delete',
													'name' => $strNameField,
													'advert_id' => $intAdvertID,
													'root_section' => $intRootSection,
													'file_id' => $intFileID
												)
											); 
										}//\\ if
										$arrResult['src'] = $arrPhoto['src'];
									}//\\ foreach
									$arrResult['append'] = true;
								}//\\ if


							}//\\ if
						}//\\ if
					}//\\ if
				} elseif ($_REQUEST['action'] == 'delete' &&  intval($_REQUEST['file_id'])) {
					if ($strTypeBlock == 'A') {
						if (isset($arrAdvert[$arrFieldProp[$strNameField]]) && count($arrAdvert[$arrFieldProp[$strNameField]])) {
							foreach ($arrAdvert[$arrFieldProp[$strNameField]] as $intPropID => $intFileID) {
								if ($intFileID == intval($_REQUEST['file_id'])) {
									CIBlockElement::SetPropertyValueCode($intAdvertID, $arrFieldProp[$strNameField], array($intPropID => array('VALUE' => array('MODULE_ID' => 'iblock', 'del' => 'Y'))));
									break;
								}//\\ if
							}//\\ foreach
						}//\\ if
					} elseif ($strTypeBlock == 'P' && $intParentId) {
						if (isset($arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]]) && count($arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]])) {
							foreach ($arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]] as $intPropID => $intFileID) {
								if ($intFileID == intval($_REQUEST['file_id'])) {
									CIBlockElement::SetPropertyValueCode($intParentId, $arrFieldProp[$strNameField], array($intPropID => array('VALUE' => array('MODULE_ID' => 'iblock', 'del' => 'Y'))));
									break;
								}//\\ if
							}//\\ foreach
						}//\\ if
					}//\\ if
				} elseif ($_REQUEST['action'] == 'file') {
					// Сохраним файл
					$arrFile = $_FILES[$strNameField];
					$arrFile['MODULE_ID'] = 'iblock';

					//$res = CFile::CheckImageFile($arrFile, 20971520, 4000, 4000);
					$res = CFile::CheckFile($arrFile, 20971520, false, 'txt,doc,docx,xls,xlsx');
					if (strlen($res)) {
						$arrResult['error'] = $res;
						$arrResult['result'] = 'fail';
					} else {
						if ($strTypeBlock == 'P' && $intParentId) {
							// Это свойство родителя
							CIBlockElement::SetPropertyValueCode($intParentId, $arrFieldProp[$strNameField], array('VALUE' => $arrFile));
							
							// Получим заново свойство
							$arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]] = array();
							//$arrFileNew = array();
							$res = CIBlockElement::GetProperty($intParentBlockID, $intParentId, 'sort', 'asc', array('ACTIVE' => 'Y', 'CODE' => $arrFieldProp[$strNameField]));
					    	while ($ob = $res->GetNext()) {
				    			if (intval($ob['VALUE'])) $arrAdvert['PARENTS'][$intParentId][$ob['CODE']][] = intval($ob['VALUE']);
					   		}//\\ while
							$intFileID = $arrAdvert['PARENTS'][$intParentId][$arrFieldProp[$strNameField]];
							$rsFile = CFile::GetByID($intFileID[0]);
        					if ($arFile = $rsFile->Fetch())
        						$arrResult['original_name'] = $arFile['ORIGINAL_NAME'];
						}//\\ if
					}//\\if					
				}//\\ if
			}//\\ if
			CIBlockElement::SetPropertyValueCode($intAdvertID, 'MODERATION', array('VALUE' => 984));
		}//\\ if
	
		//$arrResult['percent'] = rand(0, 100);
	}//\\ if
	$arrResult['advert_id'] = $intAdvertID;
	
	
	// Проверим валидацию
	// Получим заново поля и свойства
	$objE = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intBlockID, 'ID' => $intAdvertID, 'SECTION_ID' => $intRootSection, 'INCLUDE_SUBSECTIONS' => 'Y', 'ACTIVE' => 'Y'));
	if ($arrAdvert = $objE->GetNext()) {
		// Получим свойства
		$res = CIBlockElement::GetProperty($intBlockID, $intAdvertID, 'sort', 'asc', array('ACTIVE' => 'Y'));
		while ($ob = $res->GetNext())
			$arrAdvert[$ob['CODE']] = $ob['VALUE'];
	}//\\ if
	//var_dump($arrAdvert);
	//die;
	$arrValidation = array();
	if (!intval($arrAdvert['DETAIL_PICTURE'])) $arrValidation['MAIN_PHOTO'] = 'Необходимо загрузить фотографию';
	if (!intval($arrAdvert['IBLOCK_SECTION_ID']) || in_array(intval($arrAdvert['IBLOCK_SECTION_ID']), array(95, 96))) $arrValidation['BREED'] = 'Необходимо выбрать породу';
	foreach ($arrFieldRequired as $strField => $strMessage) {
		if (!strlen($arrAdvert[$arrFieldProp[$strField]])) $arrValidation[$strField] = $strMessage;
	}//\\ foreach
	$arrResult['error_validation'] = $arrValidation;
	$arrResult['percent'] = ((1 - count($arrValidation) / (count($arrFieldRequired) + 2)) * 100);
	
}//\\ if


echo json_encode($arrResult);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
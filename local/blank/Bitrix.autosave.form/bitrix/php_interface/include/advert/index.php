<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Добавить объявление");
?>
<script>
var objUrl = document.createElement('a');
objUrl.href = window.location.href;
var regexp = /^\#ID=(\d{1,6})$/i;
if (regexp.test(objUrl.hash)) {
	var arrID = objUrl.hash.split('#ID=');
	if (parseInt(arrID[1])) window.location.href = '?ID='+parseInt(arrID[1]);
}//\\ if
</script>
<?
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/fileinput.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/bootstrap.min.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/moment-with-locales.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/bootstrap-formhelpers.js');

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/fileinput.min.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/bootstrap-formhelpers.min.css');

include_once('array.php');
global $USER;

$intBlockID = ADVERT_IBLOCK;
$intRootSection = ADVERT_ROOT_SECTION;
$intParentBlockID = ADVERT_PARENTS_IBLOCK;

CModule::IncludeModule("iblock");
// Получим породы
$arrBreed = array();
$rsSections = CIBlockSection::GetList(array('name' => 'asc'), array('IBLOCK_ID' => $intBlockID, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => $intRootSection));
while ($arSection = $rsSections->GetNext()) {
	$arrBreed['REFERENCE'][] = $arSection['NAME'];
	$arrBreed['REFERENCE_ID'][] = $arSection['ID'];
}//\\ while

// Получим города
$arrCity = array();
$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array('IBLOCK_ID' => $intBlockID, 'CODE' => 'CITY'));
while($enum_fields = $property_enums->GetNext()) {
	$arrCity['REFERENCE'][] = $enum_fields['VALUE'];
	$arrCity['REFERENCE_ID'][] = $enum_fields['ID'];
}

// Получим место проживания собаки
$arrParentCity = array();
$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array('IBLOCK_ID' => $intParentBlockID, 'CODE' => 'LOCATIONS_DOGS'));
while($enum_fields = $property_enums->GetNext()) {
	$arrParentCity['REFERENCE'][] = $enum_fields['VALUE'];
	$arrParentCity['REFERENCE_ID'][] = $enum_fields['ID'];
}

// Список валют
$arrCurrency = array(
	'REFERENCE' => array('RUB', 'USD', 'EUR'),
	'REFERENCE_ID' => array('RUB', 'USD', 'EUR'),
);
$arrSex = array(
	'PUP_M' => 'М',
	'PUP_F' => 'Ж',
);
$arrAllListProps = array();

// Получим инфу об объявлении
$arrAdvert = array();
$intAdvertID = intval($_REQUEST['ID']);
// Получим данные об объявлении
if ($intAdvertID) {
	$arrAdvert['ID'] = $intAdvertID;
	//var_dump($arrField);
	// Получим основные сведения
	$arrFilter = array('ID');
	foreach($arrField as $strF)
		$arrFilter[] = $strF;
	
	// Получим основную инфу
	$objE = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intBlockID, 'ID' => $intAdvertID, 'ACTIVE' => 'Y', 'CREATED_USER_ID' => $USER->GetID()));
	if ($enum_fields = $objE->GetNext()) {
		foreach($enum_fields as $strN => $strV) {
			if (in_array($strN, $arrField))
				$arrAdvert[array_search($strN, $arrField)] = $strV;
		}//\\ foreach

		// if ($strNameField != 'PARENT_MTH_ID' && $strNameField != 'PARENT_FTH_ID' && strpos($strNameField, 'PARENT_') !== false)
		// Получим отца и мать
		$res = CIBlockElement::GetProperty($intBlockID, $intAdvertID, 'sort', 'asc', array('CODE' => 'MTH_ID'));
		if ($ob = $res->GetNext()) $arrAdvert['PARENT_MTH_ID'] = intval($ob['VALUE']);
		$res = CIBlockElement::GetProperty($intBlockID, $intAdvertID, 'sort', 'asc', array('CODE' => 'FTH_ID'));
		if ($ob = $res->GetNext()) $arrAdvert['PARENT_FTH_ID'] = intval($ob['VALUE']);
		
		// Получим поля от родителей
		if ($arrAdvert['PARENT_MTH_ID']) {
			$objP = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intParentBlockID, 'ID' => $arrAdvert['PARENT_MTH_ID'], 'ACTIVE' => 'Y'), false, false, array('*', 'PROPERTY_BREED', 'PROPERTY_OWNER'));
			if ($enum_fieldsP = $objP->GetNext()) {
				// Проверим является ли текущий пользовательм владельцем этого родителя
				$arrAdvert['PARENT_MTH_ID_ACCESS'] = false;
				if ($enum_fieldsP['CREATED_BY'] == $USER->GetID()) $arrAdvert['PARENT_MTH_ID_ACCESS'] = true;
				else {
					// Получим инфу по данному родителю
					$arrAdvert['PARENT_MTH_DATA'] = array(
						'NAME' => $enum_fieldsP['NAME'],
						'SRC' => '',
						'BREED' => '',
						'OWNER' => $enum_fieldsP['PROPERTY_OWNER_VALUE'],
					);
					// Получим породу
					if (intval($enum_fieldsP['PROPERTY_BREED_VALUE'])) {
						$res = CIBlockSection::GetByID(intval($enum_fieldsP['PROPERTY_BREED_VALUE']));
						if ($ar_res = $res->GetNext()) $arrAdvert['PARENT_MTH_DATA']['BREED'] = $ar_res['NAME'];
					}//\\ if
					// Получим первую картинку
					$res = CIBlockElement::GetProperty($intParentBlockID, $enum_fieldsP['ID'], 'id', 'asc', array('ACTIVE' => 'Y', 'CODE' => 'PHOTOS', 'EMPTY' => 'N'));
	   				if ($ob = $res->GetNext()) {
						if (intval($ob['VALUE'])) {
							$arrPhoto = CFile::ResizeImageGet(intval($ob['VALUE']), array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
							$arrAdvert['PARENT_MTH_DATA']['SRC'] = $arrPhoto['src'];
						}//\\ if
			   		}//\\ if
				}//\\ if
				
				// Проверим все поля
				foreach($enum_fieldsP as $strN => $strV) {
					$arrKeys = array_keys($arrField, $strN);
					if (count($arrKeys)) {
						foreach ($arrKeys as $strKey) {
							if (strpos($strKey, 'PARENT_MTH_') !== false) $arrAdvert[$strKey] = $strV;
						}//\\ foreach
					}//\\ if
				}//\\ foreach
			}//\\ if
		}//\\ if
		
		if ($arrAdvert['PARENT_FTH_ID']) {
			$objP = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intParentBlockID, 'ID' => $arrAdvert['PARENT_FTH_ID'], 'ACTIVE' => 'Y'), false, false, array('*', 'PROPERTY_BREED', 'PROPERTY_OWNER'));
			if ($enum_fieldsP = $objP->GetNext()) {
				// Проверим является ли текущий пользовательм владельцем этого родителя
				$arrAdvert['PARENT_FTH_ID_ACCESS'] = false;
				if ($enum_fieldsP['CREATED_BY'] == $USER->GetID()) $arrAdvert['PARENT_FTH_ID_ACCESS'] = true;
				else {
					// Получим инфу по данному родителю
					$arrAdvert['PARENT_FTH_DATA'] = array(
						'NAME' => $enum_fieldsP['NAME'],
						'SRC' => '',
						'BREED' => '',
						'OWNER' => $enum_fieldsP['PROPERTY_OWNER_VALUE'],
					);
					// Получим породу
					if (intval($enum_fieldsP['PROPERTY_BREED_VALUE'])) {
						$res = CIBlockSection::GetByID(intval($enum_fieldsP['PROPERTY_BREED_VALUE']));
						if ($ar_res = $res->GetNext()) $arrAdvert['PARENT_FTH_DATA']['BREED'] = $ar_res['NAME'];
					}//\\ if
					// Получим первую картинку
					$res = CIBlockElement::GetProperty($intParentBlockID, $enum_fieldsP['ID'], 'id', 'asc', array('ACTIVE' => 'Y', 'CODE' => 'PHOTOS', 'EMPTY' => 'N'));
	   				if ($ob = $res->GetNext()) {
						if (intval($ob['VALUE'])) {
							$arrPhoto = CFile::ResizeImageGet(intval($ob['VALUE']), array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
							 $arrAdvert['PARENT_FTH_DATA']['SRC'] = $arrPhoto['src'];
						}//\\ if
			   		}//\\ if
				}//\\ if
				
				// Проверим все поля
				foreach($enum_fieldsP as $strN => $strV) {
					$arrKeys = array_keys($arrField, $strN);
					if (count($arrKeys)) {
						foreach ($arrKeys as $strKey) {
							if (strpos($strKey, 'PARENT_FTH_') !== false) $arrAdvert[$strKey] = $strV;
						}//\\ foreach
					}//\\ if
				}//\\ foreach
			}//\\ if
		}//\\ if
		
		// Получим свойства
		foreach ($arrFieldProp as $strFieldName => $strPropName) {
			if ($strFieldName == 'PARENT_MTH_ID' || $strFieldName == 'PARENT_FTH_ID') continue;
			
			$intBlockIDSelect = $intBlockID;
			$intElementIDSelect = $intAdvertID;
			
			if (strpos($strFieldName, 'PARENT_') !== false) {
				$intBlockIDSelect = $intParentBlockID;
				if (strpos($strFieldName, 'PARENT_MTH_') !== false) $intElementIDSelect = $arrAdvert['PARENT_MTH_ID'];
				else $intElementIDSelect = $arrAdvert['PARENT_FTH_ID'];
			}//\\ if
			
			//if (strpos())
			
			if ($intBlockIDSelect && $intElementIDSelect) {
				$res = CIBlockElement::GetProperty($intBlockIDSelect, $intElementIDSelect, 'sort', 'asc', array('CODE' => $strPropName));
		    	if ($ob = $res->GetNext()) {
		    		$arrAdvert[$strFieldName] = $ob['VALUE'];
		    		// Если это список
		    		if ($ob['PROPERTY_TYPE'] == 'L') {
		    			$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array('IBLOCK_ID' => $intBlockIDSelect, 'CODE' => $ob['CODE']));
						while($enum_fields = $property_enums->GetNext()) {
							$arrAllListProps[$intElementIDSelect][$ob['CODE']][] = array(
								'ID' => $enum_fields['ID'],
								'VALUE' => $enum_fields['VALUE'],
								'TYPE' => 'L',
							);
						}
		    		} elseif($ob['PROPERTY_TYPE'] == 'F' && $ob['LIST_TYPE'] == 'L') {
		    			$resF = CIBlockElement::GetProperty($intBlockIDSelect, $intElementIDSelect, 'sort', 'asc', array('CODE' => $strPropName));
		    			while ($obF = $resF->GetNext()) {
		    				if (intval($obF['VALUE'])) {
			    				$arrPhoto = CFile::ResizeImageGet($obF['VALUE'], array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			    				$arrAllListProps[$intElementIDSelect][$ob['CODE']][] = array(
									'ID' => $obF['ID'],
									'VALUE' => $obF['VALUE'],
									'TYPE' => 'F',
									'SRC' => $arrPhoto['src'],
								);
							}//\\ if
		   				}//\\ while
		    		}//\\ if
		   		}//\\ if
	   		}//\\ if
	    }//\\ foreach
	    
	    if (isset($arrAdvert['MAIN_PHOTO']) && intval($arrAdvert['MAIN_PHOTO'])) {
	    	$arrPhoto = CFile::ResizeImageGet(intval($arrAdvert['MAIN_PHOTO']), array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
	    	$arrPhoto['id'] = intval($arrAdvert['MAIN_PHOTO']);
	    	$arrAdvert['MAIN_PHOTO'] = $arrPhoto;		    	
	    }//\\ if
	    
	    foreach (array('MTH', 'FTH') as $strParent) {

		    if (isset($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE']) && intval($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE'])) {
		    	$rsFile = CFile::GetByID(intval($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE']));
				if ($arFile = $rsFile->Fetch()) {
			    	$arrFile['id'] = intval($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE']);
			    	$arrFile['original_name'] = $arFile['ORIGINAL_NAME'];
			    	$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE'] = $arrFile;
				}//\\ if	    	
		    }//\\ if

	    	for ($intP = 1; $intP <= 14; $intP++) {
	    		if (isset($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO']) && intval($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO'])) {
			    	$arrPhoto = CFile::ResizeImageGet(intval($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO']), array('width' => 100, 'height' => 100), BX_RESIZE_IMAGE_EXACT, true);
			    	$arrPhoto['id'] = intval($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO']);
			    	$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO'] = $arrPhoto;		    	
			    }//\\ if
	    	}//\\ for
	    }//\\ foreach

	} else {
		$intAdvertID = $arrAdvert['ID'] = 0;
	}//\\ if
} else {
	// Получим свойства
	foreach ($arrFieldProp as $strFieldName => $strPropName) {
		if ($strFieldName == 'PARENT_MTH_ID' || $strFieldName == 'PARENT_FTH_ID') continue;
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $intBlockID, 'CODE' => $strPropName));
		while ($prop_fields = $properties->GetNext()) {
			if ($prop_fields['PROPERTY_TYPE'] == 'L') {
    			$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array('IBLOCK_ID' => $intBlockIDSelect, 'CODE' => $prop_fields['CODE']));
				while($enum_fields = $property_enums->GetNext()) {
					$arrAllListProps[$intAdvertID][$prop_fields['CODE']][] = array(
						'ID' => $enum_fields['ID'],
						'VALUE' => $enum_fields['VALUE'],
						'TYPE' => 'L',
					);
				}
    		}
		}
	}//\\ foreach
}//\\ if

if (!isset($arrAdvert['MAIN_PHOTO']['src'])) $arrAdvert['MAIN_PHOTO']['src'] = '/images/1.gif';

// Получим статуси продажи
$arrStatusSale = getArrayFromIBlock(26);

//var_dump($arrAllListProps);

function getArrayFromIBlock($intBlockID) {
	$arrResult = array();
	$objP = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $intBlockID, 'ACTIVE' => 'Y'), false, false, array('ID', 'NAME'));
	while ($enum_fieldsP = $objP->GetNext()) {
		$arrResult['REFERENCE'][] = $enum_fieldsP['NAME'];
		$arrResult['REFERENCE_ID'][] = $enum_fieldsP['ID'];
	}//\\ while
	return $arrResult;
}//\\ getArrayFromIBlock

function getArrayProp($strProp, $intElementIDSelect) {
	global $arrAllListProps;
	$arrResult = array();
	if (isset($arrAllListProps[$intElementIDSelect][$strProp])) {
		foreach ($arrAllListProps[$intElementIDSelect][$strProp] as $arrProp) {
			$arrResult['REFERENCE'][] = $arrProp['VALUE'];
			$arrResult['REFERENCE_ID'][] = $arrProp['ID'];
		}//\\ foreach
	}//\\ if
	
	return $arrResult;
}//\\ getArrayProp

function isShowPup($intNum) {
	global $arrAdvert;
	if ($intNum == 1) return true;
	
	foreach ($arrAdvert as $strName => $mixValue) {
		if (strpos($strName, 'PUP_') !== false && strpos($strName, '_'.$intNum) !== false && $mixValue != NULL)
			return true;
	}//\\ foreach
	
	return false;
}

?>
<style>
h1 {
margin: 0;
padding: 0;
font-size: 22px;
color: #000;
margin-bottom: 8px;
margin-left: 28px;
padding-bottom: 5px;
padding-top: 10px;
}
.csRod3 {
	width: 140px;
}
.form-horizontal .form-group .csRod {
margin-left: 0;
margin-right: 0;
}

.input-group-lg {
	padding: 4px 10px;
}

.table-bordered .form-group {
  margin-bottom: 0;
}

.input-group .input-group-addon {
	padding: 4px 12px;
}
.bfh-datepicker-calendar {
	padding-left: 15px;
}
</style>


<div id="alertSaveData" class="alert alert-warning alert-dismissible fade in" role="alert" style="position: fixed;top: 20px;right: 20px;z-index: 50;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
	<strong>Внимание!</strong> Идет автоматическое сохрание формы.
</div>
	  
<form class="pets_add2 form-horizontal" name="iblock_add" action="/board/addform/sobaki/" method="post" enctype="multipart/form-data">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ADVERT_ID" id="ADVERT_ID" value="<?=$arrAdvert['ID']?>" />
	<fieldset>
<?
if (!$USER->IsAuthorized()) {
?>
		<p class="bg-danger" style="padding: 10px; background-color: #E1E5ED;">
			Ваш е-mail будет храниться в базе сайта. При новой публикации объявления не забудьте <a href="/auth/" onclick="$('#auth-form').show();return false;">авторизироваться</a>.
		</p>
<?
}//\\ if
?>	
    	<legend></legend>

<?
if (!$USER->IsAuthorized()) {
	$strCode = $APPLICATION->CaptchaGetCode();
?>
		<p class="bg-danger hidden" style="padding: 10px;" id="blockErrorMessage"></p>
		<div class="form-group" id="blockReg">
			<label for="REG_EMAIL" class="col-lg-2 control-label">E-mail</label>
			<div class="col-lg-6">
				<span class="help-block hidden"></span>
				<input type="email" class="form-control input-sm" id="REG_EMAIL" name="REG_EMAIL" />
			</div>
			
			<div class="col-lg-4 bg-success hidden" id="blockRegSuccess">
				<h3>Вы зарегистрировались</h3>
				<p>							
					<strong>Ваш логин</strong> sdfhsdfhjhd@dfkjdfjkd.ru<br />
					<strong>Ваш пароль</strong> 7546734567
				</p>				
			</div>
			
			<div class="clearfix" style="padding-bottom: 10px;"></div>
			
			<label for="REG_CAPTCHA_WORD" class="col-lg-2 control-label csHideBlockReg">Введите цифры с картинки</label>
			<div class="col-lg-3 csHideBlockReg">
				<input type="hidden" id="REG_CAPTCHA_SID" name="REG_CAPTCHA_SID" value="<?=$strCode?>" />
				<img id="REG_CAPTCHA_IMG" src="/bitrix/tools/captcha.php?captcha_sid=<?=$strCode?>" />
			</div>
			
			<div class="col-lg-3 csHideBlockReg">
				<span class="help-block hidden"></span>
				<input type="text" class="form-control  input-sm" id="REG_CAPTCHA_WORD" name="REG_CAPTCHA_WORD" placeholder="Введите сюда цифры с картинки" />
			</div>
		</div>

		<div class="form-group center hidden" id="blockRegLoading">
			<div class="col-lg-6 col-lg-offset-2 center">
				<p class="text-center"><strong>Подождите, идет регистрация...</strong></p>
			</div>
			<div class="col-lg-6 col-lg-offset-2">
				<div class="progress progress-striped active">
					<div id="progressBar" class="progress-bar progress-bar-warning" style="width: 100%"></div>
				</div>
			</div>
		</div>

		<div class="form-group csHideBlockReg">
			<div class="col-lg-10 col-lg-offset-2">
				<button type="button" class="btn btn-primary" id="REG_BUTTON" name="REG_BUTTON">Быстрая регистрация</button>
			</div>
		</div>
<?
}//\\ if
?>	


	<legend>Общая информация</legend>
	
	<div class="form-group">
		<label for="BREED" class="col-lg-2 control-label">Порода</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<?=SelectBoxFromArray('BREED', $arrBreed, $arrAdvert['BREED'], ' ', 'id="BREED" class="form-control input-sm"')?>
		</div>
	</div>

	<div class="form-group">
		<label for="INFO" class="col-lg-2 control-label">Информация о помёте</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<textarea class="form-control input-sm" rows="3" id="INFO" name="INFO"><?=$arrAdvert['INFO']?></textarea>
			<span class="help-block">Расскажите о помёте</span>
		</div>
	</div>
	
	<div class="form-group">
		<label for="MAIN_PHOTO" class="col-lg-2 control-label">Главное фото объявления</label>
		<div class="col-lg-2">
			<img id="photo_MAIN_PHOTO" data-src="<?=$arrAdvert['MAIN_PHOTO']['src']?>" class="img-thumbnail" src="<?=$arrAdvert['MAIN_PHOTO']['src']?>" data-holder-rendered="true" width="100" height="100">
		</div>
		<div class="col-lg-8">
			<span class="help-block error-message hidden"></span>
			<input id="MAIN_PHOTO" name="MAIN_PHOTO" type="file" multiple="false" data-show-preview="false">
		</div>
	</div>

	<div class="form-group">
		<label for="BREEDER" class="col-lg-2 control-label">Заводчик</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<input type="text" class="form-control input-sm" id="BREEDER" name="BREEDER" value="<?=$arrAdvert['BREEDER']?>" />
			<span class="help-block">Укажите фамилию и имя заводчика</span>
		</div>
	</div>
	
	<div class="form-group">
		<label for="FARM" class="col-lg-2 control-label">Питомник</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<input type="text" class="form-control input-sm" id="FARM" name="FARM" value="<?=$arrAdvert['FARM']?>" />
		</div>
	</div>
	
	<div class="form-group">
		<label for="CITY" class="col-lg-2 control-label">Город</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<?=SelectBoxFromArray('CITY', $arrCity, $arrAdvert['CITY'], ' ', 'id="CITY" class="form-control input-sm"')?>
		</div>
	</div>

	<div class="form-group">
		<label for="ADDRESS" class="col-lg-2 control-label">Адрес</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<input type="text" class="form-control input-sm" id="ADDRESS" name="ADDRESS" value="<?=$arrAdvert['ADDRESS']?>" />
			<span class="help-block">город, район, округ, метро </span>
		</div>
	</div>

	<div class="form-group">
		<label for="CONTACTS" class="col-lg-2 control-label">Телефон</label>
		<div class="col-lg-10">
			<span class="help-block error-message hidden"></span>
			<input type="text" class="form-control input-sm" id="CONTACTS" name="CONTACTS" value="<?=$arrAdvert['CONTACTS']?>" />
		</div>
	</div>
	
	<div class="form-group">
		<label for="BIRTHDATE" class="col-lg-2 control-label">Дата рождения</label>
		<div class="col-lg-10 input-group bfh-datepicker cssdatetimepicker" style="padding-left: 15px; padding-right: 15px;" data-name="BIRTHDATE" data-date="<?=$arrAdvert['BIRTHDATE']?>" data-max="<?=date('d.m.Y', time())?>">
			<span class="help-block error-message hidden"></span>
			<input type="text" class="form-control input-sm datepicker" id="BIRTHDATE" name="BIRTHDATE" value="<?=$arrAdvert['BIRTHDATE']?>" />
		</div>
	</div>

<?
foreach (array('MTH', 'FTH') as $strParent) {
//	$strParent = 'MTH';
?>

	<legend>Информация <?=($strParent == 'MTH' ? 'о матери' : 'об отце')?></legend>
	<input type="hidden" name="PARENT_<?=$strParent?>_ID" id="PARENT_<?=$strParent?>_ID" class="input-sm" value="<?=$arrAdvert['PARENT_'.$strParent.'_ID']?>" />

	<div class="form-group">
		<label for="PARENT_<?=$strParent?>_NAME" class="col-lg-2 control-label">Кличка</label>
		<div class="col-lg-7">
			<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_NAME" name="PARENT_<?=$strParent?>_NAME" value="<?=$arrAdvert['PARENT_'.$strParent.'_NAME']?>" />
		</div>
		<div class="col-lg-3">
			<button type="button" class="btn btn-default searchName" data-action="<?=$strParent?>">Найти в базе</button>
		</div>
	</div>

	<div class="form-group cs02_<?=$strParent?>">
		<label for="input11" class="col-lg-2 control-label">Внести родословную</label>
		<div class="col-lg-10">
			<div role="tabpanel" id="tabs_<?=$strParent?>">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<li role="presentation" class="active"><a href="#fromBase_<?=$strParent?>" aria-controls="fromBase_<?=$strParent?>" role="tab" data-toggle="tab">Из базы</a></li>
					<li role="presentation"><a href="#addBase_<?=$strParent?>" aria-controls="addBase_<?=$strParent?>" role="tab" data-toggle="tab">Внести в базу</a></li>
				</ul>
		
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="fromBase_<?=$strParent?>">
<?
	// Родитель выбран, но он не принадлежит текущему пользователю
	if ($arrAdvert['PARENT'.$strParent.'_ID'] && !$arrAdvert['PARENT_'.$strParent.'_ID_ACCESS']) {
?>
						<div class="media" style="padding: 10px;">
							<div class="media-left media-middle">
								<img data-src="<?=$arrAdvert['PARENT_'.$strParent.'_DATA']['SRC']?>" class="img-thumbnail" src="<?=$arrAdvert['PARENT_'.$strParent.'_DATA']['SRC']?>" data-holder-rendered="true" style="width: 100px; height: 100px;">
							</div>
							<div class="media-body" style="padding-top: 20px;">
								<h4 class="media-heading"><?=$arrAdvert['PARENT_'.$strParent.'_DATA']['BREED'].' '.$arrAdvert['PARENT_'.$strParent.'_DATA']['NAME']?></h4> Владелец <?=$arrAdvert['PARENT_'.$strParent.'_DATA']['OWNER']?>
							</div>
							<div class="media-body" style="padding-top: 20px;">
								<button type="button" class="btn btn-primary selectSearch" data-action="<?=$strParent?>" data-run="unselect" data-id="<?=$arrAdvert['PARENT_'.$strParent.'_ID']?>">Отказаться</button>
							</div>
						</div>
<?		
	}//\\ if
?>					
					</div>
					<div role="tabpanel" class="tab-pane" id="addBase_<?=$strParent?>"></div>
				</div>
			</div>    
		</div>
	</div>    
    
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_OWNER" class="col-lg-2 control-label">Владелец</label>
			<div class="col-lg-10">
				<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_OWNER" name="PARENT_<?=$strParent?>_OWNER" value="<?=$arrAdvert['PARENT_'.$strParent.'_OWNER']?>" />
			</div>
		</div>
	
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_LOCATIONS_DOGS" class="col-lg-2 control-label">Место проживания собаки</label>
			<div class="col-lg-10">
				<?=SelectBoxFromArray('PARENT_'.$strParent.'_LOCATIONS_DOGS', $arrParentCity, $arrAdvert['PARENT_'.$strParent.'_LOCATIONS_DOGS'], ' ', 'id="PARENT_'.$strParent.'_LOCATIONS_DOGS" class="form-control input-sm"')?>
				<span class="help-block">выберите ближайший город</span>
			</div>
		</div>



		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_ADDRESS" class="col-lg-2 control-label">Адрес</label>
			<div class="col-lg-10">
				<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_ADDRESS" name="PARENT_<?=$strParent?>_ADDRESS" value="<?=$arrAdvert['PARENT_'.$strParent.'_ADDRESS']?>" />
				<span class="help-block">город, населенный пункт где находится собака </span>
			</div>
		</div>
    
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_BIRTHDATE" class="col-lg-2 control-label">Дата рождения</label>
			<div class="col-lg-10 input-group bfh-datepicker cssdatetimepicker" style="padding-left: 15px; padding-right: 15px;" data-name="PARENT_<?=$strParent?>_BIRTHDATE" data-date="<?=$arrAdvert['PARENT_'.$strParent.'_BIRTHDATE']?>" data-max="<?=date('d.m.Y', time())?>">
				<input type="text" class="form-control input-sm datepicker" id="PARENT_<?=$strParent?>_BIRTHDATE" name="PARENT_<?=$strParent?>_BIRTHDATE" value="<?=$arrAdvert['PARENT_'.$strParent.'_BIRTHDATE']?>" />
			</div>
		</div>
    
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_WEIGHT" class="col-lg-2 control-label">Вес</label>
			<div class="col-lg-10">
				<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_WEIGHT" name="PARENT_<?=$strParent?>_WEIGHT" value="<?=$arrAdvert['PARENT_'.$strParent.'_WEIGHT']?>" />
				<span class="help-block">для некоторых пород</span>
			</div>
		</div>
    
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_HEIGHT" class="col-lg-2 control-label">Рост</label>
			<div class="col-lg-10">
				<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_HEIGHT" name="PARENT_<?=$strParent?>_HEIGHT" value="<?=$arrAdvert['PARENT_'.$strParent.'_HEIGHT']?>" />
				<span class="help-block">для некоторых пород</span>
			</div>
		</div>
		
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_COLOR" class="col-lg-2 control-label">Окрас</label>
			<div class="col-lg-10">
				<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_COLOR" name="PARENT_<?=$strParent?>_COLOR" value="<?=$arrAdvert['PARENT_'.$strParent.'_COLOR']?>" />
				<span class="help-block">для некоторых пород</span>
			</div>
		</div>
		
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_TITLE" class="col-lg-2 control-label">Титул</label>
			<div class="col-lg-10">
				<textarea class="form-control input-sm" rows="2" id="PARENT_<?=$strParent?>_TITLE" name="PARENT_<?=$strParent?>_TITLE"><?=$arrAdvert['PARENT_'.$strParent.'_TITLE']?></textarea>
			</div>
		</div>
		
		<div class="form-group cs01_<?=$strParent?>">
			<label for="PARENT_<?=$strParent?>_PHOTOS" class="col-lg-2 control-label">Фото</label>
			<div class="col-lg-10">
				<input id="PARENT_<?=$strParent?>_PHOTOS" name="PARENT_<?=$strParent?>_PHOTOS" type="file" multiple="true" class="file-loading" data-max-file-count="5" >
			</div>
		</div>


		<div class="form-group  cs01_<?=$strParent?>">
			<label class="col-lg-2 control-label">Родословная</label>
				<div class="col-lg-10">
					<div class="panel-group" id="accordion_<?=$strParent?>" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="headingOne_<?=$strParent?>">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion_<?=$strParent?>" href="#fillYourself_<?=$strParent?>" aria-expanded="false" aria-controls="fillYourself_<?=$strParent?>">Заполнить самостоятельно</a>
								</h4>
							</div>
							<div id="fillYourself_<?=$strParent?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_<?=$strParent?>">
								<div class="panel-body">
									<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
											<tr class="active">
												<th colspan="6">Цифры в таблице соответствуют номерам собак в родословной</th>
											</tr>
											<tr class="active">
												<th>№</th>
												<th></th>
												<th>Титул</th>
												<th>Кличка</th>
												<th>Окрас</th>
												<th>Фото</th>
											</tr>
										</thead>
										<tbody>
<?
	for ($intP = 1; $intP <= 14; $intP++) {
?>
											<tr>
												<td><?=$intP?></td>
												<td><small><?=$arrPedigreeRod[$intP]?></small></td>
												<td>
													<div class="form-group csRod">
														<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_TITLES" name="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_TITLES" placeholder="Титул" value="<?=$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_TITLES']?>" />
													</div>					
												</td>
												<td>
													<div class="form-group csRod">
														<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_NAME" name="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_NAME" placeholder="Кличка" value="<?=$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_NAME']?>" />
													</div>					
												</td>
												<td>
													<div class="form-group csRod">
														<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_COLOR" name="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_COLOR" placeholder="Окрас" value="<?=$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_COLOR']?>" />
													</div>					
												</td>
												<td>
													<div class="form-group csRod">
														<div class="<?=(isset($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO']['src']) ? '' : ' hidden')?>" style="padding-bottom: 5px;">
															<img id="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_PHOTO_THUMB" data-src="<?=$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO']['src']?>" class="img-thumbnail" src="<?=$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_'.$intP.'_PHOTO']['src']?>" data-holder-rendered="true" width="100" height="100">
														</div>
														<input id="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_PHOTO" name="PARENT_<?=$strParent?>_PEDIGREE_<?=$intP?>_PHOTO" type="file" class="file-photo-small" />
													</div>
												</td>
											</tr>
<?
	}//\\ for
?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingTwo_<?=$strParent?>">
							<h4 class="panel-title">
								<a class="collapsed" data-toggle="collapse" data-parent="#accordion_<?=$strParent?>" href="#sendFile_<?=$strParent?>" aria-expanded="false" aria-controls="sendFile_<?=$strParent?>">Прислать файл</a>
							</h4>
						</div>
						<div id="sendFile_<?=$strParent?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_<?=$strParent?>">
							<div class="panel-body">
								<div class="col-lg-10<?=(isset($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE']['original_name']) ? '' : ' hidden')?>" style="padding-top: 10px;" id="PARENT_<?=$strParent?>_PEDIGREE_FILE_TXT">
									<?=(isset($arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE']['original_name']) ? 'Загружен файл: '.$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_FILE']['original_name'] : '')?>
								</div>
								<div class="col-lg-10" style="padding-top: 10px;">
									<input id="PARENT_<?=$strParent?>_PEDIGREE_FILE" name="PARENT_<?=$strParent?>_PEDIGREE_FILE" class="file-doc-small" type="file">
									<span class="help-block">Пришлите нам файл и мы сами добавим родословную</span>			        
								</div>
							</div>
						</div>
					</div>
  
  
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingThree_<?=$strParent?>">
							<h4 class="panel-title">
								<a class="collapsed" data-toggle="collapse" data-parent="#accordion_<?=$strParent?>" href="#sendLink_<?=$strParent?>" aria-expanded="false" aria-controls="sendLink_<?=$strParent?>">Прислать ссылку</a>
							</h4>
						</div>
						<div id="sendLink_<?=$strParent?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree_<?=$strParent?>">
							<div class="panel-body">
								<div class="col-lg-10" style="padding-top: 10px;">
									<input type="text" class="form-control input-sm" id="PARENT_<?=$strParent?>_PEDIGREE_LINK" name="PARENT_<?=$strParent?>_PEDIGREE_LINK" value="<?=$arrAdvert['PARENT_'.$strParent.'_PEDIGREE_LINK']?>" />
									<span class="help-block">Пришлите нам ссылку и мы сами добавим родословную</span>			        
								</div>
							</div>
						</div>
					</div>
  
  
				</div>
			</div>
		</div>

<?
}//\\ foreach
?>






















	<legend>Информация о <?=($intRootSection == 95 ? 'щенках' : 'котятах')?></legend>

<?
for ($intI = 1; $intI <= 8; $intI++) {
?>
	<div id="blockPup_<?=$intI?>"<?=(isShowPup($intI) ? '' : ' class="hidden"')?>>
	<legend><?=$arrNumPup[$intI].' '.($intRootSection == 95 ? 'щенок' : 'котенок')?></legend>
	
	<div class="form-group">
		<label for="PUP_SEX_<?=$intI?>" class="col-lg-2 control-label">Пол</label>
		<div class="col-lg-3">
			<?=SelectBoxFromArray('PUP_SEX_'.$intI, getArrayProp('PUP_SEX_'.$intI, $intAdvertID), $arrAdvert['PUP_SEX_'.$intI], ' ', 'id="PUP_SEX_'.$intI.'" class="form-control input-sm"')?>
		</div>
	</div>

	<div class="form-group">
		<label for="PUP_NAME__<?=$intI?>" class="col-lg-2 control-label">Кличка</label>
		<div class="col-lg-10">
			<input type="text" class="form-control input-sm" id="PUP_NAME__<?=$intI?>" name="PUP_NAME__<?=$intI?>" value="<?=$arrAdvert['PUP_NAME_'.$intI]?>" />
		</div>
	</div>

	<div class="form-group">
		<label for="PUP_DESC_<?=$intI?>" class="col-lg-2 control-label">Описание</label>
		<div class="col-lg-10">
			<textarea class="form-control input-sm" rows="3" id="PUP_DESC_<?=$intI?>" name="PUP_DESC_<?=$intI?>" id="input14"><?=$arrAdvert['PUP_DESC_'.$intI]?></textarea>
			<span class="help-block">Расскажите о <?=($intRootSection == 95 ? 'щенке' : 'котенке')?></span>
		</div>
	</div>

	<div class="form-group">
		<label for="PUP_WEIGHT_HEIGHT_<?=$intI?>" class="col-lg-2 control-label">Будущий вес</label>
		<div class="col-lg-10">
			<input type="text" class="form-control input-sm" id="PUP_WEIGHT_HEIGHT_<?=$intI?>" name="PUP_WEIGHT_HEIGHT_<?=$intI?>" value="<?=$arrAdvert['PUP_WEIGHT_HEIGHT_'.$intI]?>" />
			<span class="help-block">для некоторых пород</span>
		</div>
	</div>
    
	<div class="form-group">
		<label for="PUP_HEIGHT_<?=$intI?>" class="col-lg-2 control-label">Будущий рост</label>
		<div class="col-lg-10">
			<input type="text" class="form-control input-sm" id="PUP_HEIGHT_<?=$intI?>" name="PUP_HEIGHT_<?=$intI?>" value="<?=$arrAdvert['PUP_HEIGHT_'.$intI]?>" />
			<span class="help-block">для некоторых пород</span>
		</div>
	</div>
	
	<div class="form-group">
		<label for="PUP_COLOR_<?=$intI?>" class="col-lg-2 control-label">Окрас</label>
		<div class="col-lg-10">
			<input type="text" class="form-control input-sm" id="PUP_COLOR_<?=$intI?>" name="PUP_COLOR_<?=$intI?>" value="<?=$arrAdvert['PUP_COLOR_'.$intI]?>" />
			<span class="help-block">для некоторых пород</span>
		</div>
	</div>

	<div class="form-group">
		<label for="PUP_PRICE_<?=$intI?>" class="col-lg-2 control-label">Цена</label>
		<div class="col-lg-2">
			<input type="text" class="form-control input-sm" id="PUP_PRICE_<?=$intI?>" name="PUP_PRICE_<?=$intI?>" value="<?=$arrAdvert['PUP_PRICE_'.$intI]?>" />
		</div>
		<div class="col-lg-2">
			<?=SelectBoxFromArray('PUP_CURRENCY_'.$intI, $arrCurrency, $arrAdvert['PUP_CURRENCY_'.$intI], ' ', 'id="PUP_CURRENCY_'.$intI.'" class="form-control input-sm"')?>
		</div>
	</div>
<?
	// Объявление прошло модерацию, и выводим статус щенка 
	if ($arrAdvert['ID'] && $arrAdvert['MODERATION'] == 982) {
?>
	<div class="form-group">
		<label for="PUP_SALE_<?=$intI?>" class="col-lg-2 control-label">Статус продажи</label>
		<div class="col-lg-10">
			<?=SelectBoxFromArray('PUP_SALE_'.$intI, $arrStatusSale, $arrAdvert['PUP_SALE_'.$intI], ' ', 'id="PUP_SALE_'.$intI.'" class="form-control input-sm"')?>
		</div>
	</div>
<?
	}//\\ if
?>

	<div class="form-group">
		<label for="PUP_IMAGES_<?=$intI?>" class="col-lg-2 control-label">Фото</label>
		<div class="col-lg-10">
			<input id="PUP_IMAGES_<?=$intI?>" name="PUP_IMAGES_<?=$intI?>" type="file" multiple="true" class="file-loading">
		</div>
	</div>

<?
	if ($intI < 8 && !isShowPup($intI+1)) {
?>
	<div class="form-group">
		<div class="col-lg-10 col-lg-offset-2">
			<button data-open-block="<?=($intI+1)?>" type="button" class="btn btn-default openPup">Еще добавить <?=($intRootSection == 95 ? 'щенка' : 'котенка')?></button>
		</div>
	</div>
    
<?
	}//\\ if
?>
	</div>
<?
}//\\ for
?>













	<div class="form-group center">
		<div class="col-lg-10 col-lg-offset-2 center">
			<p class="text-center"><strong>Степень готовности объявления <span id="progressBarNum">0%</span></strong></p>
		</div>
		<div class="col-lg-10 col-lg-offset-2">
			<div class="progress progress-striped">
				<div id="progressBar" class="progress-bar progress-bar-warning" style="width: 0%"></div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-10 col-lg-offset-2">
			<button id="publushButton" name="PUBLUSH_BUTTON" type="button" class="btn btn-primary" disabled="disabled">Опубликовать объявление</button>
		</div>
	</div>



	</fieldset>





</form>

<div class="modal" id="publishModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><strong>Запрос на публикацию</strong></h4>
			</div>
			<div class="modal-body">
				<p>Ваше объявление будет опубликовано после проверки модератором.</p>
			</div>
			<div class="modal-footer">
				<a class="btn btn-default" href="/" role="button">Перейти на главную</a>
				<a class="btn btn-primary" href="/my_adverts/" role="button">Перейти в мои объявления</a>
			</div>
		</div>
	</div>
</div>

<script>

var objFileinputOptions = {
    browseLabel: 'Выберите файл &hellip;',
    removeLabel: 'Удалить',
    removeTitle: 'Удалить выбранные файлы',
    cancelLabel: 'Отмена',
    cancelTitle: 'Прервать текущую загрузку',
    uploadLabel: 'Загрузить',
    uploadTitle: 'Загрузить выбранные файлы',
    msgSizeTooLarge: 'Файл "{name}" (<b>{size} KB</b>) превышает разрешенный максимальный размер <b>{maxSize} KB</b> для загрузки. Пожалуйста, повторите загрузку!',
    msgFilesTooMany: 'Количество выбранных фалов для загрузки <b>({n})</b> превышает разрешенный лимит <b>{m}</b>. Пожалуйста, повторите загрузку!',
    msgFileNotFound: 'Файл "{name}" не найден!',
    msgFileSecured: 'Ограничения безопасности не дают открыть файл "{name}".',
    msgFileNotReadable: 'Файл "{name}" не может быть прочитан.',
    msgFilePreviewAborted: 'Предварительный просмотр файла прерван для "{name}".',
    msgFilePreviewError: 'Произошла ошибка при чтении файла "{name}".',
    msgInvalidFileType: 'Неправильный тип файла "{name}". Поддерживаются только типы: "{types}".',
    msgInvalidFileExtension: 'Неправильно расширение файла "{name}". Поддерживаются только расширения: "{extensions}".',
    msgValidationError: '<span class="text-danger"><i class="glyphicon glyphicon-exclamation-sign"></i> Ошибка загрузки файлов</span>',
    msgLoading: 'Загружен файл {index} из {files} &hellip;',
    msgProgress: 'Загружен файл {index} из {files} - {name} - {percent}% выполнено.',
    msgSelected: '{n} файлов выбрано',
    dropZoneTitle: 'Переместите файлы сюда &hellip;',
	fileActionSettings: {
        removeTitle: 'Удалить файл',
        uploadTitle: 'Загрузить файл',
        indicatorNewTitle: 'Еще не загружен',
        indicatorSuccessTitle: 'Загружен',
        indicatorErrorTitle: 'Ошибка загрузки',
        indicatorLoadingTitle: 'Идет загрузка ...'
    },
    previewSettings: {
        image: {width: "auto", height: "100px"}
    },
    allowedPreviewTypes: ['image'],
    allowedFileTypes: ['image'],
    allowedFileExtensions: ['jpg', 'jpeg', 'png', 'tif', 'tiff', 'gif'],
    maxFileSize: 20971520,
    showCaption: true,
    uploadUrl: 'save.php',
    maxFileCount: 5,
    uploadAsync: true
};


$(function(){
	
<?
if (!$USER->IsAuthorized()) {
?>
	$('input.input-sm, textarea.input-sm, select.input-sm, button').prop('disabled', true);
	$('input[type=file]').fileinput('disable');
	$('.bfh-datepicker').addClass('disabled');
	
	$('#REG_EMAIL, #REG_CAPTCHA_WORD, #REG_BUTTON, #auth-submit').prop('disabled', false);

<?
}
?>
	resetValidation = function(){
		$('.help-block.error-message').addClass('hidden').html('');
		$('div.has-error').removeClass('has-error');
	};
	
	showValidation = function(arrValidation) {
		$.each(arrValidation, function(strField, strMessage){
			if (strField == 'MAIN_PHOTO') {
				$('#'+strField).parents('.form-group').find('.help-block.error-message').removeClass('hidden').html('<small>'+strMessage+'</small>');
				$('#'+strField).parents('.form-group').addClass('has-error');
			} else {
				$('#'+strField).parent().find('.help-block.error-message').removeClass('hidden').html('<small>'+strMessage+'</small>');
				$('#'+strField).parent().parent().addClass('has-error');
			}//\\ if
		});
	};
	
	checkValidation = function() {
		var objData = {
			sessid: $('input[name=sessid]').val(),
			advert_id: $('#ADVERT_ID').val(),
			root_section: '<?=$intRootSection?>',
			parent_mth_id: $('#PARENT_MTH_ID').val(),
			parent_fth_id: $('#PARENT_FTH_ID').val(),
			type: 'check_validation',
			name: 'check'
		};
		resetValidation();
		$('#alertSaveData').show();
		$.post('save.php', objData, function(objResult){
			if (objResult.result == 'ok') {
				showPercent(objResult.percent);
			}//\\ if
			showValidation(objResult.error_validation);
			$('#alertSaveData').hide();
		}, 'json');
	};
	
	saveData = function() {
		var that = $(this);
		//console.log(that);
		
		var strValueField = that.val();
		if (!strValueField) strValueField = $('input[name='+that.attr('name')+']').val();
		
		var strNameField = that.attr('name');
		
		var objData = {
			sessid: $('input[name=sessid]').val(),
			advert_id: $('#ADVERT_ID').val(),
			root_section: '<?=$intRootSection?>',
			parent_mth_id: $('#PARENT_MTH_ID').val(),
			parent_fth_id: $('#PARENT_FTH_ID').val(),
			type: 'field',
			name: that.attr('name'),
			value: strValueField
		};
		
		if (strNameField != 'REG_EMAIL' && strNameField != 'REG_CAPTCHA_WORD' && strNameField != 'REG_BUTTON') {
		
			$('#alertSaveData').show();
			resetValidation();
			
			$.post('save.php', objData, function(objResult){
				if (objResult.result == 'ok') {
					//console.log(objData.advert_id.length);
					if (objData.advert_id.length == 0) {
						$('#ADVERT_ID').val(objResult.advert_id);
						window.location.href = '#ID='+objResult.advert_id;
					}//\\ if
					if (objResult.parent_id && objResult.parent_type) {
						$('#PARENT_'+objResult.parent_type+'_ID').val(objResult.parent_id).blur();
					}//\\ if
					showPercent(objResult.percent);
				}//\\ if
				
				showValidation(objResult.error_validation);
						
				$('#alertSaveData').hide();
			}, 'json');
		}//\\ if
	};//\\ saveData
	
	showPercent = function(strPercent) {
		$('#progressBar').attr('style', 'width: '+strPercent+'%');
		$('#progressBarNum').html(strPercent+'%');
		if (strPercent == 100) $('#publushButton').prop('disabled', false);
		else $('#publushButton').prop('disabled', true);
	};
	
	$('input.input-sm').blur(saveData);
	$('textarea.input-sm').blur(saveData);
	$('select.input-sm').change(saveData);
	$('.bfh-datepicker').on('change.bfhdatepicker', function(e){
		$('input[name='+$(this).data('name')+']').blur();
	});

<?
if ($USER->IsAuthorized()) {
?>
	if (parseInt($('#ADVERT_ID').val())) checkValidation();
<?
}//\\ if
?>
	
	$('.openPup').click(function(){
		var intNum = $(this).data('open-block');
		$('#blockPup_'+intNum).removeClass('hidden');
		$(this).parent().parent().hide();
	});


	$().alert();
	$('#alertSaveData').hide();
	
	var objFileinputOptions1 = $.extend({}, objFileinputOptions);
	//var objFileinputOptions1 = $.extend({}, objFileinputOptions, {showCaption: false, maxFileCount: 1, dropZoneEnabled: false});
	//objFileinputOptions1.uploadExtraData = {sessid: $('input[name=sessid]').val(), advert_id: $('#ADVERT_ID').val(), root_section: '<?=$intRootSection?>', type: 'photo', name: 'MAIN_PHOTO', action: 'upload'};
	$("#MAIN_PHOTO")
		.fileinput($.extend({}, objFileinputOptions1, {
			showCaption: false, 
			maxFileCount: 1, 
			dropZoneEnabled: false,
			uploadExtraData: function(){
				return {
					sessid: $('input[name=sessid]').val(), 
					advert_id: $('#ADVERT_ID').val(), 
					root_section: '<?=$intRootSection?>', 
					type: 'photo', 
					name: 'MAIN_PHOTO', 
					action: 'upload'
				};
			}
		}))
		.on('fileloaded', function(event, file, previewId, index, reader) {
			$(this).fileinput('upload');
		})
		/*.on('fileuploaded', function(event, data, previewId, index) {
			if (data.response.result == 'ok') {
				$('#photo_MAIN_PHOTO').attr('src', data.response.src);
			}
		})*/
		.on('filebatchuploadsuccess', function(event, data, previewId, index) {
			resetValidation();
			if (data.response.result == 'ok') {
				$('#photo_MAIN_PHOTO').attr('src', data.response.src).data('src', data.response.src);
			}//\\ if
			showValidation(data.response.error_validation);
		});

<?
for ($intI = 1; $intI <= 8; $intI++) {
	$arrFF = array('initialPreview' => array(), 'initialPreviewConfig' => array());
	if (isset($arrAllListProps[$intAdvertID]['PUP_IMAGES_'.$intI]) && count($arrAllListProps[$intAdvertID]['PUP_IMAGES_'.$intI])) {
		foreach ($arrAllListProps[$intAdvertID]['PUP_IMAGES_'.$intI] as $arrF) {
			if (strlen($arrF['SRC'])) {
				$arrFF['initialPreview'][] = "'<img src=\"".$arrF['SRC']."\" class=\"file-preview-image\">'";
				$arrFF['initialPreviewConfig'][] = "{width: '100px', url:'save.php', key: ".$arrF['VALUE'].", extra: {sessid: $('input[name=sessid]').val(), type: 'photo', action: 'delete', name: 'PUP_IMAGES_".$intI."', advert_id: $('#ADVERT_ID').val(), root_section: '".$intRootSection."', file_id: ".$arrF['VALUE']."}}";
			}
		}//\\ foreach
	}//\\ if
?>
	var objFileinputOptions2_<?=$intI?> = $.extend({}, objFileinputOptions);
	$("#PUP_IMAGES_<?=$intI?>")
		.fileinput($.extend({}, objFileinputOptions2_<?=$intI?>, {
		//.fileinput($.extend({}, {
			maxFileCount: 5,
			overwriteInitial: false,
			initialPreview: [<?=implode(',',$arrFF['initialPreview'])?>],
			initialPreviewConfig: [<?=implode(',',$arrFF['initialPreviewConfig'])?>],
			uploadExtraData: function(){
				return {
					sessid: $('input[name=sessid]').val(),
					advert_id: $('#ADVERT_ID').val(),
					root_section: '<?=$intRootSection?>',
					type: 'photo',
					name: 'PUP_IMAGES_<?=$intI?>',
					action: 'upload'
				};
			}
		}))
		.on('fileloaded', function(event, file, previewId, index, reader) {
			console.log("fileloaded");
			//$(this).fileinput('upload');
		})
		.on('fileuploaded', function(event, data, previewId, index) {
			console.log('fileuploaded');
			//$(this).fileinput('refresh', {initialPreview: data.response.initialPreview, initialPreviewConfig: data.response.initialPreviewConfig});
		})
		.on('filebatchuploadsuccess', function(event, data, previewId, index) {
			console.log('filebatchuploadsuccess');
			//$(this).fileinput('refresh', {initialPreview: data.response.initialPreview, initialPreviewConfig: data.response.initialPreviewConfig});
		})
		.on('filebatchselected', function(event, data, previewId, index) {
			console.log('filebatchselected');
		})
		.on('filepreupload', function(event, data, previewId, index) {
			console.log('filepreupload');
		})
		.on('filebatchpreupload', function(event, data, previewId, index) {
			console.log('filebatchpreupload');
		})
		.on('filebatchuploadcomplete', function(event, data, previewId, index) {
			console.log('filebatchuploadcomplete');
		});
<?
}//\\ for


foreach (array('MTH', 'FTH') as $strParent) {
	$arrFF = array('initialPreview' => array(), 'initialPreviewConfig' => array());
	$arrFile = array();
	if ($arrAdvert['PARENT_'.$strParent.'_ID'] && isset($arrAllListProps[$arrAdvert['PARENT_'.$strParent.'_ID']][$arrFieldProp['PARENT_'.$strParent.'_PHOTOS']]) && count($arrAllListProps[$arrAdvert['PARENT_'.$strParent.'_ID']][$arrFieldProp['PARENT_'.$strParent.'_PHOTOS']]))
		$arrFile = $arrAllListProps[$arrAdvert['PARENT_'.$strParent.'_ID']][$arrFieldProp['PARENT_'.$strParent.'_PHOTOS']];
	
	if (count($arrFile)) {
		foreach ($arrFile as $arrF) {
			if (strlen($arrF['SRC'])) {
				$arrFF['initialPreview'][] = "'<img src=\"".$arrF['SRC']."\" class=\"file-preview-image\">'";
				$arrFF['initialPreviewConfig'][] = "{width: '100px', url:'save.php', key: ".$arrF['VALUE'].", extra: {sessid: $('input[name=sessid]').val(), type: 'photo', action: 'delete', name: 'PARENT_".$strParent."_PHOTOS', advert_id: $('#ADVERT_ID').val(), root_section: '".$intRootSection."', parent_mth_id: $('#PARENT_MTH_ID').val(), parent_fth_id: $('#PARENT_FTH_ID').val(), file_id: ".$arrF['VALUE']."}}";
			}
		}//\\ foreach
	}//\\ if
?>
	var objFileinputOptions2_<?=$strParent?> = $.extend({}, objFileinputOptions);
	$("#PARENT_<?=$strParent?>_PHOTOS")
		.fileinput($.extend({}, objFileinputOptions2_<?=$strParent?>, {
		//.fileinput($.extend({}, {
			maxFileCount: 5,
			overwriteInitial: false,
			initialPreview: [<?=implode(',',$arrFF['initialPreview'])?>],
			initialPreviewConfig: [<?=implode(',',$arrFF['initialPreviewConfig'])?>],
			uploadExtraData: function(){
				return {
					sessid: $('input[name=sessid]').val(),
					advert_id: $('#ADVERT_ID').val(),
					root_section: '<?=$intRootSection?>',
					parent_mth_id: $('#PARENT_MTH_ID').val(),
					parent_fth_id: $('#PARENT_FTH_ID').val(),
					type: 'photo',
					name: 'PARENT_<?=$strParent?>_PHOTOS',
					action: 'upload'
				};
			}
		}))
		.on('fileloaded', function(event, file, previewId, index, reader) {
			console.log("fileloaded");
			//$(this).fileinput('upload');
		})
		.on('fileuploaded', function(event, data, previewId, index) {
			console.log('fileuploaded');
			//$(this).fileinput('refresh', {initialPreview: data.response.initialPreview, initialPreviewConfig: data.response.initialPreviewConfig});
		})
		.on('filebatchuploadsuccess', function(event, data, previewId, index) {
			console.log('filebatchuploadsuccess');
			//$(this).fileinput('refresh', {initialPreview: data.response.initialPreview, initialPreviewConfig: data.response.initialPreviewConfig});
		})
		.on('filebatchselected', function(event, data, previewId, index) {
			console.log('filebatchselected');
		})
		.on('filepreupload', function(event, data, previewId, index) {
			console.log('filepreupload');
		})
		.on('filebatchpreupload', function(event, data, previewId, index) {
			console.log('filebatchpreupload');
		})
		.on('filebatchuploadcomplete', function(event, data, previewId, index) {
			console.log('filebatchuploadcomplete');
		});

	var objFileinputOptions3_<?=$strParent?> = $.extend({}, objFileinputOptions);
	$('#PARENT_<?=$strParent?>_PEDIGREE_FILE').fileinput($.extend({}, objFileinputOptions3_<?=$strParent?>, {
		maxFileCount: 1,
		showPreview: false,
		allowedFileTypes: ['text'],
		allowedFileExtensions: ['txt', 'doc', 'docx', 'xls', 'xlsx'],
		uploadExtraData: function(){
			return {
				sessid: $('input[name=sessid]').val(),
				advert_id: $('#ADVERT_ID').val(),
				root_section: '<?=$intRootSection?>',
				parent_mth_id: $('#PARENT_MTH_ID').val(),
				parent_fth_id: $('#PARENT_FTH_ID').val(),
				type: 'photo',
				name: 'PARENT_<?=$strParent?>_PEDIGREE_FILE',
				action: 'file'
			};
		}
	}))
	.on('fileloaded', function(event, file, previewId, index, reader) {
		$(this).fileinput('upload');
	})
	.on('filebatchuploadsuccess', function(event, data, previewId, index) {
		if (data.response.result == 'ok') {
			$('#'+$(this).attr('name')+'_TXT').removeClass('hidden').html('Загружен файл: '+data.response.original_name);
		}//\\ if
	});


	$('.cs01_<?=$strParent?>, .cs02_<?=$strParent?>').hide();

	/*$('#tabs_<?=$strParent?> a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});*/

	$('#tabs_<?=$strParent?> a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if ($(e.target).attr('aria-controls') == 'fromBase_<?=$strParent?>') $('.cs01_<?=$strParent?>').hide();
		else $('.cs01_<?=$strParent?>').show();
	});

<?
	
	if (isset($arrAdvert['PARENT_'.$strParent.'_ID']) && intval($arrAdvert['PARENT_'.$strParent.'_ID'])) {
		if ($arrAdvert['PARENT_'.$strParent.'_ID_ACCESS']) { // Этот родитель текущего пользователя - разрешаем редактировать
?>
$('.cs02_<?=$strParent?>').show();
$('#tabs_<?=$strParent?> a:last').tab('show');
$('.cs01_<?=$strParent?>').show();
<?
		} else {
?>
$('.cs02_<?=$strParent?>').show();
$('#tabs_<?=$strParent?> a:first').tab('show');
$('.cs01_<?=$strParent?> input, .cs01_<?=$strParent?> select, .cs01_<?=$strParent?> textarea').prop('disabled', true);
$('.cs01_<?=$strParent?> input[type=file]').fileinput('disable');
$('.cs01_<?=$strParent?> .bfh-datepicker').addClass('disabled');

<?
		}//\\ if
	}//\\ if
}//\\ foreach
?>

	var objFileinputOptions4 = $.extend({}, objFileinputOptions);
	$('.file-photo-small').fileinput($.extend({}, objFileinputOptions4, {
			showCaption: false, 
			maxFileCount: 1, 
			showPreview: false,
			dropZoneEnabled: false,
			browseLabel: 'Загрузить', 
			browseClass: "btn btn-primary input-group-lg",
			uploadExtraData: function(){
				return {
					sessid: $('input[name=sessid]').val(),
					advert_id: $('#ADVERT_ID').val(),
					root_section: '<?=$intRootSection?>',
					parent_mth_id: $('#PARENT_MTH_ID').val(),
					parent_fth_id: $('#PARENT_FTH_ID').val(),
					type: 'photo',
					//name: $(this).attr('name'),
					action: 'upload'
				};
			}
	}))
	.on('fileloaded', function(event, file, previewId, index, reader) {
		$(this).fileinput('upload');
	})
	.on('filebatchuploadsuccess', function(event, data, previewId, index) {
		if (data.response.result == 'ok') {
			$('#'+$(this).attr('name')+'_THUMB').parent().removeClass('hidden');
			$('#'+$(this).attr('name')+'_THUMB').attr('src', data.response.src).data('src', data.response.src);
		}//\\ if
	});



	$('.searchName').click(function(){
		var that = $(this);
		that.prop('disabled', true);
		
		var strDataAction = $(this).data('action');
		$('.cs02_'+strDataAction).show();
		$('#tabs_'+strDataAction+' a:first').tab('show');

		var objData = {
			sessid: $('input[name=sessid]').val(),
			advert_id: $('#ADVERT_ID').val(),
			root_section: '<?=$intRootSection?>',
			type: 'search',
			name: 'PARENT_'+strDataAction+'_NAME',
			value: $('#PARENT_'+strDataAction+'_NAME').val()
		};
		$('#fromBase_'+strDataAction).html('<div class="form-group center" style="padding-top:15px;"><div class="col-lg-6 col-lg-offset-2 center"><p class="text-center"><strong>Подождите, идет поиск...</strong></p></div><div class="col-lg-6 col-lg-offset-2"><div class="progress progress-striped active"><div id="progressBar" class="progress-bar progress-bar-warning" style="width: 100%"></div></div></div></div>');
		
		$.post('save.php', objData, function(objResult){
			that.prop('disabled', false);
			var strHtml = '';
			if (objResult.result == 'ok') {
				$.each(objResult.search_result, function(index, val){
					console.log(val);
					if (!val.owner) val.owner = 'N/A';
					/*if (!val.breed) val.breed = '';
					if (!val.src) val.src = '';
					if (!val.name) val.name = '';*/
					strHtml = strHtml + '<div class="media" style="padding: 10px;"><div class="media-left media-middle"><img data-src="'+val.src+'" class="img-thumbnail" src="'+val.src+'" data-holder-rendered="true" style="width: 100px; height: 100px;"></div><div class="media-body" style="padding-top: 20px;"><h4 class="media-heading">'+val.breed+' '+val.name+'</h4> Владелец '+val.owner+'</div><div class="media-body" style="padding-top: 20px;"><button type="button" class="btn btn-primary selectSearch" data-action="'+strDataAction+'" data-run="select" data-id="'+val.id+'">Выбрать</button></div></div>';
				});
			}//\\ if
			if (!strHtml.length)
				strHtml = '<div class="form-group center" style="padding-top:15px;"><div class="col-lg-6 col-lg-offset-2 center"><p class="text-center"><strong>К сожалению, ничего не найдено</strong></p></div></div>';
			
			$('#fromBase_'+strDataAction).html(strHtml);
		}, 'json');
	});
	

	$(document).on('click', 'button.selectSearch', function() {
		var that = $(this);
		var strDataAction = $(this).data('action');
		var strDataRun = $(this).data('run');
		
		if (strDataRun == 'select') {
			var intID = $(this).data('id');
			$('#PARENT_'+strDataAction+'_ID').val(intID).blur();
			that.text('Отказаться').data('run', 'unselect');
			$('.cs01_'+strDataAction+' input, .cs01_'+strDataAction+' select, .cs01_'+strDataAction+' textarea').prop('disabled', true);
			$('.cs01_'+strDataAction+' input[type=file]').fileinput('disable');
			$('.cs01_'+strDataAction+' .bfh-datepicker').addClass('disabled');
		} else if (strDataRun == 'unselect'){
			$('#PARENT_'+strDataAction+'_ID').val(0).blur();
			that.text('Выбрать').data('run', 'select');
			$('.cs01_'+strDataAction+' input, .cs01_'+strDataAction+' select, .cs01_'+strDataAction+' textarea').prop('disabled', false);
			$('.cs01_'+strDataAction+' input[type=file]').fileinput('enable');
			$('.cs01_'+strDataAction+' .bfh-datepicker').removeClass('disabled');
		}//\\ if
	});


	$('#REG_BUTTON').click(function(){
		var that = $(this);
		$('#REG_BUTTON').prop('disabled', true);
		$('#blockRegLoading').removeClass('hidden');
		$('#blockReg span.help-block').addClass('hidden').html('');
		$('#blockReg div.has-error').removeClass('has-error');

		var objData = {
			sessid: $('input[name=sessid]').val(),
			email: $('#REG_EMAIL').val(),
			captcha_word: $('#REG_CAPTCHA_WORD').val(),
			captcha_sid: $('#REG_CAPTCHA_SID').val()
		};

		$.post('reg.php', objData, function(objResult){
			$('#blockRegLoading').addClass('hidden');
			if (objResult.result == 'ok') {
				$('.csHideBlockReg').hide();
				$('#blockRegSuccess').removeClass('hidden').html('<h3>Вы зарегистрировались</h3><p><strong>Ваш логин</strong> '+objResult.login+'<br /><strong>Ваш пароль</strong> '+objResult.password+'</p>');

				$('input.input-sm, textarea.input-sm, select.input-sm, button').prop('disabled', false);
				$('input[type=file]').fileinput('enable');
				$('.bfh-datepicker').removeClass('disabled');
				
				$('#REG_EMAIL').prop('disabled', true);
				$('.csHideBlockReg').hide();
				
				$('.user-menu').hide();

			} else {
				$('#REG_CAPTCHA_SID').val(objResult.captcha_sid);
				$('#REG_CAPTCHA_IMG').attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + objResult.captcha_sid);
				$('#REG_BUTTON').prop('disabled', false);
				if (objResult.error_message.length)
					$('#blockErrorMessage').removeClass('hidden').html('Ошибка! ' + objResult.error_message);
					
				$.each(objResult.error_field, function(nameField, messageError) {
					$('#'+nameField).parent().find('.help-block').removeClass('hidden').html('<small>'+messageError+'</small>');
					$('#'+nameField).parent().addClass('has-error');
				});
			}
		}, 'json');
	});

	$('#publushButton').click(function(){
		var that = $(this);
		$('#publushButton').prop('disabled', true);


		var objData = {
			sessid: $('input[name=sessid]').val(),
			advert_id: $('#ADVERT_ID').val(),
			root_section: '<?=$intRootSection?>',
			parent_mth_id: $('#PARENT_MTH_ID').val(),
			parent_fth_id: $('#PARENT_FTH_ID').val(),
			type: 'moderation',
			name: that.attr('name')
		};
		
		$.post('save.php', objData, function(objResult){
			if (objResult.result == 'ok') {
				$('#publishModal').modal('show');
			}//\\ if
		}, 'json');
	});

});


/*jQuery( function(){
jQuery('a.confirm').click(function(){
var href = jQuery(this).attr('href');
if (confirm("Вы уверены что хотите снять объявление с публикации?")) {
	return true;;
}
return false;
})
//$('.file-photo').fileinput($.extend({}, objFileinputOptions, {showCaption: false, maxFileCount: 1, showPreview: false}));
});*/
</script>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
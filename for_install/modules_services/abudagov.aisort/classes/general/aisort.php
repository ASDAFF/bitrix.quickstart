<?
IncludeModuleLangFile(__FILE__);

class CAbudagovAISort
{

	function AddTab(&$form)
	{
	    if($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/settings.php" && $_REQUEST['mid'] == 'iblock')
	    {
	    	$checked = (COption::GetOptionString("iblock", 'aisort_active', 'N') == 'Y') ? 'checked' : '';
	    	$checked2 = (COption::GetOptionString("iblock", 'aisort_section', 'N') == 'Y') ? 'checked' : '';
	        $form->tabs[] = array("DIV" => "bx-admin-aisort", "TAB" => GetMessage('Dopolnitelno'), "ICON"=>"main_user_edit", "TITLE"=>GetMessage('Dopolnitelnye_parametry'), "CONTENT"=>
	            '<tr class="heading">
					<td colspan="2"><b>'.GetMessage('Avtouvelichenie_polya_sortirovki').'</b></td>
				</tr>
	            <tr valign="middle">
	                <td class="adm-detail-content-cell-l" width="40%">
	                	<label for="aisort_active">
	                		'.GetMessage('Avtomaticheski_uvelichivat_sortirovku').'
	                	</label>
	                </td>
	                <td class="adm-detail-content-cell-r" width="60%">
	                    <input type="checkbox" name="aisort_active" id="aisort_active" value="Y" '.$checked.' class="adm-designed-checkbox"/>
	                    <label class="adm-designed-checkbox-label" for="aisort_active" title=""></label>
	                </td>
	            </tr>
	            <tr valign="middle">
	                <td class="adm-detail-content-cell-l" width="40%">
	                	<label for="aisort_step">
	                		'.GetMessage('Shag_uvelicheniya').'
	                	</label>
	                </td>
	                <td class="adm-detail-content-cell-r" width="60%">
	                    <input type="text" name="aisort_step" id="aisort_step" value="'.COption::GetOptionInt("iblock", 'aisort_step', 50).'" maxlength="255" size="5" />
	                </td>
	            </tr>
	            <tr valign="middle">
	                <td class="adm-detail-content-cell-l" width="40%">
	                	<label for="aisort_active">
	                		'.GetMessage('Uchityvat_razdel_elementa').'
	                	</label>
	                </td>
	                <td class="adm-detail-content-cell-r" width="60%">
	                    <input type="checkbox" name="aisort_section" id="aisort_section" value="Y" '.$checked2.' class="adm-designed-checkbox"/>
	                    <label class="adm-designed-checkbox-label" for="aisort_section" title=""></label>
	                </td>
	            </tr>
	            '
	        );
	    }
	}

	function OnPageStart(){

		if ($_REQUEST['aisort_active'] == 'Y') {
			COption::SetOptionString("iblock", 'aisort_active', 'Y');
		}elseif($_REQUEST['aisort_step']){
			COption::SetOptionString("iblock", 'aisort_active', 'N');

		}
		if ($_REQUEST['aisort_section'] == 'Y') {
			COption::SetOptionString("iblock", 'aisort_section', 'Y');
		}elseif($_REQUEST['aisort_step']){
			COption::SetOptionString("iblock", 'aisort_section', 'N');

		}
		if ($_REQUEST['aisort_step']) {
			COption::SetOptionInt("iblock", 'aisort_step', IntVal($_REQUEST['aisort_step']));
		}

	}

	function OnBeforeElementAdd(&$arFields)
	{
		if ($arFields['SORT'] == 500 || $arFields['SORT'] == "") {
			if (COption::GetOptionString("iblock", 'aisort_active', 'N') == 'Y') {
				$arFilter = Array(
					'ACTIVE' => 'Y',
					'IBLOCK_ID' => $arFields['IBLOCK_ID'],
				);
				$step = COption::GetOptionInt("iblock", 'aisort_step', 50);
				if(COption::GetOptionInt("iblock", 'aisort_section', 'N') == 'Y') {
					$arFilter['SECTION_ID'] = $arFields["IBLOCK_SECTION"];
				}
				$dbItem = CIBlockElement::GetList(
					Array("SORT" => "DESC"),
					$arFilter,
					false,
					Array ("nTopCount" => 1),
					Array(
						'ID',
						'IBLOCK_ID',
						'NAME',
						'SORT'
					)
				);
				if ($arItem = $dbItem->GetNext()) {
					$nSort = $arItem['SORT'];
				}
				$arFields['SORT'] = $arItem['SORT'] + $step;
			}
		}
	}

	function OnBeforeSectionAdd(&$arFields)
	{
		if ($arFields['SORT'] == 500 || $arFields['SORT'] == "") {
			if (COption::GetOptionString("iblock", 'aisort_active', 'N') == 'Y') {
				$step = COption::GetOptionInt("iblock", 'aisort_step', 50);
				$dbItem = $dbItem = CIBlockSection::GetList(
					Array("SORT" => "DESC"),
					Array(
						'ACTIVE' => 'Y',
						'IBLOCK_ID' => $arFields['IBLOCK_ID'],
					),
					false,
					Array(
						'ID',
						'IBLOCK_ID',
						'NAME',
						'SORT'
					),
					Array ("nTopCount" => 1)
				);
				if ($arItem = $dbItem->GetNext()) {
					$nSort = $arItem['SORT'];
				}
				$arFields['SORT'] = $arItem['SORT'] + $step;
			}
		}
	}

	function ChangeSortInForm(&$form)
	{
		if (COption::GetOptionString("iblock", 'aisort_active', 'N') == 'Y') {
			$iblockId = $form->customTabber->arArgs["IBLOCK"]["ID"];
			if ($iblockId > 0) {
				if (strlen($form->arFields['SECTIONS']['hidden']) > 1){
					preg_match('~value=\"(.*)\"~', $form->arFields['SECTIONS']['hidden'], $matches);
					$sectionId = end($matches);
				}
				if (!$form->customTabber->arArgs["ID"]) {

					$arFilter = Array(
						'ACTIVE' => 'Y',
						'IBLOCK_ID' => $iblockId,
					);
					$step = COption::GetOptionInt("iblock", 'aisort_step', 50);
					if (strpos($form->arParams["FORM_ACTION"], 'iblock_element_edit.php') !== false) {
						if(COption::GetOptionInt("iblock", 'aisort_section', 'N') == 'Y' && $sectionId) {
							$arFilter['SECTION_ID'] = $sectionId;
						}
						$dbItem = CIBlockElement::GetList(
							Array("SORT" => "DESC"),
							$arFilter,
							false,
							Array ("nTopCount" => 1),
							Array(
								'ID',
								'IBLOCK_ID',
								'NAME',
								'SORT'
							)
						);
						if ($arItem = $dbItem->GetNext()) {
							$nSort = $arItem['SORT'];
						}
					}elseif (strpos($form->arParams["FORM_ACTION"], 'iblock_section_edit.php') !== false) {
						$dbItem = $dbItem = CIBlockSection::GetList(
							Array("SORT" => "DESC"),
							Array(
								'ACTIVE' => 'Y',
								'IBLOCK_ID' => $arFields['IBLOCK_ID'],
							),
							false,
							Array(
								'ID',
								'IBLOCK_ID',
								'NAME',
								'SORT'
							),
							Array ("nTopCount" => 1)
						);
						if ($arItem = $dbItem->GetNext()) {
							$nSort = $arItem['SORT'];
						}
					}
					if ($arItem['SORT']) {
						$form->arFields["SORT"]["html"] = str_replace("500", $arItem['SORT']+$step, $form->arFields["SORT"]["html"]);
					}
				}
			}
		}
	}
}
?>
<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:37
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Bitrix\Main\Loader;
use Rover\Fadmin\Layout\Admin\Input;

/**
 * Class Iblock
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Iblock extends Input
{
    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function showInput()
    {
        if (!Loader::includeModule('iblock')){
            ShowError('Iblock module not found');
            return;
        }

        $valueName      = $this->input->getValueName();
        $additionsHtml  = $this->input->getDisabled() ? 'disabled="disabled"': '';
        $value          = $this->input->getValue();
        $name           = $this->input->getName();

        if ($this->input->isMultiple())
            echo self::getIBlockDropDownListMultiple($value, $name . '_type', $valueName, false, '', '', $additionsHtml);
        else
            echo GetIBlockDropDownList($value, $name . '_type', $valueName, false, '', $additionsHtml);
    }

    /**
     * @param array      $iblockIds
     * @param            $strTypeName
     * @param            $strIBlockName
     * @param bool|false $arFilter
     * @param string     $onChangeType
     * @param string     $onChangeIBlock
     * @param string     $strAddType
     * @param string     $strAddIBlock
     * @return string
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected static function getIBlockDropDownListMultiple(array $iblockIds, $strTypeName, $strIBlockName, $arFilter = false, $onChangeType = '', $onChangeIBlock = '', $strAddType = '', $strAddIBlock = '')
    {
        $html = '';

        static $arTypesAll  = array();
        static $arTypes     = array();
        static $arIBlocks   = array();

        if(!is_array($arFilter))
            $arFilter = array();
        if (!array_key_exists('MIN_PERMISSION',$arFilter) || trim($arFilter['MIN_PERMISSION']) == '')
            $arFilter["MIN_PERMISSION"] = "W";
        $filterId = md5(serialize($arFilter));

        if(!isset($arTypes[$filterId]))
        {
            $arTypes[$filterId]     = array(0 => GetMessage("IBLOCK_CHOOSE_IBLOCK_TYPE"));
            $arIBlocks[$filterId]   = array(0 => array(''=>GetMessage("IBLOCK_CHOOSE_IBLOCK")));

            $rsIBlocks = \CIBlock::GetList(array("IBLOCK_TYPE" => "ASC", "ID" => "ASC"), $arFilter);
            while($arIBlock = $rsIBlocks->Fetch())
            {
                $tmpIBLOCK_TYPE_ID = $arIBlock["IBLOCK_TYPE_ID"];
                if(!array_key_exists($tmpIBLOCK_TYPE_ID, $arTypesAll))
                {
                    $arType = \CIBlockType::GetByIDLang($tmpIBLOCK_TYPE_ID, LANG);
                    $arTypesAll[$arType["~ID"]] = $arType["~NAME"]." [".$arType["~ID"]."]";
                }
                if(!array_key_exists($tmpIBLOCK_TYPE_ID, $arTypes[$filterId]))
                {
                    $arTypes[$filterId][$tmpIBLOCK_TYPE_ID] = $arTypesAll[$tmpIBLOCK_TYPE_ID];
                    $arIBlocks[$filterId][$tmpIBLOCK_TYPE_ID] = array(0 => GetMessage("IBLOCK_CHOOSE_IBLOCK"));
                }
                $arIBlocks[$filterId][$tmpIBLOCK_TYPE_ID][$arIBlock["ID"]] = $arIBlock["NAME"]." [".$arIBlock["ID"]."]";
            }

            $html .= '
			<script type="text/javascript">
			function OnType_'.$filterId.'_Changed(typeSelect, iblockSelectID)
			{
				var arIBlocks = '.\CUtil::PhpToJSObject($arIBlocks[$filterId]).';
				var iblockSelect = BX(iblockSelectID);
				if(!!iblockSelect)
				{
					for(var i=iblockSelect.length-1; i >= 0; i--)
						iblockSelect.remove(i);
					for(var j in arIBlocks[typeSelect.value])
					{
						var newOption = new Option(arIBlocks[typeSelect.value][j], j, false, false);
						iblockSelect.options.add(newOption);
					}
				}
			}
			</script>
			';
        }

        $IBLOCK_TYPE = false;
        if(count($iblockIds) > 0)
        {
            foreach($arIBlocks[$filterId] as $iblock_type_id => $iblocks)
            {
                if(array_key_exists(reset($iblockIds), $iblocks))
                {
                    $IBLOCK_TYPE = $iblock_type_id;
                    break;
                }
            }
        }

        $htmlTypeName   = htmlspecialcharsbx($strTypeName);
        $htmlIBlockName = htmlspecialcharsbx($strIBlockName);
        $onChangeType   = 'OnType_'.$filterId.'_Changed(this, \''.\CUtil::JSEscape($strIBlockName).'\');'.$onChangeType.';';
        $onChangeIBlock = trim($onChangeIBlock);

        $html .= '<select name="'.$htmlTypeName.'" id="'.$htmlTypeName.'" onchange="'.htmlspecialcharsbx($onChangeType).'" '.$strAddType.'>'."\n";
        foreach($arTypes[$filterId] as $key => $value)
        {
            if($IBLOCK_TYPE === false)
                $IBLOCK_TYPE = $key;
            $html .= '<option value="'.htmlspecialcharsbx($key).'"'.($IBLOCK_TYPE===$key? ' selected': '').'>'.htmlspecialcharsEx($value).'</option>'."\n";
        }
        $html .= "</select>\n";
        $html .= "&nbsp;\n";
        $html .= '<select multiple="multiple" size="' . (count($arIBlocks[$filterId][$IBLOCK_TYPE]) > 5 ? '8' : '3') .'"  name="'.$htmlIBlockName.'[]" id="'.$htmlIBlockName.'"'.($onChangeIBlock != ''? ' onchange="'.htmlspecialcharsbx($onChangeIBlock).'"': '').' '.$strAddIBlock.'>'."\n";

        foreach($arIBlocks[$filterId][$IBLOCK_TYPE] as $key => $value)
            $html .= '<option value="'.htmlspecialcharsbx($key).'"'.(in_array($key, $iblockIds)? ' selected': '').'>'.htmlspecialcharsEx($value).'</option>'."\n";

        $html .= "</select>\n";

        return $html;
    }
}
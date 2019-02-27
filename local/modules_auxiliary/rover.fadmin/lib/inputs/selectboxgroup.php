<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 11.01.2016
 * Time: 17:33
 *
 * @author Pavel Shulaev (http://rover-it.me)
 */

namespace Rover\Fadmin\Inputs;

use Rover\Fadmin\Tab;
use Bitrix\Main\Event;

/**
 * Class Selectbox
 *
 * @package Rover\Fadmin\Inputs
 * @author  Pavel Shulaev (http://rover-it.me)
 */
class Selectboxgroup extends Input
{
    public static $type = self::TYPE__SELECTBOX_GROUP;

    protected static $idCache = [];
    /**
     * @var array
     */
    protected $options = [];

    /**
     * multiple selectbox size
     * @var int
     */
    protected $size = 7;

    /**
     * @param array $params
     * @param Tab   $tab
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public function __construct(array $params, Tab $tab)
    {
        parent::__construct($params, $tab);

        if (isset($params['options']))
            $this->options = $params['options'];

        if (isset($params['size']) && intval($params['size']))
            $this->size = intval($params['size']);
        elseif ($params['multiple'])
            $this->size = count($this->options) > $this->size
                ? $this->size
                : count($this->options);
        else
            $this->size = 1;
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function draw()
    {
        $valueId = $this->getValueId();

        if ($this->multiple)
            $this->showMultiLabel($valueId);
        else
            $this->showLabel($valueId);

        $params = [
            'options'       => $this->options,
            'value'         => $this->value,
            'group_name'    => $this->name . '_group',
            'item_name'     => $this->getValueName(),
            'multiple'      => $this->multiple
        ];

        echo self::getList($params);

        $this->showHelp();
    }

    /**
     * @param array $options
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $valueId
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function showMultiLabel($valueId)
    {
        ?>
        <tr>
        <td
            width="50%"
            class="adm-detail-content-cell-l"
            style="vertical-align: top; padding-top: 7px;">
            <label for="<?=$valueId?>"><?=$this->label?>:<br>
                <img src="/bitrix/images/main/mouse.gif" width="44" height="21" border="0" alt="">
            </label>
        </td>
        <td
            width="50%"
            class="adm-detail-content-cell-r"
        ><?php
    }

    /**
     * @param array $params = [
     *  'options' - options' map
     *  'value' - value(s)
     *  'multiple' - multiple
     *  'group_name'
     *  'item_name'
     *  'on_change_group'   - additional js-handler
     *  'on_change_item'    - additional js-handler
     *  'group_additional'      - additional group params
     *  'item_additional'       - additional item params
     * ]
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected static function getList(array $params)
    {
        if (empty($params['options']))
            return '';

        $optionsId = md5(serialize($params['options']));

        $value = empty($params['value']) ? [] : $params['value'];
        if (!is_array($value))
            $value = [$value];

        // search start group value
        $groupValue = null;

        if (count($value)) {
            foreach ($params['options'] as $key => $group)
                if (count(array_intersect($value, array_keys($group['options'])))){
                    $groupValue = $key;
                    break;
                }
        }

        // change group script
        $html = '';

        if(!isset(self::$idCache[$optionsId]))
            $html .= '
			<script type="text/javascript">
                function OnType_'.$optionsId.'_Changed(typeSelect, selectID)
                {
                    var items       = '.\CUtil::PhpToJSObject($params['options']).';
                    var selected    = BX(selectID);
                    
                    if(!!selected)
                    {
                        for(var i=selected.length-1; i >= 0; i--){
                            selected.remove(i);
                        }
                            
                        for(var j in items[typeSelect.value]["options"])
                        {
                            var newOption = new Option(items[typeSelect.value]["options"][j], j, false, false);
                            selected.options.add(newOption);
                        }
                    }
                }
			</script>
			';

        $params['group_name']       = empty($params['group_name']) ? '' : htmlspecialcharsbx($params['group_name']);
        $params['item_name']        = empty($params['item_name']) ? '' : htmlspecialcharsbx($params['item_name']);
        $params['on_change_type']   = 'OnType_'.$optionsId.'_Changed(this, \''.\CUtil::JSEscape($params['item_name']).'\');'
            . (empty($params['on_change_type']) ? '' : trim($params['on_change_type']));
        $params['on_change_item']   = empty($params['on_change_item']) ? '' : trim($params['on_change_item']);
        $params['group_additional'] = empty($params['group_additional']) ? '' : trim($params['group_additional']);
        $params['item_additional']  = empty($params['item_additional']) ? '' : trim($params['item_additional']);
        $params['multiple']         = empty($params['multiple']) ? false : (bool)$params['multiple'];

        $html .= '<select 
                name="' . $params['group_name'] . '"
                id="' . $params['group_name'] . '"
                onchange="'.htmlspecialcharsbx($params['on_change_type']).'" 
                '.$params['group_additional'].'>'."\n";

        foreach($params['options'] as $key => $value)
            $html .= '<option value="'.htmlspecialcharsbx($key).'"'.($groupValue===$key? ' selected': '').'>'.htmlspecialcharsEx($value).'</option>'."\n";

        $html .= "</select>\n";
        $html .= "&nbsp;\n";
        $html .= '<select ' . ($params['multiple'] ? 'multiple="multiple"' : '') . '
                    size="' . count($params['options']) > 5 ? '8' : '3' .'"
                    name="' . $params['item_name'] . ($params['multiple'] ? '[]' : '') . '"
                    id="' . $params['item_name'] . '" '.($params['on_change_item'] != ''? ' onchange="'.htmlspecialcharsbx($params['on_change_item']).'"': '').' 
                    '.$params['item_additional'].'>'."\n";

        if (!is_null($groupValue))
            foreach($params[$groupValue]['options'] as $key => $value)
                $html .= '<option value="'.htmlspecialcharsbx($key).'"'.(in_array($key, $params['value'])? ' selected': '').'>'.htmlspecialcharsEx($value).'</option>'."\n";

        $html .= "</select>\n";

        return $html;
    }
}
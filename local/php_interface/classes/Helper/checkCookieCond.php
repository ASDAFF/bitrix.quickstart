<?

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

if ( ! Loader::includeModule('catalog')) {
    return;
}

if ( ! Loader::includeModule('sale')) {
    return;
}

class checkCookieCond extends CSaleCondCtrlComplex
{

    /**основной метод для проверки выполнения условий
     * @param $arOneCondition
     *
     * @return bool
     * @throws \Bitrix\Main\SystemException
     */
    public static function isCookieCheck ($arOneCondition)
    {

        $result = false;

        $arOneCondition = unserialize($arOneCondition);

        if ($_COOKIE[$arOneCondition['value']]) {
            $result = true;
        }

        return $result;
    }


    public static function GetControlDescr ()
    {
        $description         = parent::GetControlDescr();
        $description['SORT'] = 100;

        return $description;
    }

    public static function GetControlShow ($arParams)
    {
        $arControls = static::GetControls();
        $arResult   = array(
            'controlgroup' => true,
            'group'        => false,
            'label'        => "Наличие cookie",
            'showIn'       => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children'     => array(),
        );
        foreach ($arControls as &$arOneControl) {
            $arResult['children'][] = array(
                'controlId' => $arOneControl['ID'],
                'group'     => false,
                'label'     => $arOneControl['LABEL'],
                'showIn'    => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control'   => array(
                    $arOneControl['PREFIX'],
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE']),
                ),
            );
        }
        if (isset($arOneControl)) {
            unset($arOneControl);
        }

        return $arResult;
    }

    public static function Generate ($arOneCondition, $arParams, $arControl, $arSubs = false)
    {
        return static::GetClassName() . "::isCookieCheck('" . serialize($arOneCondition) . "')";
    }

    public static function GetControls ($strControlID = false)
    {
        $arControlList = array(
            'CondPageCookieName' => array(
                'ID'           => 'CondPageCookieName',
                'FIELD'        => 'NAME',
                'FIELD_TYPE'   => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL'        => "У пользователя есть cookie",
                'PREFIX'       => "COOKIE",
                'LOGIC'        => static::GetLogic(array(
                    BT_COND_LOGIC_EQ,
                )),
                'JS_VALUE'     => array(
                    'type' => 'input',
                ),
                'PHP_VALUE'    => '',
            ),
        );

        if (false === $strControlID) {
            return $arControlList;
        } else if (isset($arControlList[$strControlID])) {
            return $arControlList[$strControlID];
        } else {
            return false;
        }
    }

    public static function GetShowIn ($arControls)
    {
        $arControls = array(CSaleCondCtrlGroup::GetControlID());

        return $arControls;
    }
}
<?

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

if ( ! Loader::includeModule('catalog')) {
    return;
}

if ( ! Loader::includeModule('sale')) {
    return;
}

class checkUrlCond extends CSaleCondCtrlComplex
{

    /**основной метод для проверки выполнения условий
     * @param $arOneCondition
     *
     * @return bool
     * @throws \Bitrix\Main\SystemException
     */
    public static function isUrlCheck ($arOneCondition)
    {

        $result = false;

        $arOneCondition = unserialize($arOneCondition);

        $requestUri = Application::getInstance()->getContext()->getRequest()->getRequestUri();

        switch ($arOneCondition['logic']) {
            case 'Equal':
                if ($requestUri === $arOneCondition['value']) {
                    $result = true;
                }
                break;
            case 'Not':
                if ($requestUri !== $arOneCondition['value']) {
                    $result = true;
                }
                break;
            case 'Contain':
                if (strpos($requestUri, $arOneCondition['value']) !== false) {
                    $result = true;
                }
                break;
            case 'NotCont':
                if (strpos($requestUri, $arOneCondition['value']) === false) {
                    $result = true;
                }
                break;
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
            'label'        => "Страницы сайта",
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
        return static::GetClassName() . "::isUrlCheck('" . serialize($arOneCondition) . "')";
    }

    public static function GetControls ($strControlID = false)
    {
        $arControlList = array(
            'CondPageUrlName' => array(
                'ID'           => 'CondPageUrlName',
                'FIELD'        => 'NAME',
                'FIELD_TYPE'   => 'string',
                'FIELD_LENGTH' => 255,
                'LABEL'        => "Страница сайта",
                'PREFIX'       => "URL",
                'LOGIC'        => static::GetLogic(array(
                    BT_COND_LOGIC_EQ,
                    BT_COND_LOGIC_NOT_EQ,
                    BT_COND_LOGIC_CONT,
                    BT_COND_LOGIC_NOT_CONT,
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
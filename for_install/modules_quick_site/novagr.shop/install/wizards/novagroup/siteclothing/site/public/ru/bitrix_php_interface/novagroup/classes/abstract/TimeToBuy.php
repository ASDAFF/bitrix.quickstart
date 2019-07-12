<?

abstract class Novagroup_Classes_Abstract_TimeToBuy
{

    protected $ID, $IBLOCK_ID, $arResult;

    function __construct($ID, $IBLOCK_ID)
    {
        $this->IBLOCK_ID = $IBLOCK_ID;
        $this->ID = $ID;
    }

    function getAction()
    {
        if(!isset($this->arResult[$this->IBLOCK_ID][$this->ID]))
        {
            $iblock = new Novagroup_Classes_General_IBlock();
            $this->arResult[$this->IBLOCK_ID][$this->ID] = $iblock->getElement(
                array(),
                array("IBLOCK_ID" => $this->IBLOCK_ID, "ID" => $this->ID),
                false,
                false,
                array("ID", "PROPERTY_TIMETOBUYACTIVETO", "PROPERTY_DISCOUNT", "PROPERTY_QUANTITY")
            );
        }
        return $this->arResult[$this->IBLOCK_ID][$this->ID];
    }

    function checkAction()
    {
        // для оптовиков функционал не доступен
        global $USER;
        $optGroup = GetGroupByCode ("opt");
        $arUserGroups = $USER->GetUserGroupArray();
        if (in_array($optGroup["ID"], $arUserGroups)) {
            return false;
        }
        $getAction = $this->getAction();
        //Если пользователь не проставил % скидки на период акции, то акция не стартует для данного товара.
        if ($getAction['PROPERTY_DISCOUNT_VALUE'] < 1) return false;
        //Quantity целое число - количество товара участвующего в акции, т.е. когда товар добавляют в корзину, а потом оформляют заказ, то после оформления заказа число должно уменьшиться на 1. Если число стало 0, то товар перестаёт участвовать в акции.
        if ($getAction['PROPERTY_QUANTITY_VALUE'] < 1) return false;
        //Если пользователь не проставил количество товара участвующего в акции, то акция проводится до момента её окончания или окончания количества на остатках данного товара.
        CheckFilterDates(ConvertTimeStamp(false, "FULL"), $getAction['PROPERTY_TIMETOBUYACTIVETO_VALUE'], $date1_wrong, $date2_wrong, $date2_less_date1);
        if ($date1_wrong == "Y" || $date2_wrong == "Y" || $date2_less_date1 == "Y") return false;
        //Если галка "Отключить для всего сайта", то функционал "Успей купить" на сайте отключается, даже если в товаре стоит дата до которая больше чем текущая.
        if (COption::GetOptionString("novagroup", "TIMETOBUY_DISABLE") == "Y") return false;
        //Если все проверки пройдены, то акция включена
        return true;
    }

    function onSaleHandler($QUANTITY=1)
    {
        $getAction = $this->getAction();
        CIBlockElement::SetPropertyValueCode($this->ID, "QUANTITY", ($getAction['PROPERTY_QUANTITY_VALUE']-$QUANTITY));
    }
}
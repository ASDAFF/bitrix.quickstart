<?php

abstract class Novagroup_Classes_Abstract_QuickOrder extends Novagroup_Classes_Abstract_IBlock
{

    protected $buttonName = "Купить в один клик";
    protected $request = array();
    protected $iBlockID, $productBlockID;
    protected $orderSelectedRows = array(
        "ID",
        "NAME",
        "CREATED_DATE",
        "DETAIL_TEXT",
        "PROPERTY_PHONE",
        "PROPERTY_EMAIL",
        "PROPERTY_URL",
        "PROPERTY_USER",
    );
    protected $orderProductSelectedRows = array(
        "ID",
        "NAME",
        "CREATED_DATE",
        "PROPERTY_PRODUCT",
        "PROPERTY_PRODUCT.NAME",
        "PROPERTY_COLOR",
        "PROPERTY_COLOR.NAME",
        "PROPERTY_SIZE",
        "PROPERTY_SIZE.NAME",
        "PROPERTY_QUANTITY"
    );
    protected $errors = array();
    protected $ORDER_LIST = array();
    protected $ORDER_ID = -1;
    protected $eventName = "QUICK_ORDER";

    static $NAME_BY_ID;

    function __construct($orderBlockID, $productBlockID)
    {
        $this->orderBlockID = (int)$orderBlockID;
        $this->productBlockID = (int)$productBlockID;

        $this->checkInstalledModule();
    }

    function sendEmail()
    {
        $arEventFields = array();
        $PRODUCT_LIST = array();

        $orderProductsID = $this->getOrderProductsID();
        foreach ($orderProductsID as $ID) {
            //получаем данные о товаре
            $ar_res = $this->__getOrderProducts($ID);
            $fields = array(
                'PROPERTY_VALUES' => array(
                    'PRODUCT' => $ar_res['PROPERTY_PRODUCT_VALUE'],
                    'COLOR' => $ar_res['PROPERTY_COLOR_VALUE'],
                    'SIZE' => $ar_res['PROPERTY_SIZE_VALUE'],
                    'QUANTITY' => $ar_res['PROPERTY_QUANTITY_VALUE']
                )
            );
            $PRODUCT_LIST[] = $this->getOrderProductNameByFields($fields);
        }
        $arEventFields["ORDER_LIST"] = implode("<br>", $PRODUCT_LIST);

        //получаем данные о заявке
        $getOrder = $this->__getOrder($this->ORDER_ID);
        if ($getOrder) {

            $arEventFields["ID"] = $getOrder["ID"];
            $arEventFields["USER_EMAIL"] = $getOrder['PROPERTY_EMAIL_VALUE'];
            $arEventFields["USER_PHONE"] = $getOrder['PROPERTY_PHONE_VALUE'];
            $arEventFields["USER_INFO"] = $getOrder['DETAIL_TEXT'];
            $arEventFields["DATE_ENTER"] = $getOrder['CREATED_DATE'];

            return CEvent::Send($this->eventName, SITE_ID, $arEventFields);
        }
        return false;
    }


    function addOrder($fields)
    {
        if ($this->checkFields($fields)) {

            $DATA = array();
            $DATA['PROPERTY_VALUES']['URL'] = $fields['url'];
            $DATA['PROPERTY_VALUES']['EMAIL'] = $fields['email'];
            $DATA['PROPERTY_VALUES']['PHONE'] = $fields['phone'];
            $DATA['DETAIL_TEXT'] = $fields['info'];
            $DATA['PROPERTY_VALUES']['USER'] = Novagroup_Classes_General_Main::getUser('ID');
            $DATA['PROPERTY_VALUES']['ORDER_LIST'] = $this->getOrderProductsID();
            $ORDER_ID = $this->__addOrder($DATA);

            if ($ORDER_ID > 0) {
                $this->ORDER_ID = $ORDER_ID;
                $this->sendEmail();
                return true;
            }
        }
        return false;
    }

    function addOrderProduct($fields)
    {
        if ($this->checkProductFields($fields)) {
            //подготовка данных
            $DATA = array();
            $DATA['PROPERTY_VALUES']['COLOR'] = $fields['colorId'];
            $DATA['PROPERTY_VALUES']['SIZE'] = $fields['sizeId'];
            $DATA['PROPERTY_VALUES']['PRODUCT'] = $fields['productId'];
            $DATA['PROPERTY_VALUES']['QUANTITY'] = $fields['quantity'];
            $this->ORDER_LIST[] = $PRODUCT_ID = $this->__addOrderProduct($DATA);
            return $PRODUCT_ID;
        } else {
            return false;
        }
    }

    function getOrderProductsID()
    {
        return (count($this->ORDER_LIST) > 0) ? $this->ORDER_LIST : array("ID" => -1);
    }

    function __getOrder($ORDER_ID)
    {
        //получаем данные о заявке
        $res = CIBlockElement::GetList(array(), array("ID" => $ORDER_ID, "IBLOCK_ID" => $this->orderBlockID), false, false, $this->orderSelectedRows);
        return $res->Fetch();
    }

    function __getOrderProducts($PRODUCT_ID)
    {
        //получаем данные о заявке
        $res = CIBlockElement::GetList(array(), array("ID" => $PRODUCT_ID, "IBLOCK_ID" => $this->productBlockID), false, false, $this->orderProductSelectedRows);
        return $res->Fetch();
    }

    function getNameByID($ID)
    {
        if(isset(self::$NAME_BY_ID[$ID]))
        {
            return self::$NAME_BY_ID[$ID];
        } else {
            if ($ID > 0) {
                $res = CIBlockElement::GetByID($ID);
                if ($ar_res = $res->GetNext()) return self::$NAME_BY_ID[$ID] = $ar_res['NAME'];
            }
            return false;
        }
    }

    function __addOrder($fields = array())
    {
        $fields["IBLOCK_ID"] = $this->orderBlockID;
        $fields["ACTIVE"] = "Y";
        $fields['NAME'] = "Новый заказ";

        $el = new CIBlockElement;
        $ORDER_ID = $el->Add($fields);
        if ($ORDER_ID > 0) {
            return $ORDER_ID;
        } else {
            $this->setError("К сожалению, в настоящее время заявка не может быть сохранена. Попробуйте повторить позднее");
            return false;
        }
    }

    function getOrderProductNameByFields($fields)
    {
        $NAME = array();
        $NAME[] = ($name = $this->getNameByID($fields['PROPERTY_VALUES']['PRODUCT'])) ?
            $name : "Товар";
        if ($name = $this->getNameByID($fields['PROPERTY_VALUES']['COLOR']))
            $NAME[] = "Цвет " . $name;
        if ($name = $this->getNameByID($fields['PROPERTY_VALUES']['SIZE']))
            $NAME[] = "Размер " . $name;
        if (trim($fields['PROPERTY_VALUES']['QUANTITY']) <> "")
            $NAME[] = "Количество " . $fields['PROPERTY_VALUES']['QUANTITY'];
        return implode(", ", $NAME);
    }

    function __addOrderProduct($fields = array())
    {
        $fields["IBLOCK_ID"] = $this->productBlockID;
        $fields["ACTIVE"] = "Y";
        $fields['NAME'] = $this->getOrderProductNameByFields($fields);

        $el = new CIBlockElement;
        $PRODUCT_ID = $el->Add($fields);
        if ($PRODUCT_ID > 0) {
            return $PRODUCT_ID;
        } else {
            $this->setError("К сожалению, в настоящее время заявка не может быть сохранена. Попробуйте повторить позднее");
            return false;
        }
    }

    function setError($error)
    {
        $this->errors = array();
        $this->error[] = $error;
    }

    function addError($error)
    {
        $this->error[md5($error)] = $error;
    }

    function getErrors()
    {
        return $this->error;
    }

    function hasErrors()
    {
        return (count($this->getErrors()) > 0) ? true : false;
    }

    function checkFields($fields = array())
    {
        $error = false;
        if (is_array($fields) and count($fields) > 0) {
            if (isset($fields['phone']) and trim($fields['phone']) <> "") {
                /*nothing*/
            } else {
                //формируем массив сообщений об ошибках
                $this->addError("Не указан номер телефона");
                $error = true;
            }

            if (isset($fields['email']) and trim($fields['email']) <> "") {
                if (check_email(trim($fields['email'])) === true) {
                    /*nothing*/
                } else {
                    //формируем массив сообщений об ошибках
                    $this->addError("Неверный адрес электронной почты");
                    $error = true;
                }
            }
        } else {
            $error = true;
        }
        return ($error === true) ? false : true;
    }

    function checkProductFields($fields)
    {
        $error = false;
        if (is_array($fields) and count($fields) > 0) {
            if (isset($fields['productId']) and trim($fields['productId']) > 0) {
                /*nothing*/
            } else {
                $error = true;
            }
        } else {
            $error = true;
        }
        return ($error === true) ? false : true;
    }
}
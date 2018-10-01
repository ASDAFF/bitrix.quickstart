<?php
/**
 * Please no code in this file. Structure your local folder
 */

use BitrixQuickStart;

//Autoload
require_once(dirname(__FILE__) . '/classes/Autoloader.php');
$autoloader = new \BitrixQuickStart\Autoloader();

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php'))
    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');

//Consts
if (file_exists(__DIR__ . '/config/const.php'))
    require_once(__DIR__ . '/config/const.php');

//Config
if (file_exists(__DIR__ . '/config/frontend.php'))
    require_once(__DIR__ . '/config/frontend.php');

//Events
if (file_exists(__DIR__ . '/config/events.php'))
    require_once(__DIR__ . '/config/events.php');

//Handlers
if (file_exists(__DIR__ . '/include/handlers.php'))
    require_once(__DIR__ . '/include/handlers.php');





\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'onSaleAdminOrderInfoBlockShow', 'onSaleAdminOrderInfoBlockShow');

function onSaleAdminOrderInfoBlockShow(\Bitrix\Main\Event $event)
{
    // $order = $event->getParameter("ORDER");

    return new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            array('TITLE' => 'Параметр 1:',
                'VALUE' => 'Значение параметра 1', 'ID' => 'param1'),
            array('TITLE' => 'Параметр 2::',
                'VALUE' => '<a href="http://1c-bitrix.ru">Значение 
                параметра 2</a>'),
        ),
        'sale'
    );
}




\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAdminSaleOrderViewDraggable", array("MyClass1", "onInit"));

class MyClass1
{
    public static function onInit()
    {
        return array("BLOCKSET" => "MyClass1",
            "getScripts"  => array("MyClass1", "mygetScripts"),
            "getBlocksBrief" => array("MyClass1", "mygetBlocksBrief"),
            "getBlockContent" => array("MyClass1", "mygetBlockContent"),
        );
    }

    public static function mygetBlocksBrief($args)
    {
        $id = !empty($args['ORDER']) ? $args['ORDER']->getId() : 0;
        return array(
            'custom1' => array("TITLE" => "Пользовательский блок для заказа №".$id),
            'custom2' => array("TITLE" => "Еще один блок для заказа №".$id),
        );
    }

    public static function mygetScripts($args)
    {
        return '<script type="text/javascript">... </script>';
    }

    public static function mygetBlockContent($blockCode, $selectedTab, $args)
    {
        $result = '';
        $id = !empty($args['ORDER']) ? $args['ORDER']->getId() : 0;

        if ($selectedTab == 'tab_order')
        {
            if ($blockCode == 'custom1')
                $result = 'Содержимое блока custom1<br> Номер заказа: '.$id;
            if ($blockCode == 'custom2')
                $result = 'Содержимое блока custom2<br> Номер заказа: '.$id;
        }

        return $result;
    }
}





\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAdminSaleOrderEditDraggable", array("MyEditClass", "onInit"));
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAdminSaleOrderCreateDraggable", array("MyEditClass", "onInit"));

class MyEditClass extends MyClass1
{
    public static function onInit()
    {
        return array(
            "BLOCKSET" => "MyEditClass",
            "check" => array("MyEditClass", "mycheck"),
            "action" => array("MyEditClass", "myaction"),
            "getScripts" => array("MyEditClass", "mygetScripts"),
            "getBlocksBrief" => array("MyEditClass", "mygetBlocksBrief"),
            "getBlockContent" => array("MyEditClass", "mygetBlockContent"),
        );
    }

    public static function myaction($args)
    {
        // заказ сохранен, сохраняем данные пользовательских блоков
        // возвращаем True в случае успеха и False - в случае ошибки
        // в случае ошибки $GLOBALS["APPLICATION"]->ThrowException("Ошибка!!!", "ERROR");
        return true;
    }

    public static function mycheck($args)
    {
        // заказ еще не сохранен, делаем проверки
        // возвращаем True, если можно все сохранять, иначе False
        // в случае ошибки $GLOBALS["APPLICATION"]->ThrowException("Ошибка!!!", "ERROR");
        return true;
    }

    public static function mygetBlockContent($blockCode, $selectedTab, $args)
    {
        $result = '';
        $id = !empty($args['ORDER']) ? $args['ORDER']->getId() : 0;

        if ($selectedTab == 'tab_order')
        {
            if ($blockCode == 'custom1')
            {
                $result = 'Содержимое блока custom1 для заказа №'.$id.'
                        <br><input name="custom1" value="cust1">';
            }
            if ($blockCode == 'custom2')
            {
                $result = 'Содержимое блока custom2 для заказа №'.$id.'
                        <br><input name="custom2" value="cust2" 
                        id="custom2_field">';
            }
        }

        return $result;
    }

    //вставим итоговую стоимость заказа в кастомное поле №2 на странице создания/изменения заказа

    public static function mygetScripts($args)
    {
        $id = !empty($args['ORDER']) ? $args['ORDER']->getId() : 0;

        return '<script type="text/javascript"> BX.ready(function(){
            BX.Sale.Admin.OrderEditPage.registerFieldsUpdaters({
                "TOTAL_PRICES": function(prices){
                    var custom = BX("custom2_field");
                    if (custom2_field)
                        custom2_field.value = prices.PRICE_TOTAL;
                }	
            });
        });	</script>';
    }

}





AddEventHandler("main", "OnAdminListDisplay", "MyOnAdminListDisplay");
function MyOnAdminListDisplay(&$list)
{
    if ($list->table_id=="tbl_sale_order") {//если это страница списка заказов, для других страниц админки будет свой table_id - чтобы его узнать, распечатайте входящий массив $list на нужной странице
        //pp($list, true);
        $list->aVisibleHeaders["CONNECTED"] =
            array(
                "id" => "CONNECTED",
                "content" => 'СВОЕ', // текст в шапке таблицы для поля CONNECTED
                "sort" => "CONNECTED",
                "default" => true,
                "align" => "left",
            );

        $list->arVisibleColumns[]= 'CONNECTED';

        foreach ($list->aRows as $row) {

            $row->addField(
                'CONNECTED',
                'any<br>html' // что будет выводиться в ячейке таблицы
            );
        }



        foreach ($list->aRows as $row){ // здесь мы вклиниваемся в контекстное меню каждой строки таблицы
            $row->aActions["all_orders"]["ICON"] = "";
            $row->aActions["all_orders"]["TEXT"] = "Все заказы пользователя";
            $row->aActions["all_orders"]["ACTION"] = "javascript:orders_ms(".$row->id.")";  // здесь мы объявляем действие - js-функция orders_ms(), в которую будем передавать параметр (в данном случае id заказа)
        }
        // $list->arActions["status_draft"] = "Все заказы пользователя"; // а здесь попадаем в меню групповых  действий  над элементами над элементами
    }


}

AddEventHandler('main', 'OnAdminContextMenuShow', 'OrderDetailAdminContextMenuShow');

// собственно сам обработчик

function OrderDetailAdminContextMenuShow(&$items){
    if ($_SERVER['REQUEST_METHOD']=='GET' && $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/sale_order_edit.php' && $_REQUEST['ID']>0)
    {
        $items[] = array(
            "TEXT"=>"Отправить письмо",
            "LINK"=>"javascript:mail_ms(".$_REQUEST['ID'].")",
            "TITLE"=>"Отправить письмо",
            "ICON"=>"adm-btn",
        );
    }
    if ($_SERVER['REQUEST_METHOD']=='GET' && $GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/sale_order_view.php' && $_REQUEST['ID']>0)
    {
        $items[] = array(
            "TEXT"=>"Отправить письмо",
            "LINK"=>"javascript:mail_ms(".$_REQUEST['ID'].")",
            "TITLE"=>"Отправить письмо",
            "ICON"=>"adm-btn",
        );
    }
}
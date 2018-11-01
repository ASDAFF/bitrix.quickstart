<?php
AddEventHandler("main", "OnAdminListDisplay", "OnAdminListDisplayHandler");
function OnAdminListDisplayHandler(&$list)
{
    if ($list->table_id == 'tbl_sale_order')
    {
        foreach ($list->aRows as $row)
        {
            $row->AddActions(array_merge(
                $row->aActions,
                [
                    array(
                        "ICON"=>"",
                        "TEXT"=>"Печать - Расширенный бланк заказа",
                        "ACTION"=>$list->ActionRedirect("sale_print.php?PROPS_ENABLE=Y&doc=order_form_ext&SHOW_ALL=Y&ORDER_ID=".$row->id)
                    )
                ]
            ));
        }
    }
}


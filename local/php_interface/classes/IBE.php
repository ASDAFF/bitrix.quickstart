<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 02.02.2019
 * Time: 6:05
 */


class IBE extends DA {

    public $arr_select_prop = array(); // массив дополниельных свойств

    public function get() {
        // >>определяем количество элементов и страниц
        $CIBlockElement = CIBlockElement::GetList(
            $arOrder = array(), $arFilter = $this->arr_filter, $arGroupBy = false, $arNavStartParams = false, $arSelectFields = Array('ID')
        );
        $all_items_count = $CIBlockElement->SelectedRowsCount();
        $this->w['count_all_items'] = $all_items_count;

        $count_all_page = ceil($all_items_count / $this->w['item_count_per_page']);
        $this->w['count_all_page'] = $count_all_page;
        // <<
        $start_elem = $this->w['item_count_per_page'] * ($this->w['page_number'] - 1);
        //echo "$start_elem";
        if ($start_elem >= $all_items_count) {
            // если запрашивается страница которой нет(т.е. элементы закончились - возвращаем пустой массив $items)
        } else {
            // >>получаем товары
            $items = array();

            $_arNavStartParams = Array(
                //@del
                //"nPageSize"=>$this->item_count_per_page, 'iNumPage'=>$this->page_number
                "nPageSize" => $this->w['item_count_per_page'], 'iNumPage' => $this->w['page_number']
            );
            if ($this->all_items == true) {
                $_arNavStartParams = false; // получить все элементы без пагинации
            }

            $CIBlockElement = CIBlockElement::GetList(
                $arOrder = $this->arr_sort, $arFilter = $this->arr_filter, $arGroupBy = false, $arNavStartParams = $_arNavStartParams, $arSelectFields = $this->arr_select
            );

            while ($ar_result = $CIBlockElement->GetNext()) {
                // получаем множественные свойства
                if (!empty($this->arr_filter['IBLOCK_ID']) && !empty($ar_result['ID']) && count($this->arr_select_prop)) {
                    $arr_res_prop = array();
                    foreach ($this->arr_select_prop as $code_prop) {
                        // получим множ свойство (связанные товары )
                        $arr_val_prop = array();
                        $db_props = CIBlockElement::GetProperty(
                            $IBLOCK_ID = $this->arr_filter['IBLOCK_ID'], $PROD_ID = $ar_result['ID'], array("sort" => "asc"), Array("CODE" => $code_prop)
                        );
                        $arr_val_prop = array();
                        while ($prop = $db_props->GetNext()) {
                            if (!empty($prop['VALUE'])) {
                                $arr_val_prop[] = $prop['VALUE'];
                            }
                        }

                        $arr_res_prop[$code_prop] = $arr_val_prop; // пополняем результат
                    }
                    $ar_result['_prop'] = $arr_res_prop;
                }
                $items[] = $ar_result;
            }
            $this->w['items'] = $items;
        }




        // <<получаем товары
        return $this->w;
    }

    //--->
    // какие доп свойства свойства будут получены методом CIBlockElement::GetProperty (хорошо годиться для множ свойств)
    public function prop() {
        $arg_list = func_get_args();
        foreach ($arg_list as $prop_code) {
            $prop_code = strtoupper($prop_code);
            $this->arr_select_prop[] = $prop_code;
        }
        return $this;
    }

    //<---

}
<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 02.02.2019
 * Time: 6:04
 */

abstract class DA {

    //public $page_number = 1;
    //public $item_count_per_page = 100;
    public $all_items = false;
    public $arr_sort = array(); // arOrder - массив для сортировки // примерный вид array("sort"=>"asc" [, ...]);
    public $arr_filter = array();  // arFilter - Массив вида array("фильтруемое поле"=>"значения фильтра" [, ...]).
    public $arr_select = array(); // arSelectFields - Массив возвращаемых полей элемента.
    // для составления конечного результата ($w - 'рабочий' массив) // @todo
    public $w = array(
        "count_all_items" => null,
        "count_all_page" => null,
        'page_number' => 1,
        'item_count_per_page' => 100,
        'err' => null,
        'items' => array()
    );

    // какую страницу получить
    public function page($page_number) {
        //@del
        //$this->page_number = $page_number;
        $this->w['page_number'] = $page_number;
        return $this;
    }

    // сколько элеменотов на странице
    public function count($item_count_per_page) {
        //@del
        //$this->item_count_per_page = $item_count_per_page;
        $this->w['item_count_per_page'] = $item_count_per_page;
        return $this;
    }

    // получить ли все элементы? (без постраничной навигации)
    public function all($all) {
        if (!empty($all)) {
            $this->all_items = true;
        }
        return $this;
    }

    //----------------------------->>>
    // сетеры для сортирвки делятся на два метода asc и desc
    /*
      @todo http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getlist.php (для элементов инфоблока)
      order - порядок сортировки, может принимать значения:
      asc - по возрастанию;
      nulls,asc - по возрастанию с пустыми значениями в начале выборки;
      asc,nulls - по возрастанию с пустыми значениями в конце выборки;
      desc - по убыванию;
      nulls,desc - по убыванию с пустыми значениями в начале выборки;
      desc,nulls - по убыванию с пустыми значениями в конце выборки;
     */
    public function asc($by) { // по возрастанию
        $by = strtolower($by);
        $this->arr_sort[$by] = 'asc';
        return $this;
    }

    public function desc($by) { // по возрастанию
        $by = strtolower($by);
        $this->arr_sort[$by] = 'desc';
        return $this;
    }

    //----------------------------->>>
    // фильтр
    // ...->filter('ACTIVE', 'Y')->filter('IBLOCK_ID', 10)....
    public function filter($field, $val_filter = '') {
        $field = strtoupper($field);
        $this->arr_filter[$field] = $val_filter;
        return $this;
    }

    // какие поля будут возвращены // параметры через запятую ->select('ID', 'PRICE' [, ...])->
    public function select() {
        $arg_list = func_get_args();
        foreach ($arg_list as $field_name) {
            $field_name = strtoupper($field_name);
            $this->arr_select[] = $field_name;
        }
        return $this;
    }

    // возвращает массив с результатами
    abstract function get();

}

/* $rj = new Rj();
  $rj->count(15)->page(3)->asc('id')->desc('sort');
  new dbug ($rj);

  exit(); */

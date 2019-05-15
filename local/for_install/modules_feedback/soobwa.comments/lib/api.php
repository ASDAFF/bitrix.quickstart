<?

namespace Soobwa\Comments;

use \Soobwa\Comments\CommentsTable;

class Api
{
    /*
     * Добовление элемента
     *
     * @arParams - принимает массив с параметрами комментария
     *
     * @return
     * */
    public static function addElement($arParams){
        $result = CommentsTable::add($arParams);
        if($result->isSuccess()){
            $id = $result->getId();
            return $id;
        }else{
            $error = $result->getErrorMessages();
            return $error;
        }
    }
    /*
     * Колличество элементов в таблице
     *
     * @param array $filter - параметры фильтра
     * @param array $order - параметры сортировки
     * @param array $limit - лимит
     * @param array $offset - смещение
     *
     * @return int - колличество строк в выборке
     * */
    public static function getCount($filter = array(), $order = array('ID'=>'ASC'), $limit = 0, $offset = 0){
        $arList = array();
        $select = array('ID');
        $result = CommentsTable::getList(
            array(
                'select' => $select,
                'filter' => $filter,
                'order' => $order,
                'limit' => $limit,
                'offset' => $offset
            )
        );
        while ($res = $result->fetch()) {
            $arList[] = $res;
        }

        return count($arList);
    }
    /*
     * Обновление элемента
     *
     * @param int $id - id сообщения
     * @param array $arParams - массив с тем что нужно изменить
     *
     * @return
     * */
    public static function updElement($id, $arParams){
        $result = CommentsTable::update($id, $arParams);

        return $result;
    }
    /*
     * Удаление элемента
     *
     * @param int $arParams - id записи которую нужно удалить
     *
     * */
    public static function delElement($arParams){
        $result = CommentsTable::delete($arParams);
        return  $result;
    }
    /*
     * Возврашает список элементов в таблице
     *
     * @param array $filter - параметры фильтра
     * @param array $order - параметры сортировки
     * @param array $limit - лимит
     * @param array $offset - смещение
     *
     *
     * @return array() - выборка
     * */
    public static function getList($select = array(), $filter = array(), $order = array('ID'=>'ASC'), $limit = 0, $offset = 0){
        $result = CommentsTable::getList(
            array(
                'select' => $select,
                'filter' => $filter,
                'order' => $order,
                'limit' => $limit,
                'offset' => $offset
            )
        );
        return $result;
    }

    /*
     * Изменить статус удаленно для сообщения
     *
     * @param int $id - ид комментария
     * @param bool $valParams - параметры (true/false)
     *
     * @return array()
     * */
    public static function statusDel($id, $valParams){
        $arFieldsUPD = array(
            'DELETE' => $valParams,
        );
        $result = self::updElement($id, $arFieldsUPD);

        return  $result;
    }

    /*
     * Изменить статус активности для сообщения
     * @param int $id - ид комментария
     * @param bool $valParams - параметры (true/false)
     *
     * @return array()
     * */
    public static function statusActive($id, $valParams){
        $arFieldsUPD = array(
            'ACTIVE' => $valParams,
        );
        $result = self::updElement($id, $arFieldsUPD);

        return  $result;
    }

}
?>
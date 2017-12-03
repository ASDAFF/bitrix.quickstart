<?

namespace Soobwa\Comments;

use \Soobwa\Comments\CommentsTable;

class Api
{
    /*
     * ���������� ��������
     *
     * @arParams - ��������� ������ � ����������� �����������
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
     * ����������� ��������� � �������
     *
     * @param array $filter - ��������� �������
     * @param array $order - ��������� ����������
     * @param array $limit - �����
     * @param array $offset - ��������
     *
     * @return int - ����������� ����� � �������
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
     * ���������� ��������
     *
     * @param int $id - id ���������
     * @param array $arParams - ������ � ��� ��� ����� ��������
     *
     * @return
     * */
    public static function updElement($id, $arParams){
        $result = CommentsTable::update($id, $arParams);

        return $result;
    }
    /*
     * �������� ��������
     *
     * @param int $arParams - id ������ ������� ����� �������
     *
     * */
    public static function delElement($arParams){
        $result = CommentsTable::delete($arParams);
        return  $result;
    }
    /*
     * ���������� ������ ��������� � �������
     *
     * @param array $filter - ��������� �������
     * @param array $order - ��������� ����������
     * @param array $limit - �����
     * @param array $offset - ��������
     *
     *
     * @return array() - �������
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
     * �������� ������ �������� ��� ���������
     *
     * @param int $id - �� �����������
     * @param bool $valParams - ��������� (true/false)
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
     * �������� ������ ���������� ��� ���������
     * @param int $id - �� �����������
     * @param bool $valParams - ��������� (true/false)
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
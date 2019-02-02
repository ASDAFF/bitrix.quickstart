<?
namespace Cpeople\Classes\Sale;

class OrderProp
{
    private $data = array();

    public static function getOrderProps($id = false)
    {
        static $result = null;
        static $resultById = null;
        static $resultByCode = null;

        if($result === null)
        {
            $rs = \CSaleOrderProps::GetList(array(), array(), false, false, array('ID', 'NAME', 'CODE'));
            while($ar = $rs->GetNext(true, false))
            {
                $obj = new OrderProp($ar);
                $resultById[ $ar['ID'] ] = &$obj;
                $resultByCode[ $ar['CODE'] ] = &$obj;
                $result[] = &$obj;
                unset($obj);
            }
        }

        return $id ? (isset($resultById[$id]) ? $resultById[$id] : (isset($resultByCode[$id]) ? $resultByCode[$id] : false)) : $result;
    }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getId()
    {
        return $this->data['ID'];
    }

    public function getName()
    {
        return $this->data['NAME'];
    }

    public function getCode()
    {
        return $this->data['CODE'];
    }
}
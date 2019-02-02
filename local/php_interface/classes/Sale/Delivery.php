<?
namespace Cpeople\Classes\Sale;

class Delivery
{
    private $data = array();

    public static function getDelivery($id = false)
    {
        static $result = null;
        static $resultById = null;

        if($result === null)
        {
            $rs = \CSaleDelivery::GetList(array(), array(), false, false, array('ID', 'NAME'));
            while($ar = $rs->GetNext(true, false))
            {
                $obj = new Delivery($ar);
                $resultById[ $ar['ID'] ] = &$obj;
                $result[] = &$obj;
                unset($obj);
            }
        }

        return $id ? (isset($resultById[$id]) ? $resultById[$id] : false) : $result;
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
}

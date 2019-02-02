<?
namespace Cpeople\Classes\Sale;

class Payment
{
    private $data = array();

    public static function getPayment($id = false)
    {
        static $result = null;
        static $resultById = null;

        if($result === null)
        {
            $rs = \CSalePaySystem::GetList(array(), array(), false, false, array('ID', 'NAME'));
            while($ar = $rs->GetNext(true, false))
            {
                $obj = new Payment($ar);
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
<?
namespace Cpeople\Classes\Sale;

class OrderPropsValue
{
    private $data = array();

    public static function getOrderPropsValue($orderId, $fetchByCode = true)
    {
        static $result = array();

        $hash = $orderId . '_' . $fetchByCode;
        if($result[$hash] === null)
        {
            $result[$hash] = array();
            $rs = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId), false, false, array('NAME', 'VALUE', 'CODE'));
            while($ar = $rs->GetNext(true, false))
            {
                $obj = new OrderPropsValue($ar);
                if($fetchByCode)
                {
                    $result[$hash][$ar['CODE']] = $obj;
                }
                else
                {
                    $result[$hash][] = $obj;
                }
            }
        }

        return $result[$hash];
    }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getName()
    {
        return $this->data['NAME'];
    }

    public function getValue()
    {
        return $this->data['VALUE'];
    }
}
<?
namespace Ns\Bitrix\Helper\IBlock;

/**
*
*/
class Checker extends \Ns\Bitrix\Helper\HelperCore
{

	public function isUnique()
	{
		$rs = \CIBlockElement::GetList(
			false,
			$this->getParams(),
			false,
			array("nTopCount" => 1),
			array("ID")
			);
		if ($rs->SelectedRowsCount())
		{
			return false;
		}
		else
		{
			return true;
		}
	}

    public function existedID()
    {
        $rs = \CIBlockElement::GetList(
            false,
            $this->getParams(),
            false,
            array("nTopCount" => 1),
            array("ID")
        );
        if ($rs->SelectedRowsCount() == 0)
        {
            return false;
        }
        else
        {
            $item = $rs->Fetch();
            return $item["ID"];
        }
    }

}


?>
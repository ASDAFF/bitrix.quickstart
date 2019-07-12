<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage siteshouse.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz\Validator;

use Bitrix\Main\Entity;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class LengthFix extends \Bitrix\Main\Entity\Validator\Base
{
	/** @var integer */
	protected $val;

	/** @var string */
	protected $errorPhraseCode = 'MLIFE_ASZ_ENTITY_VALIDATOR_LENGTH_VAL';
	protected $errorPhraseVal;

	public function __construct($val = null, $errorPhrase = array('VAL' => null))
	{
		if ($val !== null)
		{
			if (!is_int($val))
			{
				throw new ArgumentTypeException('val', 'integer');
			}

			$this->val = $val;
		}

		if (!empty($errorPhrase['VAL']))
		{
			$this->errorPhraseVal = $errorPhrase['VAL'];
		}

		parent::__construct();
	}


	public function validate($value, $primary, array $row, Entity\Field $field)
	{

		if ($this->val !== null)
		{
			if ((strlen($value) !== $this->val) && strlen($value)>0)
			{
				$mess = ($this->errorPhraseVal !== null? $this->errorPhraseVal : Loc::getMessage($this->errorPhraseCode));
				return $this->getErrorMessage($value, $field, $mess, array("#LENGTH#" => $this->val));
			}
		}

		return true;
	}
}

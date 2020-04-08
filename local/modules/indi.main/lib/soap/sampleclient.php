<?
/**
 * Individ module
 * 
 * @category	Individ
 * @package		Soap
 * @link		http://individ.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Indi\Main\Soap;

/**
 * Soap-клиент для примера
 * 
 * @category	Individ
 * @package		Soap
 */
class SampleClient extends Client
{
	protected $wsdl = 'http://bitrix.individ.ru/soap/server/re.wsdl';
	
	protected $wsdlParams = array(
		'trace' => 0,
		'connection_timeout' => 10,
		'encoding' => 'UTF-8',
		'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
		'login' => 'bitrix',
		'password' => 'bitrix',
	);

}
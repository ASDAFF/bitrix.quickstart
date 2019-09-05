<?
/**
 *  module
 * 
 * @category	
 * @package		Soap
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Site\Main\Soap;

/**
 * Soap-клиент для примера
 * 
 * @category	
 * @package		Soap
 */
class SampleClient extends Client
{
	protected $wsdl = 'http://bitrix..ru/soap/server/re.wsdl';
	
	protected $wsdlParams = array(
		'trace' => 0,
		'connection_timeout' => 10,
		'encoding' => 'UTF-8',
		'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
		'login' => 'bitrix',
		'password' => 'bitrix',
	);

}
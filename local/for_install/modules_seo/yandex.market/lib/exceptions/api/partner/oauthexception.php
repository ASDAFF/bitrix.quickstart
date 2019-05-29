<?php
namespace Yandex\Market\Exceptions\Api\Partner;

class OAuthException extends \Exception
{
	const ERROR_NO_TOKEN = 0x01;
	const ERROR_NO_CLIENT_ID = 0x02;
}

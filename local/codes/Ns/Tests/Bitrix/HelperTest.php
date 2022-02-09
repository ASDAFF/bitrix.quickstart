<?php
require "/../../Bitrix/TestTest.php";
// require_once __DIR__ . '/autoload.php';

class HelperTest extends PHPUnit_Framework_TestCase
{
	public function testCreateIblockInstance()
	{
		$class = TesTTest::Create('iblock');
		$this->assertInstanceOf("Ns\Bitrix\Helper\IBlock", $class);
	}
}
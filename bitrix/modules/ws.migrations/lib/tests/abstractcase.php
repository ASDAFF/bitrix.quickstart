<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\Migrations\Tests;


use WS\Migrations\Localization;
use WS\Migrations\Tests\Cases\ErrorException;

abstract class AbstractCase {

    private $_assertCount = 0;

    /**
     * @var \WS\Migrations\Localization
     */
    protected $localization;

    public function __construct(Localization $loc) {
        $this->localization = $loc;
    }

    /**
     * ��������� ��������� �� ������
     * @param $path
     * @param null $replace
     * @return mixed
     */
    protected function errorMessage($path, $replace = null) {
        return $this->localization->message('errors.'.$path, $replace);
    }

    static public function className() {
        return get_called_class();
    }

    static protected function exportValue($value) {
        return var_export($value, true);
    }

    abstract public function name();

    abstract public function description();

    protected function throwError($message, $dump = null) {
        $e = new ErrorException($message);
        $dump  && $e->setDump($dump);
        throw $e;
    }

    private function generateMessage($systemMessage, $userMassage) {
        return $userMassage ? $systemMessage." with message: ".$userMassage : $systemMessage;
    }

    protected function assertTrue($actual, $message = null) {
        $this->_assertTake();
        if  (!$actual) {
            $this->throwError($this->generateMessage('Value `'.self::exportValue($actual).'` not asserted as true', $message));
        }
    }

    protected function assertFalse($actual, $message = null) {
        $this->_assertTake();
        if  ($actual) {
            $this->throwError($this->generateMessage('Value `'.self::exportValue($actual).'` not asserted as false', $message));
        }
    }

    protected function assertNotEmpty($actual, $message = null) {
        $this->_assertTake();
        if  (empty($actual)) {
            $this->throwError($this->generateMessage('Value `'.self::exportValue($actual).'` not asserted as empty', $message));
        }
    }

    protected function assertEmpty($actual, $message = null) {
        $this->_assertTake();
        if  (!empty($actual)) {
            $this->throwError($this->generateMessage('Value `'.self::exportValue($actual).'` asserted as empty', $message));
        }
    }


    protected function assertEquals($actual, $expected, $message = null) {
        $this->_assertTake();
        if  ($actual != $expected) {
            $this->throwError($this->generateMessage('Value actual:`'.self::exportValue($actual).'` not equals expected:`'.self::exportValue($expected).'`', $message));
        }
    }

    protected function assertNotEquals($actual, $expected, $message = null) {
        $this->_assertTake();
        if  ($actual == $expected) {
            $this->throwError($this->generateMessage('Value actual:`'.self::exportValue($actual).'` expectation that not equals expected:`'.self::exportValue($expected).'`', $message));
        }
    }

    protected function assertCount($arActual, $expectedCount, $message = null) {
        $this->_assertTake();
        if  (count($arActual) != $expectedCount) {
            $this->throwError($this->generateMessage('Value actual:`'.self::exportValue($arActual).'` not equals count elements, expected:`'.self::exportValue($expectedCount).'`', $message));
        }
    }

    protected function assertNotCount($arActual, $expectedCount, $message = null) {
        $this->_assertTake();
        if  (count($arActual) == $expectedCount) {
            $this->throwError($this->generateMessage('Value actual:`'.self::exportValue($arActual).'` equals count elements, expected:`'.self::exportValue($expectedCount).'`', $message));
        }
    }

    protected function viewDump() {
        $values = func_get_args();
        $res = 'Dump:'."\n";
        foreach ($values as $value) {
            $res .= self::exportValue($value);
        }
        $this->throwError($res);
    }

    public function setUp() {}

    public function tearDown() {}

    public function init() {}

    public function close() {}

    /**
     * @return $this
     */
    private function _assertTake() {
        $this->_assertCount++;
        return $this;
    }

    public function getAssertsCount() {
        return $this->_assertCount;
    }
}
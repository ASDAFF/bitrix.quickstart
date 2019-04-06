<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\Migrations\Entities;

use Bitrix\Main\Type\DateTime;
use WS\Migrations\factories\DateTimeFactory;

class AppliedChangesLogModel extends BaseEntity {
    public
        $id, $groupLabel, $date, $success,
        $processName, $subjectName, $source, $updateData,
        $originalData, $description, $setupLogId;

    private $_setupLog;

    public function __construct() {
        $this->date = DateTimeFactory::createBase();
    }

    static protected function modifyFromDb($data) {
        $result = array();
        foreach ($data as $name => $value) {
            if ($name == 'date') {
                if ($value instanceof DateTime) {
                    $timestamp = $value->getTimestamp();
                    $value = DateTimeFactory::createBase();
                    $value->setTimestamp($timestamp);
                } else {
                    $value = DateTimeFactory::createBase($value);
                }
            }
            if (in_array($name, array('originalData', 'updateData'))) {
                $value = \WS\Migrations\jsonToArray($value);
            }
            $result[$name] = $value;
        }
        return $result;
    }

    static protected function modifyToDb($data) {
        $result = array();
        foreach ($data as $name => $value) {
            if ($name == 'date' && $value instanceof \DateTime) {
                $value = DateTimeFactory::createBitrix($value);
            }
            if (in_array($name, array('originalData', 'updateData'))) {
                $value = \WS\Migrations\arrayToJson($value);
            }
            $result[$name] = $value;
        }
        return $result;
    }

    static protected function map() {
        return array(
            'id' => 'ID',
            'setupLogId' => 'SETUP_LOG_ID',
            'groupLabel' => 'GROUP_LABEL',
            'date' => 'DATE',
            'processName' => 'PROCESS',
            'subjectName' => 'SUBJECT',
            'source' => 'SOURCE',
            'updateData' => 'UPDATE_DATA',
            'originalData' => 'ORIGINAL_DATA',
            'success' => 'SUCCESS',
            'description' => 'DESCRIPTION'
        );
    }

    /**
     * @return SetupLogModel
     */
    public function getSetupLog() {
        if (!$this->_setupLog) {
            $this->_setupLog = SetupLogModel::findOne(array(
                    'filter' => array('=id' => $this->setupLogId)
                )
            );
        }
        return $this->_setupLog;
    }

    public function setSetupLog(SetupLogModel $model = null) {
        $this->_setupLog = $model;
        $model->id && $this->setupLogId = $model->id;
        return $this;
    }

    static protected function gatewayClass() {
        return AppliedChangesLogTable::className();
    }
}
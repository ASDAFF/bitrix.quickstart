<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\Migrations\Processes;

use WS\Migrations\ChangeDataCollector\CollectorFix;
use WS\Migrations\Entities\AppliedChangesLogModel;
use WS\Migrations\Module;
use WS\Migrations\SubjectHandlers\BaseSubjectHandler;

class AddProcess extends BaseProcess {

    public function update(BaseSubjectHandler $subjectHandler, CollectorFix $fix, AppliedChangesLogModel $log) {
        $data = $fix->getUpdateData();
        $result = $subjectHandler->applySnapshot($data, $fix->getDbVersion());

        $data = $subjectHandler->getSnapshot($result->getId());
        $log->description = $fix->getName();
        $log->originalData = array();
        $log->updateData = $data;
        return $result;
    }

    public function rollback(BaseSubjectHandler $subjectHandler, AppliedChangesLogModel $log) {
        $id = $subjectHandler->getIdBySnapshot($log->updateData);
        return $subjectHandler->delete($id);
    }

    public function change(BaseSubjectHandler $subjectHandler, CollectorFix $fix, $data = array()) {
        $id = $subjectHandler->getIdByChangeMethod(Module::FIX_CHANGES_AFTER_ADD_KEY, $data);

        $snapshot = $subjectHandler->getSnapshot($id);
        if (!$snapshot) {
            return false;
        }
        $fix
            ->setOriginalData(array())
            ->setUpdateData($snapshot);
        return true;
    }

    public function getName() {
        return $this->getLocalization()->getDataByPath('add');
    }
}
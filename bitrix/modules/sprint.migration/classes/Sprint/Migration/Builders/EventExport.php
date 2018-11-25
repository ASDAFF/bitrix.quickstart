<?php

namespace Sprint\Migration\Builders;

use Sprint\Migration\Module;
use Sprint\Migration\VersionBuilder;
use Sprint\Migration\HelperManager;
use Sprint\Migration\Exceptions\HelperException;

class EventExport extends VersionBuilder
{

    protected function isBuilderEnabled() {
        return true;
    }

    protected function initialize() {
        $this->setTitle(GetMessage('SPRINT_MIGRATION_BUILDER_EventExport1'));
        $this->setDescription(GetMessage('SPRINT_MIGRATION_BUILDER_EventExport2'));

        $this->addField('prefix', array(
            'title' => GetMessage('SPRINT_MIGRATION_FORM_PREFIX'),
            'value' => $this->getVersionConfig()->getVal('version_prefix'),
            'width' => 250,
        ));

        $this->addField('description', array(
            'title' => GetMessage('SPRINT_MIGRATION_FORM_DESCR'),
            'width' => 350,
            'height' => 40,
        ));
    }


    protected function execute() {
        $helper = new HelperManager();

        $this->addField('event_types', array(
            'title' => GetMessage('SPRINT_MIGRATION_BUILDER_EventExport_event_types'),
            'width' => 350,
            'select' => $this->getEventTypesStructure(),
            'multiple' => 1
        ));

        $eventTypes = $this->getFieldValue('event_types');
        if (empty($eventTypes)) {
            $this->rebuildField('event_types');
        }

        $eventTypes = is_array($eventTypes) ? $eventTypes : array($eventTypes);

        $result = array();
        foreach ($eventTypes as $eventName) {

            $types = $helper->Event()->getEventTypes($eventName);
            foreach ($types as $indexType => $type){
                unset($type['ID']);
                unset($type['EVENT_NAME']);
                $types[$indexType] = $type;
            }

            $messages = $helper->Event()->getEventMessages($eventName);
            foreach ($messages as $indexMessage => $message){
                unset($message['ID']);
                unset($message['SITE_ID']);
                unset($message['TIMESTAMP_X']);
                unset($message['MESSAGE_PHP']);
                unset($message['EVENT_NAME']);
                unset($message['EVENT_MESSAGE_TYPE_ID']);
                unset($message['EVENT_MESSAGE_TYPE_NAME']);
                unset($message['EVENT_MESSAGE_TYPE_EVENT_NAME']);
                $messages[$indexMessage] = $message;
            }

            $result[$eventName] = array(
                'types' => $types,
                'messages' => $messages,
            );
        }

        $this->createVersionFile(
            Module::getModuleDir() . '/templates/EventExport.php', array(
            'result' => $result,
        ));
    }


    protected function getEventTypesStructure() {
        $helper = new HelperManager();
        $eventTypes = $helper->Event()->getEventTypes(array(
            'LID' => LANGUAGE_ID
        ));

        $structure = array();
        foreach ($eventTypes as $item) {
            $eventName = $item['EVENT_NAME'];
            $structure[$eventName] = array(
                'title' => '[' . $eventName . '] ' . $item['NAME'],
                'value' => $eventName
            );
        }

        return $structure;
    }

}
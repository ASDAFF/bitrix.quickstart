<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mnr
 * Date: 28.10.14
 * Time: 13:28
 * To change this template use File | Settings | File Templates.
 */

namespace Cpeople\Classes\Forms\Commands;

use Cpeople\Classes\Forms\Command;
use Cpeople\Classes\Forms\Form;

class ElementAddCommand extends Command
{
    private $iBlockId;
    private $elementName;
    private $fieldsUppercase;
    /**
     * @var Form
     */
    private $form;

    public function execute(Form $form)
    {
        $this->form = $form;

        $properties = $this->getPreparedProperties();
        $id = $this->addElement($properties);
        $form->setDataItem('ID', $id);
    }

    public function __construct($isCritical, $iBlockId, $elementName = NULL, $fieldsUppercase = TRUE)
    {
        parent::__construct($isCritical);

        $this->iBlockId = $iBlockId;
        $this->elementName = $elementName;
        $this->fieldsUppercase = $fieldsUppercase;
    }

    public function getPreparedProperties()
    {
        $data = $this->form->getData();
        $elementsProperties = array();

        if($this->fieldsUppercase)
        {
            $data = array_change_key_case($data, CASE_UPPER);
            $files = array_change_key_case($_FILES, CASE_UPPER);
        }

        foreach (cp_get_ib_properties($this->iBlockId) as $key => $property)
        {
            $elementsProperties[$property['ID']] = $data[$key];

            if ($property['PROPERTY_TYPE'] == 'S')
            {
                $elementsProperties[$property['ID']] = ($property['USER_TYPE'] == 'HTML')
                    ? array('VALUE' => array('TEXT' => $data[$key], 'TYPE' => 'TEXT'))
                    : $data[$key];
            }
            else if ($property['PROPERTY_TYPE'] == 'F')
            {
                if($property['MULTIPLE'] == 'Y')
                {
                    for($i = 0; $i < count($files[$key]['name']); $i++)
                    {
                        $elementsProperties[$property['ID']]['n' . $i] = array(
                            'name' => $files[$key]['name'][$i],
                            'size' => $files[$key]['size'][$i],
                            'type' => $files[$key]['type'][$i],
                            'tmp_name' => $files[$key]['tmp_name'][$i]
                        );
                    }
                }
                else
                {
                    $elementsProperties[$property['ID']] = $files[$key];
                }
            }
        }

        return $elementsProperties;
    }

    public function addElement($elementsProperties)
    {
        if(empty($this->elementName))
        {
            $this->elementName = date('Y.m.d H:i');
        }

        $el = new \CIBlockElement;

        $id = $el->Add(array(
            'IBLOCK_ID'         => $this->iBlockId,
            'PROPERTY_VALUES'   => $elementsProperties,
            'NAME'              => $this->elementName,
            "DATE_ACTIVE_FROM"  => ConvertTimeStamp(time(), "FULL")
        ));

        if (!$id)
        {
            if($this->isCritical)
            {
                throw new \Exception($el->LAST_ERROR);
            }
            else
            {
                $this->form->setErrors($this->getErrorMessage($el->LAST_ERROR));
            }
        }

        return $id;
    }
}
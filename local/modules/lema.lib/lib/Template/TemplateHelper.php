<?php

namespace Lema\Template;

/**
 * Class TemplateHelper
 * @package Lema\Template
 */
class TemplateHelper
{
    /**
     * @var \CBitrixComponentTemplate|null
     */
    protected $data = null;
    protected $component = null;
    protected $arResult = array();
    protected $arParams = array();
    protected $singleRecord = false;

    protected $arKey = 'ITEMS';

    protected $editLinks = array();

    /**
     * TemplateHelper constructor.
     * @param \CBitrixComponentTemplate $data
     */
    public function __construct(\CBitrixComponentTemplate $data)
    {
        $this->data = $data;
        $this->component = $data->getComponent();

        $this->arParams = $this->component->arParams;
        $this->arResult = $this->component->arResult;

        if(!isset($this->arResult['ITEMS']) && !isset($this->arResult['SECTIONS']))
            $this->arResult = new Item($this->arResult);
        else
        {
            $isSection = false;
            //elements
            if(isset($this->arResult['ITEMS']))
            {
                $name = 'ELEMENT';
                $msg = \GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM');
            } //sections
            elseif(isset($this->arResult['SECTIONS']))
            {
                $this->arKey = 'SECTIONS';
                $isSection = true;
                $name = 'SECTION';
                $msg = \GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM');
            } //default:elements
            else
            {
                $name = 'ELEMENT';
                $msg = \GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM');
            }

            $this->arResult['OBJ_' . $this->arKey] = array();
            $this->editLinks = array(
                'edit' => \CIBlock::GetArrayByID($this->arParams['IBLOCK_ID'], $name . '_EDIT'),
                'delete' => \CIBlock::GetArrayByID($this->arParams['IBLOCK_ID'], $name . '_DELETE'),
                'confirm' => array('CONFIRM' => $msg),
            );

            foreach($this->arResult[$this->arKey] as $k => $item)
            {
                //add edit actions
                $this->data->AddEditAction($item['ID'], $item['EDIT_LINK'], $this->editLinks['edit']);
                $this->data->AddDeleteAction($item['ID'], $item['DELETE_LINK'], $this->editLinks['delete'], $this->editLinks['confirm']);

                if($isSection)
                    $this->arResult['OBJ_' . $this->arKey][$k] = new Section($item, $this->data->GetEditAreaId($item['ID']));
                else
                    $this->arResult['OBJ_' . $this->arKey][$k] = new Item($item, $this->data->GetEditAreaId($item['ID']));
            }
        }
    }

    /**
     * @param bool $objectData
     * @return Item[]
     */
    public function items($objectData = true)
    {
        return $objectData ? $this->arResult['OBJ_' . $this->arKey] : $this->arResult[$this->arKey];
    }

    /**
     * @param bool $objectData
     * @return Section[]
     */
    public function sections($objectData = true)
    {
        return $this->items($objectData);
    }

    /**
     * @return Item
     */
    public function item()
    {
        return $this->arResult;
    }

    /**
     * @return int
     */
    public function itemCount()
    {
        return count($this->arResult[$this->arKey]);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->arResult[$name]) ? $this->arResult[$name] : null;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->get('ID');
    }

    /**
     * @return mixed|null
     */
    public function getName()
    {
        return $this->get('NAME');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 17:13
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin;


use \Rover\Fadmin\Layout\Form as FromAbstract;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use \Rover\Fadmin\Options;
use Rover\Fadmin\Tab;
use \Rover\Fadmin\Inputs\Input as InputEngine;

Loc::loadMessages(__FILE__);

/**
 * Class Form
 *
 * @package Rover\Fadmin\Layout\Admin
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Form extends FromAbstract
{
    /**
     * @var \CAdminTabControl
     */
    protected $tabControl;

    /**
     * @var mixed|string
     */
    protected $moduleId;

    /**
     * Form constructor.
     *
     * @param Options $options
     * @param array   $params
     */
    public function __construct(Options $options, array $params = array())
    {
        parent::__construct($options, $params);

        $this->tabControl   = new \CAdminTabControl("tabControl", $this->getAllTabsInfo());
        $this->moduleId     = htmlspecialcharsbx($this->options->getModuleId());

        if (empty($this->params['top_buttons']) || !is_array($this->params['top_buttons']))
            $this->params['top_buttons'] = array();
    }

    /**
     * @return Request
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            global $Update, $Apply, $RestoreDefaults, $REQUEST_METHOD;

            $params = array(
                'active_tab'        => $this->tabControl->ActiveTabParam(),
                'request_method'    => $REQUEST_METHOD,
                'update'            => $Update,
                'apply'             => $Apply,
                'restore_defaults'  => $RestoreDefaults
            );

            $this->request = new Request($this->options, $params);
        }

        return $this->request;
    }

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getAllTabsInfo()
    {
        $tabs           = $this->options->tabMap->getTabs();
        $allTabsInfo    = array();

        foreach ($tabs as $tab)
            /**
             * @var Tab $tab
             */
            $allTabsInfo[] = $this->getTabInfo($tab);

        // add group rights tab
        if ($this->options->settings->getGroupRights())
            $allTabsInfo[] = array(
                "DIV"   => "edit2",
                "TAB"   => GetMessage("MAIN_TAB_RIGHTS"),
                "ICON"  => "form_settings",
                "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
            );

        return $allTabsInfo;
    }

    /**
     *  [
     *      [
     *          'TEXT' => ...,
     *          'LINK' => ...,
     *          'TITLE' => ...
     *      ],
     *      [
     *          ...
     *      ],
     *      ...
     *  ]
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function showButtons()
    {
        if (!count($this->params['top_buttons']))
            return;

        $context = new \CAdminContextMenu($this->params['top_buttons']);
        $context->Show();
    }

    /**
     * @param Tab $tab
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function getTabInfo(Tab $tab)
    {
        $name = $tab->getName();
        $icon = "ib_settings";
        $label = strlen($tab->getSiteId())
            ? $tab->getLabel() . ' [' . $tab->getSiteId() . ']'
            : $tab->getLabel();
        $description = strlen($tab->getSiteId())
            ? $tab->getDescription() . ' [' . $tab->getSiteId() . ']'
            : $tab->getDescription();

        $params = array_merge(array('tab' => $tab),
            compact('name', 'icon', 'label', 'description'));

        $this->options->runEvent(Options::EVENT__BEFORE_GET_TAB_INFO, $params);

        return array(
            'DIV'   => $params['name'],
            'TAB'   => $params['label'],
            'ICON'  => $params['icon'],
            'TITLE' => $params['description']
        );
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function show()
    {
        $this->getRequest()->process();
        $this->showMessages();
        $this->showButtons();
        $this->showForm();
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    public function showForm()
    {
        // showing tabs
        $tabs = $this->options->tabMap->getTabs(true);

        if (!count($tabs))
            return;

        $this->tabControl->Begin();
        $this->showFormBegin();

        foreach ($tabs as $tab)
            $this->showTab($tab);

        if ($this->options->settings->getGroupRights())
            $this->showGroupRightsTab();

        $this->tabControl->EndTab();

        $this->showFormEnd();
    }

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function showFormBegin()
    {
        global $APPLICATION;

        ?><form method="post" id="fadmin-form" enctype="multipart/form-data" name='<?=$this->moduleId?>' action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($this->moduleId)?>&amp;lang=<?=LANGUAGE_ID?>"><?php
    }

    /**
     * @param Tab $tab
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function showTab(Tab $tab)
    {
        // action afterRemovePreset
        if(false === $this->options->runEvent(
                Options::EVENT__BEFORE_SHOW_TAB,
                compact('tab')))
            return;

        $this->tabControl->BeginNextTab();

        if ($this->options->settings->getUseSort())
            $tab->sort();

        $inputs = $tab->getInputs();

        foreach ($inputs as $input)
            /**
             * @var InputEngine $input
             */
            $this->showInput($input);
    }

    /**
     * @param InputEngine $input
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function showInput(InputEngine $input)
    {
        $input->loadValue();

        if ($input->getDisplay())
            Input::drawStatic($input);
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function showGroupRightsTab()
    {
        global $APPLICATION, $REQUEST_METHOD;

        $RIGHTS     = $_REQUEST['RIGHTS'];
        $SITES      = $_REQUEST['SITES'];
        $GROUPS     = $_REQUEST['GROUPS'];
        $Apply      = $_REQUEST['Apply'];
        $Update     = $_REQUEST['Update'] ?:$Apply;
        $module_id  = $_REQUEST['mid'];

        $this->tabControl->BeginNextTab();

        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
    }



    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function showFormEnd()
    {
        $request = Application::getInstance()
            ->getContext()
            ->getRequest();

        $backUrl = $request->get('back_url_settings');

        $this->tabControl->Buttons();
        ?><input
        type="submit"
        name="Update"
        value="<?=Loc::getMessage("MAIN_SAVE")?>"
        title="<?=Loc::getMessage("MAIN_OPT_SAVE_TITLE")?>"
        class="adm-btn-save">
        <input
            type="submit"
            name="Apply"
            value="<?=Loc::getMessage("MAIN_OPT_APPLY")?>"
            title="<?=Loc::getMessage("MAIN_OPT_APPLY_TITLE")?>">
        <?php if(strlen($backUrl) > 0):?>
        <input
            type="button"
            name="Cancel"
            value="<?=Loc::getMessage("MAIN_OPT_CANCEL")?>"
            title="<?=Loc::getMessage("MAIN_OPT_CANCEL_TITLE")?>"
            onclick="window.location='<?=htmlspecialcharsbx(\CUtil::addslashes($backUrl))?>'">
        <input
            type="hidden"
            name="back_url_settings"
            value="<?=htmlspecialcharsbx($backUrl)?>">
    <?php endif?>
        <input
            type="submit"
            name="RestoreDefaults"
            title="<?=Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS")?>"
            OnClick="return confirm('<?=AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')"
            value="<?=Loc::getMessage("MAIN_RESTORE_DEFAULTS")?>">
        <?=bitrix_sessid_post();?>
        <?php $this->tabControl->End();?>
        </form><?php
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    protected function showMessages()
    {
        foreach ($this->options->message->get() as $message)
            \CAdminMessage::ShowMessage($message);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.09.2017
 * Time: 15:57
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace Rover\Fadmin\Layout\Admin\Input;

use Rover\Fadmin\Layout\Admin\Input;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Schedule
 *
 * @package Rover\Fadmin\Layout\Admin\Input
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class Schedule extends Input
{
    /**
     * @var bool
     */
    protected static $assetsAdded = false;

    /**
     * @author Pavel Shulaev (http://rover-it.me)
     */
    protected function addAssets()
    {
        \CJSCore::Init(array("jquery"));

        $asset = \Bitrix\Main\Page\Asset::getInstance();

        //add css
        if (self::$assetsAdded)
            return;

        echo $asset->insertCss('/bitrix/css/rover.fadmin/vendor/jqwidgets/jqx.base.css');

        $jsPath = '/bitrix/js/rover.fadmin/vendor/jqwidgets';

        $asset->addJs($jsPath . '/jqxcore.js');
        $asset->addJs($jsPath . '/jqxbuttons.js');
        $asset->addJs($jsPath . '/jqxscrollbar.js');
        $asset->addJs($jsPath . '/jqxdata.js');
        $asset->addJs($jsPath . '/jqxdata.export.js');
        $asset->addJs($jsPath . '/jqxdate.js');
        $asset->addJs($jsPath . '/jqxscheduler.js');
        $asset->addJs($jsPath . '/jqxscheduler.api.js');
        $asset->addJs($jsPath . '/jqxdatetimeinput.js');
        $asset->addJs($jsPath . '/jqxmenu.js');
        $asset->addJs($jsPath . '/jqxcalendar.js');
        $asset->addJs($jsPath . '/jqxtooltip.js');
        $asset->addJs($jsPath . '/jqxwindow.js');
        $asset->addJs($jsPath . '/jqxcheckbox.js');
        $asset->addJs($jsPath . '/jqxlistbox.js');
        $asset->addJs($jsPath . '/jqxdropdownlist.js');
        $asset->addJs($jsPath . '/jqxnumberinput.js');
        $asset->addJs($jsPath . '/jqxradiobutton.js');
        $asset->addJs($jsPath . '/jqxinput.js');
        $asset->addJs($jsPath . '/globalization/globalize.js');
        $asset->addJs($jsPath . '/globalization/globalize.culture.ru-RU.js');

        self::$assetsAdded = true;
    }

    /**
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function showInput()
    {
        if (!$this->input instanceof \Rover\Fadmin\Inputs\Schedule)
            return;

        $this->addAssets();

        $valueId = $this->input->getValueId();

        ?><input type="hidden"
                 id="<?=$valueId?>"
                 value='<?=json_encode($this->input->getInputValue())?>'
                 name="<?=$this->input->getValueName()?>">
        <div id="scheduler-<?=$valueId?>"></div>
        <style>
            .jqx-scheduler-all-day-cell span{
                display: none;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function () {
                var appointments = [
                        <?php

                        $num = 1;

                        foreach ($this->input->getValue() as $period):	?>{
                        id: "<?=$valueId?>-<?=$num?>",
                        subject: "<?=$this->input->getPeriodLabel()?>",
                        calendar: "1",
                        start: new Date(<?=$period['start']->format('Y, ' . $period['jqwStartMonth'] .', d, H, i, s')?>),
                        end: new Date(<?=$period['end']->format('Y, ' . $period['jqwEndMonth'] .', d, H, i, s')?>)
                    },
                    <?php

                    $num++;

                    endforeach; ?>
                ];

                // prepare the data
                var source =
                    {
                        dataType: "array",
                        dataFields: [
                            { name: 'id', type: 'string' },
                            { name: 'subject', type: 'string' },
                            { name: 'calendar', type: 'string' },
                            { name: 'start', type: 'date' },
                            { name: 'end', type: 'date' }
                        ],
                        id: 'id',
                        localData: appointments
                    };
                var adapter     = new $.jqx.dataAdapter(source),
                    $scheduler  = $("#scheduler-<?=$valueId?>"),
                    $export     = $('#<?=$valueId?>');

                $scheduler.jqxScheduler({
                    //date: new $.jqx.date(),
                    date            : new $.jqx.date('todayDate'),
                    width           : <?=$this->input->getWidth()?>,
                    height          : <?=$this->input->getHeight()?>,
                    rowsHeight      : 15,
                    columnsHeight   : 30,
                    source          : adapter,
                    view            : 'weekView',
                    enableHover     : false,
                    exportSettings  : {
                        serverURL       : null,
                        characterSet    : null,
                        fileName        : null,
                        dateTimeFormatString: "S",
                        resourcesInMultipleICSFiles: true
                    },
                    showToolbar     : false,
                    resources:
                        {
                            dataField   : "calendar",
                            source      :  adapter
                        },
                    appointmentDataFields:
                        {
                            from        : "start",
                            to          : "end",
                            id          : "id",
                            subject     : "subject",
                            resourceId  : "calendar"
                        },
                    localization: {
                        firstDay: 1,
                        days: {
                            // full day names
                            names: [
                                "<?=Loc::getMessage('rover-fa__schedule-sunday')?>",
                                "<?=Loc::getMessage('rover-fa__schedule-monday')?>",
                                "<?=Loc::getMessage('rover-fa__schedule-tuesday')?>",
                                "<?=Loc::getMessage('rover-fa__schedule-wednesday')?>",
                                "<?=Loc::getMessage('rover-fa__schedule-thursday')?>",
                                "<?=Loc::getMessage('rover-fa__schedule-friday')?>",
                                "<?=Loc::getMessage('rover-fa__schedule-saturday')?>"],
                            // abbreviated day names
                            //namesAbbr: ["Sonn", "Mon", "Dien", "Mitt", "Donn", "Fre", "Sams"],
                            // shortest day names
                            //namesShort: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"]
                        },
                        editDialogFromString    : "<?=Loc::getMessage('rover-fa__schedule-start')?>",
                        editDialogToString      : "<?=Loc::getMessage('rover-fa__schedule-end')?>",
                        editDialogAllDayString  : "<?=Loc::getMessage('rover-fa__schedule-all-day')?>",
                        editDialogTitleString   : "<?=Loc::getMessage('rover-fa__schedule-edit-period')?>",
                        contextMenuEditAppointmentString: "<?=Loc::getMessage('rover-fa__schedule-edit-period')?>",
                        editDialogCreateTitleString: "<?=Loc::getMessage('rover-fa__schedule-create-period')?>",
                        contextMenuCreateAppointmentString: "<?=Loc::getMessage('rover-fa__schedule-create-period')?>",
                        editDialogSaveString    : "<?=Loc::getMessage('rover-fa__schedule-save')?>",
                        editDialogDeleteString  : "<?=Loc::getMessage('rover-fa__schedule-delete')?>",
                        editDialogCancelString  : "<?=Loc::getMessage('rover-fa__schedule-cancel')?>",
                    },
                    editDialogOpen: function (dialog, fields, editAppointment) {
                        fields.locationContainer.hide();
                        fields.repeatContainer.hide();
                        fields.subject.val("<?=$this->input->getPeriodLabel()?>");
                        fields.subjectContainer.hide();
                        fields.statusContainer.hide();
                        fields.timeZoneContainer.hide();
                        fields.colorContainer.hide();
                        fields.descriptionContainer.hide();
                        fields.resourceContainer.hide();
                    },
                    views:
                        [
                            {
                                type: 'weekView',
                                workTime:
                                    {
                                        fromDayOfWeek: 0,
                                        toDayOfWeek: 6,
                                        fromHour: -1,
                                        toHour: 24
                                    },
                                timeRuler:
                                    {
                                        formatString: "HH:mm",
                                        scale: 'hour'
                                    }
                            }
                        ]
                });

                $scheduler.on('appointmentChange appointmentDelete appointmentAdd', function (event) {
                    var timeout = event.type === "appointmentChange"
                        ? 100
                        : 200;

                    setTimeout(function(){
                        exportPeriods();
                    }, timeout);
                });

                function exportPeriods()
                {
                    var schedule = JSON.parse($scheduler.jqxScheduler('exportData', 'json')),
                        propNum, period, result = [];

                    for (propNum in schedule)
                    {
                        period = schedule[propNum];

                        delete period.id;
                        delete period.calendar;
                        delete period.subject;

                        result.push(period);
                    }
//console.log(result);
                    $export.val(JSON.stringify(result));
                }
            });
        </script>
        <?php
    }
}
BX.ready(function () {

    var bWorkFinished = false;
    var bSubmit;

    function getCleanTable(service) {
        var cleanTestTable = '<table id="'+ service +'_result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">' +
            '<tr class="heading">' +
            '<td>Текущее действие</td>' +
            '<td width="1%">&nbsp;</td>' +
            '</tr>' +
            '</table>';
        return cleanTestTable;
    }

    function set_start(val, service)
    {
        document.getElementById(service + '_work_start').disabled = val ? 'disabled' : '';
        document.getElementById(service + '_work_stop').disabled = val ? '' : 'disabled';
        document.getElementById(service + '_progress').style.display = val ? 'block' : 'none';

        if (val)
        {
            ShowWaitWindow();
            cleanTestTable = getCleanTable(service);
            document.getElementById(service + '_result').innerHTML = cleanTestTable;
            document.getElementById(service + '_status').innerHTML = 'Работаю...';
            document.getElementById(service + '_percent').innerHTML = '0%';
            document.getElementById(service + '_indicator').style.width = '0%';
            CHttpRequest.Action = work_onload;
            var iblock = document.getElementById(service + '_iblock').value;
            var limit = document.getElementById(service + '_limit').value;
            var sess_id = document.getElementById(service + '_sess_id').value;
            CHttpRequest.Send(
                self.location +
                '?work_start=Y&' + sess_id +
                '&iblock=' + iblock +
                '&limit=' + limit +
                '&service=' + service
            );
        }
        else {
            CloseWaitWindow();
        }
    }

    function work_onload(result)
    {
        try
        {
            if(!result) {
                return;
            }
            var CurrentStatus = JSON.parse(result);
            //var CurrentStatus = JSON.parse(result);
            iPercent = CurrentStatus[0];
            strNextRequest = CurrentStatus[1];
            strCurrentAction = CurrentStatus[2];
            var service = CurrentStatus[3];

            document.getElementById(service + '_percent').innerHTML = iPercent + '%';
            document.getElementById(service + '_indicator').style.width = iPercent + '%';
            document.getElementById(service + '_status').innerHTML = 'Работаю...';

            if (strCurrentAction != 'null')
            {
                oTable = document.getElementById(service + '_result_table');
                oRow = oTable.insertRow(-1);
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = strCurrentAction;
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = '';
            }

            var iblock = document.getElementById(service + '_iblock').value;
            var limit = document.getElementById(service + '_limit').value;
            var sess_id = document.getElementById(service + '_sess_id').value;

            if (strNextRequest && document.getElementById(service + '_work_start').disabled) {
                CHttpRequest.Send(
                    self.location +
                    '?work_start=Y&' + sess_id +
                    strNextRequest +
                    '&iblock=' + iblock +
                    '&limit=' + limit +
                    '&service=' + service
                );
            }

            else
            {
                set_start(0, service);
                bWorkFinished = true;
            }
        }
        catch(e)
        {
            console.log(e);
            CloseWaitWindow();
            alert('Сбой в получении данных');
        }
    }


    BX.bindDelegate(document.body, 'click', {className: 'js-work-start' },
        function () {
            var service = this.getAttribute("data-service");
            set_start(1, service);
        }
    );

    BX.bindDelegate(document.body, 'click', {className: 'js-work-stop' },
        function () {
            var service = this.getAttribute("data-service");
            set_start(0, service);
        }
    );

});
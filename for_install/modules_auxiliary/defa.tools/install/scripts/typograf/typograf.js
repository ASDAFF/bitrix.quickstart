BX.ready(function () {

    arButtons['typograf'] = [
        'BXButton',
        {
            id: 'typograf',
            iconkit: 'typograf.gif',
            name: 'typograf',
            title: DT_MESS.Typograf_Typofy,
            handler: function () {

                this.pMainObj.pMainObj.executeCommand('SelectAll');
                var sel = this.pMainObj;
                var sel_tags = sel['sLastContent'].trim();
                var sel_text = sel_tags.replace(/&/g, '#a#');

                if (sel_tags.length > 0 && sel_tags.indexOf('__bxtagname="php"') == -1 && sel_tags.indexOf('__bxtagname="component2"') == -1) {

                    var tid = jsAjax.InitThread();
                    jsAjax.AddAction(tid,
                        function (str) {
                            sel.insertHTML(str);
                        });
                    jsAjax.Post(tid, '/bitrix/tools/defatools/typograf/typograf.php', {'text': sel_text })

                }
                else {
                    alert(DT_MESS.Typograf_Error1);
                }

            }
        }
    ];

    if (arGlobalToolbar === undefined) {
        arToolbars['standart'][1].unshift(arButtons['typograf']);
    }
    else {
        arGlobalToolbar.unshift(arButtons['typograf']);
    }

});

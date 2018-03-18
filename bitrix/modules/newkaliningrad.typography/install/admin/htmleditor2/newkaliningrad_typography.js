BX.ready(function () {

    arButtons['newkaliningrad_typography'] = [
        'BXButton',
        {
            title: 'Типографировать текст',
            iconkit: 'newkaliningrad_typography.gif',
            id: 'newkaliningrad_typography',
            name: 'newkaliningrad_typography',
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
                    jsAjax.Post(tid, '/bitrix/tools/newkaliningrad_typography/newkaliningrad_typography.php?t='+Math.random(), {'text': sel_text })

                }
                else {
                    alert(DT_MESS.newkaliningrad_typography_Error1);
                }

            }
        }
    ];

    if (arGlobalToolbar === undefined) {
        arToolbars['standart'][1].unshift(arButtons['newkaliningrad_typography']);
    }
    else {
        arGlobalToolbar.unshift(arButtons['newkaliningrad_typography']);
    }

});

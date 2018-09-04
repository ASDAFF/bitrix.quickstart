<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 05.09.2018
 * Time: 2:42
 */

class DevDebug
{
    function log($data)
    {
        AddMessage2Log(var_export($data, true));
    }

    /**
     * Подсветки PHP в редакторе
     **/
    function InitPHPHighlight()
    {
        global $APPLICATION;
        $string = '<link rel="stylesheet" href="http://codemirror.net/lib/codemirror.css">
      <script src="http://codemirror.net/lib/codemirror.js"></script>
      <script src="http://codemirror.net/mode/xml/xml.js"></script>
      <link rel="stylesheet" href="http://codemirror.net/mode/javascript/javascript.css">
      <link rel="stylesheet" href="http://codemirror.net/mode/clike/clike.css">
      <script src="http://codemirror.net/mode/javascript/javascript.js"></script>
      <script src="http://codemirror.net/mode/php/php.js"></script>
      <script src="http://codemirror.net/mode/clike/clike.js"></script>
      <style>

      .CodeMirror {
        overflow: auto;
        background:white;
        height: 500px;
        width: 1000px;
        line-height: 1em;
        font-family: inherit;
      }
      .CodeMirror pre{

        font-size:15px;
        line-height: 1.2em;
      }
      </style>';
        $init = '<script type=\'text/javascript\'>
            BX.ready(function(){
            var nl=document.getElementsByTagName("textarea");

            var editor = CodeMirror.fromTextArea(nl[0], {
                     lineNumbers: true,
                     matchBrackets: true,
                     mode: "application/x-httpd-php",
                     indentUnit: 8,
                     indentWithTabs: true,
                     enterMode: "keep",
                     tabMode: "classic"
                    });});
            </script>';

        $APPLICATION->AddHeadString($string);
        $APPLICATION->AddHeadString($init);
    }
}
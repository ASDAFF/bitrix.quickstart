<?
class WBLParse {
    static $ModulePath = '/bitrix/modules/weblooter.parsearray';
    static $CountNum = 0;
    static function ParseArray($var){
        switch(gettype($var)){
            case 'array':
                self::$CountNum++;
                echo '<ul>';
                foreach($var as $key=>$val){
                    echo '<li>';
                    echo '<span class="wbl-gettype">(arr)</span><span class="wbl-key">[<span oncontextmenu="WBLContextArrayKey($(this))"><span>'.$key.'</span><div id="WBLContextArrayMenu_'.self::$CountNum.'"><a href="javascript:void(0)" onclick="WBLGetKey($(this));">Get key</a><br/><a href="javascript:void(0)" onclick="WBLGetKeysPathToKey($(this));">Get keys path to key</a></div></span>]</span>=>';
                    echo (($val==='')||(sizeof($val)===0))?'<span class="wbl-bool">(empty_val!)</span>':'';
                    self::ParseArray($val);
                    echo '</li>';
                }
                echo '</ul>';
                break;
            case 'string':
                self::$CountNum++;
                echo '<span class="wbl-gettype">(str)</span>&nbsp;<span class="wbl-val" oncontextmenu="WBLContextValue($(this))"><span>'.htmlspecialchars($var).'</span><div id="WBLContextValueMenu_'.self::$CountNum.'"><a href="javascript:void(0)" onclick="WBLGetValue($(this));">Get value</a><br/><a href="javascript:void(0)" onclick="WBLGetKeysPathToValue($(this));">Get keys path to value</a></div></span>';
                break;
            case 'integer':
                self::$CountNum++;
                echo '<span class="wbl-gettype">(int)</span>&nbsp;<span class="wbl-val" oncontextmenu="WBLContextValue($(this))"><span>'.$var.'</span><div id="WBLContextValueMenu_'.self::$CountNum.'"><a href="javascript:void(0)" onclick="WBLGetValue($(this));">Get value</a><br/><a href="javascript:void(0)" onclick="WBLGetKeysPathToValue($(this));">Get keys path to value</a></div></span>';
                break;
            case 'boolean':
                $wbl_bool='false';
                self::$CountNum++;
                if($var==1){$wbl_bool='true';}
                echo '<span class="wbl-gettype">(bool)</span>&nbsp;<span class="wbl-bool" oncontextmenu="WBLContextValue($(this))"><span>'.$wbl_bool.'</span><div id="WBLContextValueMenu_'.self::$CountNum.'"><a href="javascript:void(0)" onclick="WBLGetValue($(this));">Get value</a><br/><a href="javascript:void(0)" onclick="WBLGetKeysPathToValue($(this));">Get keys path to value</a></div></span>';
                break;
        }
    }
    static function Parse($array, $IncludeJQ='N', $divId=0){
        echo '<link rel="stylesheet" href="'.self::$ModulePath.'/js/jstree/themes/default/style.css" async></script>';
        if($IncludeJQ=='Y'){echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>';}
        echo '<script src="'.self::$ModulePath.'/js/jstree/jstree.js" async></script>';
        echo '<div class="WBLParse-wrapper">
            <div class="WBLParse-loading" style="text-align: center;">Loading...</div>
            <div class="WBLParse-layer">
            <input type="text" id="WBLParse_search_'.$divId.'" placeholder="Search in array" /><div id="WBLParse_'.$divId.'">';
        self::ParseArray($array);
        echo '</div></div></div>';
        echo '<script type="text/javascript">
            $(window).load(function(){
                $("#WBLParse_'.$divId.'").jstree({
                    "plugins" : [ "search" ]
                });
                var ParseSearchto = false;
                $("#WBLParse_search_'.$divId.'").keyup(function () {
                if(ParseSearchto) { clearTimeout(ParseSearchto); }
                ParseSearchto = setTimeout(function () {
                    var ParseSearchInArrays = $("#WBLParse_search_'.$divId.'").val();
                    $("#WBLParse_'.$divId.'").jstree(true).search(ParseSearchInArrays);
                }, 250);
                });
                $(".WBLParse-layer").css("display","block");
                $(".WBLParse-loading ").css("display","none");
            });

            function WBLCloseContexMenu(){
                $("[id^=WBLContextValueMenu_]").each(function(){
                    $(this).removeClass("open");
                });
            }
            function WBLCloseContexArrayMenu(){
                $("[id^=WBLContextArrayMenu_]").each(function(){
                    $(this).removeClass("open");
                });
            }
            function WBLContextArrayKey(Obg){
                var isNS = (navigator.appName == "Netscape") ? 1 : 0;

                if(navigator.appName == "Netscape") document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);

                function mischandler(){
                return false;
                }

                function mousehandler(e){
                var myevent = (isNS) ? e : event;
                var eventbutton = (isNS) ? myevent.which : myevent.button;
                if((eventbutton==2)||(eventbutton==3)) return false;
                }
                document.oncontextmenu = mischandler;
                document.onmousedown = mousehandler;
                document.onmouseup = mousehandler;
                WBLCloseContexMenu();
                WBLCloseContexArrayMenu();
                Obg.find("[id^=WBLContextArrayMenu_]").addClass("open");
            }
            function WBLContextValue(Obg){
                var isNS = (navigator.appName == "Netscape") ? 1 : 0;

                if(navigator.appName == "Netscape") document.captureEvents(Event.MOUSEDOWN||Event.MOUSEUP);

                function mischandler(){
                return false;
                }

                function mousehandler(e){
                var myevent = (isNS) ? e : event;
                var eventbutton = (isNS) ? myevent.which : myevent.button;
                if((eventbutton==2)||(eventbutton==3)) return false;
                }
                document.oncontextmenu = mischandler;
                document.onmousedown = mousehandler;
                document.onmouseup = mousehandler;
                WBLCloseContexMenu();
                WBLCloseContexArrayMenu();
                Obg.find("[id^=WBLContextValueMenu_]").addClass("open");
            }
            function WBLGetKey(obg){
                prompt("Your key","[\'"+obg.parent().parent().find(">span").text()+"\']");
            }
            function WBLGetValue(obg){
                prompt("Your value",obg.parent().parent().find(">span").text());
            }

            var WBLKeysPath = "";
            function GoToParentsKeys(Obg){
                if(Obg.parent().parent().parent().parent().parent().parent().attr("role")=="treeitem"){
                    WBLKeysPath="[\'"+Obg.parent().parent().parent().parent().parent().parent().find(">a>span.wbl-key>span>span").text()+"\']"+WBLKeysPath;
                    GoToParentsKeys(Obg.parent().parent().parent().parent().parent().parent().find(">a>span.wbl-key>span>span"));
                }
            }
            function WBLGetKeysPathToValue(obg){
                WBLKeysPath="";
                WBLKeysPath = "[\'"+obg.parent().parent().parent().find(">.wbl-key>span>span").text()+"\']";
                WBLKeysPath = GoToParentsKeys(obg.parent().parent().parent().find(">.wbl-key>span>span"))+WBLKeysPath;
                WBLKeysPath = WBLKeysPath.replace("undefined","");
                prompt("Your keys path to value",WBLKeysPath);
            }
            function WBLGetKeysPathToKey(obg){
                WBLKeysPath="";
//                WBLKeysPath = "[\'"+obg.parent().parent().find(">span").text()+"\']";
                WBLKeysPath = GoToParentsKeys(obg.parent().parent().find(">span"))+WBLKeysPath;
                WBLKeysPath = WBLKeysPath.replace("undefined","");
                prompt("Your keys path to value",WBLKeysPath);
            }
            $(document).click(function(){WBLCloseContexMenu();WBLCloseContexArrayMenu();});
            </script>';
    }
}
?>
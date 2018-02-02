/* 
 * Замена print_r
 * Вставить код в init.php
 * Использование: echo_r($arResult);
 * Показывается по нажатию "z"
 * Поля с тильдой скрыты по умолчанию
 */
function echo_r($data, $hideMe = true, $removeTilda = true)
{
    $rnd=rand();
    echo '<script language="JavaScript">function fireClick'.$rnd.'(node){if(document.createEvent){ var evt = document.createEvent("MouseEvents"); evt.initEvent("click", true, false); node.dispatchEvent(evt);} else if( document.createEventObject ) {node.fireEvent("onclick")} else if (typeof node.onclick == "function" ) {node.onclick();}}</script>';
    $out = print_r($data, true);
    if($removeTilda){
        $out = preg_replace('/\[~[^\]]+\]\s\=\>\sArray[^\)]+\)/Um','', $out);
        $out = preg_replace('/\R\s+\[~.+(\R)/','\\1', $out);
    }
    $out = preg_replace('/(\s*)(Array|Object)\n/iUe',
        "'\\1<a class=\"lnk l".$rnd."\" href=\"javascript:td".$rnd."(\''.(\$id = substr(md5(rand().'\\0'), 0, 7)).'\');\">\\2</a> <a class=\"lnk\" href=\"javascript:tdall".$rnd."(\''.\$id.'\');\" id=\"'.\$id.'_tdall\" style=\"display: none; margin-left:30px\">#</a><span id=\"'.\$id.'\" style=\"display: none;\">\n'",
        $out);
    $out = preg_replace('/(\s*\n\s*\))\s*$/m', '\\1</span>', $out);
    echo '<style>pre.t'.$rnd.'{font-size:11px;line-height:12px;background-color:white;color:black;}pre .l'.$rnd.'{text-decoration:none;color:blue}pre .l'.$rnd.':hover{background-color:lightblue}</style><script language="Javascript">
    function td'.$rnd.'(id){var t=document.getElementById(id);var ta=document.getElementById(id+"_tdall");var styl=(t.style.display=="inline")?"none":"inline"; t.style.display=styl;ta.style.display=styl}
    function tdall'.$rnd.'(id){var l=document.getElementById(id);var styl=0;for(i=0;i<l.childNodes.length;i++)if(l.childNodes[i].nodeName=="SPAN"){if(styl==0)styl=(l.childNodes[i].style.display=="inline")?"none":"inline";l.childNodes[i].style.display=styl;document.getElementById(l.childNodes[i].id+"_tdall").style.display=styl;}}
    </script>'."\n<pre class='dbg t$rnd'>$out</pre>";

    if($hideMe)
    {   // Показывается по нажатию Z
        echo '<style>.t'.$rnd.'{display:none; text-align:left}</style>';
        echo '<script language="JavaScript">document.onkeydown=function(e){if(e.keyCode==90)/*z*/{
        var dbglist=document.getElementsByClassName("dbg");
        for (i=0; i<dbglist.length; i++){if (dbglist[i].nodeName=="PRE")dbglist[i].style.display=(dbglist[i].style.display=="block")?"none":"block";}
        var links=document.getElementsByClassName("lnk");fireClick'.$rnd.'(links[0]);}}</script>';
    }
    else
    {
        echo '<script language="JavaScript">var links=document.getElementsByClassName("lnk");fireClick'.$rnd.'(links[0]);</script>';
    }
} 
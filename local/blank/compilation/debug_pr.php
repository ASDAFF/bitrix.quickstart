<?php 
function PR($o)
{
    $bt =  debug_backtrace();
    $bt = $bt[0];
    $dRoot = $_SERVER["DOCUMENT_ROOT"];
    $dRoot = str_replace("/","\\",$dRoot);
    $bt["file"] = str_replace($dRoot,"",$bt["file"]);
    $dRoot = str_replace("\\","/",$dRoot);
    $bt["file"] = str_replace($dRoot,"",$bt["file"]);
    ?>
    <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
    <div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?=$bt["file"]?> [<?=$bt["line"]?>]</div>
    <pre style='padding:10px;'><?print_r($o)?></pre>
    </div>
    <?
}

function dd($data)
{
    $bt =  debug_backtrace();
    $bt = $bt[0];
    $dRoot = $_SERVER["DOCUMENT_ROOT"];
    $dRoot = str_replace("/","\\",$dRoot);
    $bt["file"] = str_replace($dRoot,"",$bt["file"]);
    $dRoot = str_replace("\\","/",$dRoot);
    $bt["file"] = str_replace($dRoot,"",$bt["file"]);

    echo '<div style="font-size:9pt; color:#000; background:#fff; border:1px dashed #000;">';
    echo '<div style="padding:3px 5px; background:#99CCFF; font-weight:bold;">File:';
    echo $bt["file"];
    echo $bt["line"];
    echo '</div>';
    echo '<pre style=\'padding:10px;\'>';
    print_r($data);
    echo '</pre></div>';
    exit;
}


?>

<?php
$dir = "./";
$filelist = glob("*.html");
if ($filelist) {
    echo '<ol>';
    foreach ($filelist as $filename) {
        echo '<li><a href="' . $dir . $filename . '">' . $filename . '</a></li>';
    }
    echo '</ol>';
} else {
    echo $dir . ' -empty;<br>';
}
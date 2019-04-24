<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
require('conf_uslugi.php');
echo '<br/> $IP_PROP_MODIFY '. __LINE__.'* ' .  __FILE__ . ' <pre>';
print_r($IP_PROP_MODIFY);
echo '</pre>';

//$file_name_readable = 'upload/csv/export_file_L8oCWbRr4BJH5rw9.csv';

$file_name_readable = 'upload/csv/export_file_5rvXHAHFMVYQEsqq.csv';
$file_name_writable = substr($file_name_readable, 0, -4) . '_mod_'.rand(0, 1000).'.csv';
$file_name_writable2 = substr($file_name_readable, 0, -4) . '_mod_'.rand(0, 1000).'.csv';
$file_name_writable3 = 'upload/csv/export_file_5rvXHAHFMVYQEsqqqqq.csv';
echo '$file_name_writable ' . $file_name_writable . '<br>';


$readable = @fopen($file_name_readable, "rb");
$writable = @fopen($file_name_writable, "x");
//$writable2 = @fopen($file_name_writable2, "x");

//$file = new SplFileObject($file_name_writable3, 'w');



foreach($IP_PROP_MODIFY as $key => $PROP) {

    echo '<br/> $PROP '. __LINE__.'* ' .  __FILE__ . ' <pre>';
    print_r($PROP);
    echo '</pre>';
    if(isset($PROP["VALUES"])) {
        $replace = [];
        foreach ($PROP["VALUES"] as $elem) {
            $replace[$elem["CURRENT"]] = $elem["NEW"];
        }
        $IP_PROP_MODIFY[$key]["REPLACE"] = $replace;
    }
}

if ($readable) {
    while (($buffer_arr = fgetcsv($readable, 65535, ";")) !== false) {
       /* echo '<br/> $buffer '. __LINE__.'* ' .  __FILE__ . ' <pre>';
        print_r($buffer);
        echo '</pre>';

        echo $buffer . '<br>';
        $buffer_arr = explode(";", $buffer);*/


        echo '<br/> $buffer_arr before' . __LINE__ . '* ' . __FILE__ . ' <pre>';
        print_r($buffer_arr);
        echo '</pre>';

        foreach($IP_PROP_MODIFY as $key => $PROP) {

            $CELL_NUM_REAL = $PROP["CELL_NUM"] - 1;
            echo 'in foreach ' ;
            echo $buffer_arr[$CELL_NUM_REAL] . '   ' . strpos($buffer_arr[$CELL_NUM_REAL], "IP_PROP");
            if (strpos($buffer_arr[$CELL_NUM_REAL], "P_PROP") == 1) {
                echo '$buffer_arr[$CELL_NUM_REAL] ' . $buffer_arr[$CELL_NUM_REAL] . '<br>';

                $buffer_arr[$CELL_NUM_REAL] = "IP_PROP" . $PROP["PROP_ID_NEW"];
            } else {
                echo '<br>into else<br>';
                if(isset($PROP["REPLACE"])) {
                    echo 'else<br>';
                    if (isset($buffer_arr[$CELL_NUM_REAL])) {
                        $buffer_arr[$CELL_NUM_REAL] = $replace[$buffer_arr[$CELL_NUM_REAL]];
                    }
                }
            }


            //$file->fputcsv($buffer_arr);
            //$buffer_arr = array_map("utf8_decode", $buffer_arr);
            //var_dump($buffer_arr);

            /*$buffer_str = implode(";", $buffer_arr);
            echo '$buffer_str ' . $buffer_str . '<br>';
            fwrite($writable, $buffer_str);*/

        }
        echo '<br/> $buffer_arr after' . __LINE__ . '* ' . __FILE__ . ' <pre>';
        print_r($buffer_arr);
        echo '</pre><br><br>';
        echo 'fputcsv result ' . fputcsv($writable, $buffer_arr, ";") . '<br>';
        //my_fputcsv($writable2, $buffer_arr);
    }
    if (!feof($readable)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($readable);
}

echo '99009900';
fclose($writable);

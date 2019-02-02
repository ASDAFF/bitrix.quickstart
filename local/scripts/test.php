<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
ini_set('display_errors', '1');
echo "<hr>1--<hr>";


echo "<hr>";
echo '
    $rj1 = new IBE();
    $r1 = $rj1->select(\'ID\',\'NAME\')->prop(\'MORE_PHOTO\')->prop(\'FILES\')->filter(\'iblock_id\', 10)->asc(\'id\')->page(2)->count(10)->get();
    new dbug($r1);
';
$rj1 = new IBE();
$r1 = $rj1->select('ID','NAME')->prop('MORE_PHOTO')->prop('FILES')->filter('iblock_id', 10)->asc('id')->page(2)->count(10)->get();
new dbug($r1);



echo "<hr>";
echo '$rj2 = new IBE();
$r2 = $rj2
    ->select(\'ID\',\'NAME\')
    ->prop(\'MORE_PHOTO\')
    ->prop(\'FILES\')
    ->filter(\'iblock_id\', 10)
    ->filter(\'id\',465)
    ->get();
new dbug($r2);';
$rj2 = new IBE();
$r2 = $rj2
    ->select('ID','NAME')
    ->prop('MORE_PHOTO')
    ->prop('FILES')
    ->filter('iblock_id', 10)
    ->filter('id',465)
    ->get();
new dbug($r2);


echo "<hr>";
echo '$rj3 = new IBE();
$r3 = $rj3
    ->select(\'ID\',\'NAME\')
    ->filter(\'iblock_id\', 10)
    ->filter(\'name\',\'%Griffin%\')
    ->get();
new dbug($r3);';
$rj3 = new IBE();
$r3 = $rj3
    ->select('ID','NAME')
    ->filter('iblock_id', 10)
    ->filter('name','%Griffin%')
    ->get();
new dbug($r3);


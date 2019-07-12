<?
$value = (!empty($value)) ? $value : $alt_value;
if (!empty($value)) {
    ?>
    <div class="wrapper-l">
        <span class="brand-l"><?=$title?>:</span>
        <span><?=$value?></span>
    </div>
<?
}

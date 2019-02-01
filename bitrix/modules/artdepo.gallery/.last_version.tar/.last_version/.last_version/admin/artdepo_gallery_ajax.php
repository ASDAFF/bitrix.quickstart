<?
##############################################
# ArtDepo.Gallery module                     #
# Copyright (c) 2013 AdrDepo                 #
# http://artdepo.com.ua                      #
# mailto:depo@artdepo.cm.ua                  #
##############################################

//**************************** GALLERY ACTIONS *************************************
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once(dirname(__FILE__)."/../include.php");
require_once(dirname(__FILE__)."/../prolog.php");
    
//echo '<!--BX_ML_LOAD_OK-->';
if (!check_bitrix_sessid())
    die('<!--BX_ML_DUBLICATE_ACTION_REQUEST'.bitrix_sessid().'-->');

$action = isset($_GET['action']) ? $_GET['action'] : false;

if((int)$_REQUEST['id'] > 0)
    $id = (int)$_REQUEST['id'];

$names = array();
$name_default = "";
foreach ($_POST as $key => $value) {
    $n = urldecode($APPLICATION->UnJSEscape(trim($value)));
    if (strpos($key, "name_") === 0 && $n) {
        $names[] = $key . "=" . $n;
        if (empty($name_default))
            $name_default = $n;
    }
}
if ((int)$_POST['sort'] > 0)
    $names[] = "sort=" . (int)$_POST['sort'];
$desc = implode("&&", $names);

$parent = (int)$_POST['parent'];

if ($action == 'edit_collection' && $name_default && $desc)
{
    $id = CMedialib::EditCollection(array(
        'id' => (int)$_POST['id'],
        'name' => $name_default,
        'desc' => $desc,
        'keywords' => '',
        'parent' => $parent,
        'site' => LANGUAGE_ID,
        'type' => 1
    ));

    ?><script>window.bx_req_res = {
        id: <?echo $id === false ? 'false' : $id;?>,
        access: {
            new_col: '<?= CMedialib::CanDoOperation('medialib_new_collection', $parent)?>',
            edit: '<?= CMedialib::CanDoOperation('medialib_edit_collection', $parent)?>',
            del: '<?= CMedialib::CanDoOperation('medialib_del_collection', $parent)?>',
            new_item: '<?= CMedialib::CanDoOperation('medialib_new_item', $parent)?>',
            edit_item: '<?= CMedialib::CanDoOperation('medialib_edit_item', $parent)?>',
            del_item: '<?= CMedialib::CanDoOperation('medialib_del_item', $parent)?>',
            access: '<?= CMedialib::CanDoOperation('medialib_access', $parent)?>'
        }
    };
    window.location.assign(window.location.href);
    </script><?
}
elseif ($action == 'del_collection')
{
    $res = CArtDepoGallerySection::Delete((int)$_POST['id'])
    ?><script>window.bx_req_res = <?= ($res ? 'true' : 'false')?>;
    window.location.assign(window.location.href);</script><?
}
elseif ($action == 'get_collection' && $id)
{
    $arItem = CArtDepoGallerySection::GetByID($id);
    foreach($arItem as $k => $itm) if(is_string($itm))
        $arItemJ[$k] = htmlspecialcharsex($itm);
    echo array_to_json($arItemJ);
}
elseif ($action == 'get_item' && $id)
{
    $arItem = CArtDepoGalleryImage::GetByID($id);
    foreach($arItem as $k => $itm) if(is_string($itm))
        $arItemJ[$k] = htmlspecialcharsex($itm);
    echo array_to_json($arItemJ);
}
elseif ($action == 'edit_item')
{
    // if Add item
    $arParams = array(
	    'lang' => isset($_REQUEST['lang']) ? $_REQUEST['lang'] : 'en',
	    'site' => isset($_REQUEST['site']) ? $_REQUEST['site'] : false,
	    'id' => $id,
	    'file' => isset($_FILES["load_file"]) ? $_FILES["load_file"] : false,
	    'path' => isset($_POST["item_path"]) ? $_POST["item_path"] : '',
	    'path_site' => isset($_POST["item_path_site"]) ? $_POST["item_path_site"] : '',
	    'source_type' => isset($_POST["source_type"]) ? $_POST["source_type"] : '',
	    'name' => isset($_POST["item_name"]) ? $APPLICATION->UnJSEscape($_POST["item_name"]) : '',
	    'desc' => isset($_POST["item_desc"]) ? $APPLICATION->UnJSEscape($_POST["item_desc"]) : '',
	    'keywords' => isset($_POST["item_keywords"]) ? $APPLICATION->UnJSEscape($_POST["item_keywords"]) : '',
	    'item_collections' => $_POST["item_collections"]
    );
    // if Rename item
    if ($id) {
        $arParams = array_merge($arParams, array(
            'name' => $name_default,
            'desc' => $desc,
            'item_collections' => $parent
        ));
    }
	$res = CArtDepoGalleryImage::Edit($arParams);
	if ($id) {
	    if ($res)
	        echo "<script>window.location.assign(window.location.href);</script>";
	    else
	        echo "<script>alert('Ошибка, попробуйте ещё раз');>window.location.assign(window.location.href);</script>";
    } else {
        echo '{"success": ' . ((!$res) ? 'false' : 'true') . '}';
    }
}
else
{
    echo "What a fuk?";
}




/**
 * Converts an associative array of arbitrary depth and dimension into JSON representation.
 * source: http://www.php.net/manual/ru/function.json-encode.php#89908
 *
 * NOTE: If you pass in a mixed associative and vector array, it will prefix each numerical
 * key with "key_". For example array("foo", "bar" => "baz") will be translated into
 * {'key_0': 'foo', 'bar': 'baz'} but array("foo", "bar") would be translated into [ 'foo', 'bar' ].
 *
 * @param $array The array to convert.
 * @return mixed The resulting JSON string, or false if the argument was not an array.
 * @author Andy Rusterholz
 */
function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = "'".addslashes($key)."'";

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>

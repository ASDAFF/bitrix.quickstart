<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->isAdmin()) die();
CModule::IncludeModule('remains');
CModule::IncludeModule('iblock');
$aviability = new availability();
$matching = new matching();
 
$z = trim($_REQUEST['text']);
$z = str_replace(' ', '%', $z);
$z = '%' . $z . '%';

$res = $matching->GetList(
        array($_REQUEST['by'] => $_REQUEST['sort']),
        array('ITEM_ID' => 0, '?NAME' => $z)); 

while ($el = $res->Fetch()) {  
  if($x++>250)
      break; 
  
    ?> <tr id="v2_<?= $el['ID']; ?>" data-id="<?= $el['ID']; ?>"><td>  
            <input type="checkbox" name="match" value="<?= $el['ID']; ?>">
        </td>
        <td class="str"> <?= $el['NAME']; ?></td>
        <td class="str"><? 
    $res1 = CIBlockElement::GetByID($el["SUPPLIER_ID"]);
    if ($ar_res = $res1->GetNext())
        echo $ar_res['NAME'];
    ?></td>
        <td>
            <?
            $d = $aviability->GetList(array(), array('MATCHING_ID'=>$el['ID']));
                    if($av = $d->GetNext())
                        echo $av['DATE'];
            ?>
            
            </td>
        <td><button class="deleteBtn" data-id="<?= $el['ID']; ?>">Удалить</button>
        </td>
    </tr>  
<? } 
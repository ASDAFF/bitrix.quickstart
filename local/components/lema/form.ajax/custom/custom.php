<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$types = array(
    'text', 'password', 'email', 'tel', 'checkbox', 'radio', 'number', 'button', 'file', 'hidden', 'image', 'reset', 'submit',
    'color', 'date', 'datetime', 'datetime-local', 'range', 'search', 'time', 'url', 'month', 'week',
);
$data = isset($_GET['data']) ? json_decode($_GET['data'], true) : array();
?>
<div class="row">
    <style>
        .form-control {
            margin-bottom: 0px;
            margin-top: 7px;
            padding: 3px 5px;
            height: auto;
        }
        .form-group {
            margin-bottom: 3px;
        }
        input[type="checkbox"] {
            width: auto;
        }
        hr {
            width: 100%;
            margin: 10px auto;
            border: 1px inset #eee;
        }
        .copy-block:last-child hr {
            display: none;
        }
        .form-ajax-custom_fields {
            font-size: 11px;
        }
        .popup-window-titlebar {
            margin: 16px;
            height: auto;
            color: #337ab7;
            text-align: center;
            font-weight: bold;
        }
        .form-ajax-delete-field {
            float: right;
        }
    </style>
    <form method="get" class="form-ajax-custom_fields form-horizontal">
        <div class="copy-block" style="display:none">
            <div class="form-group">
                <a href="#" class="form-ajax-delete-field"><?=Loc::getMessage('FORM_AJAX_DELETE_FIELD');?></a>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_NAME');?>: </label>
                <div class="col-sm-8">
                    <input type="text" name="name[]" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_TITLE');?>: </label>
                <div class="col-sm-8">
                    <input type="text" name="title[]" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_TYPE');?>: </label>
                <div class="col-sm-8">
                    <select name="type[]" class="form-control">
                        <?$first=true; foreach($types as $type):?>
                            <option value="<?=$type;?>"<?if($first){$first=false;?> selected<?}?>><?=$type;?></option>
                        <?endforeach;?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_PLACEHOLDER');?>: </label>
                <div class="col-sm-8">
                    <input type="text" name="placeholder[]" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_DEFAULT');?>: </label>
                <div class="col-sm-8">
                    <input type="text" name="default[]" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_REQUIRED');?></label>
                <div class="col-sm-8">
                    <input type="checkbox" name="required[]" class="form-control" value="1">
                </div>
            </div>
            <hr>
        </div>
        <?foreach($data as $info):?>
            <div class="copy-block">
                <div class="form-group">
                    <a href="#" class="form-ajax-delete-field"><?=Loc::getMessage('FORM_AJAX_DELETE_FIELD');?></a>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_NAME');?>: </label>
                    <div class="col-sm-8">
                        <input type="text" name="name[]" class="form-control" value="<?=$info['name']?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_TITLE');?>: </label>
                    <div class="col-sm-8">
                        <input type="text" name="title[]" class="form-control" value="<?=$info['title']?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_TYPE');?>: </label>
                    <div class="col-sm-8">
                        <select name="type[]" class="form-control">
                            <?$first=true; foreach($types as $type):?>
                                <option value="<?=$type;?>"<?if($info['type'] == $type){?> selected <?}?>><?=$type;?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_PLACEHOLDER');?>: </label>
                    <div class="col-sm-8">
                        <input type="text" name="placeholder[]" class="form-control" value="<?=$info['placeholder']?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_DEFAULT');?>: </label>
                    <div class="col-sm-8">
                        <input type="text" name="default[]" class="form-control" value="<?=$info['default']?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?=Loc::getMessage('FORM_FIELD_REQUIRED');?></label>
                    <div class="col-sm-8">
                        <input type="checkbox" name="required[]" class="form-control"<?if($info['required'] == 'Y'){?> checked <?}?>>
                    </div>
                </div>
                <hr>
            </div>
        <?endforeach;?>
    </form>
</div>
<link href="bootstrap.min.css" rel="stylesheet">
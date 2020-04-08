<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<form name="iblock_add" class="form " action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="submit-success" value="<?=$arResult["OK"]?>"/>
    <?if($_GET["formresult"] == "Y"){
        ShowNote($arParams["SUCCESS_MESSAGE"]);
        return;}?>
    <?if($arResult["ERRORS"]){ShowError($arParams["ERROR_MESSAGE"]);}?>
    <div class="row">
        <?foreach ($arResult["PROPERTY_LIST"] as $propertyCode => $property){
            switch ($property["TYPE"]){
                case "T":?>
                    <div class="col-xs-12 form-group <?if($property["ERRROR"]=="Y"){echo "has-error";}?>">
                        <label class="control-label <?if($property["REQUARED"]){echo "required";}?>" for="<?=$propertyCode?>">
                            <?=$property["NAME"]?>  
                        </label>
                        <textarea class="form-control" <?=($property["REQUARED"]==1)?"required":""?> cols="" rows="10" name="<?=$propertyCode?>"><?=$property["VALUE"]?></textarea>
                    </div>
                    <?
                    break;
                case "EMAIL":?>
                    <div class="col-xs-4 form-group <?if($property["ERRROR"]=="Y"){echo "has-error";}?>">
                        <div class="row">
                            <div class="col-xs-12 ">
                                <label class="control-label <?if($property["REQUARED"]){echo "required";}?>" for="<?=$propertyCode?>">
                                <?=$property["NAME"]?></label>
                            </div>
                            <div class="col-xs-12 ">
                                <input class="form-control" type="email" name="<?=$propertyCode?>" <?=($property["REQUARED"]==1)?"required":""?> value="<?=$property["VALUE"]?>"/>
                            </div>
                        </div>
                    </div>
                    <?
                    break;
                case "PHONE":?>
                    <div class="col-xs-4 form-group <?if($property["ERRROR"]=="Y"){echo "has-error";}?>">
                        <div class="row">
                            <div class="col-xs-12 ">
                                <label class="control-label <?if($property["REQUARED"]){echo "required";}?>" for="<?=$propertyCode?>">
                                <?=$property["NAME"]?></label>
                            </div>
                            <div class="col-xs-12 ">
                                <input class="form-control" type="phone" name="<?=$propertyCode?>" <?=($property["REQUARED"]==1)?"required":""?> value="<?=$property["VALUE"]?>"/>
                            </div>
                        </div>
                    </div>
                    <?
                    break;
                case "L":?>
                    <div class="col-xs-4 form-group <?if($property["ERRROR"]=="Y"){echo "has-error";}?>">
                        <div class="row">
                            <div class="col-xs-12 ">
                                <label class="control-label <?if($property["REQUARED"]){echo "required";}?>" for="<?=$propertyCode?>">
                                <?=$property["NAME"]?></label>
                            </div>
                            <div class="col-xs-12 ">
                                <select class="form-control" name="<?=$propertyCode?>" <?=($property["REQUARED"]==1)?"required":""?>>
                                    <?foreach ($property["VALUES"] as $valueType){?>
                                        <option value="<?=$valueType["ID"]?>" <?=($valueType["ID"]==$property["VALUE"])?"selected":""?>><?=$valueType["VALUE"]?></option>
                                        <?}?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?
                    break;
                case "HIDDEN":
                    ?>
                    <div class="col-xs-4 ">
                        <input type="hidden" name="<?=$propertyCode?>"  value="<?=$property["VALUE"]?>"/>
                    </div>
                    <?break;
                default:
                    ?>
                    <div class="col-xs-4  form-group <?if($property["ERRROR"]=="Y"){echo "has-error";}?>">
                        <div class="row">
                            <div class="col-xs-12 ">
                                <label class="control-label <?if($property["REQUARED"]){echo "required";}?>" for="<?=$propertyCode?>"><?=$property["NAME"]?>
                                </label>
                            </div>
                            <div class="col-xs-12 ">
                                <input  class="form-control" type="text" name="<?=$propertyCode?>" <?=($property["REQUARED"]==1)?"required":""?> value="<?=$property["VALUE"]?>"/>
                            </div>
                        </div>
                    </div>
                    <?break;
            }
        }?>
    </div>

    <button class="btn btn-static margin-bottom-10" type="submit"><?=GetMessage("SUBMIT")?></button>
    <input name="submit-form" type="hidden" value="Y"/>
    <p class="help-block">
    <span class="required"></span><?=GetMessage("REQUIRED")?> </p>
</form>
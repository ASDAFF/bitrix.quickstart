<?
__IncludeLang(__DIR__."/lang/ru/form.php");
?>
<div class="row">

    <div class="control-group"><!-- error-->
        <label for="phone" class="control-label"><?=GetMessage('PHONE_LABEL')?><span class="req">*</span>

        </label>
        <div class="controls">
            <input type="text" id="phone" value="<?=$arParams['REQUEST']['phone'] ?>" placeholder="" name="phone" id="email9" class="input-block-level required">

        </div>
    </div>
    <div class="control-group">
        <label class="control-label"><?=GetMessage('INFO_LABEL')?>
        </label>
        <div class="controls">
            <textarea name="info" rows="3" class="input-block-level row-dop"></textarea>
        </div>
    </div>
</div>
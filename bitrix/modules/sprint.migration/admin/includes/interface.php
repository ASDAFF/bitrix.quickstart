<?php
/** @var $versionManager \Sprint\Migration\VersionManager */
?>
<input type="hidden" value="<?= bitrix_sessid() ?>" name="send_sessid"/>
<div id="migration-container">

    <div class="sp-group">
        <div class="sp-group-row2">
            <div class="sp-block sp-white">
                <div id="migration_migrations" class="sp-scroll"></div>
            </div>
            <div class="sp-block">
                <div id="migration_progress" class="sp-scroll"></div>
            </div>
        </div>
    </div>

    <div class="sp-group">
        <div class="sp-group-row1">
            <div class="sp-block">
                <input type="button" value="<?= GetMessage('SPRINT_MIGRATION_UP_START') ?>"
                       onclick="migrationMigrationsUpConfirm();" class="adm-btn-green"/>
                <input type="button" value="<?= GetMessage('SPRINT_MIGRATION_DOWN_START') ?>"
                       onclick="migrationMigrationsDownConfirm();"/>
                <div class="sp-filter">
                    <? $search = \Sprint\Migration\Module::getDbOption('admin_versions_search', ''); ?>
                    <input placeholder="<?= GetMessage('SPRINT_MIGRATION_SEARCH') ?>" style="" type="text"
                           value="<?= $search ?>" class="adm-input" name="migration_search"/>
                    <? $view = \Sprint\Migration\Module::getDbOption('admin_versions_view', 'list'); ?>
                    <select class="sp-stat">
                        <option <? if ($view == 'list'): ?>selected="selected"<? endif ?>
                                value="list"><?= GetMessage('SPRINT_MIGRATION_TOGGLE_LIST') ?></option>
                        <option <? if ($view == 'new'): ?>selected="selected"<? endif ?>
                                value="new"><?= GetMessage('SPRINT_MIGRATION_TOGGLE_NEW') ?></option>
                        <option <? if ($view == 'installed'): ?>selected="selected"<? endif ?>
                                value="installed"><?= GetMessage('SPRINT_MIGRATION_TOGGLE_INSTALLED') ?></option>
                        <option <? if ($view == 'status'): ?>selected="selected"<? endif ?>
                                value="status"><?= GetMessage('SPRINT_MIGRATION_TOGGLE_STATUS') ?></option>
                    </select>
                    <input type="button" value="<?= GetMessage('SPRINT_MIGRATION_SEARCH') ?>" class="sp-search"/>
                </div>
            </div>
        </div>
    </div>

    <div class="sp-separator"></div>

    <? $builderGroup = 'default';
    include __DIR__ . '/builder_group.php' ?>

    <div class="sp-separator"></div>

    <? $builderGroup = 'configurator';
    include __DIR__ . '/builder_group.php' ?>

    <div class="sp-separator"></div>

    <div class="sp-group">
        <div class="sp-block">
            <div class="sp-noblock_title">
                <?= GetMessage('SPRINT_MIGRATION_CONFIG') ?>: <?= $versionManager->getVersionConfig()->getCurrent('title') ?>
            </div>
            <div class="sp-noblock_body">
                <? include __DIR__ . '/config_list.php' ?>
            </div>
        </div>
    </div>

</div>
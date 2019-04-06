<?php

/**
 * @var $version
 * @var $description
 * @var $extendUse
 * @var $extendClass
 */

?><?php echo "<?php\n" ?>

namespace Sprint\Migration;

<?php echo $extendUse ?>

class <?php echo $version ?> extends <?php echo $extendClass ?>

{

    protected $description = "<?php echo $description ?>";

    public function up() {
        $helper = new HelperManager();

        <?foreach ($items as $item):?>
            $helper->Agent()->saveAgent(<?php echo var_export($item, 1) ?>);
        <? endforeach; ?>
    }

    public function down() {
        $helper = new HelperManager();

        //your code ...

    }

}

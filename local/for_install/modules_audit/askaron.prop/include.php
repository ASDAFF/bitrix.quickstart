<?
CModule::AddAutoloadClasses(
    "askaron.prop",
    array(
        '\Askaron\Prop\Store' => 'lib/store.php',
	    '\Askaron\Prop\Price' => 'lib/price.php',
	    '\Askaron\Prop\Group' => 'lib/group.php',
	    '\Askaron\Prop\Iblock' => 'lib/iblock.php',
	    '\Askaron\Prop\IblockProperty' => 'lib/iblockproperty.php',
    )
);

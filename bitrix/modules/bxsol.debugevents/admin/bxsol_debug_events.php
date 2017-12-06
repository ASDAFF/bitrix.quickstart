<?php
/**
 * Mineev Aleksey (2016 ©)
 * alekseym@bxsolutions.ru
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once __DIR__ . '/../include.php';
CJSCore::Init(array("jquery"));

$APPLICATION->AddHeadScript('/bitrix/js/bxsol.debugevents/debug_events.js');

use BxSol\CDebugEvents;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('BXSOL_DEBUG_EVENTS_TITLE'));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$manager = New CDebugEvents;
$events  = $manager->getEvents();

if (!is_array($events))
{
	$events = array();
}

$allEvents = array();

?>
    <div class="bxsol__support">
        <a target="_blank" class="bxsol__support__rate_us"
           href="http://marketplace.1c-bitrix.ru/bxsol.debugevents"><?= Loc::getMessage('BXSOL__SUPPORT_RATE_US') ?></a>
        <a target="_blank" class="bxsol__support__write_us"
           href="http://bitrixsolutions.ru/"><?= Loc::getMessage('BXSOL__SUPPORT_WRITE_US') ?></a>
    </div>

    <input type="text" id="bxsol_events__search" class="bxsol_events__search"
           placeholder="<?= Loc::getMessage('BXSOL_DEBUG_EVENTS_FIND') ?>">
<?php

foreach ($events as $module_id => $events)
{
	$module_info = $manager->getModuleInfo($module_id);
	$label       = isset($module_info['MODULE_NAME']) ? $module_info['MODULE_NAME'] . ' (' . $module_id . ')' : $module_id;

	?>
    <div class="bxsol_events">

        <h1 class="bxsol_events__module_header"><?= $label ?></h1>
		<?php
		ksort($events);

		foreach ($events as $event_name => $handlers)
		{
			$event_name = strtolower($event_name);
			$allEvents[] = $event_name;

			echo '<div class="ev__' . $event_name . ' bxsol_events__event"><div class="bxsol_events__event_name">' . CDebugEvents::formatEvent(strtoupper($event_name)) . ' </div>' . '<div class="bxsol_events__handlers">';

			foreach ($handlers as $h)
			{
				$class = $method = $handler = $file_path = $line = $module_id = '';

				if (isset($h['TO_MODULE_ID']))
				{
					$module_id = $h['TO_MODULE_ID'];
					\CModule::IncludeModule($module_id);
				}

				if (isset($h['TO_CLASS']) && !empty($h['TO_CLASS']))
				{
					$class = $h['TO_CLASS'];

					if (isset($h['TO_METHOD']))
					{
						$method = $h['TO_METHOD'];
					}

				}
                elseif (isset($h['CALLBACK']) && !empty($h['CALLBACK']))
				{
					if (is_array($h['CALLBACK']))
					{
						if (isset($h['CALLBACK'][0]))
						{
							$class = $h['CALLBACK'][0];
						}

						if (isset($h['CALLBACK'][1]))
						{
							$method = $h['CALLBACK'][1];
						}
					}
					else
					{
						$method = $h['CALLBACK'];
					}
				}
                elseif (isset($h['TO_PATH']) && !empty($h['TO_PATH']))
				{
					$file_path = $h['TO_PATH'];
				}

				if (is_object($class))
				{
					$class = get_class($class);
				}

				$reflFunc = null;

				$manager->fixBugs($class);

				if ($class != '' && class_exists($class, true) && method_exists($class, $method))
				{
					$reflFunc = new ReflectionMethod($class, $method);
				}
                elseif ($method != '' && function_exists($method))
				{
					$reflFunc = new ReflectionFunction($method);
				}

				if (!is_null($reflFunc))
				{
					$file_path = $reflFunc->getFileName();

					$file_path = str_replace('\\', '/', $file_path);
					$file_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file_path);
					$line      = $reflFunc->getStartLine();
				}

				if (!empty($h['SORT']))
				{
					$handler .= '<span class="bxsol_events__handlers__sort" title="sort order">' . $h['SORT'] . '</span>';
				}

				if ($class != '')
				{
					$handler .= '<span class="bxsol_events__class">' . $class . ($method != '' ? '::' : '') . '</span>';
				}

				if ($method != '')
				{
					$handler .= '<span class="bxsol_events__class_method">' . $method . '()</span>';
				}
				else
				{
					$handler .= '<span class="bxsol_events__class_method">---</span>';
				}

				$handler = '<span class="bxsol_events__class-wrap">' . $handler . '</span>';

				if ($file_path != '')
				{
					$file_name = $file_path;
					$handler   .= ' <a target="_blank" class="bxsol_events__class_source" href="/bitrix/admin/fileman_file_edit.php?full_src=Y&path=' . urlencode($file_path) . '" title="' . Loc::getMessage('BXSOL_DEBUG_EVENTS_GOTO_FILE') . '">' . $file_name . ':' . $line . '</a>';
				}

				echo '<p class="bxsol_events__handlers__hadler">' . $handler . '</p>';
			}
			echo '</div></div>';
		}
		?>

    </div>

	<?php
}

?>
    <script>
        var bxsol_debug_events = <?=json_encode($allEvents)?>;
    </script>

<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

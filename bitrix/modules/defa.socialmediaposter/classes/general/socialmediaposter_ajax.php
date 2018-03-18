<?

IncludeModuleLangFile(__FILE__);

class DSocialMediaPosterAJAX
{
	private static $IBLOCK_ID;
	private static $ELEMENT_ID;
	private static $SOCNET;

	public function OnProlog()
	{
		/** @var $USER CUser */
		global $USER;

		self::$IBLOCK_ID = $IBLOCK_ID = (int)$_REQUEST['IBLOCK_ID'];
		self::$ELEMENT_ID = $ELEMENT_ID = (int)$_REQUEST['ID'];
		self::$SOCNET = (string)$_REQUEST['socnet'];

		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		if (
			$_POST['_defa_action'] === 'smp'
			&& self::$ELEMENT_ID > 0 && self::$IBLOCK_ID > 0
			&& ctype_lower(self::$SOCNET)
			&& $USER->IsAuthorized()
			&& CModule::IncludeModule('iblock')
		) {
			if (
				class_exists('CIBlockElementRights')
				&& !CIBlockElementRights::UserHasRightTo(self::$IBLOCK_ID, self::$ELEMENT_ID, "element_edit")
			){
				die();
			}
			define("STOP_STATISTICS", true);
			$response = self::postSingle();

			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			echo CUtil::PhpToJSObject($response);
			die();
		}

	}

	private function postSingle()
	{
		$socnet = self::$SOCNET;
		$response = array('socnet' => $socnet, 'status' => 'error', 'message' => 'error');

		try {
			if (strlen(self::post(self::$ELEMENT_ID, $socnet, 50))) {
				/** @noinspection PhpDynamicAsStaticMethodCallInspection */
				$response['status'] = 'error';
				$response['message'] = GetMessage('SOCIALMEDIAPOSTER_AJAX_ERROR_WHILE_POSTING');
			} else {
				$response['status'] = 'ok';
			}
		} catch (Exception $e) {
			/** @noinspection PhpDynamicAsStaticMethodCallInspection */
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}

		return $response;

	}

	private function post($element_id, $socnet, $times)
	{
		/** @noinspection PhpDynamicAsStaticMethodCallInspection */
		return DSocialMediaPosterShedule::Execute($element_id, $socnet, $times);
	}

}

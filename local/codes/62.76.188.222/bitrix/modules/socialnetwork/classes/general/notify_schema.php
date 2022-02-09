<?
IncludeModuleLangFile(__FILE__);

class CSocNetNotifySchema
{
	public function __construct()
	{
	}

	public static function OnGetNotifySchema()
	{
		return array(
			"socialnetwork" => array(
				"invite_user" => Array(
					"NAME" => GetMessage('SONET_NS_INVITE_USER'),
					"MAIL" => true,
					"XMPP" => false,
				),
				"invite_group" => Array(
					"NAME" => GetMessage('SONET_NS_INVITE_GROUP'),
					"MAIL" => true,
					"XMPP" => false,
				),
			),
		);
	}
}

?>

<?php

namespace Api\BXEditor;

//bitrix/js/fileman/html_editor/html-controls.js

/*
== base ==
Button
Dialog
Controls

== dialogs ==
Image = ImageDialog;
Link = LinkDialog;
Video = VideoDialog;
Source = SourceDialog;
Anchor = AnchorDialog;
Table = TableDialog;
Settings = SettingsDialog;
Default = DefaultDialog;
Specialchar = SpecialcharDialog;
InsertList = InsertListDialog;
*/

class Button
{
	public static function init()//\Bitrix\Main\Event $event
	{
		\CJSCore::RegisterExt('api_bxeditor_button', array(
			 'js' => '/bitrix/js/api.bxeditor/button.js',
		));

		\CJSCore::Init(array('api_bxeditor_button'));
	}
}
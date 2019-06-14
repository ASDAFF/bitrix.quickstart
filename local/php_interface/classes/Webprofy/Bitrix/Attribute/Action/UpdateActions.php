<?php

	namespace Webprofy\Bitrix\Attribute\Action;
	
	use Webprofy\General\Container;
	use Webprofy\Bitrix\Attribute\Action\Actions;

	class UpdateActions extends Actions{
		protected $fillActions = array(
			'Webprofy\Bitrix\Attribute\Action\Update\SetAction',
			'Webprofy\Bitrix\Attribute\Action\Update\MathAction',
			'Webprofy\Bitrix\Attribute\Action\Update\StringAction',
			'Webprofy\Bitrix\Attribute\Action\Update\PhpAction',
		);
	}
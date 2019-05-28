<?php

	namespace Webprofy\Bitrix\Attribute\Action;
	
	use Webprofy\General\Container;
	use Webprofy\Bitrix\Attribute\Action\Actions;

	use Webprofy\Bitrix\Attribute\Action\Compare\BetweenAction;
	use Webprofy\Bitrix\Attribute\Action\Compare\EqualAction;

	class CompareActions extends Actions{
		protected $fillActions = array(
			'Webprofy\Bitrix\Attribute\Action\Compare\EqualAction',
			'Webprofy\Bitrix\Attribute\Action\Compare\BetweenAction',
		);
	}
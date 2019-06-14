<?php

namespace Rees46\Includes;

use Rees46\Component\RecommendRenderer;
use Rees46\Options;

class Controller
{
	public static function route()
	{
		switch($_REQUEST['action']) {
			case 'recommend':
				RecommendRenderer::run();
				break;

			case 'css':
				self::renderCss();
				break;

			default:
				die();
		}
	}

	public static function renderCss()
	{
		header('Content-type: text/css');

		echo Options::getRecommenderCSS();
	}
}

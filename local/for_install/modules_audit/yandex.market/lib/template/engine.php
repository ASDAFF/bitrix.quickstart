<?php

namespace Yandex\Market\Template;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Iblock;

class Engine
{
	/**
	 * Load required dependecies
	 *
	 * @return bool
	 * @throws \Bitrix\Main\LoaderException
	 */
	public static function load()
	{
		return Main\Loader::includeModule('iblock');
	}

	/**
	 * Complite template string to node
	 *
	 * @param $template
	 *
	 * @return Node\Root
	 */
	public static function compileTemplate($template)
	{
		$rootNode = new Node\Root();

		static::parseTemplateTree($template, $rootNode);

		return $rootNode;
	}

	/**
	 * Splits template by tokens and builds execution tree.
	 *
	 * @param string $template Source expression.
	 * @param Node\Root $parent Root node.
	 *
	 * @return Node\Root
	 */
	protected static function parseTemplateTree($template, Node\Root $parent)
	{
		list($template, $modifiers) = Iblock\Template\Helper::splitTemplate($template);
		if ($modifiers != "")
			$parent->setModifiers($modifiers);

		$parsedTemplate = preg_split('/({=|})/',  $template, -1, PREG_SPLIT_DELIM_CAPTURE);
		while (($token = array_shift($parsedTemplate)) !== null)
		{
			$node = null;

			if ($token === "{=")
			{
				$node = self::parseFormula($parsedTemplate);
			}
			elseif ($token !== "")
			{
				$node = new Iblock\Template\NodeText($token);
			}

			if ($node)
				$parent->addChild($node);
		}
		return $parent;
	}

	/**
	 * Parses "{=" part of the template. Moves internal pointer right behind balanced "}"
	 * after {= a field of the entity should follow
	 * or a function call.
	 *
	 * @param array[]string &$parsedTemplate Template tokens.
	 *
	 * @return Node\Field|Node\Operation|null
	 */
	protected static function parseFormula(array &$parsedTemplate)
	{
		$node = null;
		if (($token = array_shift($parsedTemplate)) !== null)
		{
			if (preg_match("/^([a-zA-Z0-9_]+)\\.([a-zA-Z0-9_.]+)\\s*\$/", $token, $match))
			{
				$node = new Node\Field($match[1], $match[2]);
			}
			elseif (preg_match("/^([a-zA-Z0-9_]+)(.*)\$/", $token, $match))
			{
				$node = new Node\Operation($match[1]);
				self::parseFunctionArguments($match[2], $parsedTemplate, $node);
			}
		}
		//Eat up to the formula end
		while (($token = array_shift($parsedTemplate)) !== null)
		{
			if ($token === "}")
				break;
		}
		return $node;
	}

	/**
	 * Adds function arguments to a $function.
	 * An formula may be evaluated as oa argument.
	 * An number or
	 * A string in double quotes.
	 *
	 * @param string $token Expression string.
	 * @param array[]string &$parsedTemplate Template tokens.
	 * @param Node\Operation $function Function object to which arguments will be added.
	 *
	 * @return void
	 */
	protected static function parseFunctionArguments($token, array &$parsedTemplate, Node\Operation $function)
	{
		$token = ltrim($token, " \t\n\r");

		if ($token !== "")
			self::explodeFunctionArgument($token, $function);

		while (($token = array_shift($parsedTemplate)) !== null)
		{
			if ($token === "}")
			{
				array_unshift($parsedTemplate, $token);
				break;
			}
			elseif ($token === "{=")
			{
				$node = self::parseFormula($parsedTemplate);
				if ($node)
					$function->addParameter($node);
			}
			elseif ($token !== "")
			{
				self::explodeFunctionArgument($token, $function);
			}
		}
	}

	/**
	 * Explodes a string into function arguments.
	 * Numbers or strings.
	 *
	 * @param string $token Expression string.
	 * @param Node\Operation $function Function object to which arguments will be added.
	 *
	 * @return void
	 */
	protected static function explodeFunctionArgument($token, Node\Operation $function)
	{
		if (preg_match_all("/
			(
				[a-zA-Z0-9_]+\\.[a-zA-Z0-9_.]+
				|[0-9]+
				|\"[^\"]*\"
			)
			/x", $token, $wordList)
		)
		{
			foreach ($wordList[0] as $word)
			{
				if ($word !== "")
				{
					if (preg_match("/^([a-zA-Z0-9_]+)\\.([a-zA-Z0-9_.]+)\\s*\$/", $word, $match))
					{
						$node = new Node\Field($match[1], $match[2]);
					}
					else
					{
						$node = new Iblock\Template\NodeText(trim($word, '"'));
					}
					$function->addParameter($node);
				}
			}
		}
	}
}
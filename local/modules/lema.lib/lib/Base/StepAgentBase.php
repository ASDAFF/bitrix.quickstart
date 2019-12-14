<?php

namespace Lema\Base;


class StepAgentBase
{
    protected static $STEP_SIZE = 100;
    protected static $AGENT_INTERVAL_SECONDS = 180;
    protected static $interValTime = 1800;


    /**
     * @param callable $closure
     *
     * @access protected
     *
     * @final
     */
    final public static function start(callable $closure)
    {
        static::addAgent($closure);
    }

    /**
     * Start agents
     *
     * @param int $step
     * @param callable $closure
     *
     * @access public
     *
     * @final
     */
    final public static function startAgents($step = 1, callable $closure)
    {
        $step = $closure($step);
        if(($step))
        {
            static::removeStepAgent($step - 1, $closure);
            static::addStepAgent($step, $closure);
        }
        else
            static::removeStepAgent($step, $closure);
    }

    /**
     * Add agents
     *
     * @return null
     * @param callable $closure
     *
     * @access public
     *
     * @final
     */
    final public static function addAgent($closure)
    {
        $res = \CAgent::GetList(array('ID' => 'DESC'), array('NAME' => get_called_class() . '::startAgents(%'));
        if($row = $res->Fetch())
        {
            return null;
        }
        //exit;
        \CAgent::AddAgent(
            get_called_class() . '::startAgents(1, "' . $closure . '");',
            '',
            'N',
            static::$interValTime,
            date('d.m.Y H:i:s', strtotime('+'.static::$interValTime.' second')),
            'Y',
            date('d.m.Y H:i:s', strtotime('+'.static::$interValTime.' second')),
            30
        );
    }

    /**
     * Add step agent
     *
     * @param $step
     * @param callable $closure
     *
     * @access protected
     *
     * @final
     */
    final protected static function addStepAgent($step, $closure)
    {
        \CAgent::AddAgent(
            get_called_class().'::startAgents('.intval($step).', "' . $closure . '");', // имя функции
            '',
            'N',
            86400,
            date('d.m.Y H:i:s',strtotime('+'.static::$AGENT_INTERVAL_SECONDS.' second')),
            'Y',                                  // агент активен
            date('d.m.Y H:i:s',strtotime('+'.static::$AGENT_INTERVAL_SECONDS.' second')),
            30
        );
    }

    /**
     * Remove step agent from db
     *
     * @param $step
     * @param callable $closure
     *
     * @return void
     *
     * @access protected
     *
     * @final
     */
    final protected static function removeStepAgent($step, $closure)
    {
        \CAgent::RemoveAgent(get_called_class().'::startAgents('.intval($step).', "' . $closure . '");');
    }
}
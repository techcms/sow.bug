<?php namespace Sow\util;

if (!class_exists('FirePHP', false)) {
    require 'FirePHP/FirePHP.class.php';
}

class FB
{
    /**
     * Set an Insight console to direct all logging calls to
     * 
     * @param object $console The console object to log to
     * @return void
     */
    public static function setLogToInsightConsole($console)
    {
        \FirePHP::getInstance(true)->setLogToInsightConsole($console);
    }

    /**
     * Enable and disable logging to Firebug
     * 
     * @see FirePHP->setEnabled()
     * @param boolean $enabled TRUE to enable, FALSE to disable
     * @return void
     */
    public static function setEnabled($enabled)
    {
        \FirePHP::getInstance(true)->setEnabled($enabled);
    }
  
    /**
     * Check if logging is enabled
     * 
     * @see FirePHP->getEnabled()
     * @return boolean TRUE if enabled
     */
    public static function getEnabled()
    {
        return \FirePHP::getInstance(true)->getEnabled();
    }
  
    /**
     * Specify a filter to be used when encoding an object
     * 
     * Filters are used to exclude object members.
     * 
     * @see FirePHP->setObjectFilter()
     * @param string $class The class name of the object
     * @param array $filter An array or members to exclude
     * @return void
     */
    public static function setObjectFilter($class, $filter)
    {
      \FirePHP::getInstance(true)->setObjectFilter($class, $filter);
    }
  
    /**
     * Set some options for the library
     * 
     * @see FirePHP->setOptions()
     * @param array $options The options to be set
     * @return void
     */
    public static function setOptions($options)
    {
        \FirePHP::getInstance(true)->setOptions($options);
    }

    /**
     * Get options for the library
     * 
     * @see FirePHP->getOptions()
     * @return array The options
     */
    public static function getOptions()
    {
        return \FirePHP::getInstance(true)->getOptions();
    }

    /**
     * Log object to firebug
     * 
     * @see http://www.firephp.org/Wiki/Reference/Fb
     * @param mixed $object
     * @return true
     * @throws Exception
     */
    public static function send()
    {
        $args = func_get_args();
        return call_user_func_array(array(\FirePHP::getInstance(true), 'fb'), $args);
    }

    /**
     * Start a group for following messages
     * 
     * Options:
     *   Collapsed: [true|false]
     *   Color:     [#RRGGBB|ColorName]
     *
     * @param string $name
     * @param array $options OPTIONAL Instructions on how to log the group
     * @return true
     */
    public static function group($name, $options=null)
    {
        return \FirePHP::getInstance(true)->group($name, $options);
    }

    /**
     * Ends a group you have started before
     *
     * @return true
     * @throws Exception
     */
    public static function groupEnd()
    {
        return self::send(null, null, \FirePHP::GROUP_END);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::LOG
     * @param mixes $object
     * @param string $label
     * @return true
     * @throws Exception
     */
    public static function log($object, $label=null)
    {
        return self::send($object, $label, \FirePHP::LOG);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::INFO
     * @param mixes $object
     * @param string $label
     * @return true
     * @throws Exception
     */
    public static function info($object, $label=null)
    {
        return self::send($object, $label, \FirePHP::INFO);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::WARN
     * @param mixes $object
     * @param string $label
     * @return true
     * @throws Exception
     */
    public static function warn($object, $label=null)
    {
        return self::send($object, $label, \FirePHP::WARN);
    }

    /**
     * Log object with label to firebug console
     *
     * @see FirePHP::ERROR
     * @param mixes $object
     * @param string $label
     * @return true
     * @throws Exception
     */
    public static function error($object, $label=null)
    {
        return self::send($object, $label, \FirePHP::ERROR);
    }

    /**
     * Dumps key and variable to firebug server panel
     *
     * @see FirePHP::DUMP
     * @param string $key
     * @param mixed $variable
     * @return true
     * @throws Exception
     */
    public static function dump($key, $variable)
    {
        return self::send($variable, $key, \FirePHP::DUMP);
    }

    /**
     * Log a trace in the firebug console
     *
     * @see FirePHP::TRACE
     * @param string $label
     * @return true
     * @throws Exception
     */
    public static function trace($label)
    {
        return self::send($label, \FirePHP::TRACE);
    }

    /**
     * Log a table in the firebug console
     *
     * @see FirePHP::TABLE
     * @param string $label
     * @param string $table
     * @return true
     * @throws Exception
     */
    public static function table($label, $table)
    {
        return self::send($table, $label, \FirePHP::TABLE);
    }

}
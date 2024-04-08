<?php

/**
 * Logger
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Logging\Log;

use Laminas\Log\Logger as LaminasLogger;
use Laminas\Log\LoggerInterface;

/**
 * Logger
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Logger
{
    /**
     * @var LaminasLogger
     */
    private static $logger;

    /**
     * @param LaminasLogger $logger
     */
    public static function setLogger(LaminasLogger $logger)
    {
        self::$logger = $logger;
    }

    /**
     * @return LaminasLogger
     */
    public static function getLogger()
    {
        return self::$logger;
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function emerg($message, $extra = array())
    {
        return self::$logger->emerg($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function alert($message, $extra = array())
    {
        return self::$logger->alert($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function crit($message, $extra = array())
    {
        return self::$logger->crit($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function err($message, $extra = array())
    {
        return self::$logger->err($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function warn($message, $extra = array())
    {
        return self::$logger->warn($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function notice($message, $extra = array())
    {
        return self::$logger->notice($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function info($message, $extra = array())
    {
        return self::$logger->info($message, $extra);
    }

    /**
     * @param string $message
     * @param array|\Traversable $extra
     * @return LoggerInterface
     */
    public static function debug($message, $extra = array())
    {
        return self::$logger->debug($message, $extra);
    }

    /**
     * @param $priority
     * @param $message
     * @param array $extra
     * @return LaminasLogger
     */
    public static function log($priority, $message, $extra = array())
    {
        return self::$logger->log($priority, $message, $extra);
    }

    /**
     * Log data using a response status code to set the priority
     *
     * @param int    $status  Https response status code (eg 200, 404, 500)
     * @param string $message Message to log
     * @param array  $extra   Extra log data
     *
     * @return LaminasLogger
     */
    public static function logResponse($status, $message, $extra = array())
    {
        return self::$logger->log(\Laminas\Log\Logger::DEBUG, $message, $extra);
    }

    /**
     * Log an Exception
     *
     * @param \Exception $e        The exception to be logged
     * @param int        $priority One of \Laminas\Log\Logger::*
     */
    public static function logException(\Exception $e, $priority = \Laminas\Log\Logger::DEBUG)
    {
        $message = sprintf(
            "Code %s : %s\n%s Line %d",
            $e->getCode(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        static::log($priority, $message, ['trace' => $e->getTraceAsString()]);
    }
}

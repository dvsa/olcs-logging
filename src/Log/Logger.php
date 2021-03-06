<?php

/**
 * Logger
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Logging\Log;

use Zend\Log\Logger as ZendLogger;
use Zend\Log\LoggerInterface;

/**
 * Logger
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Logger
{
    /**
     * @var ZendLogger
     */
    private static $logger;

    /**
     * @param ZendLogger $logger
     */
    public static function setLogger(ZendLogger $logger)
    {
        self::$logger = $logger;
    }

    /**
     * @return ZendLogger
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
     * @return ZendLogger
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
     * @return ZendLogger
     */
    public static function logResponse($status, $message, $extra = array())
    {
        if ($status < 400) {
            $priority = \Zend\Log\Logger::DEBUG;
        } elseif ($status < 500) {
            $priority = \Zend\Log\Logger::INFO;
        } else {
            $priority = \Zend\Log\Logger::ERR;
        }

        return self::$logger->log($priority, $message, $extra);
    }

    /**
     * Log an Exception
     *
     * @param \Exception $e        The exception to be logged
     * @param int        $priority One of \Zend\Log\Logger::*
     */
    public static function logException(\Exception $e, $priority = \Zend\Log\Logger::DEBUG)
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

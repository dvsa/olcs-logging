<?php

namespace Olcs\Logging\Log;

use Psr\Log\AbstractLogger as AbstractPsrLogger;
use Laminas\Log\Logger as LaminasLogger;
use Psr\Log\LogLevel;

/**
 * Class LaminasLogPsr3Adapter
 *
 * @package Olcs\Logging\Log
 */
class LaminasLogPsr3Adapter extends AbstractPsrLogger
{
    /**
     * Map native PHP errors to priority
     *
     * @var array
     */
    protected $map = array(
        LogLevel::EMERGENCY => LaminasLogger::EMERG,
        LogLevel::ALERT     => LaminasLogger::ALERT,
        LogLevel::CRITICAL  => LaminasLogger::CRIT,
        LogLevel::ERROR     => LaminasLogger::ERR,
        LogLevel::WARNING   => LaminasLogger::WARN,
        LogLevel::NOTICE    => LaminasLogger::NOTICE,
        LogLevel::INFO      => LaminasLogger::INFO,
        LogLevel::DEBUG     => LaminasLogger::DEBUG
    );

    /**
     * @var LaminasLogger
     */
    protected $log;

    /**
     * @param LaminasLogger $log
     */
    public function __construct(LaminasLogger $log)
    {
        $this->log = $log;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        return $this->log->log($this->map[$level], $message, $context);
    }
}

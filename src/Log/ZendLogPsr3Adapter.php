<?php

namespace Olcs\Logging\Log;

use Psr\Log\AbstractLogger as AbstractPsrLogger;
use Zend\Log\Logger as ZendLogger;
use Psr\Log\LogLevel;

/**
 * Class ZendLogPsr3Adapter
 *
 * @package Olcs\Logging\Log
 */
class ZendLogPsr3Adapter extends AbstractPsrLogger
{
    /**
     * Map native PHP errors to priority
     *
     * @var array
     */
    protected $map = array(
        LogLevel::EMERGENCY => ZendLogger::EMERG,
        LogLevel::ALERT     => ZendLogger::ALERT,
        LogLevel::CRITICAL  => ZendLogger::CRIT,
        LogLevel::ERROR     => ZendLogger::ERR,
        LogLevel::WARNING   => ZendLogger::WARN,
        LogLevel::NOTICE    => ZendLogger::NOTICE,
        LogLevel::INFO      => ZendLogger::INFO,
        LogLevel::DEBUG     => ZendLogger::DEBUG
    );

    /**
     * @var ZendLogger
     */
    protected $log;

    /**
     * @param ZendLogger $log
     */
    public function __construct(ZendLogger $log)
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

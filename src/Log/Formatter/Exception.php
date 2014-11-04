<?php

namespace Olcs\Logging\Log\Formatter;

use Zend\Log\Formatter\Base;

/**
 * Class Exception
 * @package Olcs\Logging\Log\Formatter
 */
class Exception extends Base
{
    /**
     * @var string
     */
    protected $dateTimeFormat = 'Y-m-d h:m:s';

    /**
     * @param array $event
     * @return string
     */
    public function format($event)
    {
        $exception = isset($event['extra']['exception']) ? $event['extra']['exception'] : new \Exception();
        $data = isset($event['extra']['data']) ? $event['extra']['data'] : [];
        $data['remoteIp'] = $event['extra']['remoteIp'];
        $event['extra']['data'] = $data;

        $event = parent::format($event);
        //need to improve this, currently the zend log error handler doesn't capture all the info we need...
        return sprintf(
            "^^*%s.%d||%d||%s||%s||%s||%s||%s||%s:%d||%s||%s||%s||%s||\n%s",
            $event['timestamp'],
            $event['microsecs'],
            $event['priority'],
            $event['priorityName'],
            isset($event['extra']['type']) ? $event['extra']['type'] : '',
            $event['extra']['userId'],
            $event['extra']['sessionId'],
            $event['extra']['requestId'],
            $exception->getFile(),
            $exception->getLine(),
            get_class($exception), //exception type
            $exception->getCode(), //error code
            $exception->getMessage(),
            isset($event['extra']['data']) ? $event['extra']['data'] : '',
            $exception->getTraceAsString()
        );
    }
}

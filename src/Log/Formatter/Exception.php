<?php

namespace Olcs\Logging\Log\Formatter;

/**
 * Class Exception
 * @package Olcs\Logging\Log\Formatter
 */
class Exception extends AbstractFormatter
{
    /**
     * @param array $event
     * @return string
     */
    public function format($event)
    {
        if (isset($event['extra']['exception'])) {
            return $this->formatExtraException($event);
        }

        return $this->formatException($event);
    }

    protected function formatException($event)
    {
        $data = isset($event['extra']['data']) ? $event['extra']['data'] : [];
        $data['remoteIp'] = $event['extra']['remoteIp'];
        $event['extra']['data'] = $data;

        $event = parent::format($event);
        //need to improve this, currently the zend log error handler doesn't capture all the info we need...
        return sprintf(
            "^^*%s.%d||%d||%s||%s||%s||%s||%s||%s:%d||%s||%s||%s||%s||\n%s",
            $this->getTimestamp($event),
            $event['microsecs'],
            $event['priority'],
            $event['priorityName'],
            isset($event['extra']['type']) ? $event['extra']['type'] : '',
            $event['extra']['userId'],
            $event['extra']['sessionId'],
            $event['extra']['requestId'],
            $event['extra']['file'],
            $event['extra']['line'],
            'Unknown',
            $event['priority'],
            $event['message'],
            isset($event['extra']['data']) ? $event['extra']['data'] : '',
            $event['extra']['trace']
        );
    }

    protected function formatExtraException($event)
    {
        $exception = $event['extra']['exception'];
        $data = isset($event['extra']['data']) ? $event['extra']['data'] : [];
        $data['remoteIp'] = $event['extra']['remoteIp'];
        $event['extra']['data'] = $data;

        $event = parent::format($event);
        //need to improve this, currently the zend log error handler doesn't capture all the info we need...
        return sprintf(
            "^^*%s.%d||%d||%s||%s||%s||%s||%s||%s:%d||%s||%s||%s||%s||\n%s",
            $this->getTimestamp($event),
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

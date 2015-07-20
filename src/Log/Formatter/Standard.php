<?php

namespace Olcs\Logging\Log\Formatter;

/**
 * Class Standard
 * @package Olcs\Logging\Log\Formatter
 */
class Standard extends AbstractFormatter
{
    /**
     * @param array $event
     * @return string
     */
    public function format($event)
    {
        $data = isset($event['extra']['data']) ? $event['extra']['data'] : [];
        $data['remoteIp'] = $event['extra']['remoteIp'];
        $event['extra']['data'] = $data;

        $event = parent::format($event);

        return sprintf(
            "^^*%s.%0-6s||%d||%s||%s||%s||%s||%s||%s||%s||%s",
            $this->getTimestamp($event),
            $event['microsecs'],
            $event['priority'],
            $event['priorityName'],
            isset($event['extra']['type']) ? $event['extra']['type'] : '',
            $event['extra']['userId'],
            $event['extra']['sessionId'],
            $event['extra']['requestId'],
            isset($event['extra']['location']) ? $event['extra']['location'] : '',
            $event['message'],
            isset($event['extra']['data']) ? $event['extra']['data'] : ''
        );
    }
}

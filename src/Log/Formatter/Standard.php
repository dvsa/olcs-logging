<?php

namespace Olcs\Logging\Log\Formatter;

use Zend\Log\Formatter\Base;

class Standard extends Base
{
    protected $dateTimeFormat = 'Y-m-d h:m:s';

    public function format($event)
    {
        $data = isset($event['extra']['data']) ? $event['extra']['data'] : [];
        $data['remoteIp'] = $event['extra']['remoteIp'];
        $event['extra']['data'] = $data;

        $event = parent::format($event);

        return sprintf(
            "^^*%s.%0-6s||%d||%s||%s||%s||%s||%s||%s||%s||%s",
            $event['timestamp'],
            $event['microsecs'],
            $event['priority'],
            $event['priorityName'],
            isset($event['extra']['type']) ? $event['extra']['type'] : '' ,
            $event['extra']['userId'],
            $event['extra']['sessionId'],
            $event['extra']['requestId'],
            isset($event['extra']['location']) ? $event['extra']['location'] : '',
            $event['message'],
            isset($event['extra']['data']) ? $event['extra']['data'] : ''
        );
    }
}
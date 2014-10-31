<?php

namespace Olcs\Logging\Log\Formatter;

use Zend\Log\Formatter\Base;

class Exception extends Base
{
    protected $dateTimeFormat = 'y-M-d h:m:s';

    public function format($event)
    {
        $event = parent::format($event);
        //need to improve this, currently the zend log error handler doesn't capture all the info we need...
        return sprintf(
            "^^*%s.%d||%d||%s||%s||%s||%s||%s||%s||%s||%s||%s||%s||%s",
            $event['timestamp'],
            $event['microsecs'],
            $event['priority'],
            $event['priorityName'],
            isset($event['extra']['type']) ? $event['extra']['type'] : '' ,
            $event['extra']['userId'],
            $event['extra']['sessionId'],
            $event['extra']['requestId'],
            isset($event['extra']['location']) ? $event['extra']['location'] : '',
            '', //exception type
            '', //error code
            $event['message'],
            isset($event['extra']['data']) ? $event['extra']['data'] : '',
            $event['extra']['trace']
        );
    }
}
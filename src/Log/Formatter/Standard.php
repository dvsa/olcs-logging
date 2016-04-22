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
        // get extra data, remove items that are already in the log format (to avoid them loggin twice)
        $otherExtra = isset($event['extra']) ? $event['extra'] : [];
        unset($otherExtra['userId']);
        unset($otherExtra['sessionId']);
        unset($otherExtra['requestId']);
        unset($otherExtra['location']);

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
            $this->normalize($otherExtra)
        );
    }
}

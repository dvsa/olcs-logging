<?php

namespace Olcs\Logging\Log\Formatter;

/**
 * Class Standard
 * @package Olcs\Logging\Log\Formatter
 */
class Standard extends AbstractFormatter
{
    /**
     * Format a log event
     *
     * @param array $event Array of event data
     *
     * @return string
     */
    public function format($event)
    {
        // get extra data, remove items that are already in the log format (to avoid them logging twice)
        $otherExtra = isset($event['extra']) ? $event['extra'] : [];
        unset($otherExtra['userId']);
        unset($otherExtra['sessionId']);
        unset($otherExtra['requestId']);
        unset($otherExtra['location']);

        $event = parent::format($event);

        $data = [
            'timestamp' => $this->getTimestamp($event) .'.'. $event['microsecs'],
            "log_priority" => $event['priority'],
            "log_priority_name" => $event['priorityName'],
            "log-entry-type" => isset($event['extra']['type']) ? $event['extra']['type'] : '',
            "openam-uuid" => $event['extra']['userId'],
            "openam_session_token" => $event['extra']['sessionId'],
            "correlation_id" => $event['extra']['requestId'],
            "location" => isset($event['extra']['location']) ? $event['extra']['location'] : '',
            "relevant-message" => $event['message'],
            "relevant-data" => $otherExtra,
        ];

        return $this->normalize($data);
    }
}

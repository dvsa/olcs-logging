<?php

namespace Olcs\Logging\Log\Formatter;

use DateTimeInterface;
use Laminas\Log\Formatter\Base;
use Laminas\Log\Formatter\FormatterInterface;

class Standard implements FormatterInterface
{
    private Base $laminasBaseFormatter;
    private string $outputDateTimeFormat = 'Y-m-d H:i:s';
    protected $dateTimeFormat = DateTimeInterface::W3C;

    public function __construct(Base $laminasBaseFormatter)
    {
        $this->laminasBaseFormatter = $laminasBaseFormatter;
        $this->laminasBaseFormatter->setDateTimeFormat($this->dateTimeFormat);
    }

    /**
     * Return a UTC formatted timestamp for the log output
     *
     * @return false|string
     */
    protected function getTimestamp(array $event)
    {
        return gmdate($this->outputDateTimeFormat, strtotime($event['timestamp']));
    }

    /**
     * Format a log event
     *
     * @param array $event Array of event data
     *
     * @return string
     */
    public function format($event)
    {
        $event = $this->laminasBaseFormatter->format($event);

        // get extra data, remove items that are already in the log format (to avoid them logging twice)
        $otherExtra = isset($event['extra']) ? $event['extra'] : [];
        unset($otherExtra['userId']);
        unset($otherExtra['sessionId']);
        unset($otherExtra['location']);

        $data = [
            'timestamp' => $this->getTimestamp($event) . '.' . $event['microsecs'],
            "log_priority" => $event['priority'],
            "log_priority_name" => $event['priorityName'],
            "log-entry-type" => isset($event['extra']['type']) ? $event['extra']['type'] : '',
            "openam-uuid" => $event['extra']['userId'],
            "openam_session_token" => $event['extra']['sessionId'],
            "correlation_id" => $this->getCorrelationId($event),
            "location" => isset($event['extra']['location']) ? $event['extra']['location'] : '',
            "relevant-message" => $event['message'],
            "relevant-data" => $otherExtra,
        ];

        return @json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the correlation ID to add to the log
     *
     * @param array $event Log event
     *
     * @return mixed
     */
    private function getCorrelationId($event)
    {
        return isset($event['extra']['correlationId']) ?
            $event['extra']['correlationId'] :
            $event['extra']['requestId'];
    }

    /**
     * We only need this to conform to the Laminas interface
     *
     * {@inheritDoc}
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * We only need this to conform to the Laminas interface
     *
     * {@inheritDoc}
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = (string) $dateTimeFormat;
        return $this;
    }
}

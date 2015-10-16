<?php

namespace Olcs\Logging\Log\Formatter;

use Zend\Log\Formatter\Base;

/**
 * Abstract formatter class
 * @package Olcs\Logging\Log\Formatter
 */
abstract class AbstractFormatter extends Base
{
    /**
     * @var string
     */
    protected $dateTimeFormat = \DateTime::W3C;

    protected $outputDateTimeFormat = 'Y-m-d H:i:s';

    /**
     * Return a UTC formatted timestamp for the log output
     *
     * @param array $event
     * @return string
     */
    protected function getTimestamp($event)
    {
        return gmdate($this->outputDateTimeFormat, strtotime($event['timestamp']));
    }
}

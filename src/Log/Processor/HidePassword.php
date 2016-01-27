<?php

namespace Olcs\Logging\Log\Processor;

use \Zend\Log\Processor\ProcessorInterface;

/**
 * Class HidePassword
 * Strip any password from being logged
 *
 * @package Olcs\Logging\Log\Processor
 */
class HidePassword implements ProcessorInterface
{
    private $replaceWith = '*** HIDDEN PASSWORD ***';

    /**
     * Processes log event and removed any password
     *
     * @param  array $event
     * @return array
     */
    public function process(array $event)
    {
        // recurse through the event
        array_walk_recursive(
            $event,
            function (&$value, $key) {
                // if the array key is "password", to cover POST, GET, etc
                if ($key == 'password') {
                    $value = $this->replaceWith;
                }

                // if value is a string and contains the string "password", to cover when in querystring
                if (is_string($value) && strpos($value, 'password') !== false) {
                    $value = $this->replaceWith;
                }
            }
        );

        return $event;
    }
}

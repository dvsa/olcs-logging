<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Log\Processor\ProcessorInterface;

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
     */
    #[\Override]
    public function process(array $event): array
    {
        // recurse through the event
        array_walk_recursive(
            $event,
            function (&$value, $key) {
                // if "password" is in the key or value, then mask the value
                if (
                    (stripos($key, 'password') !== false) ||
                    (is_string($value) && stripos($value, 'password') !== false) ||
                    // CognitoAdapter can throw a trace that doesnt contain the string 'password' but has creds in it. Suppress these.
                    (is_string($value) && strpos($value, 'CognitoAdapter') !== false)
                ) {
                    $value = $this->replaceWith;
                }
            }
        );

        return $event;
    }
}

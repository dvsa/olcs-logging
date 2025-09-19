<?php

namespace Olcs\Logging\Log\Processor;

use Laminas\Log\Processor\RequestId as LaminasRequestId;

/**
 * Class RequestId
 * @package Olcs\Logging\Log\Processor
 */
class RequestId extends LaminasRequestId
{
    /**
     * Get the request identifier, make this method public
     *
     * @return string
     */
    #[\Override]
    public function getIdentifier()
    {
        return parent::getIdentifier();
    }
}

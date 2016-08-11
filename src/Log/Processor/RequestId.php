<?php

namespace Olcs\Logging\Log\Processor;

use Zend\Log\Processor\RequestId as ZendRequestId;

/**
 * Class RequestId
 * @package Olcs\Logging\Log\Processor
 */
class RequestId extends ZendRequestId
{
    /**
     * Get the request identifier, make this method public
     *
     * @return string
     */
    public function getIdentifier()
    {
        return parent::getIdentifier();
    }
}

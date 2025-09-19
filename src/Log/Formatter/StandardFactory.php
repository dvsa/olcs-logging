<?php

namespace Olcs\Logging\Log\Formatter;

use Laminas\Log\FormatterPluginManager;
use Laminas\Log\Formatter\Base;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class StandardFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     */
    #[\Override]
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Standard
    {
        /** @var FormatterPluginManager $logFormatterManager */
        $logFormatterManager = $container->get('LogFormatterManager');
        return new Standard($logFormatterManager->get(Base::class));
    }
}

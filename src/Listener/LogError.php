<?php

namespace Olcs\Logging\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Helper\LogException;

/**
 * Class LogError
 * @package Olcs\Logging\Listener
 */
class LogError implements ListenerAggregateInterface, FactoryInterface
{
    use ListenerAggregateTrait;

    private $identifier;

    /**
     * @var LogException
     */
    protected $logExceptionHelper;

    /**
     * @param \Olcs\Logging\Helper\LogException $logExceptionHelper
     */
    public function setLogExceptionHelper($logExceptionHelper)
    {
        $this->logExceptionHelper = $logExceptionHelper;
    }

    /**
     * @return \Olcs\Logging\Helper\LogException
     */
    public function getLogExceptionHelper()
    {
        return $this->logExceptionHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 0);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onDispatchError'), 0);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setLogExceptionHelper($serviceLocator->get('Olcs\Logging\Helper\LogException'));
        $this->setIdentifier(
            $serviceLocator->get('LogProcessorManager')
                ->get(\Olcs\Logging\Log\Processor\RequestId::class)
                ->getIdentifier()
        );

        return $this;
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatchError(MvcEvent $e)
    {
        if (!$e->getParam('exception')) {
            return;
        }
        // Don't log these exceptions
        if ($e->getParam('exceptionNoLog')) {
            return;
        }
        $data = [];

        $routeMatch = $e->getRouteMatch();
        if ($routeMatch) {
            $data = $routeMatch->getParams();
        }

        $this->getLogExceptionHelper()->logException(
            $e->getParam('exception'),
            ['data' => $data]
        );
    }

    /**
     * Get Correlation Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set Correlation Identifier
     *
     * @param string $identifier Correlation Identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }
}

<?php

namespace Olcs\Logging\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Helper\LogException;

/**
 * Class LogError
 * @package Olcs\Logging\Listener
 */
class LogError implements ListenerAggregateInterface, FactoryInterface
{
    use ListenerAggregateTrait;

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
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 10000);
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
}

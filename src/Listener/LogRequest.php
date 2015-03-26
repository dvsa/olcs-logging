<?php

namespace Olcs\Logging\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Log\LoggerAwareTrait;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LogRequest
 * @package Olcs\Logging\Listener
 */
class LogRequest implements ListenerAggregateInterface, FactoryInterface
{
    use ListenerAggregateTrait;
    use LoggerAwareTrait;

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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onDispatch'), 10000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onDispatchEnd'), 10000);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setLogger($serviceLocator->get('Logger'));
        return $this;
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        if ($this->isConsole($e)) {
            $data = [
                'path' => $e->getRequest()->getScriptName(),
                'params' => $e->getRequest()->getParams()
            ];
        } else {
            $routeMatch = $e->getRouteMatch();
            $data = [
                'path' => $e->getRequest()->getUri()->__toString(),
                'method' => $e->getRequest()->getMethod(),
                'route_params' => ($routeMatch? $routeMatch->getParams(): []),
                'get' => $e->getRequest()->getQuery(),
                'post' => $e->getRequest()->getPost(),
                'headers' => $e->getRequest()->getHeaders()->toArray()
            ];
        }
        $this->getLogger()->info(
            'Request received',
            [
                'data' => $data,
            ]
        );
    }

    /**
     * @param MvcEvent $e
     */
    public function onDispatchEnd(MvcEvent $e)
    {
        if (!$this->isConsole($e)) {
            $data = [
                'code' => $e->getResponse()->getStatusCode(),
                'status' => $e->getResponse()->getReasonPhrase()
            ];
            $this->getLogger()->info('Request completed', ['data' => $data]);
        }
    }

    /**
     * Is the request coming from console
     *
     * @param MvcEvent $e
     * 
     * @return bool
     */
    private function isConsole(MvcEvent $e)
    {
        return ($e->getRequest() instanceof \Zend\Console\Request);
    }
}

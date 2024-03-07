<?php

namespace Olcs\Logging\Listener;

use Laminas\Http\Request;
use Laminas\Http\Response;
use Psr\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Log\LoggerAwareTrait;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Olcs\Logging\CliLoggableInterface;

class LogRequest implements ListenerAggregateInterface, FactoryInterface
{
    use ListenerAggregateTrait;
    use LoggerAwareTrait;

    private const MAX_CONTENT_LENGTH_TO_LOG = 2048;

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), 10000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'), 10000);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onDispatchEnd'), 10000);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogRequest
    {
        $this->setLogger($container->get('Logger'));
        return $this;
    }

    /**
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $e)
    {
        if ($this->isConsole($e)) {
            /** @var CliLoggableInterface $request */
            $request = $e->getRequest();

            $data = [
                'path' => $request->getScriptPath(),
                'params' => $request->getScriptParams(),
            ];
        } else {
            /** @var Request $request */
            $request = $e->getRequest();
            $routeMatch = $e->getRouteMatch();
            $data = [
                'path' => $request->getUri()->__toString(),
                'method' => $request->getMethod(),
                'route_params' => ($routeMatch ? $routeMatch->getParams() : []),
                'get' => $request->getQuery()->getArrayCopy(),
                'post' => $request->getPost()->getArrayCopy(),
                'headers' => $request->getHeaders()->toArray(),
            ];
            // Log the request content, unless it's huge. This is useful as many
            // POST requests don't actually send form data but a JSON-encoded
            // request body instead
            if (
                $request->getHeader('Content-Length')
                && $request->getHeader('Content-Length')->getFieldValue() < self::MAX_CONTENT_LENGTH_TO_LOG
            ) {
                $data['content'] = $request->getContent();
            } else {
                $data['content'] = 'MAX_CONTENT_LENGTH_TO_LOG exceeded';
            }
        }

        $this->getLogger()->debug('Request received', ['data' => $data]);
    }

    public function onDispatch(MvcEvent $e)
    {
        if (!$this->isConsole($e)) {
            $cm = $e->getApplication()->getServiceManager()->get('ControllerManager');

            $data = [
                'controller' => get_class($cm->get($e->getRouteMatch()->getParam('controller'))),
                'action' => $e->getRouteMatch()->getParam('action')
            ];
            $this->getLogger()->debug('Request dispatched', ['data' => $data]);
        }
    }

    public function onDispatchEnd(MvcEvent $e)
    {
        if (!$this->isConsole($e)) {
            /** @var Request $request */
            $request = $e->getRequest();

            /** @var Response $response */
            $response = $e->getResponse();

            $data = [
                'request' => $request->getUriString(),
                'code' => $response->getStatusCode(),
                'status' => $response->getReasonPhrase(),
            ];

            if ($response->isServerError()) {
                $this->getLogger()->err('Request completed', ['data' => $data]);
            } elseif ($response->isClientError()) {
                $this->getLogger()->info('Request completed', ['data' => $data]);
            } else {
                $this->getLogger()->debug('Request completed', ['data' => $data]);
            }
        }
    }

    /**
     * Is the request coming from console
     */
    private function isConsole(MvcEvent $e): bool
    {
        return ($e->getRequest() instanceof CliLoggableInterface);
    }
}

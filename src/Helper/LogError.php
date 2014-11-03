<?php

namespace Olcs\Logging\Helper;

use Zend\Log\LoggerAwareTrait;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Log\Logger;

class LogError implements FactoryInterface
{
    use LoggerAwareTrait;

    public function logError($level, $message, $file, $line)
    {
        $iniLevel = error_reporting();

        if ($iniLevel & $level) {
            if (isset(Logger::$errorPriorityMap[$level])) {
                $priority = Logger::$errorPriorityMap[$level];
            } else {
                $priority = Logger::INFO;
            }
            $this->getLogger()->log($priority, $message, array(
                'location' => $file . ':' . $line,
            ));
        }

        // no idea why this is required, however if it's not set only the first error gets logged.
        set_error_handler([$this, 'logError']);

        return false;
    }

    public function logShutdownError()
    {
        $error = error_get_last();
        if (null !== $error && $error['type'] === E_ERROR) {
            $this->logError(E_ERROR, $error['message'], $error['file'], $error['line']);
        }
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
}

<?php

namespace QafooLabs\Bundle\FrameworkExtraBundle\View;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;

/**
 * Guess Templates based on Symfony and SensioFrameworkExtra conventions
 */
class SymfonyConventionsTemplateGuesser implements TemplateGuesser
{
    /**
     * @var BundleLocation
     */
    private $bundleLocation;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser
     */
    private $parser;

    public function __construct(BundleLocation $bundleLocation, ControllerNameParser $parser)
    {
        $this->bundleLocation = $bundleLocation;
        $this->parser = $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function guessControllerTemplateName($controller, $format, $engine)
    {
        list($bundleName, $controllerName, $actionName) = $this->parseControllerCommand($controller);

        return $this->createTemplateReference($bundleName, $controllerName, $actionName, $format, $engine);
    }

    private function parseControllerCommand($controller)
    {
        list($className, $method) = $this->extractControllerCallable($controller);

        $bundleName     = $this->bundleLocation->locationFor($className);
        $controllerName = $this->extractControllerName($className);
        $actionName     = $this->extractActionName($method);

        return array($bundleName, $controllerName, $actionName);
    }

    private function createTemplateReference($bundleName, $controllerName, $actionName, $format, $engine)
    {
        return sprintf('%s:%s:%s.%s.%s', $bundleName, $controllerName, $actionName, $format, $engine);
    }

    private function extractControllerCallable($controller)
    {
        if (false === strpos($controller, '::')) {
            $controller = $this->parser->parse($controller);
        }

        return explode('::', $controller, 2);
    }

    private function extractControllerName($className)
    {
        if (!preg_match('/Controller\\\(.+)Controller$/', $className, $matchController)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a controller class (it must be in a "Controller" sub-namespace and the class name must end with "Controller")', $className));
        }

        return $matchController[1];
    }

    private function extractActionName($method)
    {
        if (!preg_match('/^(.+)Action$/', $method, $matchAction)) {
            throw new \InvalidArgumentException(sprintf('The "%s" method does not look like an action method (it does not end with Action)', $method));
        }

        return $matchAction[1];
    }
}

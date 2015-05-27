<?php

/**
 * Base class which provides a convenience property to retrieve the service container.
 */
abstract class DartsGame_Controller_AbstractController extends Zend_Controller_Action
{
    /**
     * @var Pimple\Container
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Make the container available to all subclassing controllers.
        $this->container = $this->getInvokeArg('bootstrap')->getResource('container');
    }
}

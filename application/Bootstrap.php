<?php

use Pimple\Container;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initializes the DI container.
     *
     * @return Container
     */
    protected function _initContainer()
    {
        $container = new Container;

        $container['session'] = function ($container) {
            return new DartsGame_Service_Session($container['playersTable']);
        };

		$container['sorting'] = function () {
            return new  DartsGame_Service_Sorting();
        };
		
        $container['turnFactory'] = function () {
            return new  DartsGame_Service_TurnFactory();
        };

        $container['scoreBoard'] = function ($container) {
            return new DartsGame_Service_ScoreBoard(
                $container['playersTable'],
                $container['session'],
                $container['turnFactory']
            );
        };

        $container['gameManager'] = function ($container) {
            return new DartsGame_Service_GameManager(
                $container['playersTable'],
                $container['scoreBoard'],
                $container['session'],
                $container['sorting']
            );
        };

        $container['playersTable'] = function () {
            return new DartsGame_Model_Table_Players();
        };

        return $container;
    }
}

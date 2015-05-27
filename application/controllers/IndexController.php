<?php

/**
 * Entry point for application users.
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * Shows the list of players about to start the tournament.
     */
    public function indexAction()
    {
        // If there's a game in progress, redirect to the turn action.
        $session = new DartsGame_Service_Session(); // Bad
        if ($session->getGame()) {
            $this->redirect('game/turn');
        } else {
            $playersTable = new DartsGame_Model_Table_Players(); // Bad
            $this->view->players = $playersTable->fetchAll();
        }
    }
}

<?php

/**
 * Entry point for application users.
 */
class IndexController extends DartsGame_Controller_AbstractController
{
    /**
     * Shows the list of players about to start the tournament.
     */
    public function indexAction()
    {
        /** @var DartsGame_Service_Session $session */
        $session = $this->container['session'];
        // If there's a game in progress, redirect to the turn action.
        if ($session->getGame()) {
            $this->redirect('game/turn');
        } else {
            /** @var DartsGame_Model_Repository_PlayersInterface $playersTable */
            $playersTable = $this->container['playersTable'];
            $this->view->players = $playersTable->fetchAll();
        }
    }
}

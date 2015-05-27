<?php

/**
 * Handles the game flow.
 */
class GameController extends Zend_Controller_Action
{
    /**
     * Allows to start a new game using the existing players.
     */
    public function startAction()
    {
        $session = new DartsGame_Service_Session(); // Bad

        // Make sure a game is not in progress before allowing the user to start a new one.
        if ($this->getRequest()->isGet() && !$session->getGame()) {
            $gameManager = new DartsGame_Service_GameManager(); // Bad
            $gameManager->start();
            $this->redirect('game/turn');
        } else {
            $this->redirect('/');
        }
    }

    /**
     * Manages the current turn.
     */
    public function turnAction()
    {
        $session = new DartsGame_Service_Session(); // Bad
        $scoreBoard = new DartsGame_Service_ScoreBoard(); // Bad

        $game = $session->getGame();
        if (!$game) {
            $this->redirect('/');
        }

        $form = new DartsGame_Form_Turn();
        $params = $this->getRequest()->getParams();
        $form->populate($params);
        if ($this->getRequest()->isPost() && $form->isValid($params)) {
            $scoreBoard->registerScores(
                $game->getCurrentPlayer(),
                array_values($params['scores']),
                array_values($params['multipliers'])
            );

            $gameManager = new DartsGame_Service_GameManager(); // Bad
            if ($gameManager->advance()) {
                $this->redirect('game/turn');
            } else {
                $this->redirect('game/winner');
            }
        } else {
            // Print current standings to the view
            $playersTable = new DartsGame_Model_Table_Players(); // Bad
            $this->view->form = $form;
            $this->view->game = $session->getGame();
            $this->view->players = $playersTable->fetchAllAsArray();
            $this->view->scoreBoard = $scoreBoard;
        }
    }

    /**
     * Shows the game winner.
     */
    public function winnerAction()
    {
        $session = new DartsGame_Service_Session(); // Bad
        $gameManager = new DartsGame_Service_GameManager(); // Bad

        $this->view->game = $session->getGame();
        $this->view->winner = $gameManager->getWinner();
    }

    /**
     * Ends the game.
     */
    public function endAction()
    {
        $gameManager = new DartsGame_Service_GameManager(); // Bad
        $gameManager->end();
        $this->redirect('/');
    }

    /**
     * Resets the game.
     */
    public function resetAction()
    {
        $session = new DartsGame_Service_Session(); // Bad
        $session->reset();
        $this->redirect('/');
    }
}

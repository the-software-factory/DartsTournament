<?php

/**
 * Handles the game flow.
 */
class GameController extends DartsGame_Controller_AbstractController
{
    /**
     * @var DartsGame_Service_GameManagerInterface
     */
    private $gameManager;

    /**
     * @var DartsGame_Model_Repository_PlayersInterface
     */
    private $playersTable;

    /**
     * @var DartsGame_Service_ScoreBoardInterface
     */
    private $scoreBoard;

    /**
     * @var DartsGame_Service_SessionInterface
     */
    private $session;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // NOTE: Zend1 does not easily allow to use dependency injection on controllers, so we just "simulate" it.
        $this->gameManager = $this->container['gameManager'];
        $this->playersTable = $this->container['playersTable'];
        $this->scoreBoard = $this->container['scoreBoard'];
        $this->session = $this->container['session'];
    }

    /**
     * Allows to start a new game using the existing players.
     */
    public function startAction()
    {
        // Make sure a game is not in progress before allowing the user to start a new one.
        if ($this->getRequest()->isGet() && !$this->session->getGame()) {
            $this->gameManager->start();
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
        $game = $this->session->getGame();
        if (!$game) {
            $this->redirect('/');
        }

        $form = new DartsGame_Form_Turn();
        $params = $this->getRequest()->getParams();
        $form->populate($params);
        if ($this->getRequest()->isPost() && $form->isValid($params)) {
            $this->scoreBoard->registerScores(
                $game->getCurrentPlayer(),
                array_values($params['scores']),
                array_values($params['multipliers'])
            );
            if ($this->gameManager->advance()) {
                $this->redirect('game/turn');
            } else {
                $this->redirect('game/winner');
            }
        } else {
            // Print current standings to the view
            $this->view->form = $form;
            $this->view->game = $this->session->getGame();
            $this->view->players = $this->playersTable->findAll();
            $this->view->scoreBoard = $this->scoreBoard;
        }
    }

    /**
     * Shows the game winner.
     */
    public function winnerAction()
    {
        $this->view->game = $this->session->getGame();
        $this->view->winner = $this->gameManager->getWinner();
    }

    /**
     * Ends the game.
     */
    public function endAction()
    {
        $this->gameManager->end();
        $this->redirect('/');
    }

    /**
     * Resets the game.
     */
    public function resetAction()
    {
        $this->session->reset();
        $this->redirect('/');
    }
}

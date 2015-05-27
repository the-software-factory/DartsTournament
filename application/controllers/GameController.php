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
            // Create a new game and store it in session
            $game = new DartsGame_Model_Game();
            $session = new DartsGame_Service_Session(); // Bad
            $session->setGame($game);
            $this->advanceGame();

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

        $game = $session->getGame();
        if (!$game) {
            $this->redirect('/');
        }

        $form = new DartsGame_Form_Turn();
        $params = $this->getRequest()->getParams();
        $form->populate($params);
        if ($this->getRequest()->isPost() && $form->isValid($params)) {
            $this->registerScores(
                $game->getCurrentPlayer(),
                array_values($params['scores']),
                array_values($params['multipliers'])
            );

            if ($this->advanceGame()) {
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
        }
    }

    /**
     * Shows the game winner.
     */
    public function winnerAction()
    {
        $session = new DartsGame_Service_Session(); // Bad

        $this->view->game = $session->getGame();
        $this->view->winner = $this->getWinner();
    }

    /**
     * Ends the game.
     */
    public function endAction()
    {
        $winner = $this->getWinner();
        if (!$this->getWinner()) {
            throw new LogicException("Can't end a game without a winner.");
        }

        $winner->incrementGamesWon();
        $winner->save();

        $session = new DartsGame_Service_Session(); // Bad
        $session->reset();

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

    /**
     * Advances the status of the game, by setting the next player as next. If necessary, also increments the turn.
     *
     * @return bool TRUE if the game state advanced correctly, FALSE if it couldn't advance because somebody won.
     */
    private function advanceGame()
    {
        // First, check whether the current player won.
        if ($this->getWinner()) {
            return false;
        }

        // We just follow an alphabetic ordering (by first name).
        $playersTable = new DartsGame_Model_Table_Players(); // Bad
        $sortedPlayers = $playersTable->fetchAllAsArray();
        usort($sortedPlayers, function (DartsGame_Model_Player $player1, DartsGame_Model_Player $player2) {
            return strcasecmp($player1->getFullName(), $player2->getFullName());
        });

        // If the current player is not the last in the turn, return the next one. Otherwise return null.
        $session = new DartsGame_Service_Session(); // Bad
        $game = $session->getGame();
        $currentPlayerIndex = array_search($game->getCurrentPlayer(), $sortedPlayers);

        if ($currentPlayerIndex === count($sortedPlayers) - 1 || $currentPlayerIndex === false) {
            $game->incrementCurrentTurnNumber();
            $game->setCurrentPlayer($sortedPlayers[0]);
        } else {
            $game->setCurrentPlayer($sortedPlayers[$currentPlayerIndex + 1]);
        }

        return true;
    }

    /**
     * If the game is over, returns the winner. Otherwise, returns null.
     *
     * @return DartsGame_Model_Player|null
     */
    private function getWinner()
    {
        $session = new DartsGame_Service_Session(); // Bad
        $player = $this->getPlayerInPosition(1);
        if ($session->getGame()->getScoreForPlayer($player) === 0) {
            return $player;
        }

        return null;
    }

    /**
     * Builds the player ranking.
     *
     * @return array An array of player IDs, ordered following the ranking.
     */
    private function buildRank()
    {
        $rank = array();
        $session = new DartsGame_Service_Session(); // Bad
        $playersTable = new DartsGame_Model_Table_Players(); // Bad
        foreach ($playersTable->fetchAllAsArray() as $player) {
            $rank[$player->getID()] = $session->getGame()->getScoreForPlayer($player);
        }
        asort($rank);

        return array_keys($rank);
    }

    /**
     * Returns the player in the given position. NOTE: position is 1-based, so the winner would have position 1.
     *
     * @param int $position
     * @return DartsGame_Model_Player
     * @throws Exception When passing an invalid scoreboard position.
     */
    private function getPlayerInPosition($position)
    {
        $rank = $this->buildRank();
        if (!array_key_exists($position-1, $rank)) {
            throw new Exception('Invalid scoreboard position.');
        }

        $playersTable = new DartsGame_Model_Table_Players(); // Bad
        return $playersTable->findByID($rank[$position-1]);
    }

    /**
     * Register scores for the given player.
     *
     * @param DartsGame_Model_Player $player
     * @param int[] $baseScores
     * @param int[] $multipliers
     */
    private function registerScores(DartsGame_Model_Player $player, array $baseScores, array $multipliers)
    {
        // Validate input
        // Exactly 3 multipliers should be provided
        if (count($multipliers) !== 3) {
            throw new InvalidArgumentException("Exactly 3 multipliers should be provided. " . count($multipliers) . " given [" . implode(', ', $multipliers) . "]");
        }

        // Constraint: 0 <= multiplier <= 3
        foreach ($multipliers as $multiplier) {
            if ($multiplier > 3
                || $multiplier <= 0) {
                throw new InvalidArgumentException("Invalid multiplier: $multiplier. Multiplier values should be > 0 and <= 3.");
            }
        }

        // Exactly 3 scores should be provided
        if (count($baseScores) !== 3) {
            throw new InvalidArgumentException("Exactly 3 scores should be provided. " . count($baseScores) . " given [" . implode(', ', $baseScores) . "]");
        }

        for ($scoreIndex = 0; $scoreIndex < 3; $scoreIndex++) {
            $score = $baseScores[$scoreIndex];

            // Score cannot be negative
            if ($score < 0) {
                throw new InvalidArgumentException("Invalid score: $score. Scores cannot be negative.");
            }
            else if ($score > 20
                && $score != 25
                && $score != 50) {
                throw new InvalidArgumentException("Invalid score: $score. Scores accepted values are <= 20, 25 or 50.");
            }

            // Can only multiply scores <= 20
            if ($multipliers[$scoreIndex] > 1
                && $score > 20) {
                throw new InvalidArgumentException("Multipliers can be applied only to scores <= 20. Score: $score, Multiplier: {$multipliers[$scoreIndex]}.");
            }
        }

        $session = new DartsGame_Service_Session(); // Bad
        $game = $session->getGame();

        // Create a new turn and store scores
        $turn = new DartsGame_Model_Turn($player, $game->getCurrentTurnNumber()); // Bad

        for ($i = 0; $i < count($baseScores); $i++) {
            if ($baseScores[$i] !== null) {
                // Note: shots are 1-based.
                $turn->setScoreForShot($i + 1, $baseScores[$i], $multipliers[$i]);
            }
        }
        $game->addTurn($turn);
    }
}

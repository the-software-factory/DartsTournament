<?php

/**
 * Orchestrates the game flow.
 */
class DartsGame_Service_GameManager
{
    /**
     * Starts a new game.
     */
    public function start()
    {
        // Create a new game and store it in session
        $game = new DartsGame_Model_Game();
        $session = new DartsGame_Service_Session(); // Bad
        $session->setGame($game);
        $this->advance();

        return $game;
    }

    /**
     * Ends a game and registers the winning. Can only be called when there's a winner.
     *
     * @throws LogicException When called without a winner.
     */
    public function end()
    {
        $winner = $this->getWinner();
        if (!$this->getWinner()) {
            throw new LogicException("Can't end a game without a winner.");
        }

        $winner->incrementGamesWon();
        $winner->save();

        $session = new DartsGame_Service_Session(); // Bad
        $session->reset();
    }

    /**
     * Advances the status of the game, by setting the next player as next. If necessary, also increments the turn.
     *
     * @return bool TRUE if the game state advanced correctly, FALSE if it couldn't advance because somebody won.
     */
    public function advance()
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
    public function getWinner()
    {
        $scoreBoard = new DartsGame_Service_ScoreBoard(); // Bad
        $player = $scoreBoard->getPlayerInPosition(1);
        if ($scoreBoard->getScoreForPlayer($player) === 0) {
            return $player;
        }

        return null;
    }
}

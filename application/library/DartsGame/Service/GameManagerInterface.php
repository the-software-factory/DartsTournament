<?php

/**
 * Orchestrates the game flow.
 */
interface DartsGame_Service_GameManagerInterface
{
    /**
     * Starts a new game.
     */
    public function start();

    /**
     * Ends a game and registers the winning. Can only be called when there's a winner.
     *
     * @throws LogicException When called without a winner.
     */
    public function end();

    /**
     * Advances the status of the game, by setting the next player as next. If necessary, also increments the turn.
     *
     * @return bool TRUE if the game state advanced correctly, FALSE if it couldn't advance because somebody won.
     */
    public function advance();

    /**
     * If the game is over, returns the winner. Otherwise, returns null.
     *
     * @return DartsGame_Model_Player|null
     */
    public function getWinner();
}

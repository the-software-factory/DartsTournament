<?php

/**
 * Handles session state related to a game.
 */
interface DartsGame_Service_SessionInterface
{
    /**
     * Returns the current game.
     *
     * @return DartsGame_Model_Game
     */
    public function getGame();

    /**
     * Sets a new game in session.
     *
     * @param DartsGame_Model_Game $game
     */
    public function setGame(DartsGame_Model_Game $game);

    /**
     * Resets the current session state.
     */
    public function reset();
}

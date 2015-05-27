<?php

/**
 * Handles session state related to a game.
 */
class DartsGame_Service_Session extends Zend_Session_Namespace
{
    const SESSION_NAMESPACE = "darts-game";

    /**
     * Construct.
     */
    public function __construct()
    {
        parent::__construct(self::SESSION_NAMESPACE);
    }

    /**
     * Returns the current game.
     *
     * @return DartsGame_Model_Game
     */
    public function getGame()
    {
        // Since the player is stored in session, we need to reconnect it to its table.
        if ($this->game && $this->game->getCurrentPlayer() && !$this->game->getCurrentPlayer()->isConnected()) {
            $this->game->getCurrentPlayer()->setTable(new DartsGame_Model_Table_Players()); // Bad
        }

        return $this->game;
    }

    /**
     * Sets a new game in session.
     *
     * @param DartsGame_Model_Game $game
     */
    public function setGame(DartsGame_Model_Game $game)
    {
        $this->game = $game;
    }

    /**
     * Resets the current session state.
     */
    public function reset()
    {
        $this->resetSingleInstance(self::SESSION_NAMESPACE);
        $this->game = null;
    }
}

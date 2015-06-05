<?php

/**
 * Represents a single dart game.
 */
class DartsGame_Model_Game
{
    /**
     * @var array
     */
    private $turnsByPlayerID = array();

    /**
     * @var int
     */
    private $currentTurnNumber = 0;

    /**
     * @var DartsGame_Model_Player
     */
    private $currentPlayer;
	
	/**
	 * @var That variable determines the type of game
	 */
	private $playOff = false;
	
	/**
	 * @param Boolean that determines whether the playoff is active or not
	 */
	public function setPlayOff($bool)
	{
		$this->playOff = $bool;
	}

	/**
	 * @return The type of game
	 */
	 public function getPlayOff()
	{
		return $this->playOff;
	}

    /**
     * @param DartsGame_Model_Turn $turn
     */
    public function addTurn(DartsGame_Model_Turn $turn)
    {
        if (!array_key_exists($turn->getPlayer()->getID(), $this->turnsByPlayerID)) {
            $this->turnsByPlayerID[$turn->getPlayer()->getID()] = array();
        }
        $this->turnsByPlayerID[$turn->getPlayer()->getID()][] = $turn;
    }

    /**
     * Returns the current turn number.
     *
     * @return int
     */
    public function getCurrentTurnNumber()
    {
        return $this->currentTurnNumber;
    }

    /**
     * Increments the current turn number.
     */
    public function incrementCurrentTurnNumber()
    {
        $this->currentTurnNumber++;
    }

    /**
     * Returns the player who is playing in the current game.
     *
     * @return DartsGame_Model_Player
     */
    public function getCurrentPlayer()
    {
        return $this->currentPlayer;
    }

    /**
     * Sets the player who is playing in the current game.
     *
     * @param DartsGame_Model_Player $player
     */
    public function setCurrentPlayer(DartsGame_Model_Player $player)
    {
        $this->currentPlayer = $player;
    }

    /**
     * Returns all the turns for a given player.
     *
     * @param DartsGame_Model_Player $player
     * @return DartsGame_Model_Turn[]
     */
    public function getTurnsForPlayer(DartsGame_Model_Player $player)
    {
        if (array_key_exists($player->getID(), $this->turnsByPlayerID)) {
            return $this->turnsByPlayerID[$player->getID()];
        }

        return array();
    }
}

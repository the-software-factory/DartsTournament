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
	private $isPlayOff = false;
	
	/**
     * @var DartsGame_Service_TurnFactoryInterface
     */
    private $bestScore = 0;
	
	
	/**
	 * @param int score to be checked
	 * @return true if the score is greater than the best score
	 */
	public function isBestScore($score)
	{
		if($score > $this->bestScore){
			$this->bestScore = $score;
			return true;
		} 
		return false;
	}
	
	/**
	 * @param int score to be checked
	 * @return true if the score is lower than the best score
	 */
	public function isWorstScore($score)
	{
		if($score < $this->bestScore){
			return true;
		} 
		return false;
	}
	
	/**
	 * @return int the best score
	 */
	public function getBestScore()
	{
		return $this->bestScore;
	}
	
	/**
	 * @param Boolean that determines whether the playoff is active or not
	 */
	public function setIsPlayOff($bool)
	{
		$this->isPlayOff = $bool;
	}

	/**
	 * @return The type of game
	 */
	 public function getIsPlayOff()
	{
		return $this->isPlayOff;
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

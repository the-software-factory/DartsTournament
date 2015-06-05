<?php

/**
 * Handles and stores players' scores through a game.
 */
interface DartsGame_Service_ScoreBoardInterface
{
	/**
	 * @param int score to be checked
	 * @return true if the score is greater than the best score
	 */
	public function isBestScore($score);
	
	/**
	 * @return int the best score
	 */
	public function getBestScore();
	
    /**
     * Returns the player in the given position. NOTE: position is 1-based, so the winner would have position 1.
     *
     * @param int $position
     * @return DartsGame_Model_Player
     */
    public function getPlayerInPosition($position);

    /**
     * Returns the player in the given position.
     *
     * @param DartsGame_Model_Player $player
     * @return int
     */
    public function getScoreForPlayer(DartsGame_Model_Player $player);

    /**
     * Register scores for the given player.
     *
     * @param DartsGame_Model_Player $player
     * @param int[] $baseScores
     * @param int[] $multipliers
     */
    public function registerScores(DartsGame_Model_Player $player, array $baseScores, array $multipliers);
}

<?php

/**
 * Handles and stores players' scores through a game.
 */
class DartsGame_Service_ScoreBoard
{
    /**
     * The score at which every match starts.
     */
    const STARTING_SCORE = 501;

    /**
     * Returns the player in the given position. NOTE: position is 1-based, so the winner would have position 1.
     *
     * @param int $position
     * @return DartsGame_Model_Player
     * @throws Exception When passing an invalid scoreboard position.
     */
    public function getPlayerInPosition($position)
    {
        $rank = $this->buildRank();
        if (!array_key_exists($position-1, $rank)) {
            throw new Exception('Invalid scoreboard position.');
        }

        $playersTable = new DartsGame_Model_Table_Players(); // Bad
        return $playersTable->findByID($rank[$position-1]);
    }

    /**
     * Returns the player in the given position.
     *
     * @param DartsGame_Model_Player $player
     * @return int
     */
    public function getScoreForPlayer(DartsGame_Model_Player $player)
    {
        $session = new DartsGame_Service_Session(); // Bad
        $turns = $session->getGame()->getTurnsForPlayer($player);
        $grandTotal = self::STARTING_SCORE;
        foreach ($turns as $turn) {
            $turnScore = $turn->getTotalScore();
            if (!$turn->isBust($grandTotal)) {
                $grandTotal -= $turnScore;
            }
        }

        return $grandTotal;
    }

    /**
     * Register scores for the given player.
     *
     * @param DartsGame_Model_Player $player
     * @param int[] $baseScores
     * @param int[] $multipliers
     */
    public function registerScores(DartsGame_Model_Player $player, array $baseScores, array $multipliers)
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

    /**
     * Builds the player ranking.
     *
     * @return array An array of player IDs, ordered following the ranking.
     */
    protected function buildRank()
    {
        $rank = array();
        $playersTable = new DartsGame_Model_Table_Players(); // Bad
        foreach ($playersTable->fetchAllAsArray() as $player) {
            $rank[$player->getID()] = $this->getScoreForPlayer($player);
        }
        asort($rank);

        return array_keys($rank);
    }
}

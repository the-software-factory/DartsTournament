<?php

/**
 * Represents a single turn of a dart game, for a single player.
 */
class DartsGame_Model_Turn
{
    /**
     * @var DartsGame_Model_Player
     */
    private $player;

    /**
     * @var int
     */
    private $number;

    /**
     * @var int[]
     */
    private $baseScores = array();

    /**
     * @var int[]
     */
    private $multipliers = array();

    /**
     * @param DartsGame_Model_Player $player
     * @param $number
     */
    public function __construct(DartsGame_Model_Player $player, $number)
    {
        $this->player = $player;
        $this->number = $number;
    }

    /**
     * Returns the player related to this turn.
     *
     * @return DartsGame_Model_Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Returns the turn number.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $shot
     * @param int $baseScore
     * @param int $multiplier
     */
    public function setScoreForShot($shot, $baseScore, $multiplier)
    {
        $this->validateShotNumber($shot);
        if (!in_array($multiplier, array(1, 2, 3))) {
            throw new InvalidArgumentException("Multiplier can only be 1, 2 or 3.");
        }

        $this->baseScores[$shot - 1] = $baseScore;
        $this->multipliers[$shot - 1] = $multiplier;
    }

    /**
     * Throws an exception if the shot number is not valid.
     *
     * @param int $shot
     * @throws Exception If shot
     */
    private function validateShotNumber($shot)
    {
        if (!in_array($shot, array(1, 2, 3))) {
            throw new InvalidArgumentException("Shot can only be 1, 2 or 3.");
        }
    }

    /**
     * Returns true if the player busted the current turn.
     * A turn is considered busted if either:
     * - subtracting the current turn value  would make the player's score negative, or
     * - the player would reach zero with this turn, but he did not score a double multiplier with its last shot,
     *
     * @param int $currentScore
     * @return bool
     */
    public function isBust($currentScore)
    {
        $nextScore = $currentScore - $this->getTotalScore();
        // If the resulting next score was negative, the player busted.
        if ($nextScore < 0) {
            return true;
        } else {
            // If the resulting next score was zero, then we need to check whether the last shot was a double
            if ($nextScore == 0) {
                $lastShot = -1;
                // Search the last non-zero shot.
                for ($shotNumber = 1; $shotNumber <= 3; $shotNumber++) {
                    if ($this->getBaseScoreForShot($shotNumber) > 0) {
                        $lastShot = $shotNumber;
                    }
                }

                // If the last non-zero shot does not have a double multiplier, than the player busted.
                if ($this->getMultiplierForShot($lastShot) != 2 && !$this->getBaseScoreForShot($lastShot) == 50) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the player's score in this turn.
     *
     * @return int
     */
    public function getTotalScore()
    {
        $score = 0;
        for ($i = 1; $i <= 3; $i++) {
            $score += $this->getBaseScoreForShot($i) * $this->getMultiplierForShot($i);
        }

        return $score;
    }

    /**
     * Returns the base score for the given shot (to be multiplied).
     *
     * @param int $shot
     * @return int
     */
    public function getBaseScoreForShot($shot)
    {
        $this->validateShotNumber($shot);

        return $this->baseScores[$shot - 1];
    }

    /**
     * Returns the multiplier for the given shot.
     *
     * @param int $shot
     * @return int
     */
    public function getMultiplierForShot($shot)
    {
        $this->validateShotNumber($shot);

        return $this->multipliers[$shot - 1];
    }
}

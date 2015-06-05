<?php

/**
 * {@inheritdoc}
 */
class DartsGame_Service_ScoreBoard implements DartsGame_Service_ScoreBoardInterface
{
    /**
     * The score at which every match starts.
     */
    const STARTING_SCORE = 501;

    /**
     * @var DartsGame_Model_Repository_PlayersInterface
     */
    private $playersRepository;

    /**
     * @var DartsGame_Service_SessionInterface
     */
    private $session;

    /**
     * @var DartsGame_Service_TurnFactoryInterface
     */
    private $turnFactory;

    /**
     * @param DartsGame_Model_Repository_PlayersInterface $playersRepository
     * @param DartsGame_Service_SessionInterface $session
     * @param DartsGame_Service_TurnFactoryInterface $turnFactory
     */
    public function __construct(
        DartsGame_Model_Repository_PlayersInterface $playersRepository,
        DartsGame_Service_SessionInterface $session,
        DartsGame_Service_TurnFactoryInterface $turnFactory
    ) {
        $this->playersRepository = $playersRepository;
        $this->session = $session;
        $this->turnFactory = $turnFactory;
		$this->bestScore = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayerInPosition($position)
    {
        $rank = $this->buildRank();
        if (!array_key_exists($position - 1, $rank)) {
            throw new Exception('Invalid scoreboard position.');
        }

        return $this->playersRepository->findByID($rank[$position - 1]);
    }

    /**
     * Builds the player ranking.
     *
     * @return array An array of player IDs, ordered following the ranking.
     */
    protected function buildRank()
    {
        $rank = array();
        foreach ($this->playersRepository->findAll() as $player) {
            $rank[$player->getID()] = $this->getScoreForPlayer($player);
        }
        asort($rank);

        return array_keys($rank);
    }

    /**
     * {@inheritdoc}
     */
    public function getScoreForPlayer(DartsGame_Model_Player $player)
    {
        $turns = $this->session->getGame()->getTurnsForPlayer($player);
        $grandTotal = self::STARTING_SCORE;
		$flag = false;
        foreach ($turns as $turn) {
            $turnScore = $turn->getTotalScore();
			if($flag && $player->getPlayOff()){
            	$grandTotal += $turnScore;
			} else {
				if (!$turn->isBust($grandTotal)) {
                	$grandTotal -= $turnScore;
					if ($grandTotal === 0){
						$flag=true;
					}
            	}
			}
        }

        return $grandTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function registerScores(DartsGame_Model_Player $player, array $baseScores, array $multipliers)
    {
        // Validating input
        // Exactly 3 multipliers should be provided
        if (count($multipliers) !== 3) {
            throw new InvalidArgumentException("Exactly 3 multipliers should be provided. " . count($multipliers) . " given [" . implode(', ',
                    $multipliers) . "]");
        }

        // Constraint: 0 <= multiplier <= 3
        foreach ($multipliers as $multiplier) {
            if ($multiplier > 3 || $multiplier <= 0) {
                throw new InvalidArgumentException("Invalid multiplier: $multiplier. Multiplier values should be > 0 and <= 3.");
            }
        }

        // Exactly 3 scores should be provided
        if (count($baseScores) !== 3) {
            throw new InvalidArgumentException("Exactly 3 scores should be provided. " . count($baseScores) . " given [" . implode(', ',
                    $baseScores) . "]");
        }

        for ($scoreIndex = 0; $scoreIndex < 3; $scoreIndex++) {
            $score = $baseScores[$scoreIndex];

            // Score cannot be negative
            if ($score < 0) {
                throw new InvalidArgumentException("Invalid score: $score. Scores cannot be negative.");
            } elseif ($score > 20 && $score != 25 && $score != 50) {
                throw new InvalidArgumentException("Invalid score: $score. Scores accepted values are <= 20, 25 or 50.");
            }

            // Can only multiply scores <= 20
            if ($multipliers[$scoreIndex] > 1 && $score > 20) {
                throw new InvalidArgumentException("Multipliers can be applied only to scores <= 20. Score: $score, Multiplier: {$multipliers[$scoreIndex]}.");
            }
        }

        $game = $this->session->getGame();

        // Create a new turn and store scores
        $turn = $this->turnFactory->create($player, $game->getCurrentTurnNumber());

        for ($i = 0; $i < count($baseScores); $i++) {
            if ($baseScores[$i] !== null) {
                // Note: shots are 1-based.
                $turn->setScoreForShot($i + 1, $baseScores[$i], $multipliers[$i]);
            }
        }
        $game->addTurn($turn);
    }
}

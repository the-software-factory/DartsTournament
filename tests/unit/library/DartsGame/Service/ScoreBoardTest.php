<?php

require_once(__DIR__ . '/../Dummy/Session.php');
require_once(__DIR__ . '/../Dummy/TurnFactory.php');

/**
 * Tests for TurnManager
 */
class DartsGame_Service_ScoreBoardTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing that exceptions are correctly thrown if invalid scores or multipliers are provided
     *
     * @dataProvider invalidScoresDataProvider
     * @covers       DartsGame_Service_ScoreBoard::registerScores
     *
     * @param array $baseScores
     * @param array $multipliers
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     */
    public function testInvalidScoresRegistration(
        $baseScores,
        $multipliers,
        $expectedExceptionClass,
        $expectedExceptionMessage
    ) {
        /**
         * DUMMIES
         */
        $dummySession = new DartsGame_Dummy_Session();
        //                            ^^^^^
        $dummyTurnFactory = new DartsGame_Dummy_TurnFactory();
        //                                ^^^^^
        $dummyPlayersTable = new DartsGame_Dummy_Repository_Players();
        //                                 ^^^^^

        /**
         * At this point, the test will fail if no Exception is risen.
         */
        $this->setExpectedException($expectedExceptionClass, $expectedExceptionMessage);

        /**
         * This is the actual system under test
         */
        $sut = new DartsGame_Service_ScoreBoard($dummyPlayersTable, $dummySession, $dummyTurnFactory);
        $player = new DartsGame_Model_Player();
        $sut->registerScores($player, $baseScores, $multipliers);
    }

    /**
     * Data provider for valid scores.
     *
     * array(
     *      array(<SCORE_FIRST_SHOT>,<SCORE_second_SHOT>,<SCORE_THIRD_SHOT>, ...),
     *      array(<MULTIPLIER_FIRST_SHOT>,<MULTIPLIER_second_SHOT>,<MULTIPLIER_THIRD_SHOT>, ...),
     *      <EXPECTED_EXCEPTION_CLASS>,
     *      <EXPECTED_EXCEPTION_MESSAGE>,
     *
     * @return array
     */
    public function invalidScoresDataProvider()
    {
        return array(

            // Not more than 3 multipliers
            array(
                array(
                    1,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Exactly 3 multipliers should be provided. 4 given [1, 1, 1, 1]',
            ),

            // Not less than 3 multipliers
            array(
                array(
                    1,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Exactly 3 multipliers should be provided. 2 given [1, 1]',
            ),

            // A multiplier cannot be zero
            array(
                array(
                    1,
                    2,
                    3,
                ),
                array(
                    1,
                    0,
                    1,
                ),
                'InvalidArgumentException',
                'Invalid multiplier: 0. Multiplier values should be > 0 and <= 3.',
            ),

            // A multiplier cannot be negative
            array(
                array(
                    1,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    -1,
                ),
                'InvalidArgumentException',
                'Invalid multiplier: -1. Multiplier values should be > 0 and <= 3.',
            ),

            // A multiplier cannot be greater than 3
            array(
                array(
                    1,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    4,
                ),
                'InvalidArgumentException',
                'Invalid multiplier: 4. Multiplier values should be > 0 and <= 3.',
            ),

            // Not more than 3 scores
            array(
                array(
                    1,
                    2,
                    3,
                    1,
                ),
                array(
                    1,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Exactly 3 scores should be provided. 4 given [1, 2, 3, 1]',
            ),

            // Not less than 3 scores
            array(
                array(
                    1,
                    2,
                ),
                array(
                    1,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Exactly 3 scores should be provided. 2 given [1, 2]',
            ),

            // A score cannot be negative
            array(
                array(
                    -1,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Invalid score: -1. Scores cannot be negative.',
            ),

            // A single score cannot be larger that 50
            array(
                array(
                    51,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Invalid score: 51. Scores accepted values are <= 20, 25 or 50.',
            ),

            // A single score cannot be > 20 and != 25 or 50
            array(
                array(
                    33,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Invalid score: 33. Scores accepted values are <= 20, 25 or 50.',
            ),

            // A single score cannot be larger that 20 if a multiplier > 1 is provided
            array(
                array(
                    25,
                    2,
                    3,
                ),
                array(
                    2,
                    1,
                    1,
                ),
                'InvalidArgumentException',
                'Multipliers can be applied only to scores <= 20. Score: 25, Multiplier: 2.',
            ),
        );
    }

    /**
     * Testing that scores have been registered correctly
     *
     * @dataProvider validScoresDataProvider
     * @covers       DartsGame_Service_ScoreBoard::registerScores
     *
     * @param array $baseScores
     * @param array $multipliers
     */
    public function testValidScoresRegistration($baseScores, $multipliers)
    {
        // Creates a player and table accessor
        $player = new DartsGame_Model_Player(array('data' => array('id' => 1)));
        $dummyPlayersTable = new DartsGame_Dummy_Repository_Players();

        /**
         * MOCKS
         */
        $mockTurn = $this->getMockBuilder('DartsGame_Model_Turn')
            ->setConstructorArgs(array($player, 1))
            ->getMock();
        $mockTurn
            ->expects($this->exactly(3))
            ->method('setScoreForShot')
            ->withConsecutive(
                array($this->equalTo(1), $this->equalTo($baseScores[0]), $this->equalTo($multipliers[0])),
                array($this->equalTo(2), $this->equalTo($baseScores[1]), $this->equalTo($multipliers[1])),
                array($this->equalTo(3), $this->equalTo($baseScores[2]), $this->equalTo($multipliers[2])));

        $mockGame = $this->getMockBuilder('DartsGame_Model_Game')
            ->getMock();
        $mockGame
            ->expects($this->once())
            ->method('addTurn')
            ->with($mockTurn);

        // Stubs
        $stubSession = new ScoreBoardTestStubSession($mockGame);
        $stubTurnFactory = new ScoreBoardTestStubTurnFactory($mockTurn);

        $sut = new DartsGame_Service_ScoreBoard($dummyPlayersTable, $stubSession, $stubTurnFactory);

        // We are not asserting any expected value here, we just want to make sure that
        // 1. three shots are registered with the expected values
        // 2. a turn is added to the game upon score registration
        $sut->registerScores($player, $baseScores, $multipliers);
    }

    /**
     * Data provider for valid scores.
     *
     * array(
     *      array(<SCORE_FIRST_SHOT>,<SCORE_second_SHOT>,<SCORE_THIRD_SHOT>),
     *      array(<MULTIPLIER_FIRST_SHOT>,<MULTIPLIER_second_SHOT>,<MULTIPLIER_THIRD_SHOT>),
     *      <TURN_FINAL_SCORE>, <= int
     *      <INITIAL_SCORE>,    <= int
     *      <EXPECTED RESULT>,  <= bool
     *
     * @return array
     */
    public function validScoresDataProvider()
    {

        return array(

            // Positive Scores: no multipliers
            array(
                array(
                    1,
                    2,
                    3,
                ),
                array(
                    1,
                    1,
                    1,
                ),
            ),

            // Positive Scores: no multipliers, string should be accepted as well
            array(
                array(
                    '1',
                    '2',
                    '3',
                ),
                array(
                    '1',
                    '1',
                    '1',
                ),
            ),

            // Positive Scores: over 20
            array(
                array(
                    25,
                    50,
                    1,
                ),
                array(
                    1,
                    1,
                    1,
                ),
            ),

            // Zeros Scores: no multipliers
            array(
                array(
                    0,
                    0,
                    0,
                ),
                array(
                    1,
                    1,
                    1,
                ),
            ),

            // One zero: no multipliers
            array(
                array(
                    1,
                    0,
                    3,
                ),
                array(
                    1,
                    1,
                    1,
                ),
            ),

            // Positive Scores with multipliers
            array(
                array(
                    5,
                    6,
                    7,
                ),
                array(
                    1,
                    2,
                    3,
                ),
            ),

            // A zero score with a multiplier applied
            array(
                array(
                    1,
                    0,
                    3,
                ),
                array(
                    1,
                    3,
                    1,
                ),
            ),

            // Closing with a multiplier
            array(
                array(
                    1,
                    0,
                    3,
                ),
                array(
                    1,
                    1,
                    2,
                ),
            ),

            // Closing with a center
            array(
                array(
                    1,
                    0,
                    50,
                ),
                array(
                    1,
                    1,
                    1,
                ),
            ),

            // Closing is not valid without a double or a center
            array(
                array(
                    1,
                    1,
                    2,
                ),
                array(
                    1,
                    1,
                    1,
                ),
            ),

            // The perfect shot (edge case)
            array(
                array(
                    20,
                    20,
                    20,
                ),
                array(
                    3,
                    3,
                    3,
                ),
            ),

        );
    }
}

/**
 * Simple session that always returns the game it is built for.
 */
class ScoreBoardTestStubSession implements DartsGame_Service_SessionInterface {

    /**
     * @var DartsGame_Model_Game
     */
    public $_game;

    /**
     * @param DartsGame_Model_Game $game
     */
    public function __construct(DartsGame_Model_Game $game)
    {
        $this->_game = $game;
    }

    /**
     * {@inheritdoc}
     */
    public function getGame()
    {
        return $this->_game;
    }

    /**
     * {@inheritdoc}
     */
    public function setGame(DartsGame_Model_Game $game)
    {
        // TODO: Implement setGame() method.
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // TODO: Implement reset() method.
    }
}

/**
 * Simple session that always returns the game it is built for.
 */
class ScoreBoardTestStubTurnFactory implements DartsGame_Service_TurnFactoryInterface
{
    /**
     * @var
     */
    public $turn;

    /**
     * @param DartsGame_Model_Turn $turn
     */
    public function __construct(DartsGame_Model_Turn $turn)
    {
        $this->turn = $turn;
    }

    /**
     * {@inheritdoc}
     */
    public function create(DartsGame_Model_Player $player, $number)
    {
        return $this->turn;
    }
}

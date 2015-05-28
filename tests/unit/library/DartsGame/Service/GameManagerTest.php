<?php

require_once(__DIR__ . '/../Dummy/Session.php');
require_once(__DIR__ . '/../Dummy/TurnFactory.php');
require_once(__DIR__ . '/../Dummy/Repository/Players.php');

/**
 * Tests for GameManager
 */
class DartsGame_Service_GameManagerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    /**
     * Testing that a winner is correctly returned in a game.
     *
     * @dataProvider dataProviderGetWinner
     * @covers DartsGame_Service_GameManager::getWinner
     *
     * @param DartsGame_Model_Player $player
     * @param int $playerScore
     * @param DartsGame_Model_Player|null $expectedResult
     */
    public function testGetWinner($player, $playerScore, $expectedResult)
    {
        // Dummy objects used for constructor, but not used during tests.
        $dummySession = new DartsGame_Dummy_Session();
        $dummyTurnFactory = new DartsGame_Dummy_TurnFactory();
        $dummyPlayersTable = new DartsGame_Dummy_Repository_Players();

        // Mocking the score board
        $mockedScoreBoard = $this
            ->getMockBuilder('DartsGame_Service_ScoreBoard')
            ->setConstructorArgs(array($dummyPlayersTable, $dummySession, $dummyTurnFactory))
            ->getMock();

        // Retrieving the player in first position
        $mockedScoreBoard
            ->expects($this->once())
            ->method('getPlayerInPosition')
            ->willReturn($player);

        // Retrieving the score for the player
        $mockedScoreBoard
            ->expects($this->once())
            ->method('getScoreForPlayer')
            ->willReturn($playerScore);

        $gameManager = new DartsGame_Service_GameManager($dummyPlayersTable, $mockedScoreBoard, $dummySession);
        $this->assertEquals($expectedResult, $gameManager->getWinner());
    }

    /**
     * Data provider for winner extraction.
     *
     * array(
     *      <PLAYER>,
     *      <SCORE>,
     *      <EXPECTED WINNER OR NULL>,
     * )
     *
     * @return array
     */
    public function dataProviderGetWinner()
    {
        $player1 = new DartsGame_Model_Player();

        return array(

            // Player 1 ranks first, but it has not closed yet
            array(
                $player1,
                1,
                null
            ),

            // Player 1 ranks first and has closed
            array(
                $player1,
                0,
                $player1
            ),
        );
    }
}

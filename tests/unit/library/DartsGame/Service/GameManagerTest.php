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
     * @param array $playersData
     * @param DartsGame_Model_Player|null $expectedResult
     * @param string $message
     */
    public function testGetWinner($playersData, $expectedResult, $message)
    {
        $playerInFirstPosition = NULL;
        $playerInFirstPositionScore = NULL;
        $getScoreForPlayerMap = array();
        $getTurnsForPlayerMap = array();

        foreach ($playersData as $playerData) {
            $player = $playerData[0];
            $playerScore = $playerData[1];
            $playerTurns = $playerData[2];

            array_push($getScoreForPlayerMap, array($player, $playerScore));
            array_push($getTurnsForPlayerMap, array($player, $playerTurns));

            if (is_null($playerInFirstPositionScore) || $playerScore < $playerInFirstPositionScore){
                $playerInFirstPosition = $player;
                $playerInFirstPositionScore = $playerScore;
            }
        }

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
            ->willReturn($playerInFirstPosition);

        // Retrieving the score for the player
        $mockedScoreBoard
            ->method('getScoreForPlayer')
            ->willReturn($this->returnValueMap($getScoreForPlayerMap));

        // Retrieving the turn for the player
        $mockedScoreBoard
            ->method('getTurnsForPlayer')
            ->willReturn($this->returnValueMap($getTurnsForPlayerMap));

        $gameManager = new DartsGame_Service_GameManager($dummyPlayersTable, $mockedScoreBoard, $dummySession);
        $winner = $gameManager->getWinner();

        $this->assertEquals($expectedResult, $winner, $message);
    }

    /**
     * Data provider for winner extraction.
     *
     * @return array
     */
    public function dataProviderGetWinner()
    {
        $player1 = new DartsGame_Model_Player();
        $player2 = new DartsGame_Model_Player();
        $player3 = new DartsGame_Model_Player();

        return array(

            array(
                array(
                    array($player1, 1, 5),
                    array($player2, 2, 5),
                    array($player3, 3, 5)
                ),
                NULL,
                'The winner should score 0, nobody score 0'
            ),

            array(
                array(
                    array($player1, 0, 6),
                    array($player2, 1, 5),
                    array($player3, 2, 5)
                ),
                NULL,
                'The winner should score 0 with same turns of others, it has one turn more'
            ),

             // TODO null does not match expected type "object"
            /*array(
                array(
                    array($player1, 0, 6),
                    array($player2, 1, 6),
                    array($player3, 2, 6)
                ),
                $player1,
                'Player 1 scores 0 in the same turns of others, it wins'
            )*/
        );
    }
}

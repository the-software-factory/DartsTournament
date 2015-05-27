<?php

/**
 * {@inheritdoc}
 */
class DartsGame_Service_GameManager implements DartsGame_Service_GameManagerInterface
{
    /**
     * @var DartsGame_Model_Repository_PlayersInterface
     */
    private $playersRepository;

    /**
     * @var DartsGame_Service_ScoreBoardInterface
     */
    private $scoreBoard;

    /**
     * @var DartsGame_Service_SessionInterface
     */
    private $session;

    /**
     * @param DartsGame_Model_Repository_PlayersInterface $playersRepository
     * @param DartsGame_Service_ScoreBoardInterface $scoreBoard
     * @param DartsGame_Service_SessionInterface $session
     */
    public function __construct(
        DartsGame_Model_Repository_PlayersInterface $playersRepository,
        DartsGame_Service_ScoreBoardInterface $scoreBoard,
        DartsGame_Service_SessionInterface $session
    ) {
        $this->playersRepository = $playersRepository;
        $this->scoreBoard = $scoreBoard;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        // Create a new game and store it in session
        $game = new DartsGame_Model_Game();
        $this->session->setGame($game);
        $this->advance();

        return $game;
    }

    /**
     * {@inheritdoc}
     */
    public function advance()
    {
        // First, check whether the current player won.
        if ($this->getWinner()) {
            return false;
        }

        // We just follow an alphabetic ordering (by first name).
        $sortedPlayers = $this->playersRepository->findAll();
        usort($sortedPlayers, function (DartsGame_Model_Player $player1, DartsGame_Model_Player $player2) {
            return strcasecmp($player1->getFullName(), $player2->getFullName());
        });

        // If the current player is not the last in the turn, return the next one. Otherwise return null.
        $game = $this->session->getGame();
        $currentPlayerIndex = array_search($game->getCurrentPlayer(), $sortedPlayers);

        if ($currentPlayerIndex === count($sortedPlayers) - 1 || $currentPlayerIndex === false) {
            $game->incrementCurrentTurnNumber();
            $game->setCurrentPlayer($sortedPlayers[0]);
        } else {
            $game->setCurrentPlayer($sortedPlayers[$currentPlayerIndex + 1]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getWinner()
    {
        $player = $this->scoreBoard->getPlayerInPosition(1);
        if ($this->scoreBoard->getScoreForPlayer($player) === 0) {
            return $player;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        $winner = $this->getWinner();
        if (!$this->getWinner()) {
            throw new LogicException("Can't end a game without a winner.");
        }

        $winner->incrementGamesWon();
        $winner->save();

        $this->session->reset();
    }
}

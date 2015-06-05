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
     * @var DartsGame_Service_SortingInterface
     */
    private $sorting;

    /**
     * @param DartsGame_Model_Repository_PlayersInterface $playersRepository
     * @param DartsGame_Service_ScoreBoardInterface $scoreBoard
     * @param DartsGame_Service_SessionInterface $session
     */
    public function __construct(
        DartsGame_Model_Repository_PlayersInterface $playersRepository,
        DartsGame_Service_ScoreBoardInterface $scoreBoard,
        DartsGame_Service_SessionInterface $session,
        DartsGame_Service_SortingInterface $sorting
    ) {
        $this->playersRepository = $playersRepository;
        $this->scoreBoard = $scoreBoard;
        $this->session = $session;
		$this->sorting = $sorting;
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
    	$game = $this->session->getGame();
    
        if($game->getPlayOff()){
        	$sortedPlayers = $this->sorting->sort($this->playersRepository->findAllPlayOff());
    		$currentPlayerIndex = array_search($game->getCurrentPlayer(), $sortedPlayers);
			$score = $this->scoreBoard->getScoreForPlayer($game->getCurrentPlayer());
			//Check whether the current player reached bestscore or draw
        	if ($this->scoreBoard->isBestScore($score)) {
				for ($i=0;$i<$currentPlayerIndex;$i++){
					$sortedPlayers[$i]->setPlayOff(false);
				}
        	}
        } else {
        	$sortedPlayers = $this->sorting->sort($this->playersRepository->findAll());
			if($game->getCurrentPlayer()){
				// Check whether the current player reached zero.
        		$this->reachedZero($game->getCurrentPlayer());
			} else {
				$game->incrementCurrentTurnNumber();
            	$game->setCurrentPlayer($sortedPlayers[0]);
				return true;
			} 			
		}
        
        // If the current player is not the last in the turn, return the next one. Otherwise return null.
        $currentPlayerIndex = array_search($game->getCurrentPlayer(), $sortedPlayers);
        if ($currentPlayerIndex === count($sortedPlayers) - 1) {
        	$cont = count($this->playersRepository->findAllPlayOff());
			//We have only one winner
			if($cont==1){
				return false;
			}
			//We have multiple winners, we proceed to playoff
			if ($cont > 1){
				$game->setPlayOff(true);
				//Set only the players who will participate in the playoff
				$sortedPlayers = $this->playersRepository->findAllPlayOff();
			}
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
    public function reachedZero(DartsGame_Model_Player $player)
    {
        if ($this->scoreBoard->getScoreForPlayer($player) === 0) {
           	$player->setPlayOff(true);
			return true;
		}
		return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getWinner()
    {
        $player = $this->playersRepository->findAllPlayOff();
        return $player[0];
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

        $this->session->reset();
    }
}

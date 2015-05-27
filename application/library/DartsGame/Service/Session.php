<?php

/**
 * {@inheritdoc}.
 *
 * Implementation using Zend's session storage.
 */
class DartsGame_Service_Session extends Zend_Session_Namespace implements DartsGame_Service_SessionInterface
{
    const SESSION_NAMESPACE = "darts-game";

    /**
     * @var DartsGame_Model_Repository_PlayersInterface
     */
    private $playersTable;

    /**
     * @param DartsGame_Model_Repository_PlayersInterface $playersTable
     */
    public function __construct(DartsGame_Model_Repository_PlayersInterface $playersTable)
    {
        parent::__construct(self::SESSION_NAMESPACE);

        $this->playersTable = $playersTable;
    }

    /**
     * {@inheritdoc}
     */
    public function getGame()
    {
        // Since the player is stored in session, we need to reconnect it to its table.
        if ($this->game && $this->game->getCurrentPlayer() && !$this->game->getCurrentPlayer()->isConnected()) {
            $this->game->getCurrentPlayer()->setTable($this->playersTable);
        }

        return $this->game;
    }

    /**
     * {@inheritdoc}
     */
    public function setGame(DartsGame_Model_Game $game)
    {
        $this->game = $game;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->resetSingleInstance(self::SESSION_NAMESPACE);
        $this->game = null;
    }
}

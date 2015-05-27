<?php

/**
 * Provides access to players instances.
 */
interface DartsGame_Model_Repository_PlayersInterface
{
    /**
     * Returns an array containing all the players.
     *
     * @return DartsGame_Model_Player[]
     */
    public function findAll();

    /**
     * Returns the player with id $id.
     *
     * @param int $id
     * @return DartsGame_Model_Game
     */
    public function findByID($id);
}

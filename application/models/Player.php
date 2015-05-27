<?php

/**
 * Represents a single player.
 */
class DartsGame_Model_Player extends Zend_Db_Table_Row_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected $_tableClass = 'DartsGame_Model_Table_Players';

    /**
     * Returns the player ID.
     *
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Returns the player's full name.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Returns the player's email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function incrementGamesWon()
    {
        $this->games_won++;
    }

    public function getGamesWon()
    {
        return $this->games_won;
    }
}

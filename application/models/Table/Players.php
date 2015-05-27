<?php

/**
 * Provides access to players instances.
 */
class DartsGame_Model_Table_Players extends Zend_Db_Table_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected $_name = 'players';

    /**
     * {@inheritdoc}
     */
    protected $_rowClass = 'DartsGame_Model_Player';

    /**
     * Returns an array of players.
     *
     * @return DartsGame_Model_Player[]
     */
    public function fetchAllAsArray()
    {
        $result = array();
        foreach ($this->fetchAll() as $row) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Returns the player with id $id.
     *
     * @param int $id
     * @return Zend_Db_Table_Row_Abstract
     * @throws Zend_Db_Table_Exception
     */
    public function findByID($id)
    {
        return $this->find($id)->current();
    }
}

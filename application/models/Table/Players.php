<?php

/**
 * {@inheritdoc}
 */
class DartsGame_Model_Table_Players extends Zend_Db_Table_Abstract implements DartsGame_Model_Repository_PlayersInterface
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
     * {@inheritdoc}
     */
    public function findAll()
    {
        $result = array();
        foreach ($this->fetchAll() as $row) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findByID($id)
    {
        return $this->find($id)->current();
    }
}

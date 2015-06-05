<?php

/**
 * 
 */
interface DartsGame_Service_SortingInterface
{
    /**
     * We just follow an alphabetic ordering (by first name).
     *
     * @param DartsGame_Model_Player[] $players
     * @return DartsGame_Model_Player[]
     */
    public function sort(array $players);
}

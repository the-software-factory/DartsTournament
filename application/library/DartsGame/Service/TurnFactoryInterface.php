<?php

/**
 * A service to create turns instances.
 */
interface DartsGame_Service_TurnFactoryInterface
{
    /**
     * Returns a new turn instance.
     *
     * @param DartsGame_Model_Player $player
     * @param $number
     * @return mixed
     */
    public function create(DartsGame_Model_Player $player, $number);

}

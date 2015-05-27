<?php

class DartsGame_Service_TurnFactory implements DartsGame_Service_TurnFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(DartsGame_Model_Player $player, $number)
    {
        return new DartsGame_Model_Turn($player, $number);
    }
}

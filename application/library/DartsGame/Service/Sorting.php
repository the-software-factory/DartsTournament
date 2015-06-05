<?php

/**
 * Handles and stores players' scores through a game.
 */
class DartsGame_Service_Sorting implements  DartsGame_Service_SortingInterface
{
    /**
     * {@inheritdoc}
     */
    public function sort(array $players)
	{
		usort($players, function (DartsGame_Model_Player $player1, DartsGame_Model_Player $player2) {
            return strcasecmp($player1->getFullName(), $player2->getFullName());
        });
		return $players;
	}
}

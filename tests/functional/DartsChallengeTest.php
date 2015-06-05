<?php

/**
 * Class DartsChallengeTests
 */
class DartsChallengeTests extends PHPUnit_Framework_TestCase
{
    /**
     * @var \RemoteWebDriver
     */
    protected $webDriver;

    /**
     * @var string
     */
    protected $url = 'http://localhost:8080';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->webDriver->close();
    }

    /**
     * Tests an extremely unrealistic game made of perfect rounds for all players.
     */
    public function testFullGame()
    {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'GitHub'
        $this->assertContains('Darts Tournament!', $this->webDriver->getTitle());

        // First page.
        $this->webDriver->findElement(WebDriverBy::id('start-new-game'))->click();

        // Simulate 3 perfect rounds for all players
        for ($round = 1; $round < 3; $round++) {
            $this->assertContains('Round #'.$round, $this->webDriver->getTitle());
            for ($player = 1; $player <= 4; $player++) {
                $this->executePerfectRound();
            }
        }
		
		// Simulate 1 perfect rounds for three players, these will go to first playoffs
        for ($round = 1; $round < 3; $round++) {
            $this->assertContains('Round #'.$round, $this->webDriver->getTitle());
            for ($player = 1; $player <= 3; $player++) {
                $this->executePerfectRound();
            }
        }
		
		// Simulate 1 perfect rounds for two players, these will go to second playoffs
        for ($round = 1; $round < 3; $round++) {
            $this->assertContains('Round #'.$round, $this->webDriver->getTitle());
            for ($player = 1; $player <= 2; $player++) {
                $this->executePerfectRound();
            }
        }

        // The next player will do a perfect round
        $this->executePerfectRound();
        sleep(5);
        $this->assertContains('YAY!', $this->webDriver->getTitle());
    }

    /**
     * Tests that the "reset game" button extremely unrealistic game made of perfect rounds for all players.
     */
    public function testResetGame()
    {
        $this->webDriver->get($this->url);
        $this->assertContains('Darts Tournament!', $this->webDriver->getTitle());
        $this->webDriver->findElement(WebDriverBy::id('start-new-game'))->click();
        $this->assertContains('Round #1', $this->webDriver->getTitle());
        $this->webDriver->findElement(WebDriverBy::id('reset-game'))->click();
        $this->assertContains('Darts Tournament!', $this->webDriver->getTitle());
    }

    /**
     * Utility function that wraps a set of actions to update the form with a perfect round.
     */
    private function executePerfectRound()
    {
        // Perfect game: "treble 20 (60), treble 19 (57) and bullseye (50)"
        $this->webDriver->findElement(WebDriverBy::id('scores-scores0'))
            ->click()->clear();
        $this->webDriver->getKeyboard()->sendKeys('20');

        $this->webDriver->findElement(WebDriverBy::id('multipliers-multipliers0'))
            ->findElement(WebDriverBy::cssSelector("option[value='3']"))
            ->click();

        $this->webDriver->findElement(WebDriverBy::id('scores-scores1'))
            ->click()->clear();
        $this->webDriver->getKeyboard()->sendKeys('19');
        $this->webDriver->findElement(WebDriverBy::id('multipliers-multipliers1'))
            ->findElement(WebDriverBy::cssSelector("option[value='3']"))
            ->click();

        $this->webDriver->findElement(WebDriverBy::id('scores-scores2'))
            ->click()->clear();
        $this->webDriver->getKeyboard()->sendKeys('50');

        $this->webDriver->findElement(WebDriverBy::id('next-turn'))->click();
    }
}
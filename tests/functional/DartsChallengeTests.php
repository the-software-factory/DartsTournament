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

    public function setUp()
    {
        $capabilities = array(\WebDriverCapabilityType::BROWSER_NAME => 'firefox');
        $this->webDriver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    public function testGame()
    {
        $this->webDriver->get($this->url);
        // checking that page title contains word 'GitHub'
        $this->assertContains('Darts Tournament!', $this->webDriver->getTitle());
    }

    public function tearDown()
    {
        $this->webDriver->close();
    }

}

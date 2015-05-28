<?

/**
 * Testing players retrieval from the database
 */
class DartsGame_Model_Table_PlayersTest extends PHPUnit_Framework_TestCase
{
    /**
     * ALWAYS EXECUTED BEFORE EACH TEST.
     *                        ^^^^
     */
    public function setUp()
    {
        /*
         * We always begin a new transaction before each tests is run.
         */
        Zend_Db_Table::getDefaultAdapter()->beginTransaction();
    }

    /**
     * ALWAYS EXECUTED AFTER EACH TEST.
     *                       ^^^^
     */
    public function tearDown()
    {
        /*
         * We always make sure that the transaction is rolled back after each test execution, so the database
         * will remain in its initial state.
         */
        Zend_Db_Table::getDefaultAdapter()->rollBack();
    }

    /**
     * Testing retrieval of all players
     *
     * @covers DartsGame_Model_Table_Players::findAll
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function testFetchAllAsArray()
    {
        // Inserting fake data
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->insert('players', array('id' => 1, 'first_name' => 'Player 1'));
        $db->insert('players', array('id' => 2, 'first_name' => 'Player 2'));
        $db->insert('players', array('id' => 3, 'first_name' => 'Player 3'));

        $playersTable = new DartsGame_Model_Table_Players();
        $result = $playersTable->findAll();

        // asserting that 3 players are loaded
        $this->assertEquals(3, count($result));

        $sumOfIDs = 0;
        foreach ($result as $player) {
            $this->assertInstanceOf('DartsGame_Model_Player', $player);
            $this->assertTrue(in_array($player->getID(), array(1, 2, 3)));
            $this->assertEquals("Player " . $player->getID(), $player->first_name);
            $sumOfIDs += $player->getID();
        }

        // asserting that all the (correct) players are loaded
        $this->assertEquals(6, $sumOfIDs);
    }

    /**
     * Testing retrieval of all players when no records exist in the DB
     *
     * @covers DartsGame_Model_Table_Players::findAll
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function testFetchAllAsArrayWithNoRecords()
    {
        $playersTable = new DartsGame_Model_Table_Players();
        $result = $playersTable->findAll();

        // asserting that 3 players are loaded
        $this->assertEquals(0, count($result));
    }

    /**
     * Testing retrieval of a single player that exists in the DB
     *
     * @covers DartsGame_Model_Table_Players::findbyID
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function testFetchByIdWhenExists()
    {
        // Inserting fake data
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->insert('players', array('id' => 1, 'first_name' => 'Player 1'));
        $db->insert('players', array('id' => 2, 'first_name' => 'Player 2'));
        $db->insert('players', array('id' => 3, 'first_name' => 'Player 3'));

        $playersTable = new DartsGame_Model_Table_Players();
        $result = $playersTable->findByID(2);

        $this->assertInstanceOf('DartsGame_Model_Player', $result);
        $this->assertEquals("Player 2", $result->first_name);
    }

    /**
     * Testing retrieval of a single player that DOES NOT exist in the DB
     *
     * @covers DartsGame_Model_Table_Players::findByID
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function testFetchByIdWhenNotExists()
    {
        // Inserting fake data
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->insert('players', array('id' => 1, 'first_name' => 'Player 1'));
        $db->insert('players', array('id' => 2, 'first_name' => 'Player 2'));
        $db->insert('players', array('id' => 3, 'first_name' => 'Player 3'));

        $playersTable = new DartsGame_Model_Table_Players();
        $result = $playersTable->findByID(4);

        $this->assertNull($result);
    }

    /**
     * Testing retrieval of a single player that DOES NOT exist in the DB and there are no records in the DB.
     *
     * @covers DartsGame_Model_Table_Players::findbyID
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function testFetchByIdWhenNotExistsWithNoRecords()
    {
        $playersTable = new DartsGame_Model_Table_Players();
        $result = $playersTable->findByID(1);

        $this->assertNull($result);
    }
}

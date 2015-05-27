<?

///**
// * Defines operations for an ATM machine.
// */
//interface ATM {
//
//    /**
//     * Withdraws money from your card.
//     *
//     * @param number $amount
//     * @return mixed
//     */
//    public function withdraw($amount);
//
//    /**
//     * Returns the balance of your card.
//     *
//     * @return number
//     */
//    public function getBalance();
//
//}
//
//
//class OrderProcessor {
//
//    const MAX_PROCESSABLE_AMOUNT = 2000;
//
//    public function processPayment($amount, $orderID) {
//
//        if ($amount <= 0) {
//            throw new Exception("Negative payment amount: $amount");
//        }
//
//        if ($amount > self::MAX_PROCESSABLE_AMOUNT) {
//            throw new Exception("Amount $amount exceeds maximum processable amount: " . self::MAX_PROCESSABLE_AMOUNT);
//        }
//
//        if (is_null($orderID)) {
//            throw new Exception("Null order id.");
//        }
//
//        $paymentID = $this->creditCardGateway->pay($amount);
//        if (!$paymentID) {
//            throw new Exception("Error while processing payment.");
//        }
//
//        if ($this->orderManager->register($orderID, $paymentID)) {
//            throw new Exception("Error while registering order payment.");
//        }
//    }
//
//}

/** An interface */
interface SomeInterface {

    public function method1();

    public function method2();
}

/** A class that needs an instance of SomeInterface
 *  in constructor */
class SomeClass {

    public function __construct(SomeInterface $obj) { $this->obj = $obj; }

    public function methodUnderTest() {
        // ... code DOES NOT USE $this->obj ... //
    }
}

/** A test case */
class SomeTest {

    public function testMethod() {
        $dummyArgument = new DummyImplementation();
        $sut = new SomeClass($dummyArgument);
        $result = $sut->methodUnderTest();
        // ... assertions on $result ... //
    }

}

class My_Real_Facebook_API_Client {

    public function getAllPosts() {
        // ... retrieves all posts from the real facebook API ... //
    }
}

class SocialNetworkManager {

    public function __construct(My_Real_Facebook_API_Client $apiClient) {
        $this->apiClient = $apiClient;
    }

    public function retrievePosts() { /* Uses the real API client */ }
}

class SocialNetworkManagerTest extends PHPUnit_Framework_TestCase {

    public function testSocialNetworkManager() {

        // Mocking Scored Board
        $mockedAPI = $this
            ->getMockBuilder('My_Real_Facebook_API_Client')
            ->setConstructorArgs(1, 2, 3)
            ->getMock();
        $mockedAPI
            ->expects($this->once())
            ->method('getAllPosts')
            ->willReturn(array(
                array('id' => 1, 'title' => 'First Post'),
                array('id' => 2, 'title' => 'Second Post'),
                array('id' => 3, 'title' => 'Third Post'),
            ));

        $sut = new SocialNetworkManager($mockedAPI);
        $result = $sut->retrievePosts(); // will return the array above
        // Make assertions on $result
    }

}


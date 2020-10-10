<?php
  require dirname(__DIR__) . "/vendor/twilio-php-main/src/Twilio/autoload.php";
  require dirname(__DIR__) . "/includes/constants.php";
  require dirname(__DIR__) . "/includes/constants_passwords.php";
  use Twilio\Rest\Client;

  define("CLIENT_ID", 1);
  define("HELPED_CODE", 20);
  define("CONTACT_ID", 11);
  define("INITIAL_TEXT", "Hello! Thank you so much for contacting SCAS! We are happy to provide further help with your case. Would you like us to follow up in a week? (Y/N)");

  $clients = array(new PrototypeClient(CLIENT_ID, "Ife", "Omidiran", "832", "6608763", false, false));

  class PrototypeClient {
    protected $ClientID;
    protected $FirstName;
    protected $LastName;
    protected $Phone1AreaCode;
    protected $Phone1Number;
    protected $ShouldFollowUp;
    protected $InitialText;

    public function __construct($ClientID, $FirstName, $LastName, $Phone1AreaCode, $Phone1Number, $ShouldFollowUp, $InitialText) {
      $this->ClientID = $ClientID;
      $this->FirstName = $FirstName;
      $this->LastName = $LastName;
      $this->Phone1AreaCode = $Phone1AreaCode;
      $this->Phone1Number = $Phone1Number;
      $this->ShouldFollowUp = $ShouldFollowUp;
      $this->IsInitialText = $IsInitialText;
    }

    public function getClientID() {
      return $this->ClientID;
    }

    public function getFirstName() {
      return $this->FirstName;
    }

    public function getNumber() {
      return $this->Phone1AreaCode . $this->Phone1Number;
    }

    public function getShouldFollowUp() {
      return $this->ShouldFollowUp;
    }

    public function getIsInitialText() {
      return $this->IsInitialText;
    }

    public function setShouldFollowUp($val) {
      $this->ShouldFollowUp = $val;
    }

    public function setIsInitialText($val) {
      $this->IsInitialText = $val;
    }

    public function __toString() {
      return "Name: ".$this->FirstName." ".$this->LastName.PHP_EOL
          ."Number: ".$this->Phone1AreaCode."-". $this->Phone1Number.PHP_EOL
          ."Should follow up: ".($this->ShouldFollowUp ? "yes" : "no").PHP_EOL;
    }
  }

  class PrototypeContact {
    protected $ContactID;
    protected $ContactTypeID;
    protected $ClientID;
    protected $ClientArray;

    public function __construct($ContactID, $ContactTypeID, $ClientID, $ClientArray) {
      $this->ContactID = $ContactID;
      $this->ContactTypeID = $ContactTypeID;
      $this->ClientID = $ClientID;
      $this->ClientArray = $ClientArray;

      $client = $this->getClientInfo();

      if ($this->ContactTypeID == HELPED_CODE) {
        $client->setIsInitialText(true);
        // $this->sendInitialText($client->getNumber());
      }
    }

    private function getClientInfo() {
      foreach ($this->ClientArray as $client) {
        if ($client->getClientID() == $this->ClientID) {
          return $client;
        }
      }
      return null;
    }

    private function sendInitialText($number) {
      $twilio_client = new Client(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);
      $twilio_client->messages->create(
        $number,
        array(
          "from" => TWILIO_FROM_NUMBER,
          "body" => INITIAL_TEXT
        )
      );
    }
  }

  $contacts = array(new PrototypeContact(CONTACT_ID, HELPED_CODE, CLIENT_ID, $clients));
?>

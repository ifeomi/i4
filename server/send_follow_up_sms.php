<?php
  require dirname(__DIR__) . "/server/send_sms.php";

  define("FOLLOW_UP_TEXT", "Hello $name! This is SCAS following up on our case. What is the "
      . "status of your case? (Ongoing/Won/Lost/Settled)");

  foreach ($clients as $client) {
    $name = $client->getFirstName();
    if ($client->getShouldFollowUp()) {
      $twilio_client->messages->create(
        $number,
        array(
          "from" => $client->getPhoneNumber(),
          "body" => FOLLOW_UP_TEXT
        )
      );
    }
  }
?>

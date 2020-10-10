<?php
  require dirname(__DIR__) . "/server/send_sms.php";
  use Twilio\TwiML\MessagingResponse;
  // Set the content-type to XML to send back TwiML from the PHP Helper Library
  // header("content-type: text/xml");

  class Responses {
    // initial text response state
    const A = "Okay, we'll follow up in one week. In the meantime, you can call "
        . "SCAS with any updates to your case."; // flow ends with D or E
    const B = "Okay. If you would like to take a survey to help us improve our "
        . "services, you can do so here: [LINK]"; // end flow here
    const ERROR_RESPONSE_INITIAL = "Please send either Y/N.";

    // follow-up state: one week later...
    const D = "Okay. Please call SCAS to give us an update on your case."; // end flow
    const E = "Thank you for the update. If you would like to take a survey to "
        . "help us improve our services, you can do so here: [LINK]"; // end flow
    const ERROR_RESPONSE_FOLLOW_UP = "Please send one of: Ongoing, Won, Lost, or Settled.";
  }

  $from = $_REQUEST["From"];
  $body = strtoupper($_REQUEST["Body"]);
  $response = new MessagingResponse();

  function remove_country_code($phone_num) {
    return preg_replace("/^\+?1|\|1|\D/", "", ($phone_num));
  }

  function get_client_from_num($phone_num, $clients) {
    $num = remove_country_code($phone_num);
    foreach ($clients as $client) {
      if (strcmp($num, $client->getNumber()) == 0) {
        return $client;
      }
    }
    return null;
  }

  $client = get_client_from_num($from, $clients);

  if ($client->getIsInitialText()) {
    if ($body == "Y" || $body == "YES") {
      $client->setShouldFollowUp(true);
      $client->setIsInitialText(false);
      $response->message(
        Responses::A
      );
    }

    elseif ($body == "N" || $body == "NO") {
      $client->setShouldFollowUp(false);
      $client->setIsInitialText(false);
      $response->message(
        Responses::B
      );
    }

    else {
      $response->message(
        Responses::ERROR_RESPONSE_INITIAL
      );
    }
  }

  else {
    if ($body == "ONGOING") {
      $response->message(
        Responses::D
      );
    }

    elseif ($body == "WON" || $body == "LOST" || $body == "SETTLED") {
      $response->message(
        Responses::E
      );
    }

    else {
      $response->message(
        Responses::ERROR_RESPONSE_FOLLOW_UP
      );
    }
  }

  echo $response;

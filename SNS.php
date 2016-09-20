<?php
// Load the AWS SDK for PHP
require_once("aws/aws-autoloader.php");
require_once("config.php");

use GuzzleHttp\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\Exception\CredentialsException;


class Amazonsns_SNS_PUSH
{
  private $SNS;
  private $CONFIG;
  private $CredentialProvider;

  private function getCredentials()
  {
    return function () {

      $key = GSL_SNS_PUSH_KEY;
      $secret = GSL_SNS_PUSH_SECRET;

      if ($key && $secret) {
        return Promise\promise_for(
          new Credentials($key, $secret)
        );
      }

      $msg = 'Could not retrieve credentials';
      return new RejectedPromise(new CredentialsException($msg));
    };
  }


  /**
   * This function keeps state for the credential provider
   * @return mixed
   */
  private function getCredentialProvider()
  {
    if ($this->CredentialProvider) {
      return $this->CredentialProvider;
    }

    $provider = $this->getCredentials();
    $this->CredentialProvider = CredentialProvider::memoize($provider);

    return $this->CredentialProvider;
  }

  function getSNSClient() {
    if($this->SNS) {
      return $this->SNS;
    }

    $region = GSL_SNS_PUSH_REGION;

    $this->SNS = new Aws\Sns\SnsClient([
      'version' => 'latest',
      'region'  => $region,
      'credentials' => $this->getCredentialProvider()
    ]);

    return $this->SNS;
  }

  function getAllTopics()
  {

    $sns = $this->getSNSClient();

    $topics = array();
    $result = $sns->listTopics();

    foreach($result['Topics'] as $arn) {
      $topics[$arn['TopicArn']] = preg_replace('/.*:/', '', $arn['TopicArn']);
    }

    //todo: Loop if we need to get more topics.

    return $topics;
  }

  function publishToTopic($topicArn, $message, $title = "") {
    $sns = $this->getSNSClient();

    $params = array(
      "TopicArn" => $topicArn,
      "Message" => $message,
      "Subject" => $title
    );

    if (is_array($message)) {
      $params['Message'] = json_encode($message);
      $params['MessageStructure'] = "json";
    }

    $result = $sns->publish($params);

    //Todo: Fix this up so it is a bit prettier, error checking etc.

    return $result;
  }

  function composeDefaultMessageStructure($message, $subject) {
    return array(
      "default" => $message,
      "APNS" => json_encode(array("aps" => array("alert" => $message, "title" => $subject))),
      "APNS_SANDBOX" => json_encode(array("aps" => array("alert" => $message))),
      "GCM" => json_encode(array(
        "data" => array("message" => $message, "title" => $subject),
      )),
      "ADM" => json_encode(array(
        "data" => array("message" => $message, "title" => $subject),
      )),
    );
  }
}

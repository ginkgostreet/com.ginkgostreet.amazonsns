<?php
// Load the AWS SDK for PHP
require_once("aws/aws-autoloader.php");

use GuzzleHttp\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\Exception\CredentialsException;


class CRM_Amazonsns_SNS
{
  private $SNS;
  private $CONFIG;
  private $CredentialProvider;

  private function getCredentials()
  {
    return function () {

      $key = CRM_Core_BAO_Setting::getItem("com.ginkgostreet.amazonsns", "amazon_sns_api_key");
      $secret = CRM_Core_BAO_Setting::getItem("com.ginkgostreet.amazonsns", "amazon_sns_api_secret");

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

    $region = CRM_Core_BAO_Setting::getItem("com.ginkgostreet.amazonsns", "amazon_sns_region");

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
      "APNS" => json_encode(array("aps" => array("alert" => $message))),
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

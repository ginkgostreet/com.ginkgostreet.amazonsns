<?php

require_once 'config.php';
require_once 'SNS.php';

$debugging = (array_key_exists("show_debugging", $_POST)) ? ($_POST['show_debugging'] == 1) : false;

$sns = new Amazonsns_SNS_PUSH();

//Send the message
if (array_key_exists("SendMessage", $_POST) && $_POST['SendMessage'] == 1) {


  $topic = $_POST['sns_topic'];
  $message = $_POST['msg_body'];
  $title = $_POST['msg_title'];


  if ($topic && $message && $title) {
    try {
      $composedData = $sns->composeDefaultMessageStructure($message, $title);

      $result = $sns->publishToTopic($topic, $composedData, $title);

      if($debugging) {
        $json = json_encode($composedData);
        $jsonError = json_last_error(). ": ". json_last_error_msg();

        echo "<div class='success'>Message Sent<br /><hr /><br />" .
          nl2br(str_replace(" ", "&nbsp;", print_r($result, true))) .
          "<hr />Topic: {$topic}".
          "<hr />Title: '{$title}' ".
          "<hr />Message: '{$message}' ".
          "<hr />Composed Data: ". nl2br(str_replace(" ", "&nbsp;", print_r($composedData, true))).
          "<hr />Json: '{$json}' ".
          "<hr />Json Error: '{$jsonError}' ".
          "</div>";
      } else {
        echo "<div class='success'>Message Sent</div>";
      }


    } catch (Exception $e) {
      $json = json_encode($composedData);
      $jsonError = json_last_error(). ": ". json_last_error_msg();
      echo "<div class='error'>Error Sending Push: ". $e->getMessage() .
        "<hr />Topic: {$topic}".
        "<hr />Title: '{$title}' ".
        "<hr />Message: '{$message}' ".
        "<hr />Composed Data: ". nl2br(str_replace(" ", "&nbsp;", print_r($composedData, true))).
        "<hr />Json: '{$json}' ".
        "<hr />Json Error: '{$jsonError}' ".
        "</div>";
    }
    $title = $message = "";
  } else {
    echo "<div class='error'>All Fields are Required</div>";
  }


} else {
  $title = $message = "";
}


//Fetch the Topics
$topics = $sns->getAllTopics();

?>
<link rel="stylesheet" href="sns.css" />
<form action="" method="post" name="Push" id="Push">

  <div class="section">
    <div class="label"><label for="sns_topic">Topic <span class="required">*</span></label></div>
    <div class="content">
      <select name="sns_topic" id="sns_topic" class="required">
        <?php foreach ($topics as $value => $topic) {
          echo "<option value='$value'>$topic</option>\n";
          }
          ?>
      </select>
    </div>
    <div class="clear"></div>
  </div>

  <div class="section">
    <div class="label"><label for="msg_title">Title <span class="required">*</span></label></div>
    <div class="content"><input name="msg_title" size="75" type="text" id="msg_title" value="<?php echo $title; ?>" /></div>
    <div class="clear"></div>
  </div>

  <div class="section">
    <div class="label"><label for="msg_body">Message <span class="required">*</span></label></div>
    <div class="content"><textarea name="msg_body" id="msg_body" class="required" cols="70" rows="10"><?php echo $message; ?></textarea></div>
    <div class="clear"></div>
  </div>

  <br />
  <div class="section">
    <div class="content">
      <input name="show_debugging" type="checkbox" id="show_debugging" value="1" <?php echo $debugging ? "checked" : ""; ?> />
      <label for="show_debugging">Display Debugging Info</label>
    </div>
    <div class="clear"></div>
  </div>

  <br />
  <input name="SendMessage" value="1" type="hidden" />
  <div class="section"><input value="Send Message" type="submit" /></div>

</form>
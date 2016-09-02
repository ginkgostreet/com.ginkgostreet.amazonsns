<?php

require_once 'config.php';
require_once 'SNS.php';


$sns = new Amazonsns_SNS_PUSH();

//Send the message
if (array_key_exists("SendMessage", $_POST) && $_POST['SendMessage'] == 1) {


  $topic = $_POST['sns_topic'];
  $message = $_POST['msg_body'];
  $title = $_POST['msg_title'];


  if ($topic && $message && $title) {
    $sns->publishToTopic(
      $topic,
      $sns->composeDefaultMessageStructure($message, $title),
      $title
    );

    echo "<div class='success'>Message Sent</div>";
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
    <div class="content"><input name="msg_title" type="text" id="msg_title" value="<?php echo $title; ?>"></div>
    <div class="clear"></div>
  </div>

  <div class="section">
    <div class="label"><label for="msg_body">Message <span class="required">*</span></label></div>
    <div class="content"><textarea name="msg_body" id="msg_body" class="required"><?php echo $message; ?></textarea></div>
    <div class="clear"></div>
  </div>

  <input name="SendMessage" value="1" type="hidden" />
  <div class="section"><input value="Send Message" type="submit" /></div>

</form>
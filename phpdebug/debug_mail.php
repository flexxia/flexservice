<?php

/**
 *
 */
$name = \Drupal::request()->get("name");
// Try to load by email.
$user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(array('mail' => $name));
$user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(array('name' => $name));
$account = reset($users);


$account = \Drupal::entityTypeManager()->getStorage('user')->load(300);
$cc = user_pass_reset_url($account);

// Mail one time login URL and instructions using current language.
$mail = _user_mail_notify('password_reset', $account);


/**
 *
 */
$to = "jojo.zhou@metiore.ca";
$to = "dong.xu@metiore.ca";
$subject = "Test mail";
$message = "Hello! This is a simple email message.";
$from = "noreply@eample.com";
$headers = "From:" . $from;
mail($to,$subject,$message,$headers);
echo "Mail Sent.";

/**
 * postfix
 */
//
echo "测试邮件正文" | mail -s "邮件标题" dong.xu@metiore.ca

/**
 * sendmail
 */
service sendmail status
/usr/sbin/sendmail -i -- dong.xu@metiore.ca < message_file

/usr/sbin/sendmail dong.xu@metiore.ca
Subject: Test Send Mail
Hello World

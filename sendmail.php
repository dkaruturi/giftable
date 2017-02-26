<?php
  // Set path for the PHPMailer files.  These must have been previously
  // unzipped and placed into the stated directory.  Be sure to match
  // up the directories in your installation (i.e. you do not have to have
  // your files in the same directory that I have here, as long as you can
  // locate them).  To download / install the necessary files, see:
  // https://github.com/Synchro/PHPMailer
  
  // On MAC the path is usually:
//  $mailpath = '/Applications/XAMPP/xamppfiles/PHPMailer';

  
  // Also note that Windows installations have different path names - be sure
  // to follow the syntax correctly.
  // Also, on my Windows version, to get this to work I had to do the following:
  // 	Edit file 'php.ini'  (you need to find where that is)
  //	Locate the line:  extension=php_openssl.dll
  //	If there is a semicolon (;) at the beginning of the line, delete it
  //	Save the file
  //	Start / restart Apache
  
  
  // Add the new path items to the previous PHP path
//  $path = get_include_path();
//  set_include_path($path . PATH_SEPARATOR . $mailpath);
  require 'class.phpmailer.php';
  require 'class.smtp.php';
  
  // PHPMailer is a class -- we will discuss classes and PHP object-oriented
  // programming soon.  However, you should be able to copy / use this
  // technique without fully understanding PHP OOP.
  $mail = new PHPMailer();
 
  $mail->IsSMTP(); // telling the class to use SMTP
  $mail->SMTPAuth = true; // enable SMTP authentication
  $mail->SMTPSecure = "tls"; // sets tls authentication
  $mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
  $mail->Port = 587; // set the SMTP port for GMAIL server; or your email server port
  $mail->Username = "cs4501.fall15@gmail.com"; // email username
  $mail->Password = "UVACSROCKS"; // email password
    
    
  $sender = strip_tags($_SESSION["sender"]);
  $receiver = strip_tags($_SESSION["receiver"]);
//    echo $receiver;
  $subj = strip_tags($_SESSION["subject"]);
  $msg = strip_tags($_SESSION["msg"]);

 if (isset($_SESSION["init"])) {
      $db = new mysqli('localhost', 'root', '', 'localhost');
      $results = $db->query("SELECT `email` FROM Users WHERE `type` = 'admin'");
      if($results->num_rows > 0) {
            while($row = mysqli_fetch_array($results))
            {
                $mail->addAddress($row[0]);
            }
      }
     unset($_SESSION["init"]);
 }
  // Put information into the message
  $mail->addAddress($receiver);
  $mail->SetFrom($sender);
  $mail->Subject = "$subj";
  $mail->Body = "$msg";

  // echo 'Everything ok so far' . var_dump($mail);
  if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
   } 
   else { 
//       echo 'Message has been sent'; 
   }
    
  
?>
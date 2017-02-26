<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];
    }
    $con = mysqli_connect('localhost','root','','localhost');
    if (!$con) {
        die('Could not connect: ' . mysqli_error($con));
    }

    $q = $_REQUEST["q"];

    if ($q == "seeTickets") {
        mysqli_select_db($con,"Users");
        $name = "SELECT name FROM Users WHERE `username` = '".$username."'";
        $result = mysqli_fetch_array(mysqli_query($con,$name));
        $sql = "SELECT `Ticket #`,`Received`,`Subject`,`Status` FROM Tickets WHERE `Sender Name` = '".$result[0]."'";
        $result =mysqli_fetch_all(mysqli_query($con,$sql));
        
        foreach ($result as $row) {
            $row = array_unique($row);
            $row = '["'.implode('", "', $row).'"];';
            print_r($row);
        }
        
        mysqli_close($con);
    }
    else if ($q == "submitTicket") {
        
        $today = date("Y-m-d G:i:s",time()); 

        $prob = $_REQUEST["problem"];
        $subj = $_REQUEST["subject"];
        mysqli_select_db($con,"Users");
        $sql = "SELECT `name`,`email` FROM Users WHERE `username` = '".$username."'";
        $result = mysqli_fetch_array(mysqli_query($con,$sql));
        $name = $result[0];
        $email = $result[1];

        
        $_SESSION["sender"] = "Ticket System Mailer";
        $_SESSION["receiver"] = $email;
        $_SESSION["subject"] = $subj;
        $_SESSION["msg"] = $prob;
        $_SESSION["init"] = "";

        include('sendmail.php');  
        
        unset($_SESSION["init"]);
        mysqli_select_db($con,"Tickets");
        $query = "INSERT INTO `Tickets` (`Ticket #`,`Received`, `Sender Name`, `Sender Email`, `Subject`, `Tech`, `Status`) VALUES (NULL,'$today','$name','$email','$subj','','open')";
        mysqli_query($con,$query);

        $query = "select `Ticket #` from Tickets where received = '$today'";

        $problemNum = mysqli_fetch_array(mysqli_query($con,$query));
        mysqli_select_db($con,"Problem");
        $query = "INSERT INTO `Problem` VALUES ('$problemNum[0]','$prob')";
        mysqli_query($con,$query);
        

        mysqli_select_db($con,"Users");
        $name = "SELECT name FROM Users WHERE `username` = '".$username."'";
        $result = mysqli_fetch_array(mysqli_query($con,$name));
        $sql = "SELECT `Ticket #`,`Received`,`Subject`,`Status` FROM Tickets WHERE `received` = '".$today."'";
        $result = mysqli_fetch_array(mysqli_query($con,$sql));

        $result = array_unique($result);
        $result = '["'.implode('", "', $result).'"]';
        print_r($result);

//        var_dump($result);
        mysqli_close($con);
        
    }
    else if ($q == "changePassword") {
            
        mysqli_select_db($con,"Users");
    
        $userid = $_REQUEST["userid"];
        
        if ($userid == $_SESSION["username"]) {
            $newpass = MD5($_REQUEST["newpass"]); 

            $query = "UPDATE `Users` SET `password`='$newpass' WHERE `username` = '$userid'";
            
            mysqli_query($con,$query);
            
        }
        
    }
    else if ($q == "name") {
        mysqli_select_db($con,"Users");
        echo $username;
        $sql = "SELECT `name` FROM Users WHERE `username` = '".$username."'";
        echo $sql;
        $result = mysqli_fetch_array(mysqli_query($con,$sql));
        echo $result[0];
    }
    else if ($q == "start") {
        mysqli_select_db($con,"Users");
        $sql = "SELECT * FROM Tickets";
        $result =mysqli_fetch_all(mysqli_query($con,$sql));
        
        foreach ($result as $row) {
            $query = "SELECT `Problem`.`Problem`
                FROM `Tickets` , `Problem`
                WHERE ( `Ticket #` = `ID` && `ID` = $row[0])";
            $problem = mysqli_fetch_array(mysqli_query($con,$query));
            array_push($row,$problem[0]);
            $row = array_unique($row);
            $row = '["'.implode('", "', $row).'"];';
            print_r($row);
        }
        
        mysqli_close($con);
    }
    else if ($q == "toggle_status") {
        echo $q;
        $ticketnum = $_REQUEST["ticket"];
        $status = $_REQUEST["status"];
//        echo $ticketnum + "  ";
        mysqli_select_db($con,"Tickets");
        $query = "SELECT `Sender Email`,`Subject` FROM Tickets WHERE `Ticket #` = $ticketnum";
        $ticketinfo = mysqli_fetch_array(mysqli_query($con,$query));
        
        if ($status == 'open') {
            $query = "UPDATE `Tickets` SET `Status`='closed' WHERE `Ticket #` = $ticketnum";
        } else {
            $query = "UPDATE `Tickets` SET `Status`='open' WHERE `Ticket #` = $ticketnum";
        }
        mysqli_query($con,$query);

//        print_r($ticketinfo);
        $_SESSION["sender"] = "Ticket System Mailer";
        $_SESSION["receiver"] = $ticketinfo[0];
        $_SESSION["subject"] = "Ticket Status Change";
        $_SESSION["msg"] = "Your ticket titled '".$ticketinfo[1]."' has been closed";
        
//        include('sendmail.php');
    }
    else if ($q == "assign_self") {
        $ticketnum = $_REQUEST['ticket'];
        $currtech = $_REQUEST['currtech'];
        mysqli_select_db($con,"Users");
        $sql = "SELECT `name` FROM Users WHERE `username` = '".$username."'";
        $result = mysqli_fetch_array(mysqli_query($con,$sql));

        $result = explode(' ', $result[0]);
        mysqli_select_db($con,"Tickets");
        if ($currtech == '') {
            $query = "UPDATE `Tickets` SET `Tech`='$result[0]' WHERE `Ticket #` = $ticketnum";
            mysqli_query($con,$query);
            echo $result[0];
        } 
        else if ($currtech == $result[0]) {
            $query = "UPDATE `Tickets` SET `Tech`='' WHERE `Ticket #` = $ticketnum";
            mysqli_query($con,$query);
            echo 'none';
        }
        else {
            echo 'This ticket is already assigned';
        }
    }
    else if ($q == "email_sender") {
        
        $prob = $_REQUEST["problem"];
        $subj = $_REQUEST["subject"];
        $email = $_REQUEST["receiver"];
        
        $email = explode(' ',$email);
        $email = implode('+',$email);
        
        
        $_SESSION["sender"] = "Ticket System Mailer";
        $_SESSION["receiver"] = $email;

        $_SESSION["subject"] = $subj;
        $_SESSION["msg"] = $prob;

//        $_SESSION["init"] = "";
        include('sendmail.php');

    }
    else if ($q == "delete") {
        $ticketnum = $_REQUEST["ticket"];
        mysqli_select_db($con,"Tickets");
        $query = "DELETE FROM `Tickets` WHERE `Ticket #` = $ticketnum";
        mysqli_query($con,$query);
        echo "Ticket Deleted";
    }
    else if ($q == "reset") {
        $userid = $_REQUEST["userid"];
        mysqli_select_db($con,"Users");
        $sql = "SELECT `email` FROM Users WHERE `username` = '".$userid."'";
        $userExists = mysqli_fetch_array(mysqli_query($con,$sql));
        
        if ($userExists)
        {
            // Create a unique salt. This will never leave PHP unencrypted.
            $salt = "498#2D83B631%3800EBD!801600D*7E3CC13";
            // $_SESSION["salt"] = $salt;
            // Create the unique user password reset key
            $password = crypt("sm7gc", $salt.$userid);
            
            // Add unique key to user information in Admin Table
            $query = "UPDATE `Users` SET `hash` = '$password' WHERE username = '$userid'";
            
            // Create a url which we will direct them to reset their password
            $pwrurl = "http://localhost/Assignment3/src/resetform.php?q=".$password;

            // Mail them their key
            $mailbody = "Click here to reset password: " . $pwrurl;
            
            $_SESSION["sender"] = "Ticket System Mailer";
            $_SESSION["receiver"] = $userExists[0];
            $_SESSION["subject"] = "Reset Ticket System Password";
            
            $_SESSION["msg"] = $mailbody;
            
            include('sendmail.php');
        }
    }
    else if ($q == "register") {
        $userid = $_REQUEST["userid"];
        $name = $_REQUEST["name"];
        $email = $_REQUEST["email"];
        $password = $_REQUEST["password"];
        
        mysqli_select_db($con,"Users");

        $query = "SELECT * FROM `Users` WHERE `username` = '$userid' and `password` = MD5('$password')";
        
        $exists = mysqli_fetch_array(mysqli_query($con,$query)); 
        
        if (!$exists) {
            $query = "INSERT INTO `Users` VALUES(NULL,'$name','$userid',MD5('$password'),'$email','','user')";
            mysqli_query($con,$query); 
            echo "new user";
        }
    }
    else if ($q == "resetPassword") {
        
    }
?>
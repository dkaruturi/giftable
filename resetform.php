<html>
<link rel="stylesheet" type="text/css" href="login.css">
<body>   
 <?
        session_start();
    
        //Checks url to make sure user corresponds to url unique id
        if(isset($_POST["password"])) {
            $hash = $_SESSION["hash"];
            unset($_SESSION["hash"]);
            $username= $_POST["username"];
            $password = MD5($_POST["password"]);   
            
            echo $password;
            $db = new mysqli('localhost', 'root', '', 'localhost');
            
            $result = $db->query("SELECT * FROM `Users` WHERE `username` = '$username' AND `hash` = '$hash[1]'");
            
            echo "SELECT * FROM `Users` WHERE `username` = '$username' AND `hash` = '$hash[1]'";
            $result = mysqli_fetch_array($result);
            print_r($result);
            
            if ($result[0]) {
                $db->query("UPDATE `Users` SET `password`='$password' WHERE `username` = '$username'");
                header('Location: login.php');
                exit;
            } else {
                header('Location: login.php');
                exit;
            }
    
        } else {
            $_SESSION["hash"] = explode("?q=",$_SERVER['REQUEST_URI']);
    ?> 
    
<div id="content">   
    <h1>Set New Password</h1>
<form action="resetform.php" method="post">
    <p>
    <label>Username
    <input type="text" name="username" required>
    </label> 
    </p>

    <p>
    <label>Password
    <input type="password" name="password">
    </label>
    </p>

    <p><input type="submit" onclick="resetPassword()" name="submit" value="Submit"/></p>

</form>         
</div>
    
    
<?php
    } 
    ?>
</body>
</html>
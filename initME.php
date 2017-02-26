<!DOCTYPE html>
<html>
 <head>
 </head>
 <body>
 <?php
     
     // INITIALIZE DATABASE
     
      $db = new mysqli('localhost', 'root', '', 'localhost');
      if ($db->connect_error):
          die ("Could not connect to db: " . $db->connect_error);
      endif;

      $db->query("drop table Tickets"); 
      $db->query("drop table Users");
      $db->query("drop table Problem");

     
       $result = $db->query(
                "CREATE TABLE `localhost`.`Users` ( `User #` INT NOT NULL AUTO_INCREMENT, `name` VARCHAR(100) NOT NULL ,`username` VARCHAR(100) NOT NULL , `password` VARCHAR(100) NOT NULL , `email` VARCHAR(100) NOT NULL, `hash` VARCHAR(500) NOT NULL, `type` VARCHAR(100) NOT NULL , PRIMARY KEY (`User #`))") or die ("Invalid: " . $db->error);
      
      $db->query("INSERT INTO `USERS` VALUES (NULL,'Sanjana Mendu','sanjana',MD5('sanjana'),'sanjanamendu@gmail.com','','admin')");
      $db->query("INSERT INTO `USERS` VALUES (NULL,'Admin1','admin1',MD5('admin1'),'sanjanamendu+admin1@gmail.com','','admin')");
      $db->query("INSERT INTO `USERS` VALUES (NULL,'Admin2','admin2',MD5('admin2'),'sanjanamendu+admin1@gmail.com','','admin')");
      $db->query("INSERT INTO `USERS` VALUES (NULL,'User1','user1',MD5('user1'),'sanjanamendu+admin1@gmail.com','','user')");
      $db->query("INSERT INTO `USERS` VALUES (NULL,'Jane Tennison','user2',MD5('user2'),'sanjanamendu+tennison@gmail.com','','user')");
     
      $result = $db->query(
                "CREATE TABLE `localhost`.`Problem` (`ID` INT NOT NULL, `Problem` VARCHAR(100) NOT NULL , PRIMARY KEY (`ID`))") or die ("Invalid: " . $db->error);
      
      $result = $db->query(
                 "CREATE TABLE `localhost`.`Tickets` ( `Ticket #` INT NOT NULL AUTO_INCREMENT, `Received` TIMESTAMP NOT NULL , `Sender Name` VARCHAR(100) NOT NULL , `Sender Email` VARCHAR(100) NOT NULL , `Subject` VARCHAR(100) NOT NULL, `Tech` VARCHAR(100) NOT NULL , `Status` VARCHAR(30) NOT NULL, PRIMARY KEY (`Ticket #`) )") or die ("Invalid: " . $db->error);
     
      $cars = file("tickets.flat");
     
      foreach ($cars as $carstring)
      {
          $carstring = rtrim($carstring);
          $car = explode(',', $carstring);
          $car[1] = date("Y-m-d G:i:s", strtotime($car[1]));
          $car[3] = "sanjanamendu+".$car[3];
          
          $query = "INSERT INTO `Tickets` VALUES
          ('$car[0]','$car[1]','$car[2]','$car[3]','$car[4]','$car[6]','$car[7]')"; 
          $db->query($query) or die ("Invalid insert " . $db->error);
          $query = "INSERT INTO `Problem` VALUES
          ('$car[0]', '$car[5]')"; 
          $db->query($query) or die ("Invalid insert " . $db->error);
      } 
     
//      echo "filled tickets <br/>";
     
      echo "<b>The database has been initialized with the following tables:</b>";
      echo "<br /><br />";


      $tables = array("Tickets"=>array("Ticket #", 
                                   "Received", "Sender Name", "Sender Email", "Subject","Tech","Status"),
                      "Problem"=>array("ID","Problem"),
                      "Users"=>array("User #","username", "password","email","type"));
     
      foreach ($tables as $curr_table=>$curr_keys):
         $query = "select * from " . $curr_table; #Define query
         $result = $db->query($query);  #Eval and store result
         $rows = $result->num_rows; #Det. num. of rows
         $keys = $curr_keys;
?>
      <table border = "1">
      <caption><?php echo $curr_table;?></caption>
      <tr align = "center">
<?php
         foreach ($keys as $next_key):
             echo "<th>$next_key</th>";
         endforeach;
         echo "</tr>"; 
         for ($i = 0; $i < $rows; $i++):  #For each row in result table
             echo "<tr align = center>";
             $row = $result->fetch_array();  #Get next row
             foreach ($keys as $next_key)  #For each column in row
             {
                  echo "<td> $row[$next_key] </td>"; #Write data in col
             }
             echo "</tr>";
         endfor;
         echo "</table><br />";
      endforeach;
?>

<form action="login.php" method="post">
    <p>
    <label><button type="submit" name="reset">Proceed to Login Screen</button>
    </label>
    </p> 
</form>
          
          
 </body>
</html>
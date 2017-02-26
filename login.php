<html>
<link rel="stylesheet" type="text/css" href="login.css">
<?php 
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    } 
    
    $_SESSION["sort"] = "";
    $q = "";
    
    if (isset($_REQUEST["q"])) {
        $q = $_REQUEST["q"];
    }
    if ($q == "logout") {
        session_unset();
    }
    
//    Check credentials
    if(isset($_SESSION["admin"]) || isset($_POST["username"])) {
        
        if (isset($_POST["username"])) {
            
            $username = $_POST["username"];
            $password = $_POST["password"];
            
            
            $_SESSION["username"] = $username;
            $db = new mysqli('localhost', 'root', '', 'localhost');
            $query = $db->query("select type from Users where username = '$username' and  password = MD5('$password')");
            $query = mysqli_fetch_array($query);
            
            if ($query[0] == 'user') {
               $_SESSION["admin"] = "";
               header("Location: user.php");
            } 
            else if ($query[0] == 'admin') {
               $_SESSION["admin"] = "";
               header("Location: admin.php");
            }
        }
    } 
    else {
?>
<script>
    
    var registerResult;
    
    window.onclick = function(event) {
        var modal1 = document.getElementById('resetModal');
        var modal2 = document.getElementById('registerModal');
        if (event.target == modal1) {
            modal1.style.display = "none";
        } else if (event.target == modal2){
            modal2.style.display = "none";
            var elements = document.getElementById('registerUserForm').childNodes;
            for (var i in elements) {
                if (i!=6) elements[i].value = "";
            }
            document.getElementById('register_footer').innerHTML = "";
        }
    };
    
    function makeRequest(str) {
        if (window.XMLHttpRequest) {
            xmlhttp=new XMLHttpRequest();
        } 
        else {
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (str == "reset") {
            var userid = document.getElementById('userid').value;
            xmlhttp.onreadystatechange = function() { 
              console.log(xmlhttp.responseText);
            };
            xmlhttp.open("GET","process.php?q="+str+"&userid="+userid,true);
            xmlhttp.send();
        }
        if (str == "register") {
            var userid = document.getElementById('reguserid').value;
            var name = document.getElementById('firstname').value+" "+document.getElementById('lastname').value;
            var email = document.getElementById('email').value;
            var password = document.getElementById('pass').value;
            
            xmlhttp.onreadystatechange = function() { 
              console.log(xmlhttp.responseText);
              completeRegister(xmlhttp.responseText);
            };
            xmlhttp.open("GET","process.php?q="+str+"&userid="+userid+"&name="+name+"&email="+email+"&password="+password,true);
            xmlhttp.send();
        }
    } 
    
    function showResetModal() {
        var modal = document.getElementById('resetModal');
        var header = document.getElementById("reset_header");
        var form = document.getElementById("reset_form");
    
        modal.style.display = 'block';
        header.style.display = 'block';
        form.style.display = 'block';
    }
   
    function showRegisterModal() {
        var modal = document.getElementById('registerModal');
        var header = document.getElementById("register_header");
        var form = document.getElementById("register_form");
    
        modal.style.display = 'block';
        header.style.display = 'block';
        form.style.display = 'block';
    }
    
    function createPassForm() {
        var e = document.getElementById("reset_form");
        e.style.display = 'block';

        var form = document.createElement("form");
        form.id = "resetPasswordForm";
        var userid = document.createElement("input");
            userid.id = "userid";
            userid.type = "text";
            userid.placeholder = "Username";
        var submit = document.createElement('input');
            submit.id = "submit";
            submit.type = "button";
            submit.onclick = function() {
                makeRequest("reset");
                completeNewPass();
            };
            submit.setAttribute("value", "Send Reset Email");

        form.appendChild(userid);
        form.appendChild(submit);
        e.appendChild(form);
    } 
    
    function createForm() {
        var e = document.getElementById("register_form");
        e.style.display = 'block';

        var form = document.createElement("form");
        form.id = "registerUserForm";
        var userid = document.createElement("input");
            userid.id = "reguserid";
            userid.type = "text";
            userid.placeholder = "User ID";
        var firstname = document.createElement("input");
            firstname.id = "firstname";
            firstname.type = "text";
            firstname.placeholder = "First Name";
        var lastname = document.createElement("input");
            lastname.id = "lastname";
            lastname.type = "text";
            lastname.placeholder = "Last Name";
        var email = document.createElement("input");
            email.id = "email";
            email.type = "email";
            email.placeholder = "Email";
        var pass = document.createElement("input");
            pass.id = "pass";
            pass.type = "password";
            pass.placeholder = "Password";
        var conf_pass = document.createElement("input");
            conf_pass.id = "conf_pass";
            conf_pass.type = "password";
            conf_pass.placeholder = "Confirm Password";
        var submit = document.createElement('input');
            submit.id = "submit";
            submit.type = "button";
            submit.onclick = function() {
                var password = document.getElementById('pass').value;
                var confirm = document.getElementById('conf_pass').value;
                if (confirm == password) {
                    document.getElementById('register_footer').innerHTML = "";
                    registerResult = "";
                    makeRequest("register");
                } else {
                    alert("Passwords do not match!");
                }
            };
            submit.setAttribute("value", "Register");

        form.appendChild(userid);
        form.appendChild(firstname);
        form.appendChild(lastname);
        form.appendChild(email);
        form.appendChild(pass);
        form.appendChild(conf_pass);
        form.appendChild(submit);
        e.appendChild(form);
    }   
    
    function completeNewPass() {
        if (document.getElementById("userid").value != '') {
            document.getElementById("userid").value = '';
            alert("Password reset link has been sent!");
        }
    }
    
    function completeRegister(str) {
        if (str == "") {
            document.getElementById('register_footer').innerHTML = "ERROR: User already exists";
        } 
        else {
            document.getElementById('register_footer').innerHTML = "New User Successfully Registered";
        }
    }
    
    function resetPassword() {
        showResetModal();
        if (document.getElementById("reset_form").innerHTML == "") {
            createPassForm();
        }
    }
    
    function registerUser() {
        showRegisterModal();
        if (document.getElementById("register_form").innerHTML == "") {
            createForm();
            console.log(document.getElementById('registerUserForm').childNodes);
        }
    }
    
</script>    
<body>  
<div id="resetModal" class="modal" style="display: none;">
  <div class="modal-content" id='reset_content'>
      <div class="modal-header" id="reset_header">Reset Password</div>
      <h4>Please enter your user ID. The reset link will be sent to the email on file.</h4>
    <div class="modal-form" id="reset_form"></div>
  </div>
</div> 
    
<div id="registerModal" class="modal" style="display: none;">
  <div class="modal-content" id='register_content'>
    <div class="modal-header" id="register_header">Register New User</div>
    <div class="modal-form" id="register_form"></div>
    <div class="modal-footer" id="register_footer"></div>
  </div>
</div>     
    
<div id="content">    
<form action="login.php" method="post">
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

    <p><button type="submit">Login</button></p>
</form>

    <p>
    <label id="reset"> Forgot your password? | 
        <a onclick="resetPassword()">Reset Password</a>
    </label>
    </p> 

    <p>
    <label id="register">Not a user? |
        <a onclick="registerUser()">Click here to register</a>
    </label>
    </p> 
        
</div>
    
<?php } ?> 
</div>    
</body>
</html>
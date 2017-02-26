<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="user.css">
 
<?php  
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    }  
    if (isset($_SESSION["username"])) {  
?>  
    
<script>
       
var seenTickets = false;
    
// AJAX Request Function
function makeRequest(str) {
  if (window.XMLHttpRequest) {
    xmlhttp=new XMLHttpRequest();
  } else {
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (str == "submitTicket") {
      var subject = document.getElementById('subject').value;
      var problem = document.getElementById('problem').value;
      xmlhttp.onreadystatechange = function() { 
          console.log(xmlhttp.responseText);
          formatTable(xmlhttp, str); 
      };
      xmlhttp.open("GET","process.php?q="+str+"&subject="+subject+"&problem="+problem,true);
      xmlhttp.send();
  }
  if (str == "seeTickets") {
      xmlhttp.onreadystatechange = function() { formatTable(xmlhttp, str); };
      xmlhttp.open("GET","process.php?q="+str,true);
      xmlhttp.send();
  }
  if (str == "changePassword") {
      var userid = document.getElementById('userid').value;
      var newpass = document.getElementById('newpass').value;
      var conf = document.getElementById('confirm_newpass').value;
      xmlhttp.onreadystatechange = function() {
          completeNewPass();
      };
      xmlhttp.open("GET","process.php?q="+str+"&userid="+userid+"&newpass="+newpass,true);
      xmlhttp.send();
  }
}      
    
function formatTable(httpRequest, str) {
    if (httpRequest.readyState == 4) {
        if (httpRequest.status == 200) {
            if (httpRequest.responseText != '') {
                var txt = httpRequest.responseText;
                txt = httpRequest.responseText.split(';')
                var curr;
                for (curr in txt) {
                    if (txt[curr] != '') {
                        var doc = JSON.parse(txt[curr]);
                        updateTable(doc);
                    }
                } 
                if (str == "submitTicket") {
                    window.alert("Ticket was successfully submitted!");
                    document.getElementById("subject").value = '';
                    document.getElementById("problem").value = '';
                    toggleButtons("form");
                }
            }
        } else {
            alert('There was a problem with the request.');
        }
    }
}   
 
function updateTable(arr) {
    var tbl = document.getElementById('table');
    var tbdy = document.getElementById('tbdy');
    console.log(arr);
    for (var i = 0; i < arr.length/4; i++) {
        var tr = document.createElement('tr');
        for (var j = 0; j < 4; j++) {
            var td = document.createElement('td');
                td.appendChild(document.createTextNode(arr[j+(4*i)]));
            tr.appendChild(td);
        }
        tbdy.appendChild(tr);
    }
    tbl.appendChild(tbdy);
}    
   
// DOM for submit ticket form      
function createForm() {
    var e = document.getElementById("form");
    e.style.display = 'block';
    
    var form = document.createElement("form");
    form.id = "submitTicketForm";
    var subj = document.createElement("input");
        subj.id = "subject";
        subj.type = "text";
        subj.placeholder = "Subject";
    var prob = document.createElement("textarea");
        prob.id = "problem";
        prob.placeholder = "Problem";
    var submit = document.createElement('input');
        submit.id = "submit";
        submit.type = "button";
        submit.onclick = function() {makeRequest("submitTicket");};
        submit.setAttribute("value", "Submit");
    
    form.appendChild(subj);
    form.appendChild(prob);
    form.appendChild(submit);
    e.appendChild(form);
}       
    
// DOM for change password form        
function createPassForm() {
    var e = document.getElementById("passform");
    e.style.display = 'block';
    
    var form = document.createElement("form");
    form.id = "submitTicketForm";
    var userid = document.createElement("input");
        userid.id = "userid";
        userid.type = "text";
        userid.placeholder = "Existing User Id";
    var newpass = document.createElement("input");
        newpass.id = "newpass";
        newpass.type = "password";
        newpass.placeholder = "New Password";
    var confirm_newpass = document.createElement("input");
        confirm_newpass.id = "confirm_newpass";
        confirm_newpass.type = "password";
        confirm_newpass.placeholder = "Confirm New Password";
    var submit = document.createElement('input');
        submit.id = "submit";
        submit.type = "button";
        submit.onclick = function() {makeRequest("changePassword");};
        submit.setAttribute("value", "Submit");
    
    form.appendChild(userid);
    form.appendChild(newpass);
    form.appendChild(confirm_newpass);
    form.appendChild(submit);
    e.appendChild(form);
} 
      
// Change password helper function     
function completeNewPass() {
    document.getElementById("userid").value = '';
    document.getElementById("newpass").value = '';
    document.getElementById("confirm_newpass").value = '';
    toggleButtons("passform");
} 
    
// Manage shown buttons  
function toggleButtons(str) {
    var buttons = document.getElementById("buttons");
    var e = document.getElementById(str);
    if (buttons.style.display == 'none') {
        buttons.style.display = 'block';
        var button5 = document.getElementById("back_".concat(str));
        button5.style.display = 'none';
        e.style.display = 'none';
    } else if (buttons.style.display == 'block')  { 
        buttons.style.display = 'none';
        var button5 = document.getElementById("back_".concat(str));
        button5.style.display = 'block';
        e.style.display = 'block';
    } else {
        buttons.style.display = 'none';
    }
}   
    
// View My Tickets helper function      
function seeTickets() {
    var e = document.getElementById("tableview");
    if (!seenTickets)
    {
        seenTickets = true;
        var button = document.createElement("button");
        var txt = document.createTextNode("Back");
        button.appendChild(txt);
        button.id = "back_tableview";
        button.onclick = function() {toggleButtons("tableview");}; 
        makeRequest("seeTickets");
        e.appendChild(button); 
        toggleButtons("tableview");
        e.style.display = 'block';
    }
    else 
    {    
        toggleButtons("tableview");
    } 
}    
  
// Submit Ticket helper function      
function submitTicket() {
    var e = document.getElementById("form");
    if (e.innerHTML == '')
    {
        var button = document.createElement("button");
        var txt = document.createTextNode("Back");
        button.appendChild(txt);
        button.id = "back_form";
        button.onclick = function() {toggleButtons("form");}; 
        createForm();
        e.appendChild(button); 
        toggleButtons("form");
        e.style.display = 'block';
    }
    else 
    {    
        toggleButtons("form");     
    } 
}     
 
// Change password helper function      
function changePassword() {
    var e = document.getElementById("passform");
    if (e.innerHTML == '')
    {
        var button = document.createElement("button");
        var txt = document.createTextNode("Back");
        button.appendChild(txt);
        button.id = "back_passform";
        button.onclick = function() {toggleButtons("passform");}; 
        createPassForm();
        e.appendChild(button); 
        toggleButtons("passform");
        e.style.display = 'block';
    }
    else 
    {    
        toggleButtons("passform");     
    } 
}    

// Logout redirect     
function logout() {
     window.location="login.php?q=logout";
}    

</script>
</head>
<body>

<div id="all">
    
<div id="buttons">  
    <h4>Please choose an option</h4>
    <div id="cssbtn">
        <button id="submitTicket" onclick="submitTicket()">Submit new ticket</button>
        <button id="seeTickets" onclick="seeTickets()">See all of my tickets</button>
        <button id="changePassword" onclick="changePassword()">Change password</button>
        <button id="logout" onclick="logout()">Logout</button>
    </div>
</div> 
    
<div id="form"></div>   

<div id="passform"></div>    
    
<div id="tableview" style="display:none;">
    <table id="table" border="1">
        <thead>
          <tr>
            <td>Ticket #</td>
            <td>Received</td>
            <td>Subject</td>
            <td>Status</td>
          </tr>
        </thead>
        <tbody id="tbdy"></tbody>
    </table>
   
</div>         
    
</div>    
    
</body>
<?php } ?>    
</html>
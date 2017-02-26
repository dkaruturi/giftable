<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="admin.css">
 
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

var name;
var status;
var currview;
var selectedTicket;
var ticketinfo;
var toggleBtn = "";  
var checkedAssign;
   
// Click to get out of modal view
window.onclick = function(event) {
    var modal = document.getElementById('selected');
    var header = document.getElementById("selected_header");
    var body = document.getElementById("selected_body");
    var footer = document.getElementById("selected_footer");
    if (event.target == modal) {
        modal.style.display = "none";
        header.innerHTML = "";
        body.innerHTML = "";
        footer.innerHTML = "";
    }
};

// Initially load table
window.onload = function() {
    makeRequest("name");
    makeRequest("start"); 
};
    
// AJAX Request Function
function makeRequest(str) {
    if (window.XMLHttpRequest) {
    xmlhttp=new XMLHttpRequest();
    } 
    else {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (str == "name") {
        xmlhttp.onreadystatechange = function() {
            var fullname = xmlhttp.responseText.split(' ');
            if (name == '' && fullname[0]!="") {
              name = fullname[0];
            }
            name = 'Sanjana';
        };
        xmlhttp.open("GET","process.php?q="+str,true);
        xmlhttp.send();
    }
    else if (str == "start") {
        xmlhttp.onreadystatechange = function() { 
//        console.log(xmlhttp.responseText);
            if (xmlhttp.readyState == 4) {
                initTable(xmlhttp);
            } };
        xmlhttp.open("GET","process.php?q="+str,true);
        xmlhttp.send();
    }
    else if (str == "changePassword") {
        var userid = document.getElementById('userid').value;
        var newpass = document.getElementById('newpass').value;
        var conf = document.getElementById('confirm_newpass').value;
        xmlhttp.onreadystatechange = function() { console.log(xmlhttp.responseText); };
        xmlhttp.open("GET","process.php?q="+str+"&userid="+userid+"&newpass="+newpass,true);
        xmlhttp.send();
    }
    else if (str == "toggle_status") {
        xmlhttp.onreadystatechange = function() { console.log(xmlhttp.responseText); }
        xmlhttp.open("GET","process.php?q="+str+"&ticket="+parseInt(selectedTicket.children[0].innerHTML)+"&status="+status,true);
        xmlhttp.send();
    }
    else if (str == "assign_self") {
        xmlhttp.onreadystatechange = function() { 
            
//            console.log(xmlhttp.responseText);
            if (xmlhttp.responseText == 'none') {
                checkedAssign = true;
                selectedTicket.children[5].innerHTML = '';
                document.getElementById('selected_body').childNodes[18].innerHTML = '';
            }
            else if (xmlhttp.responseText == name) {
                checkedAssign = true;
                selectedTicket.children[5].innerHTML = xmlhttp.responseText;
                document.getElementById('selected_body').childNodes[18].innerHTML = xmlhttp.responseText;
            } else if (!checkedAssign) {
//                console.log(xmlhttp.responseText); 
                document.getElementById('selected_footer').innerHTML = xmlhttp.responseText;
                console.log(document.getElementById('selected_footer').innerHTML);
            }
        };
//        console.log(selectedTicket.children[5].innerHTML);
        xmlhttp.open("GET","process.php?q="+str+"&ticket="+parseInt(selectedTicket.children[0].innerHTML)+"&currtech="+selectedTicket.children[5].innerHTML,true);
        xmlhttp.send();
    }
    else if (str == "email_sender") {
        var subject = document.getElementById('subject').value;
        var problem = document.getElementById('problem').value;
        var receiver = selectedTicket.children[3].innerHTML
//            console.log(subject + " " + problem + " " + receiver);
            xmlhttp.onreadystatechange = function() { console.log(xmlhttp.responseText);
            if (xmlhttp.responseText == '') {
                document.getElementById('selected_footer').innerHTML = "Email sucessfully sent!";
            } 
        };
        xmlhttp.open("GET","process.php?q="+str+"&subject="+subject+"&problem="+problem+"&receiver="+receiver,true);
        xmlhttp.send();
    }
    else if (str == "delete") {
        xmlhttp.onreadystatechange = function() { 
            console.log(xmlhttp.responseText);                                
            if (xmlhttp.responseText != '') {
                document.getElementById('selected_footer').innerHTML = xmlhttp.responseText;
            }
        };
        xmlhttp.open("GET","process.php?q="+str+"&ticket="+parseInt(selectedTicket.children[0].innerHTML),true);
        xmlhttp.send();
    }
}
 
// Parse all table data
function initTable(httpRequest, str) {
    if (httpRequest.readyState == 4) {
        if (httpRequest.status == 200) {
            if (httpRequest.responseText != '') {
                var tbl = document.getElementById('table');
                var tbdy = document.getElementById('tbdy');
                var txt = httpRequest.responseText.split(';');
                var curr;
                for (curr in txt) {
                    if (txt[curr] != '') {
                        var arr = JSON.parse(txt[curr]);
                        for (var i = 0; i < arr.length/8; i++) {
                            var tr = document.createElement('tr');
                            tr.onclick = function () { selectTicket(this); }
                            for (var j = 0; j < 8; j++) {
                                var td = document.createElement('td');
                                if (j==7) {
                                    td.style.display = 'none';
                                }
                                td.appendChild(document.createTextNode(arr[j+(8*i)]));
                                tr.appendChild(td);
                            }
                            tbdy.appendChild(tr);
                        }
                        tbl.appendChild(tbdy);
                    }
                } 
            }
        } else {
            alert('There was a problem with the request.');
        }
    }
    currview = 'view_open';
    viewTickets(6,"open");
}   
    
// Sort Table in JS function
function sortTable(colnum) {
    
    var table = document.getElementById("table");
    var tbody = document.getElementById("tbdy");
    var items = tbody.children;   
    
    var columnVals = [];
    var rows = [];
    
    for (var i in items) {
        if (items[i].nodeType == 1) {
            rows.push(items[i]);
            var entry = items[i].children[colnum].innerHTML;
            if (colnum == 0) {
                columnVals.push(parseInt(entry));
            } else {
                columnVals.push(entry);
            }
        }
    }

//    console.log(columnVals);
    
    columnVals.sort(function(a, b) {
      return a == b ? 0 : (a < b ? 1 : -1);
    });

//    console.log(columnVals);
        
    var new_tbody = document.createElement("tbody");
    new_tbody.id = "tbdy";
    while ((val=columnVals.pop()) != null) {
        for (var k=0; k<rows.length; k++) {
            if (rows[k].children[colnum].innerHTML == val) {
                new_tbody.appendChild(rows[k]);
            }
        }
    }
    
    table.removeChild(tbody);
    table.appendChild(new_tbody);
    
}
  
// Select Ticket and show in Modal
function selectTicket(row) {
    var modal = document.getElementById('selected');
    var header = document.getElementById("selected_header");
    var body = document.getElementById("selected_body");
    
    var cells = row.childNodes;
    var head_row = document.getElementById("thead").childNodes[1];
    var label = head_row.children;
    
    header.innerHTML = "Ticket #"+cells[0].innerHTML;
    
    selectedTicket = row;
    
    for (var i=1; i<label.length; i++) {
        body.innerHTML += "<b>"+label[i].innerHTML+"</b> : <a>"+cells[i].innerHTML+"</a></br>";
    }
    
    modal.style.display = 'block';
    header.style.display = 'block';
    body.style.display = 'block';
}   

// Create email sender form
function emailSender() {
    var e = document.getElementById("selected_form");
    if (e.innerHTML == '')
    {
        var button = document.createElement("button");
        var txt = document.createTextNode("Back");
        button.appendChild(txt);
        button.id = "back_selected_form";
        button.onclick = function() {
            document.getElementById("subject").value = "";
            document.getElementById("problem").value = "";
            toggleButtons("selected_form");
        }; 
        createForm();
        e.appendChild(button); 
        toggleButtons("selected_form");
        e.style.display = 'block';
    }
    else 
    {    
        toggleButtons("selected_form");
    } 
}     
    
// DOM for email form    
function createForm() {
    var e = document.getElementById("selected_form");
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
        submit.onclick = function() {makeRequest("email_sender");};
        submit.setAttribute("value", "Send");
    
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
        submit.onclick = function() {
            makeRequest("changePassword");
            completeNewPass();
        };
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
    alert("Password successfully changed!");
} 
    
// Show View Open Tickets    
function toggleButtons(str) {
    if (str == "view_open") {
            var btn = document.getElementById(str);
            btn.id = toggleBtn;
            btn.onclick = function() { action(btn.id); };
            btn.innerHTML = setButtonVal(btn.id);
            toggleBtn = "";
} 
    else if (str == "selected_form") {
        var buttons = document.getElementById("selected_buttons");
        var content = document.getElementById("selected_body");
        var e = document.getElementById(str);
        if (buttons.style.display == 'none') {
            buttons.style.display = 'block';
            content.style.display = 'block';
            var button5 = document.getElementById("back_".concat(str));
            button5.style.display = 'none';
            e.style.display = 'none';
        } else if (buttons.style.display == 'block')  { 
            buttons.style.display = 'none';
            content.style.display = 'none';
            var button5 = document.getElementById("back_".concat(str));
            button5.style.display = 'block';
            e.style.display = 'block';
        } else {
            buttons.style.display = 'none';
            content.style.display = 'none';
        }
    } 
    else if (toggleBtn == "") {
        var btn = document.getElementById(str);
            toggleBtn = btn.id;
        console.log(toggleBtn);
        btn.id = "view_open";
        btn.onclick = function() { action("view_open"); }
        btn.innerHTML = "View Open Tickets";
    } 
    else {
        var btn1 = document.getElementById("view_open");
        var btn2 = document.getElementById(str);

        btn1.id = toggleBtn;
        btn1.onclick = function() { action(btn1.id); };
        btn1.innerHTML = setButtonVal(btn1.id);

        toggleBtn = btn2.id;
        console.log(toggleBtn);
        btn2.id = "view_open";
        btn2.onclick = function() { action("view_open"); };
        btn2.innerHTML = "View Open Tickets";
    }
}     

// Show and Hide HTML elements using DOM   
function toggleItem(item) {
    var buttons = document.getElementById("buttons");
    var table = document.getElementById("tableview");
    if (buttons.style.display == 'none') {
        var button5 = document.getElementById("back");
        var e = document.getElementById(item);
        button5.style.display = 'none';
        e.style.display = 'none';
        buttons.style.display = 'block';
        table.style.display = 'block';
    } else if (buttons.style.display == 'block')  { 
        var button5 = document.getElementById("back");
        var e = document.getElementById(item);
        button5.style.display = 'block';
        e.style.display = 'block';
        buttons.style.display = 'none';
        table.style.display = 'none';
    } else {
        buttons.style.display = 'none';
        table.style.display = 'none';
    }
}     
  
// View Open Tickets helper function    
function setButtonVal(str) {
    switch(str) {
    case "view_all":
        return "View All Tickets";
        break;
    case "view_my":
        return "View My Tickets";
        break;
    case "view_unassigned":
        return "View Unassigned Tickets";
        break;
    }
}    
  
// View All Tickets helper function
function resetView() {
    for (var i = 0, row; row = tbdy.rows[i]; i++) {
        row.style.display = '';
    }
}
    
// Show appropriate tickets
function viewTickets(colnum, str) {  
    resetView();
    var tbdy = document.getElementById('tbdy');
    for (var i = 0, row; row = tbdy.rows[i]; i++) {
        curr = row.cells;
        console.log(str);
        if (str == 'none') {
            if (curr[colnum].innerHTML != '') {
                row.style.display = 'none';
            }
        } else if (str == '') {
            row.style.display = '';
        } else if (curr[colnum].innerHTML != str) {
            row.style.display = 'none';
        }
    }
}  
    
// Find Similar/Same Sender Tickets helper function   
function findTickets(param,str) {
    if (param == "all") {
        var table = document.getElementById("table");
        var tbody = document.getElementById("tbdy");
        var items = tbody.children;   

        var columnVals = [];
        
        for (var i in items) {
            if (items[i].nodeType == 1) {
                var entry = items[i].children[2].innerHTML;
                if (entry != str) {
                    items[i].style.display = 'none';
                }
            }
        }
    }
    else if (param == "similar") {
        var arr = str.toLowerCase().split(' ');
        
        var table = document.getElementById("table");
        var tbody = document.getElementById("tbdy");
        var items = tbody.children;   

        var columnVals = [];
        
        for (var i in items) {
            var similar = false;
            if (items[i].nodeType == 1) {
                var entry = items[i].children[4].innerHTML.toLowerCase();
                entry = entry.split(' ');
//                console.log(entry);
                for (var j in arr) {
                    if (entry.includes(arr[j])) {
                        similar = true;
                    }
                }
                if (!similar) {
                    items[i].style.display = 'none';
                }
            }
        }
//        console.log(arr);   
    }
}    
 
// Handle change password form
function changePassword() {
    var e = document.getElementById("passform");
    if (e.innerHTML == '')
    {
        var button = document.createElement("button");
        var txt = document.createTextNode("Back");
        button.appendChild(txt);
        button.id = "back";
        button.onclick = function() { toggleItem("passform"); }; 
        createPassForm();
        e.appendChild(button); 
        toggleItem("passform");  
        e.style.display = 'block';
    }
    else 
    {    
        toggleItem("passform");     
    } 
}    

// Manage button click events   
function action(str) {
    if (str == "view_all") {
        currview = "view_all";
        resetView();
        toggleButtons(str);
    }
    if (str == "view_my") {
        currview = "view_my";
        viewTickets(5,name)
        toggleButtons(str);
         
     }
    if (str == "view_unassigned") {
        currview = "view_unassigned";
        viewTickets(5,'none')
        toggleButtons(str);
    }
    if (str == "view_open") {
        currview = "view_open";
        viewTickets(6,"open");
        toggleButtons(str);
    }
    if (str == "changePassword") {
        changePassword();
    }
    if (str == "toggle_status") {
        document.getElementById('selected_footer').innerHTML = '';
//            console.log(toggleBtn);
        status = selectedTicket.children[6].innerHTML;
        makeRequest('toggle_status');
        if (status == 'open') { 
            selectedTicket.children[6].innerHTML = 'closed';
            document.getElementById('selected_body').childNodes[22].innerHTML = 'closed';
        } 
        else { 
            selectedTicket.children[6].innerHTML = 'open'; 
            document.getElementById('selected_body').childNodes[22].innerHTML = 'open';
        }
    }
    if (str == "assign_self") {
        makeRequest('assign_self');
        checkedAssign = false;
    }
    if (str == "email_sender") {
        emailSender();
        if (document.getElementById('selected_footer').innerHTML != '') {
            selectedTicket.children[5].innerHTML = name;
        }
    }
    if (str == "delete") {
        makeRequest('delete'); document.getElementById('tbdy').removeChild(selectedTicket);
        if (document.getElementById('selected_footer').innerHTML != '') {
            selectedTicket.children[5].innerHTML = name;
        }
    }
    if (str == "find_all") {
        findTickets("all",selectedTicket.children[2].innerHTML)
    }
    if (str == "find_similar") {
        findTickets("similar",selectedTicket.children[4].innerHTML);
    }
}        
     
// Logout redirect    
function logout() {
     window.location="login.php?q=logout";
}    
  
// Modal display helper function
function hideFooter(footer) {
    footer.innerHTML = '';
}   
    
</script>
</head>
<body>
 
<div id="all">
    
<div id="buttons">  
    <div id="cssbtn">
        <button id="view_all" onclick="action('view_all')">View All Tickets</button>
        <button id="view_my" onclick="action('view_my')">View My Tickets</button>
        <button id="view_unassigned" onclick="action('view_unassigned')">View Unassigned Tickets</button>
        <button id="changePassword" onclick="action('changePassword')">Change password</button>
        <button id="logout" onclick="logout()">Logout</button>
    </div>
</div>   

<div id="passform"></div>      
  
<div id="selected" class="modal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header" id="selected_header"></div>
    <div class="modal-body" id="selected_body"></div>
    <div class="modal-form" id="selected_form"></div>
    <div class="modal-buttons" id="selected_buttons">
        <button id="toggle_status" onclick="action('toggle_status')">Close/Reopen</button>
        <button id="assign_self" onclick="action('assign_self')">Assign/Unassign Self</button>
        </br>
        <button id="email_sender" onclick="action('email_sender')">Email Sender</button>
        <button id="delete" onclick="action('delete')">Delete</button>
        </br>
        <button id="find_all" onclick="action('find_all')">Find All From Sender</button>
        <button id="find_similar" onclick="action('find_similar')">Find Similar</button>
    </div>
    <div class="modal-footer" id="selected_footer" onclick='hideFooter(this)'></div>
  </div>
</div>  
    
<div id="tableview" style="display:block;">
    <table id="table" border="1">
        <thead id="thead">
          <tr>
            <td onclick="sortTable(0)">Ticket #</td>
            <td onclick="sortTable(1)">Received</td>
            <td onclick="sortTable(2)">Sender Name</td>
            <td onclick="sortTable(3)">Sender Email</td>
            <td onclick="sortTable(4)">Subject</td>
            <td onclick="sortTable(5)">Tech</td>
            <td onclick="sortTable(6)">Status</td>
            <td style="display:none;">Problem</td>
          </tr>
        </thead>
        <tbody id="tbdy"></tbody>
    </table>
   
</div>         
    
</div>    

</body>
<?php } ?>
</html>
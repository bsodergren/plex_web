function logout() {
    var xmlhttp;
    if (window.XMLHttpRequest) {
          xmlhttp = new XMLHttpRequest();
    }
    // code for IE
    else if (window.ActiveXObject) {
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (window.ActiveXObject) {
      // IE clear HTTP Authentication
      document.execCommand("ClearAuthenticationCache");
      window.location.href='/cwp/';
    } else {
        xmlhttp.open("GET", '/cwp/home.php', true, "logout", "logout");
        xmlhttp.send("");
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {window.location.href='/cwp/home.php';}
        }


    }


    return false;
}

function editPlaceholder(id) {
  var x = document.getElementById(id).placeholder;
  if (x !== "") {
    document.getElementById(id).value = x;
  }
}

function checkValue(id) {
    var ph = document.getElementById(id).placeholder;
    var n =  document.getElementById(id).value;
    if (ph == n) {
        document.getElementById(id).value = "";
  }
  }
  
  function setNull(id) {

        document.getElementById(id).value = "NULL";
  
  }
    

 function popup(mylink, windowname,width=800,height=400)
 {
    if (! window.focus)return true;
    var href; 
    if (typeof(mylink) == 'string') href=mylink; else href=mylink.href;
    window.open(href, windowname, 'width='+width+',height='+height+',scrollbars=yes'); 
    return false; 
} 
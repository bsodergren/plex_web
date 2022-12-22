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
    document.getElementById(id).style = "background:white";

  }
}

function hideSubmit(id,text)
{

  
  console.log(id);

  document.getElementById('hiddenSubmit_'+id).value = text; 
}

function doSubmitValue(id)
{
  document.getElementById(id).value = id;
}

function editRadioValue(id)
{

      document.getElementById(id).value = "1";
    
}

function checkValue(id) {
    var ph = document.getElementById(id).placeholder;
    var n =  document.getElementById(id).value;


    const t_arr = id.split('_');

    console.log(t_arr[1]);

    if(t_arr[1] == "studio")
    {
      var ph = "___"
    }

    if(t_arr[1] == "substudio")
    {
      var ph = "___"
    }

    if (ph == n) {
        document.getElementById(id).value = "";
    } else {
      document.getElementById(id).style = "background:white";
    }
  }
  
  function setNull(id)
  {
    var f_id = id + "_id"
    document.getElementById(f_id).value = "NULL";
    document.getElementById(f_id).style = "background:black";
  }
    

 function popup(mylink, windowname,width=800,height=400)
 {
    if (! window.focus)return true;
    var href; 
    if (typeof(mylink) == 'string') href=mylink; else href=mylink.href;
    window.open(href, windowname, 'width='+width+',height='+height+',scrollbars=yes'); 
    return false; 
} 

function editPlaceholder(id) {
  var x = document.getElementById(id).placeholder;

   if (x !== "") {
    document.getElementById(id).value = x;
    document.getElementById(id).style = "background:white";

  }
}

function hideSubmit(id,text)
{
  /*
  if( text == 'save'){
    let redirect_value = document.getElementById('redirect_'+id).value;
    redirect_value += "#video_" + id;
    console.log(redirect_value);
    document.getElementById('redirect_'+id).value = redirect_value ; 
  }
*/
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
    
 function popup(mylink, windowname,width=900,height=600)
 {
    if (! window.focus)return true;
    var href; 
    if (typeof(mylink) == 'string') href=mylink; else href=mylink.href;
    window.open(href, windowname, 'width='+width+',height='+height+',scrollbars=yes'); 
    return false; 
} 



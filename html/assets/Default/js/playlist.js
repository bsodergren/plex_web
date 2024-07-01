let addedTextForm = false;
Element.prototype.remove = function () {
    this.parentElement.removeChild(this);
};
NodeList.prototype.remove = HTMLCollection.prototype.remove = function () {
    for (var i = this.length - 1; i >= 0; i--) {
        if (this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
};

$(document).ready(function () {
    $(".playlist_selector").click(function () {
        var text = $(this);
        if (text[0].checked == false) {
            clearTextBox();
            addedTextForm = false;
        }
        showTextBox();
    });
});

function clearTextBox() {
    $("#playlistTextBox").attr("class", "hidden");
    $("#playlistInputTextBox").remove();
}

function showTextBox() {
    var linkList = document.querySelectorAll(".playlist_selector");

    for (var i = 0; i < linkList.length; i++) {
        if (linkList[i].checked == true) {
            if (addedTextForm == false) {
                var textBox = document.createElement("input");
                textBox.id = "playlistInputTextBox";
                textBox.name = "playlist_name";
                textBox.type = "text";
                textBox.setAttribute("class","pltextBox")

                var form = $("#playlistListBox");
                form.append(textBox);

                $("#playlistTextBox").attr("class", "hidden");
                form.attr("style", "display:block;");

                addedTextForm = true;
                return true;
            }
        }
    }
}

$(document).ready(function () {
    $(".playlistData button").click(function () {
        var text = $(this).attr("id");

        if (text == "PlayAll") {
            if( GetFilename(window.location.href) == 'favorites'){
                return false;
            }
            playlistSubmit(text);
        }

        if (text == "AddAll") {
            $(".playlist_selector").attr("checked", "checked");
            showTextBox();
        }

        if (text == "Clear") {
            $(".playlist_selector").attr("checked", false);
            clearTextBox();
        }

        if (text == "AddPlaylist") {
            playlistSubmit(text);
        }
        if (text == "AddToPlaylist") {
            var playlistid = document.getElementById("PlaylistSelectID").value;
            console.log(playlistid);
            playlistSubmit(text, playlistid);
        }
    });
});

$(document).ready(function () {
    $(".playlistData span").click(function () {
        var text = $(this).attr("id");

        if (text == "AddAll") {
            $(".playlist_selector").attr("checked", "checked");
            showTextBox();
        }


    });
});

function playlistSubmit(action, playlistid = null) {



    var playlistName = "";
    var searchId = null;
    var searchName = document.getElementById("searchId");

    if(searchName != null){
        var searchId = searchName.value;
    }


    var playlistNameItem = document.getElementById("playlistInputTextBox");
    if (playlistNameItem != null) {
        playlistName = playlistNameItem.value;
    }
    if( GetFilename(window.location.href) == 'favorites'){
        playlistName ='Favorites'
    }

    var linkList = document.querySelectorAll(".playlist_selector");
    const videoidList = [];
    for (var i = 0; i < linkList.length; i++) {
        if (linkList[i].checked == true) {
            videoidList.push(linkList[i].value);
        }
    }
    var postData = [];
    postData = {
        search_id: searchId,
        submit: "playlist",
        playlist_name: playlistName,
        // action: action,
        playlist: videoidList,
    };

    postData[action] = true;
    if (playlistid != null) {
        postData["PlaylistID"] = playlistid;
    }

    $.ajax({
        url: "process.php",
        type: "POST",
        data: postData,
        cache: false,
        success: function (data) {
            if (action == "PlayAll") {
                popup(data, "video_popup");
            }
            if (action == "AddPlaylist") {
                window.location.href = data;
            }
            if (action == "AddToPlaylist") {
                $(".playlist_selector").attr("checked", false);
                clearTextBox();
            }
            console.log(data, action);
        },
    });
}

function GetFilename(url)
{
   if (url)
   {
      var m = url.toString().match(/.*\/(.+?)\./);
      if (m && m.length > 1)
      {
         return m[1];
      }
   }
   return "";
}


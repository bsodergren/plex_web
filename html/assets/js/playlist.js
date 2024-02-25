let addedTextForm = false
Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
    for(var i = this.length - 1; i >= 0; i--) {
        if(this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
}

$(document).ready(function () {
    $('.playlist_selector').click(function () {
        var text = $(this)
        if(text[0].checked == false)
        {
            clearTextBox();
            addedTextForm = false
        }
        showTextBox();
    })
})

function clearTextBox()
{
    
    $('#playlistTextBox').attr('class', 'hidden')
    $("#playlistInputTextBox").remove();

}

function showTextBox(){
    var linkList = document.querySelectorAll('.playlist_selector')

    for (var i = 0; i < linkList.length; i++) {
        if (linkList[i].checked == true) {
            if (addedTextForm == false) {

                var textBox = document.createElement('input')
                textBox.id = 'playlistInputTextBox'
                textBox.name = 'playlist_name'
                textBox.type = 'text'

                var form = $('#playlistTextBox')
                form.append(textBox)
                form.attr('class', 'btn')

                addedTextForm = true
                return true
            }
        }
    }
}
         


$(document).ready(function () {
    $('.playlistData button').click(function () {
        var text = $(this).attr('id')
        console.log(text)
        if (text == 'PlayAll') {
            playlistSubmit(text)
        }

        if (text == 'AddPlaylist') {
            playlistSubmit(text)
        }
        if (text == 'AddToPlaylist') {
            playlistSubmit(text)
        }
    })
})

$(document).ready(function () {
    $('.playlistData span').click(function () {
        var text = $(this).attr('id')

        if (text == 'AddAll') {
            $('.playlist_selector').attr('checked', 'checked')
            showTextBox();
        }

        if (text == 'Clear') {
            $('.playlist_selector').attr('checked', false)
            clearTextBox();

        }
    })
})

function playlistSubmit (action) {
    // let mybutton = document.getElementById('ajaxform')
    var form = $('#ajaxform')

    var hiddenAction = document.createElement('input')
    hiddenAction.value = 'true'
    hiddenAction.name = action
    hiddenAction.type = 'hidden'
    form.append(hiddenAction)

    var playlist = document.createElement('input')
    playlist.value = 'playlist'
    playlist.name = 'submit'
    playlist.type = 'submit'

    form.append(playlist)
    form.submit()
}
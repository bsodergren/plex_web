


$(document).ready(function () {
    $('.playlistData button').click(function () {
        var text = $(this).attr('id')
        if (text == 'PlayAll') {
            playlistSubmit(text)
        }

        if (text == 'AddPlaylist') {
            playlistSubmit(text)
        }
    })
})

$(document).ready(function () {
    $('.playlistData span').click(function () {
        var text = $(this).attr('id')

        if (text == 'AddAll') {
            $('.playlist_selector').attr('checked', 'checked')
        }

        if (text == 'Clear') {
            $('.playlist_selector').attr('checked', false)
        }
    })
})

function playlistSubmit (action) {
    // let mybutton = document.getElementById('ajaxform')
    var form = $('#ajaxform')
    console.log(action);

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


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

    var form = $('#ajaxform')

    var hiddenAction = document.createElement('input')
    hiddenAction.value = 'true'
    hiddenAction.name = action
    hiddenAction.type = 'hidden'

    var playlist = document.createElement('input')
    playlist.value = 'playlist'
    playlist.name = 'submit'
    playlist.type = 'submit'

    form.append(hiddenAction)
    form.append(playlist)
}
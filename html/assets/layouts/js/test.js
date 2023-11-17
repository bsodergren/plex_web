var $editTitle = $('.editTitle')

function makeTitleEditable () {
    $editTitle.editable({
        emptyMessage: 'Please write something...',
        callback: function (data) {
            console.log('Stopped editing ' + data.$el[0].nodeName)
            if (data.content) {
                $.ajax({
                    type: 'post',
                    url: 'process.php?video_edit=1',

                    data: jQuery.param({ Title: data.content })
                })
                console.log('   * The text was changed -> ' + data.content)
            }
        }
    })
}

// Listen on when elements getting edited
$editTitle.on('edit', function ($textArea) {
    console.log('Started editing element ' + this.nodeName)
})

makeTitleEditable()

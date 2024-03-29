
var $!!EDITABLE!! = $('.!!EDITABLE!!')

function !!FUNCTION!!() {
    $!!EDITABLE!!.editable({
        emptyMessage: 'Please write something...',
        callback: function (data) {
            console.log('Stopped editing ' + data.$el[0].nodeName)
            if (data.content) {
                $.ajax({
                    type: 'post',
                    url: 'process.php',
                    data: jQuery.param({
                        submit: 'updateChapter',
                        !!ID_NAME!!: data.content,
                        video_key: '!!VIDEO_KEY!!'
                        }),
                        success: function (data) {
                            console.log(data);
                            window.opener.location.reload(true);
                           // window.close();
                        }
                })
                console.log('   * The text was changed -> ' + data.content)
            } 
        }
    })
}

// Listen on when elements getting edited
$!!EDITABLE!!.on('edit', function () {
    console.log('Started editing element ' + this.nodeName)
})

!!FUNCTION!!()

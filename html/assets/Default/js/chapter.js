
var $editChapter = $('.editChapter')

function chapterEditor() {
    $editChapter.editable({
        emptyMessage: 'Please write something...',
        callback: function (data) {
            console.log('Stopped editing ' + data.$el[0].nodeName)

            editBox = data.$el[0].id;
            const editBoxArr = editBox.split("_");
            var metafield = editBoxArr[0];
            var videoId = editBoxArr[1];
            var chapterId = editBoxArr[2];

            chapterAction = 'updateChapter'
            let value = "";

            if (data.content !== false) {
                 value = data.content.trim();

                if (value == "") {
                    chapterAction = 'deleteChapter'
                }


                $.ajax({
                    type: 'post',
                    url: 'process.php',
                    data: jQuery.param({
                        submit: chapterAction,
                        id: videoId,
                        chapterText: value,
                        chapterId: chapterId,
                        }),
                        success: function (data) {
                            const videoCell = document.getElementById("videoChapterList");
                            console.log(videoCell)
                           // if (videoCell != null) {
                                videoCell.innerHTML = data;
                           // }
                            // console.log(data);
                            // window.opener.location.reload(true);
                           // window.close();
                        }
                })
                console.log('   * The text was changed -> ' + data.content)
            }
            //}
        }
    })
}

// Listen on when elements getting edited
$editChapter.on('edit', function () {
    console.log('Started editing element ' + this.nodeName)
})

chapterEditor()

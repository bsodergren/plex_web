let mybutton = document.getElementById('myBtn')

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction()
}

function scrollFunction () {
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        mybutton.style.display = 'block'
    } else {
        mybutton.style.display = 'none'
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction () {
    document.body.scrollTop = 0 // For Safari
    document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
}

const writeLog = function (msg) {
    let date = new Date()
    window.console.log(date.toISOString() + ' ' + msg)
}

jQuery(document).ready(function () {
    $('.rating').on('change', function (e) {
        console.log('Rating selected: ' + e.target.id)
        console.log('Rating selected: ' + $(this).val())
        $.ajax({
            type: 'post',
            url: 'process.php',
            data: {
                submit: 'rating',
                id: e.target.id,
                rating: $(this).val()
            },
            cache: false,
            success: function () {
                // window.opener.location.reload(true);
                // window.close();
            }
        })
    })
})

$('#ajaxform').submit(function (e) {
    var postData = $(this).serializeArray()
    $.ajax({
        url: 'process.php',
        type: 'POST',
        data: postData,
        success: function (data) {
            const substring = 'video.php'
            if (data.includes(substring) == true) {
                popup(data, 'PlaylistWindow')
            } else {
                window.location = data
            }
            r
        }
    })
})

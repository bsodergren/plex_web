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
        $.ajax({
            type: 'post',
            url: 'process.php',
            data: {
                submit: 'rating',
                id: e.target.id,
                rating: $(this).val()
            },
            cache: false,
            success: function (data) {
                let close = document.getElementById('close_window')
                if (window.opener != null) {
                    window.opener.location.reload(true)
                    if (close == null) {
                        
                        window.close()
                    }
                } else {
               //     window.location.reload(true)
                }
                //
            }
        })
    })
})

$('#ajaxform').submit(function (e) {
    var postData = $(this).serializeArray()
    console.info(postData)
    $.ajax({
        url: 'process.php',
        type: 'POST',
        data: postData,
        success: function (data) {
            const substring = 'video.php'
            if (data.includes(substring) == true) {
                popup(data, 'PlaylistWindow')
            } else {
                window.location.href = data
            }
        }
    })
})


const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

console.log (tooltipList);
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))


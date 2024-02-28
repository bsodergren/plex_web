function editPlaceholder (id) {
    var x = document.getElementById(id).placeholder

    if (x !== '') {
        document.getElementById(id).value = x
        document.getElementById(id).style = 'background:white'
    }
}

function hideSubmit (id, text) {
    document.getElementById('hiddenSubmit_' + id).value = text
}

function doSubmitValue (id) {
    document.getElementById(id).value = id
}

function editRadioValue (id) {
    document.getElementById(id).value = '1'
}

function checkValue (id) {
    var ph = document.getElementById(id).placeholder
    var n = document.getElementById(id).value

    const t_arr = id.split('_')
    if (t_arr[1] == 'studio') {
        var ph = '___'
    }

    if (t_arr[1] == 'substudio') {
        var ph = '___'
    }

    if (ph == n) {
        document.getElementById(id).value = ''
    } else {
        document.getElementById(id).style = 'background:white'
    }
}

function setNull (id) {
    var f_id = id + '_id'
    document.getElementById(f_id).value = 'NULL'
    document.getElementById(f_id).style = 'background:black'
}

function openOnce (url, target) {
    // open a blank "target" window
    // or get the reference to the existing "target" window
    var winref = window.open('', target, '')

    // if the "target" window was just opened, change its url
    if (winref.location.href === 'about:blank') {
        winref.location.href = url
    }
    return winref
}

function popup (mylink, windowname, width = 1028, height = 800) {
    //console.log(width + ' ' + height);

    var href
    if (typeof mylink == 'string') href = mylink
    else href = mylink.href

    var winref = window.open(
        href,
        windowname,
        'scrollbars=yes,width=' + width + ',height=' + height + ''
    )

    if (winref.location.href != 'about:blank') {
        winref.location.href = href + '&r=true'
    }

    return winref
}
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

function previewHover (e, thumb, prev) {
    console.log(e.id)

    if (prev == '') {
        return false
    } else {
        var element = document.getElementById(e.id)

        element.addEventListener('mouseenter', event => {
            element.src = prev
        })

        element.addEventListener('mouseleave', event => {
            element.src = thumb
        })
    }
}
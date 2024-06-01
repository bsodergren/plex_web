

const sideBar = document.querySelector("ul.nav-menu.nav-tabs.nav-sidebar");
if (sideBar != null) {

    let SideBarHeight = sideBar.clientHeight + 20;

    let sideBarHeader = document.querySelector(".nav-sidebar-header");
    if (sideBarHeader != null) {
        sideBarHeader.style.top = SideBarHeight + "px";
        sideBarHeader.style.position = "absolute";
        sideBarHeader.style.width = "180px";
        let sideBarsort = document.querySelector(".nav-sidebar-sort");

        sideBarsort.style.top = SideBarHeight + 30 + "px";
        sideBarsort.style.position = "absolute";
        sideBarsort.style.width = "180px";
    }
}

let mybutton = document.getElementById("myBtn");
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

function scrollFunction() {
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

function getCurrentPage() {}

function prevPage() {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    var currentPage = urlParams.get("current");
    urlParams.delete("current");
    nextPage = parseInt(currentPage) - 1;
    if (nextPage == 0) {
        return false;
    }
    urlParams.append("current", nextPage);
    var url = window.location.pathname + "?" + urlParams.toString();
    window.location.href = url;
}
function nextPage() {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    var currentPage = urlParams.get("current");
    if (currentPage == null) {
        currentPage = 1;
    }
    urlParams.delete("current");
    const lastPageValue = document.querySelector(".page-link.lastPage");

    var lastPage = lastPageValue.textContent;
    if (lastPage == currentPage) {
        return false;
    }

    nextPage = parseInt(currentPage) + 1;
    urlParams.append("current", nextPage);
    var url = window.location.pathname + "?" + urlParams.toString();
    window.location.href = url;
}

const writeLog = function (msg) {
    let date = new Date();
    window.console.log(date.toISOString() + " " + msg);
};

function clickButton(e, close) {
    let video_id = document.getElementById("DorRvideoId");

    $.ajax({
        type: "post",
        url: "process.php",
        data: {
            submit: e.value,
            id: video_id.value,
        },
        cache: false,
        success: function (data) {
            if (close == true) {
                window.opener.location.reload(true);
                window.close();
            } else {
                window.location.reload(true);
            }
        },
    });

    return false;
}

jQuery(document).ready(function () {
    $(".rating").on("change", function (e) {
        $.ajax({
            type: "post",
            url: "process.php",
            data: {
                submit: "rating",
                id: e.target.id,
                rating: $(this).val(),
            },
            cache: false,
            success: function (data) {
                let close = document.getElementById("close_window");
                if (window.opener != null) {
                    window.opener.location.reload(true);
                    if (close == null) {
                        window.close();
                    }
                } else {
                    //     window.location.reload(true)
                }
                //
            },
        });
    });
});

$("#ajaxform").submit(function (e) {
    var postData = $(this).serializeArray();
    $.ajax({
        url: "process.php",
        type: "POST",
        data: postData,
        success: function (data) {
            const substring = "video.php";
            if (data.includes(substring) == true) {
                popup(data, "PlaylistWindow");
            } else {
                window.location.href = data;
            }
        },
    });
});

var $editMetadata = $(".editMetadata");

function metaEditor() {
    $editMetadata.editable({
        emptyMessage: "Please write something...",
        callback: function (data) {
            editBox = data.$el[0].id;
            const editBoxArr = editBox.split("_");
            var metafield = editBoxArr[0];
            var videoId = editBoxArr[1];

            if (data.content !== false) {
                let value = data.content.trim();

                if (value == "") {
                    value = "NULL";
                }
                $.ajax({
                    type: "post",
                    url: "process.php",
                    data: jQuery.param({
                        submit: "updateVideoCard",
                        field: metafield,
                        value: value,
                        video_id: videoId,
                    }),
                    success: function (data) {
                        let close = document.getElementById("reload");
                        console.log(" window reload -> " + close);

                        if (close != null) {
                            window.opener.location.reload(true);
                            window.location.reload(true);
                        }
                    },
                });
                console.log("   * The text was changed -> " + value);
            }
        },
    });
}

$editMetadata.on("edit", function () {
    console.log("Started editing element " + this.nodeName);
});

metaEditor();



function getNextVideo(videoid)
{
    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            submit: "nextVideoCard",
            videoid: videoid

        },
        cache: false,
        success: function (data) {
             console.log(data)
              window.location.href = data;

        },
    });
}

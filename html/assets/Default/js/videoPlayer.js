

const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

const popoverTriggerList = document.querySelectorAll(
    '[data-bs-toggle="popover"]'
);
const popoverList = [...popoverTriggerList].map(
    (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
);


// $("#removeVideo").submit(function (e) {
//     var postData = $(this).serializeArray();
//     console.info(postData);
//     // $.ajax({
//     //     url: 'process.php',
//     //     type: 'POST',
//     //     data: postData,
//     //     success: function (data) {
//     //         console.info(data);
//     //         window.location.href = data
//     //     }
//     // })
// });


function videoPlaylistButton(videoId, playlistId,page) {
    var currentId = null;
    var total = null;

    console.log(videoId, playlistId,page)

    $.ajax({
        url: "process.php",
        type: "POST",
        data: {
            submit: "playlist",
            action: "AddToPlaylist",
            PlaylistID: playlistId,
            playlist: { 0: videoId },
            Video_ID: videoId,
            AddToPlaylist: true,
            VideoPlayer: page,
            total: total,
        },
        cache: false,
        success: function (data) {



            const element = document.getElementById("VideoPlaylistLabel_" + videoId);
            const dropDownelement = document.getElementById("dropdown-menu_" + videoId);
            element.textContent = "Added !";
            element.style.display = "block";
            setTimeout(function () {
                $("#VideoPlaylistLabel_" + videoId).fadeOut(400);
                element.style.display = "none";
            }, 2000);

            setTimeout(function () {
                $("#dropdown-menu_" + videoId).fadeOut(400);
                dropDownelement.classList.toggle("show");
            }, 2000);


            console.log(data)
            return false;
            // const element = document.getElementById("VideoPlaylistLabel");
            // element.textContent = "Added !";
            // element.style.display = "block";
            // setTimeout(function () {
            //     $("#VideoPlaylistLabel").fadeOut(400);
            //     element.style.display = "none";
            // }, 2000);
            // console.log(page)
            // if (page == "grid") {
            //     const videoCell = document.getElementById("VideoPlaylistLabel_" + videoId);
            //     videoCell.innerHTML = data;

            // }

            //    window.location.href = data
        },
    });
}

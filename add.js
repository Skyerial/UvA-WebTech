//const filmTitle = "TestTestTEST";
//const filmPoster = "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Placeholder_view_vector.svg/681px-Placeholder_view_vector.svg.png";
//const filmServiceImg = "https://www.tekstmaatje.nl/wp-content/uploads/2020/06/placeholder.png";
var filmPoster;
var filmTitle;
const filmService = "netflix"; // No capitals!!! Output needs to match DB!!!

function addToPlaylist(playlist) {
    const body = JSON.stringify({
        title: filmTitle,
        picture: filmPoster,
        service: filmService,
        playlist: playlist,
    });

    const request = new Request('add_to_playlist.php', {
        method: 'POST', body: body
    });
    fetch(request);
}

function get_info(id) {
    filmPoster = document.getElementById(("poster" + id)).getAttribute('value');
    filmTitle = document.getElementById(("title" + id)).getAttribute('value');
}

// addToPlaylist("future watching")
function to_watch(id) { get_info(id); addToPlaylist("future watching"); }

function cur_watching(id) { get_info(id); addToPlaylist("currently watching"); }

function watched(id) { get_info(id); addToPlaylist("finished watching"); }

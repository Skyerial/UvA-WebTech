const filmTitle = "Test";
const filmPoster = "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Placeholder_view_vector.svg/681px-Placeholder_view_vector.svg.png";
const filmServiceImg = "https://www.tekstmaatje.nl/wp-content/uploads/2020/06/placeholder.png";
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

function to_watch(id) { console.log(id); addToPlaylist("future watching"); }

function cur_watching() { addToPlaylist("currently watching"); }

function watched() { addToPlaylist("finished watching"); }

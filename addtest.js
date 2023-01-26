
function sendData(id, playlist) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist
    })

    //console.log(JSON.parse(body));
    const request = new Request('switch_playlist.php', {
        method: 'POST', body: body, credentials: 'include',
        headers: {
            'Cookie': document.cookie
        }
    });
    fetch(request).then(response => response.text()).then(result => console.log(result));
    //.then(result => console.log(result));
}


function to_watch(id) { sendData(id, "future watching"); }

function cur_watching(id) { sendData(id, "currently watching"); }

function watched(id) { sendData(id, "finished watching"); }
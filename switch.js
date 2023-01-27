function sendData(id, playlist) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist
    })

    //console.log(JSON.parse(body));
    const request = new Request('switch_playlist.php', {
        method: 'POST', body: body
    });
    fetch(request).then(response => response.text()).then(result => console.log(result));
    //.then(result => console.log(result));
}

function deleteData(id, playlist) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist
    })

    const request = new Request('remove_from_playlist.php', {
        method: 'POST', body: body
    });
    fetch(request).then(response => response.text()).then(result => console.log(result));
}




function to_watch(id) { sendData(id, "future watching"); }

function cur_watching(id) { sendData(id, "currently watching"); }

function watched(id) { sendData(id, "finished watching"); }

function delete_item(id, playlist) { deleteData(id, playlist); }
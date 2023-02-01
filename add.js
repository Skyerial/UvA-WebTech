// Send required data to server
// In: id: card id.
//     playlist: playlist where card should be stored.
async function sendData(id, playlist) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist
    })

    const request = new Request('add_to_playlist.php', {
        method: 'POST', body: body
    });
    try {
        const response = await fetch(request);
        if (response.status != 200) {
            console.log("response is not 200");
        }
        const text = await response.text();
        if (text == "not logged in") {
            alert("Log in to make use of this functionality");
        }
    } catch (error) {
        console.log("an error occured");
    }

}

// Functions that get called by the buttons on the cards.
function toWatch(id) { sendData(id, "future watching"); }

function curWatching(id) { sendData(id, "currently watching"); }

function watched(id) { sendData(id, "finished watching"); }

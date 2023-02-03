// Send required data to server
// In: id: card id.
//     watchlist: watchlist where card should be stored.
async function sendData(id, watchlist) {
    const body = JSON.stringify({
        id: id,
        watchlist: watchlist
    })

    const request = new Request('add_to_watchlist.php', {
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

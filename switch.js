async function sendData(id, playlist, action) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist,
        action: action
    })

    //console.log(JSON.parse(body));
    const request = new Request('switch_playlist.php', {
        method: 'POST', body: body
    });
    //fetch(request).then(response => response.text()).then(result => console.log(result));
    try {
        const response = await fetch(request);
        if (response.status != 200) {
            console.log("response is not 200");
        }
        const text = await response.text();
        console.log(text);
    } catch (error) {
        console.log("an error occured");
    }
}

async function deleteData(id, playlist, action) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist,
        action: action
    })

    const request = new Request('switch_playlist.php', {
        method: 'POST', body: body
    });
    //fetch(request).then(response => response.text()).then(result => console.log(result));
    try {
        const response = await fetch(request);
        if (response.status != 200) {
            console.log("response is not 200");
        }
        const text = await response.text();
        console.log(text);
    } catch (error) {
        console.log("an error occured");
    }
}


function deleteCard(id) {
    var cardID = "card" + id;
    var card = document.getElementById(cardID);
    card.style.opacity = "0";
    card.style.width = "0";
    card.style.padding = "0";
    card.style.margin = "0"

    var delayInMilliseconds = 700;

    setTimeout(function() {
        card.remove();
    }, delayInMilliseconds);
}

function toWatch(id) { sendData(id, "future watching", "add"); deleteCard(id);  }

function curWatching(id) { sendData(id, "currently watching", "add"); deleteCard(id); }

function watched(id) { sendData(id, "finished watching", "add"); deleteCard(id); }

function deleteItem(id, playlist) { deleteData(id, playlist, "remove"); deleteCard(id); }
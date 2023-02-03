async function sendData(id, watchlist, action) {
    const body = JSON.stringify({
        id: id,
        watchlist: watchlist,
        action: action
    })

    //console.log(JSON.parse(body));
    const request = new Request('switch_watchlist.php', {
        method: 'POST', body: body
    });
    try {
        const response = await fetch(request);
        if (response.status != 200) {
            console.log("response is not 200");
        }
        const text = await response.text();
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

function deleteItem(id, watchlist) { sendData(id, watchlist, "remove"); deleteCard(id); }
const options = {
    method: "GET",
    headers: {
        "API-key": "822d9746853668f907a5cd20eba5215d3a6b72c4a9452dfac502bc44e3951b2f"
    }
};

function ownApiCall() {
    fetch("https://webtech-in01.webtech-uva.nl/v1/232", options)
    // .then(response => response.json)
    .then(response => console.log(response))
    .catch(err => console.error(err));
}
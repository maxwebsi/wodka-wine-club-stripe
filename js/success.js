window.addEventListener("load", function () {
    // document.getElementById('loading-page').classList.add('hide');
    let sessionId = '';
    if (document.getElementById('session-id')) {
        sessionId = document.getElementById('session-id').value;
    }
        fetch(__domainUrl + "get-checkout-session?session_id=" + sessionId)
            .then(function (result) {
                return result.json()
            })
            .then(function (session) {
                console.log(2);
                document.getElementById('response').innerHTML = session.message;
                // var sessionJSON = JSON.stringify(session, null, 2);
                // document.querySelector("pre").textContent = sessionJSON;
                document.getElementById('loading-page').classList.add('hide');
            })
            .catch(function (err) {
                console.log('Error when fetching Checkout session', err);
                document.getElementById('loading-page').classList.add('hide');
            });
    // } else {
    //     document.getElementById('loading-page').classList.add('hide');
    // }
});




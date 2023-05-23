var resetCustomerPw = function () {
    let password = document.getElementById('new-password').value;
    let idUser = document.getElementById('user-id').value;
    return fetch(__domainUrl + "update-password.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            idUser: idUser,
            password: password
        })
    }).then(function (result) {
        return result.json();
    });
}

window.addEventListener("load", function () {
    document.getElementById('loading-page').classList.add('hide');
    document.getElementById('message').innerHTML = '';

    // User login
    document
        .getElementById("form-reset")
        .addEventListener("submit", function (evt) {
            evt.preventDefault();
            document.getElementById('loading-page').classList.remove('hide');
            if (document.getElementById('new-password').value == document.getElementById('new-password-2').value) {
                resetCustomerPw().then(function (data)  {
                    document.getElementById('message').innerHTML = data.message;
                    document.getElementById('loading-page').classList.add('hide');
                });
            } else {
                // Passwords do not match
                let message = '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
                message += 'Passwords do not match';
                message += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                message += '<span aria-hidden="true">&times;</span>';
                message += '</button>';
                message += '</div>';
                document.getElementById('message').innerHTML = message;
                document.getElementById('loading-page').classList.add('hide');
            }
        });
});
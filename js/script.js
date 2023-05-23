var createUserSession = function () {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let priceId = false;
    if (document.getElementById('price-id')) {
        priceId = document.getElementById('price-id').value;
    }
    return fetch(__domainUrl + "create-user-session.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            email: email,
            password: password,
            priceId: priceId
        })
    }).then(function (result) {
        return result.json();
    });
}

var createCheckoutSession = function (priceId, customerId) {
    return fetch(__domainUrl + "create-checkout-session.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            priceId: priceId,
            customerId: customerId
        })
    }).then(function (result) {
        return result.json();
    })
}

var createCustomer = function () {
    let name = document.getElementById('new-name').value;
    let surname = document.getElementById('new-surname').value;
    let email = document.getElementById('new-email').value;
    let password = document.getElementById('new-password').value;
    let tel = document.getElementById('new-tel').value;
    let priceId = 0;
    if (document.getElementById('price-id')) {
        priceId = document.getElementById('price-id').value;
    }
    return fetch(__domainUrl + "create-customer.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            name: name,
            surname: surname,
            email: email,
            password: password,
            tel: tel,
            priceId: priceId
        })
    }).then(function (result) {
        return result.json();
    });
}

var actionAfterLogin = function (data, stripe) {
    if (data.subscription == 'OK') {
        createCheckoutSession(data.priceId, data.customerId).then(function (data) {
            // Iubenda invia consent solution
            _iub.cons_instructions.push(["submit", {
                writeOnLocalStorage: false, // default: false
                form: {
                    selector: document.getElementById('form-registration'),
                },
                consent: {
                    legal_notices: [

                        {
                            identifier: 'privacy_policy',

                        },

                        {

                            identifier: 'cookie_policy',

                        },

                        {

                            identifier: 'term',
                        }
                    ],
                }
            },{
                success: function(response) {
                    console.log(response);
                    // Call Stripe.js method to redirect to the new Checkout page
                    stripe
                        .redirectToCheckout({
                            sessionId: data.sessionId
                        })
                        .then(handleResult);
                },
                error: function(response) {
                    console.log(response);

                    // Call Stripe.js method to redirect to the new Checkout page
                    stripe
                        .redirectToCheckout({
                            sessionId: data.sessionId
                        })
                        .then(handleResult);
                }
            }])
        });
    } else if (data.issue == "OK") {
        // document.getElementById('customer-id').value = data.customerId;
        document.getElementById('wrap-login-form').classList.toggle("d-none");
        document.getElementById('wrap-reg-form').classList.toggle("d-none");
        document.getElementById('logged').classList.toggle("show");
        document.getElementById('logout').classList.remove('d-none');
        document.getElementById('loading-page').classList.add('hide');
        document.getElementById('sub-container').classList.add('h-100');
    } else if (data.issue == false && data.message) {
        document.getElementById('message').innerHTML = data.message;
        document.getElementById('loading-page').classList.add('hide');
    }
}

var getInfoPrice = function() {
    priceId = document.getElementById('price-id').value;
    return fetch(__domainUrl + "get-product.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            priceId: priceId
        })
    }).then(function (result) {
        return result.json();
    });
}

var createCustomerPortalSession = function() {
    return fetch(__domainUrl + "create-customer-portal-session.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        }
    }).then(function (result) {
        return result.json();
    });
}

window.addEventListener("load", function () {
    /* Get your Stripe publishable key to initialize Stripe.js */
    fetch(__domainUrl + "config.php", {
        headers: {
            "Content-Type": "application/json"
        }
    })
        .then(function (result) {
            return result.json();
        })
        .then(function (json) {
            var publishableKey = json.publishableKey;
            var stripe = Stripe(publishableKey);

            // Inizializza Iubenda per la consent solution
            _iub.cons_instructions.push(["init", {
                api_key: "hc970GKDU2ioSQ5UDhWtmTXJnguuzZng",
                logger: "console",
                log_level: "debug",
                sendFromLocalStorageAtLoad: false}
            ]);

            if (document.getElementById('price-id')) {
                getInfoPrice().then(function(data) {
                    if (data.name) {
                        document.getElementById('product-title').innerText = data.name;
                    }
                    if (data.description) {
                        document.getElementById('product-description').innerText = data.description;
                    }
                    if (data.unit_price) {
                        document.getElementById('product-price').innerText = data.unit_price / 100;
                    }
                    if (data.images) {
                        document.getElementById('product-image').setAttribute('src', data.images[0]);
                    }
                    document.getElementById('loading-page').classList.add('hide');
                })
            } else {
                document.getElementById('loading-page').classList.add('hide');
            }

            // User login
            document
                .getElementById("form-login")
                .addEventListener("submit", function (evt) {
                    evt.preventDefault();
                    document.getElementById('message').innerHTML = '';
                    document.getElementById('loading-page').classList.remove('hide');
                    createUserSession().then(function (data) {
                        actionAfterLogin(data, stripe);
                    });
                });

            // Create customer
            document
                .getElementById('form-registration')
                .addEventListener('submit', function (evt) {
                    evt.preventDefault();
                    document.getElementById('message').innerHTML = '';
                    document.getElementById('loading-page').classList.remove('hide');
                    createCustomer().then(function (data) {
                        actionAfterLogin(data, stripe);
                    });
                });

            // Show login form
            document
                .getElementById('show-login-form')
                .addEventListener('click', function (evt) {
                    evt.preventDefault();
                    document.getElementById('message').innerHTML = '';
                    document.getElementById('wrap-reg-form').classList.remove('show');
                    document.getElementById('wrap-login-form').classList.add('show');
                    document.getElementById('container').classList.add('h-100');
                });

            // Show registration form
            document
                .getElementById('show-registration-form')
                .addEventListener('click', function (evt) {
                    evt.preventDefault();
                    document.getElementById('message').innerHTML = '';
                    document.getElementById('wrap-login-form').classList.remove('show');
                    document.getElementById('container').classList.remove('h-100');
                    document.getElementById('wrap-reg-form').classList.add('show');
                });
        });
});


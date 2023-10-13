
const stripe = Stripe(stripePublicKey);

        const elements = stripe.elements();

         // Stripe injects an iframe into the DOM 
        const card = elements.create("card");
            card.mount("#card-element");
            card.on("change", function (event) { 
            // Disable the Pay button if there are no card details in the Element 
                document.querySelector("button").disabled = event.empty; 
                document.querySelector("#card-error").textContent = event.error ? event.error.message : ""; 
            });

        const form = document.getElementById("payment-form");

        form.addEventListener("submit", function (event) { 
            event.preventDefault();

            stripe
                .confirmCardPayment(clientSecret, { 
                    payment_method: { 
                        card: card 
                    } 
                })

                .then(function (result) {
                    if (result.error) { 
                        // Show error to your customer 
                        console.log(result.error.message); 
                    } else {  
                        // The payment succeeded! 
                        window.location.href = redirectAfterSuccessUrl;
                    } 
                }); 
        });
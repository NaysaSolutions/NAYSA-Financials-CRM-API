<!DOCTYPE html>
<html>
<head>
    <title>Stripe Payment Test</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        #card-element {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2>Stripe Test Payment Form</h2>

    <form id="payment-form">
        <div id="card-element"><!-- Stripe Card Element --></div>
        <button id="submit">Pay Now</button>
        <div id="error-message" style="color:red;"></div>
    </form>

    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        const form = document.getElementById('payment-form');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            // Call Laravel backend to create payment intent
            const response = await fetch('/create-payment-intent', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });

            const data = await response.json();
            const clientSecret = data.clientSecret;

            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card
                }
            });

            if (result.error) {
                document.getElementById('error-message').textContent = result.error.message;
            } else {
                if (result.paymentIntent.status === 'succeeded') {
                    alert('Payment successful!');
                }
            }
        });
    </script>
</body>
</html>

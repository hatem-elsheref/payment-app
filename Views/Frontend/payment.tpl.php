<script src="https://js.stripe.com/v3/"></script>

<div class="row mb-4">
    <?php alert();?>
    <div class="col-md-12 order-md-1">
        <h4 class="mb-3">Complete Payment</h4>
        <p id="paymentMessage"></p>
        <form class="needs-validation"  action="/checkout" method="post" id="payment-form">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="name">name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>
            <div class="row">
                <div id="payment-element">
                    <!--Stripe.js injects the Payment Element-->
                </div>
            </div>




            <hr class="mb-4">
            <button class="btn btn-primary btn-lg btn-block" type="submit" id="submit">
                <div class="spinner-border" style="display: none" id="spinner" role="status"></div>
                <span id="processTxt" style="display: none">processing...</span>
                <span id="submitBtnTxt">Pay Now</span>
            </button>
        </form>
    </div>
</div>


<script>
    // This is your test publishable API key.
    const stripe = Stripe("<?=config('app.services.stripe.publishable_key')?>");

    let elements;

    initialize();

    document
        .querySelector("#payment-form")
        .addEventListener("submit", handleSubmit);

    // Fetches a payment intent and captures the client secret
    async function initialize() {
        const { clientSecret } = await fetch("/pay", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "order_id=<?=session("order_id")?>",
        }).then((r) => r.json());

        elements = stripe.elements({ clientSecret });

        const paymentElementOptions = {
            layout: "tabs",
        };

        const paymentElement = elements.create("payment", paymentElementOptions);
        paymentElement.mount("#payment-element");
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setLoading(true);

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                // Make sure to change this to your payment completion page
                return_url: "<?=trim(url(), "/") . '/payment/'.session('order_id')?>",
            },
        });

        if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
        } else {
            showMessage("An unexpected error occurred.");
        }

        setLoading(false);
    }

    // ------- UI helpers -------

    function showMessage(messageText) {
        const messageContainer = document.querySelector("#paymentMessage");

        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;

        setTimeout(function () {
            messageContainer.classList.add("hidden");
            messageText.textContent = "";
        }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("#submit").disabled = true;
            document.querySelector("#submitBtnTxt").style.display = "none";
            document.querySelector("#spinner").style.display = "inline-block";
            document.querySelector("#processTxt").style.display = "none";
        } else {
            document.querySelector("#submit").disabled = false;
            document.querySelector("#submitBtnTxt").style.display = "inline";
            document.querySelector("#spinner").style.display = "none";
            document.querySelector("#processTxt").style.display = "none";

        }
    }
</script>
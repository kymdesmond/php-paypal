<?php
include 'layout/layout_head.php';
// Redirect to the home page if id parameter not found in URL
if(empty($_GET['id'])){
    header("Location: index.php");
}

// Include and initialize database class
include 'DBClass.php';
$db = new DB;

// Include and initialize paypal class
include 'PaypalExpressClass.php';
$paypal = new PaypalExpress;

//Include and initialize mpesa class
include 'Mpesa.php';
$mpesa = new Mpesa;

// Get product ID from URL
$productID = $_GET['id'];

// Get product details
$conditions = array(
    'where' => array('id' => $productID),
    'return_type' => 'single'
);
$productData = $db->getRows('products', $conditions);

// Redirect to the home page if product not found
if(empty($productData)){
    header("Location: index.php");
}
?>

<div class="container" align="center">
<div class="card">
<div class="col-md-8">
<div class="">
<h2 class="card-title">Checkout</h2>
</div>
<div class="card-body">
<form action="process-mpesa-payment.php" method="post">
  <div class="form-group">
     <!-- Product details -->
    <h5>Name: <?php echo $productData['name']; ?></h5>
    <h6>Price: <?php echo $productData['price']; ?></h6>
    
    <!-- Checkout buttons -->
    <!-- checkout with MPesa -->
    <input type="hidden" name="price" value="<?php echo $productData['price']?>">
    <label for="msisdn">Mpesa Number</label>
    <input class="form-control" type="number" name="msisdn" min="12" placeholder="254712345678">
    <input class="btn btn-success" type="submit">

    <!-- checkout with paypal -->
    <div id="paypal-button"></div>

</div>
</form>
</div>
</div>
</div>

<div class="item">

	<script src="https://www.paypalobjects.com/api/checkout.js"></script>
	<!--
JavaScript code to render PayPal checkout button
and execute payment
-->
<script>
paypal.Button.render({
    // Configure environment
    env: '<?php echo $paypal->paypalEnv; ?>',
    client: {
        sandbox: '<?php echo $paypal->paypalClientID; ?>',
        production: '<?php echo $paypal->paypalClientID; ?>'
    },
    // Customize button (optional)
    locale: 'en_US',
    style: {
        size: 'small',
        color: 'gold',
        shape: 'pill',
    },
    // Set up a payment
    payment: function (data, actions) {
        return actions.payment.create({
            transactions: [{
                amount: {
                    total: '<?php echo $productData['price']; ?>',
                    currency: 'USD'
                }
            }]
      });
    },
    // Execute the payment
    onAuthorize: function (data, actions) {
        return actions.payment.execute()
        .then(function () {
            // Show a confirmation message to the buyer
            //window.alert('Thank you for your purchase!');
            
            // Redirect to the payment process page
            window.location = "process.php?paymentID="+data.paymentID+"&token="+data.paymentToken+"&payerID="+data.payerID+"&pid=<?php echo $productData['id']; ?>";
        });
    }
}, '#paypal-button');
</script>
</div>
<?php
//footer
include 'layout/layout_footer.php';
?>
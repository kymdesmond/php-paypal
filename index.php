<?php
//include nav and footer
include 'layout/layout_head.php';
// Include and initialize database class
include 'DBClass.php';
$db = new DB;

// Get all products
$products = $db->getRows('products');
?>

<div class="jumbotron">
<h1>Shoop! Today</h1>
</div>

<div class="main main-raised">
  <div class="container">
    <div class="section text-center">
      <h2 class="title">Products</h2>

<div class="row">
<?php
// List all products
if(!empty($products)){
    foreach($products as $row){
?>
        <div class="col-md-4 col-sm-6">
        <div class="card" style="width: 20rem;">
            <img class="card-img-top" src="https://images.unsplash.com/photo-1517303650219-83c8b1788c4c?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=bd4c162d27ea317ff8c67255e955e3c8&auto=format&fit=crop&w=2691&q=80" alt="Card image cap">
            <div class="card-body">
            <p class="card-text">Name: <?php echo $row['name']; ?><br>Price: <?php echo $row['price']; ?></p>
            <a type="button" class="btn btn-primary" href="checkout.php?id=<?php echo $row['id']; ?>">BUY</a>
        </div>
        </div>
        </div>

<?php        
    }
}else{
    echo '<p>Product(s) not found...</p>';
}
?>
</div>
    </div>
  </div>
</div>
<?php
//include footer
include 'layout/layout_footer.php';

?>
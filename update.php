
<?php 
require_once "../13_curl/index.php";
require_once "functions.php";

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=product_crud','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$statement = $pdo->prepare('SELECT * FROM products WHERE id=:id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

// dumper($product);
// exit;

$errors = [];
$title = $product['title'];
$description = $product['description'];
$price = $product['price'];


if($_SERVER["REQUEST_METHOD"]==="POST"){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $image = $_FILES['image'] ?? null;
    $imagePath = $product['image'];
    
    if (!is_dir('images')) {
            mkdir('images');
    };
    
    if ($image && $image['tmp_name']) {
        if ($product['image']){
            unlink($product['image']);
        };

        $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
        mkdir(dirname($imagePath));
        move_uploaded_file($image['tmp_name'], $imagePath);
    };

    if(!$title){
       $errors[] = "Product title is required";
    };

    if(!$price){
        $errors[] = "Product price is required";
    };
     
     
    
   if(empty($errors)){
   

    $statement = $pdo->prepare("UPDATE products SET title = :title , 
        image= :image, description=:description,
         price = :price WHERE id = :id");

        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        $statement->execute();
        header('Location: index.php');
   };

}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products App</title>
    <link rel="stylesheet" href="app.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>
  <body>

  <p>
    <a href="index.php" class="btn btn-warning">🔙Back to products</a>
  </p>
    <h1>Update Product: <b><?php echo $product['title'] ?></b></h1>
    <?php if ($product['image']): ?>
    <img src="<?php echo $product['image'] ?>" alt="" class="update-image">
    <?php endif; ?>

    <?php  if(!empty($errors)) :?>

        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <p> <?php echo $error ?></p>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label>Product Image</label> <br>
    <input type="file" name="image"  >
  </div>

  <div class="mb-3">
    <label>Product Title</label>
    <input type="text" class="form-control" name="title" value="<?php echo $title ?>">
  </div>

  <div class="mb-3">
    <label>Product Description</label>
    <textarea class="form-control" name="description" value="<?php echo $description ?>"></textarea>
  </div>

  <div class="mb-3">
    <label>Product Price</label>
    <input type="number" step=".01" name="price" class="form-control" value="<?php echo $price ?>">
  </div>
 
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

</body>

</html>  
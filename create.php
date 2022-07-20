
<?php 
require_once "../13_curl/index.php";
require_once "functions.php";

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=product_crud','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//image=&title=&description=&price=

// dumper($_GET)
// dumper($_POST)
// dumper($_SERVER);
// dumper($_FILES);

$errors = [];
$title='';
$price='';
$description='';

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');

    $image = $_FILES['image'] ?? null;
    $imagePath = '';
    
    if (!is_dir('images')) {
            mkdir('images');
    }

    if ($image && $image['tmp_name']) {
        $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
        mkdir(dirname($imagePath));
        move_uploaded_file($image['tmp_name'], $imagePath);
    }

    if(!$title){
       $errors[] = "Product title is required";
    };

    if(!$price){
        $errors[] = "Product price is required";
    };
     
     
    
   if(empty($errors)){
   

    $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
                            VALUES (:title, :image, :description, :price, :date)"
    );
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':date', $date);
        $statement->execute();
        header('Location: index.php');
   };

}



// 3:07:31

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
    <h1>Create Product</h1>

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
<?php
include 'db_connection_CRUD_1.php';

$errmsg = '';
$successmsg = '';

//Fetch Categories

$cat_stmt = $db_con->prepare("SELECT * FROM categoriess");
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['btnsave']))
{
	$productname = $_POST['product_name'];
	$description = $_POST['description'];
	$productstock = $_POST['product_stock'];
	$category_id = $_POST['category_id'];
	$has_attributes = isset($_POST['has_attributes']) ? 1 : 0;
	$sizes = isset($_POST['sizes']) ? implode(',', $_POST['sizes']) : '';
	$colors = isset($_POST['colors']) ? $_POST['colors'] : '';

	//Image Upload

	$imgfile = $_FILES['product_image']['name'];
	$tmp_dir = $_FILES['product_image']['tmp_name'];
	$imgsize = $_FILES['product_image']['size'];

	if(empty($productname) || empty($description) || empty($imgfile) || empty($productstock))
	{
		$errmsg = "All fields are required";
	}

	else
	{
		$upload_dir = "uploads/";
		$imgext = strtolower(pathinfo($imgfile, PATHINFO_EXTENSION));
		$valid_extensions = ['jpg','jpeg','png','gif'];	

		$productpic = rand(1000,1000000000).".".$imgext;

		if(in_array($imgext, $valid_extensions) && $imgsize < 5000000)
		{
			move_uploaded_file($tmp_dir, $upload_dir.$productpic);
		}

		else
		{
			$errmsg  = "Imvalid image file or size";
		}
	}

	if(empty($errmsg))
	{
		$stmt =$db_con->prepare("INSERT INTO products(product_name,description,product_image,stock_amount,has_attributes,category_id)VALUES(:pname,:pdesc,:pimage,:pstock,:hasattr,:cat_id)"); //:pname agula parametter

		$stmt->bindParam(':pname',$productname);
		$stmt->bindParam(':pdesc',$description);
		$stmt->bindParam(':pimage',$productpic);
		$stmt->bindParam(':pstock',$productstock);
		$stmt->bindParam(':hasattr',$has_attributes);
		$stmt->bindParam(':cat_id',$category_id);
		if($stmt->execute())
		{
			$lastproductid=$db_con->lastInsertId();
			if($has_attributes)
			{
				$attr_stmt = $db_con->prepare("INSERT INTO attributes(product_id,sizes,colors)VALUES(:pid,:psize,:pcolor)");
				$attr_stmt->bindParam(':pid',$lastproductid);
				$attr_stmt->bindParam(':psize',$sizes);
				$attr_stmt->bindParam(':pcolor',$colors);
				$attr_stmt->execute();

			}
			$successmsg ="New Product Inserted";
		}
		else
		{
			$errmsg ="Error Occured";
		}


		
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Add New Products</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha512-Dop/vW3iOtayerlYAqCgkVr2aTr2ErwwTYOvRFUpzl2VhCMJyjQF0Q9TjUXIo6JhuM/3i0vVEt2e/7QQmnHQqw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

	<div class="container mt-5">
		<h3 class="mb-4">Add Products</h3>

		<?php if(!empty($errmsg)) echo "<div class='alert alert-danger'>$errmsg</div>"; ?>

		<?php if(!empty($successmsg)) echo "<div class='alert alert-danger'>$successmsg</div>"; ?>

		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label>Product name:</label>
				<input type="text" name="product_name" class="form-control" required>
			</div>

			<div class="form-group">
				<label>Description:</label>
				<textarea name="description" class="form-control"></textarea>
			</div>

			<div class="form-group">
				<label>Product Image:</label>
				<input type="file" name="product_image" class="form-control" required>
			</div>

			<div class="form-group">
				<label>Stock Amount:</label>
				<input type="number" name="product_stock" class="form-control" required>
			</div>

			<div class="form-group">
				<label>Category:</label>
				<select name="category_id" class="form-control" required>
					<option value="">Select Category</option>
					<?php
					if (!empty($categories))
					 {
                    foreach ($categories as $row)
                      {
                     echo '<option value="'.$row['category'].'">'.$row['category'].'</option>';
                       }
                     }  
               else {
                     echo '<option value="">category Not Found</option>';
                       }


					?>
				</select>
			</div>
			<div class="form-check mb-3">
				<input type="checkbox" name="has_attributes" class="form-check-input" id="hasAttributes" onchange="toggleAttributes()">

				<label class="form-check-label">Has Attributes ?</label>
			</div>

			<div id ="attributeSection" style="display: none;">
				<div class="form-group">
					<label>Sizes:</label>
					<label class="checkbox-inline mr-2">
						<input type="checkbox" name="sizes[]" value="L">L
					</label>

					<label class="checkbox-inline mr-2">
						<input type="checkbox" name="sizes[]" value="XL">XL
					</label>

					<label class="checkbox-inline mr-2">
						<input type="checkbox" name="sizes[]" value="XXL">XXL
					</label>
				</div>

				<div class="form-group">
					<label>Colors:</label>
					<input type="color" name="" class="color-input">
					<button type="button" class="btn btn-sm btn-secondary" onclick=" ">Add Color</button>
					<div id="colorList" class="mt-2"></div>
					<input type="hidden" name="colors" id="colors">
				</div>
			</div>

			<button type="submit" name="btnsave" class="btn btn-success">Save</button>
		</form>
	</div>

</body>
</html>

<script>
	let selectedColor = [];
	function toggleAttributes()
	{
		const attrsection = document.getElementById('attributeSection');
		attrsection.style.display =document.getElementById('hasAttributes').checked?'block':'none';


	}
	function Addcolor()
	{
		const colorinput = document.querySelector('color-input');
		const color = colorinput.value;
		if(!selectedColor.includes(color))
		{
			selectedColor.push(color);
			updateColor();
		}
		
	}
	function updateColor()
	{
		
	}
</script>
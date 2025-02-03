<?php
require "../config/config.php";

session_start();
if (empty($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$user = $_SESSION["user"];

if ($_POST) {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    if (($_FILES["image"]["name"] != null)) {
        $name = $_FILES["image"]["name"];
        $type = $_FILES["image"]["type"];
        $tmp_name = $_FILES["image"]["tmp_name"];

        if ($type != "image/jpeg" && $type != "image/png") {

            echo "<script>alert('Image must be png,jpg,jpeg!')</script>";
        } else {
            move_uploaded_file($tmp_name, "images/$name");

            $statement = $db->prepare("UPDATE posts SET title=:title, content=:content, image=:image WHERE id=:id");

            $result = $statement->execute([
                "id" => $id,
                "title" => $title,
                "content" => $content,
                "image" => $name,
            ]);

            if ($result) {
                echo "<script>alert('Successfully Updated.');window.location.href='index.php';</script>";
            }
        }
    } else {
        $statement = $db->prepare("UPDATE posts SET title=:title, content=:content WHERE id=:id");
        $result = $statement->execute([
            "id" => $id,
            "title" => $title,
            "content" => $content,
        ]);

        if ($result) {
            echo "<script>alert('Successfully Updated.');window.location.href='index.php';</script>";
        }
    }
}
$id = $_GET["id"];

$statement = $db->prepare("SELECT * FROM posts WHERE id=:id");
$statement->execute([
    "id" => $id,
]);

$result = $statement->fetch();


?>

<!-- header -->
<?php include("header.php"); ?>

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="hidden" name="id" value="<?= $result['id'] ?>">
                                <label for="title">Title</label>
                                <input type="text" name="title" id="title" class="form-control" value="<?= $result['title'] ?>">
                            </div>

                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea name="content" id="content" class="form-control" rows="8"><?= $result['content'] ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="image">Image</label><br>
                                <?php if($result["image"]): ?>
                                    <img src="images/<?= $result['image'] ?>" alt="" id="img" width="150px" height="150px">
                                    <a href="remove.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-danger">Remove</a>
                                    <input type="file" name="image" id="image" onchange="document.getElementById('img').src = window.URL.createObjectURL(this.files[0])">
                                <?php else: ?>
                                    <img id="img" src="" alt="your image" width="150px" height="150px" style="display: none;">
                                    <a href="remove.php?id=<?= $result['id'] ?>" id="rmbtn"  class="btn btn-sm btn-danger" style="display: none;">Remove</a>
                                    <input type="file" name="image" id="image" onchange="document.getElementById('img').src = window.URL.createObjectURL(this.files[0]);document.getElementById('img').style.display = 'inline-block';document.getElementById('rmbtn').style.display = 'inline-block';"> 
                                <?php endif ?>
                                
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success">SUBMIT</button>
                                <a href="index.php" class="btn btn-secondary">BACK</a>  
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->
<!-- footer -->
<?php include("footer.html"); ?>
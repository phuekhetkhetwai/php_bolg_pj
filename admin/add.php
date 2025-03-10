<?php
session_start();

require "../config/config.php";
require "../config/common.php";


if (empty($_SESSION["user"])) {
    header("location: login.php");
    exit();
}

$user = $_SESSION["user"];

if ($user["role"] != 1) {
    header("location: login.php");
    exit();
}

if ($_POST) {

    if (empty($_POST["title"]) || empty($_POST["content"])) {
        if(empty($_POST["title"])) {
            $titleError = "Title cannot be null";
        }

        if(empty($_POST["content"])) {
            $contentError = "Content cannot be null";
        }
    } else {
        $title = $_POST["title"];
        $content = $_POST["content"];
        $name = $_FILES["image"]["name"];
        $type = $_FILES["image"]["type"];
        $tmp_name = $_FILES["image"]["tmp_name"];

        if ($name != null) {
            if ($type != "image/jpeg" && $type != "image/png") {

                echo "<script>alert('Image must be png,jpg,jpeg!')</script>";
            } else {
                move_uploaded_file($tmp_name, "images/$name");

                $statement = $db->prepare("INSERT INTO posts (title,content,image,author_id) VALUE (:title,:content,:image,:author_id)");

                $result = $statement->execute([
                    "title" => $title,
                    "content" => $content,
                    "image" => $name,
                    "author_id" => $user["id"],
                ]);

                if ($result) {
                    echo "<script>alert('Successfully added.');window.location.href='index.php'</script>";
                }
            }
        } else {
            $statement = $db->prepare("INSERT INTO posts (title,content,author_id) VALUE (:title,:content,:author_id)");

            $result = $statement->execute([
                "title" => $title,
                "content" => $content,
                "author_id" => $user["id"],
            ]);

            if ($result) {
                echo "<script>alert('Successfully added.');window.location.href='index.php'</script>";
            }
        }
    }
}
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
                        <form action="add.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="<?= $_SESSION['_token'] ?>">
                            <div class="form-group">
                                <label for="title">Title</label><p class="text-danger"><?php echo empty($titleError) ? "" : "*".$titleError ?></p>
                                <input type="text" name="title" id="title" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="content">Content</label><p class="text-danger"><?php echo empty($contentError) ? "" : "*".$contentError ?></p>
                                <textarea name="content" id="content" class="form-control" rows="8"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="image">Image</label><br>
                                <img id="img" src="" alt="your image" width="150px" height="150px" style="display: none;">
                                <input type="file" name="image" id="image" onchange="document.getElementById('img').src = window.URL.createObjectURL(this.files[0]);document.getElementById('img').style.display = 'inline-block';">
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

<!-- <input type="file" onchange="showImage(this)"/> -->
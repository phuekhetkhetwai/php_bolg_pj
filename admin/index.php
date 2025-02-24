<?php
require "../config/config.php";
require "../config/common.php";

session_start();
if (empty($_SESSION["user"])) {
  header("location: login.php");
  exit();
}

$user = $_SESSION["user"];

if($user["role"] != 1) {
  header("location: login.php");
  exit();
}
?>

<!-- header -->
<?php include("header.php"); ?>

<?php

if(isset($_POST["search"])) {
  setcookie("search",$_POST["search"], time() + (86400 * 30), "/");
} else {
  if(empty($_GET["pageno"])) {
    unset($_COOKIE["search"]);
    setcookie("search", "" ,time() -1, "/");
  }
}

if(!empty($_GET["pageno"])) {
  $pageno = $_GET["pageno"];
}else {
  $pageno = 1;
}

$numOfRecs = 3;
$offset = ($pageno - 1) * $numOfRecs;

if(isset($_POST["search"]) || isset($_COOKIE["search"])) {

  $searchKey = isset($_POST["search"]) ? $_POST["search"] : $_COOKIE["search"];

  $statement = $db->prepare("SELECT * FROM posts WHERE title LIKE '%$searchKey%' ORDER BY id DESC");
  $statement->execute();
  $results = $statement->fetchAll();

  $total_pages = ceil(count($results) / $numOfRecs);

  $statement = $db->prepare("SELECT * FROM posts WHERE title LIKE '%$searchKey%' ORDER BY id DESC LIMIT $offset,$numOfRecs");
  $statement->execute();
  $datas = $statement->fetchAll();

} else {

  $statement = $db->prepare("SELECT * FROM posts ORDER BY id DESC");
  $statement->execute();
  $results = $statement->fetchAll();

  $total_pages = ceil(count($results) / $numOfRecs);

  $statement = $db->prepare("SELECT * FROM posts ORDER BY id DESC LIMIT $offset,$numOfRecs");
  $statement->execute();
  $datas = $statement->fetchAll();

}

?>

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Blog Listings</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="mb-3">
              <a href="add.php" type="button" class="btn btn-success">New Blog Post</a>
            </div>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th style="width: 10px">No.</th>
                  <th>Title</th>
                  <th>Content</th>
                  <th style="width: 150px">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($datas): ?>
                  <?php $i = 1; ?>
                  <?php foreach ($datas as $data): ?>
                    <tr>
                      <td><?= $i ?></td>
                      <td><?= escape($data["title"]) ?></td>
                      <td><?= escape(substr($data["content"], 0, 100)) ?></td>
                      <td>
                        <a href="edit.php?id=<?= $data["id"]; ?>;" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete.php?id=<?= $data["id"]; ?>;" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                      </td>
                    </tr>
                  <?php $i++; ?>
                  <?php endforeach ?>
                <?php endif ?>
              </tbody>
            </table>
          
            <nav aria-label="Page navigation example" class="mt-4" style="float: right;">
            <ul class="pagination">
              <li class="page-item"><a href="?pageno=1" class="page-link">First</a></li>
              <li class="page-item <?php if($pageno <= 1){echo "disabled";} ?>">
                <a href="<?php if($pageno <= 1){echo "#";}else{echo "?pageno=".($pageno-1);} ?>" class="page-link">Prev</a></li>
              <li class="page-item"><a href="#" class="page-link"><?= $pageno ?></a></li>
              <li class="page-item <?php if($pageno >= $total_pages ){echo "disabled";} ?>">
                <a href="<?php if($pageno >= $total_pages){echo "#";}else{echo "?pageno=".($pageno+1);} ?>" class="page-link">Next</a>
              </li>
              <li class="page-item"><a href="?pageno=<?= $total_pages ?>" class="page-link">Last</a></li>
            </ul>
            </nav>

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
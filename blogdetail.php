<?php
session_start();

require "config/config.php";
require "config/common.php";

if (empty($_SESSION["user"])) {
  header("location: login.php");
  exit();
}

$user = $_SESSION["user"];

if ($_POST) {
  if (empty($_POST["content"]) ) {
    if(empty($_POST["content"])) {
        $contentError = "Comment cannot be null";
    }

} else {
    $content = $_POST["content"];
    $author_id = $user['id'];
    $post_id = $_GET["id"];

    $statement = $db->prepare("INSERT INTO comments (content,author_id,post_id) VALUE (:content,:author_id,:post_id)");

    $result = $statement->execute([
      "content" => $content,
      "author_id" => $author_id,
      "post_id" => $post_id,
    ]);

    if ($result) {
      header("location: blogdetail.php?id=" . $_GET["id"]);
    }
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Blog Detail</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini">
  <?php
  $statement = $db->prepare("SELECT * FROM posts WHERE id=:id");
  $statement->execute([
    "id" => $_GET["id"]
  ]);

  $data = $statement->fetch();

  // print_r($data);
  ?>
  <div class="wrapper">
    <div class="">
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col">
              <!-- Box Comment -->
              <div class="card card-widget">
                <div class="card-header">
                  <div class="card-title" style="float: none; text-align: center;">
                    <h4><?= escape($data['title']) ?></h4>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-6">
                      <img class="img-fluid pad" src="admin/images/<?= $data['image'] ?>" alt="Photo">
                    </div>
                  </div>
                  <br><br>
                  <p><?= escape($data['content']) ?></p>
                  <br>
                  <h4>Comments</h4>
                  <hr>
                </div>
                <!-- /.card-body -->
                <?php

                $statement = $db->prepare("SELECT * FROM comments WHERE post_id=:post_id");
                $statement->execute([
                  "post_id" => $_GET["id"]
                ]);

                $datas = $statement->fetchAll();

                ?>

                <?php foreach ($datas as $data): ?>
                  <div class="card-footer card-comments">
                    <div class="card-comment">
                      <?php
                        $author_id = $data['author_id'];
                        $statement = $db->prepare("SELECT * FROM users WHERE id=:author_id");

                        $statement->execute([
                          "author_id" => $author_id
                        ]);

                        $result = $statement->fetch();
                      ?>
                      <!-- User image -->
                      <!-- <img class="img-circle img-sm" src="dist/img/user3-128x128.jpg" alt="User Image"> -->

                      <div class="comment-text" style="margin-left: 0px !important;">
                        <span class="username">
                          <?= empty($result) ? "invalid user" : escape($result["name"]) ?>
                          <span class="text-muted float-right"><?= escape($data['created_at']) ?></span>
                        </span><!-- /.username -->
                        <?= escape($data['content']) ?>
                      </div>
                      <!-- /.comment-text -->
                    </div>
                    <!-- /.card-comment -->
                  </div>
                  <!-- /.card-footer -->
                <?php endforeach ?>
                <div class="card-footer">
                  <form action="" method="post">
                  <input type="hidden" name="_token" value="<?= $_SESSION['_token'] ?>">
                    <div>
                      <p class="text-danger"><?php echo empty($contentError) ? "" : "*".$contentError ?></p>
                      <input type="text" name="content" class="form-control form-control-sm" placeholder="Press enter to post comment">
                    </div>
                  </form>
                </div>
                <!-- /.card-footer -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->

      <!-- <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
        <i class="fas fa-chevron-up"></i>
      </a> -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer" style="margin-left: 0px !important;">
    <div class="float-right d-none d-sm-inline">
      <a href="index.php" class="btn btn-secondary">Back</a>
      <a href="logout.php" class="btn btn-default">Logout</a>
    </div>
      <strong>Copyright &copy; 2024 <a href="#">A Programmer</a>.</strong> All rights reserved.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
</body>

</html>
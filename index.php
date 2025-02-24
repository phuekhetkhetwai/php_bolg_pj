<?php
require "config/config.php";
require "config/common.php";


session_start();
if (empty($_SESSION["user"])) {
  header("location: login.php");
  exit();
}

$user = $_SESSION["user"];
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Blog</title>
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
  <div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div class="">
      <section class="content-header">
        <div class="container-fluid">
          <h1 style="text-align: center;">Blog Site</h1>
        </div>
      </section>

      <?php

        if (isset($_GET["pageno"])) {
          $pageno = $_GET["pageno"];
        } else {
          $pageno = 1;
        }

        $numOfRecs = 6;
        $offset = ($pageno - 1) * $numOfRecs;

        $statement = $db->prepare("SELECT * FROM posts ORDER BY id DESC");
        $statement->execute();
        $results = $statement->fetchAll();

        $total_pages = ceil(count($results) / $numOfRecs);

        $statement = $db->prepare("SELECT * FROM posts ORDER BY id DESC LIMIT $offset,$numOfRecs");
        $statement->execute();
        $datas = $statement->fetchAll();
      ?>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <?php foreach ($datas as $data): ?>
              <div class="col-md-4">
                <div class="card card-widget">
                  <div class="card-header">
                    <div class="card-title" style="float: none; text-align: center;">
                      <h4><?= escape($data["title"]) ?></h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <a href="blogdetail.php?id=<?= $data['id'] ?>"><img class="img-fluid pad" src="admin/images/<?= $data["image"] ?>" alt="Photo" style="width: 100%; height: 300px"></a>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            <?php endforeach ?>
          </div>
          <!-- /.row -->
          <div class="row justify-content-end">
            <nav aria-label="Page navigation example" style="margin-right: 10px;">
              <ul class="pagination">
                <li class="page-item"><a href="?pageno=1" class="page-link">First</a></li>
                <li class="page-item <?php if ($pageno <= 1) {echo "disabled";} ?>">
                  <a href="<?php if ($pageno <= 1) {echo "#";} else { echo "?pageno=" . ($pageno - 1); } ?>" class="page-link">Prev</a>
                </li>
                <li class="page-item"><a href="#" class="page-link"><?= $pageno ?></a></li>
                <li class="page-item <?php if ($pageno >= $total_pages) { echo "disabled";} ?>">
                  <a href="<?php if ($pageno >= $total_pages) {echo "#"; } else { echo "?pageno=" . ($pageno + 1); } ?>" class="page-link">Next</a>
                </li>
                <li class="page-item"><a href="?pageno=<?= $total_pages ?>" class="page-link">Last</a></li>
              </ul>
            </nav>
          </div>
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>

    <!-- <a id="back-to-top" href="#" class="btn btn-primary back-to-top" role="button" aria-label="Scroll to top">
      <i class="fas fa-chevron-up"></i>
    </a> -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer" style="margin-left: 0px !important;">
    <div class="float-right d-none d-sm-inline">
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
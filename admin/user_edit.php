<?php
require "../config/config.php";

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

if ($_POST) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    if(isset($_POST["role"])){
        $role = $_POST["role"];
    } else {
        $role = 0;
    }
    
    $statement = $db->prepare("SELECT * FROM users WHERE email=:email AND id!=:id");

    $statement->execute([
      "email" => $email,
      "id" => $id,
    ]);

    $user = $statement->fetch();

  if($user) {
      echo "<script>alert('Email duplicated!!');</script>";
      
    } else {
        $statement = $db->prepare("UPDATE users SET name=:name, email=:email, password=:password, role=:role WHERE id=:id");
    
        $result = $statement->execute([
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "password" => $password,
            "role" => $role,
        ]);
            
        if($result) {
            echo "<script>alert('Successfully updated.');window.location.href='user_list.php'</script>";
        }
    }
}

$id = $_GET["id"];

$statement = $db->prepare("SELECT * FROM users WHERE id=:id");
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
                        <form action="" method="post">
                            <div class="form-group">
                                <input type="hidden" name="id" value="<?= $result['id'] ?>">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?= $result['name'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= $result['email'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" value="<?= $result['password'] ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label><br>
                                
                                    <input type="checkbox" name="role" id="role" value="1" <?php if($result["role"] == 1): ?>checked <?php endif ?>>
                                
                            </div>
                            <div class="form-group">
                                <button class="btn btn-success">SUBMIT</button>
                                <a href="user_list.php" class="btn btn-secondary">BACK</a>  
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
<?php
session_start();

require "../config/config.php";
require "../config/common.php";

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
    $password = password_hash($_POST["password"],PASSWORD_DEFAULT);

    if (empty($_POST["name"]) || empty($_POST["email"]) ) {
        if(empty($_POST["name"])) {
            $nameError = "Name cannot be null";
        }

        if(empty($_POST["email"])) {
            $emailError = "Email cannot be null";
        }

    } elseif(!empty($_POST["password"]) && strlen($_POST["password"]) < 4){
        $passwordError = "Password shoule be long.";
    }else {

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

            if($_POST["password"] != null){
                $statement = $db->prepare("UPDATE users SET name=:name, email=:email, password=:password, role=:role WHERE id=:id");

                $result = $statement->execute([
                    "id" => $id,
                    "name" => $name,
                    "email" => $email,
                    "password" => $password,
                    "role" => $role,
                ]);

            }else{
                $statement = $db->prepare("UPDATE users SET name=:name, email=:email, role=:role WHERE id=:id");

                $result = $statement->execute([
                    "id" => $id,
                    "name" => $name,
                    "email" => $email,
                    "role" => $role,
                ]);
            }
                
            if($result) {
                echo "<script>alert('Successfully updated.');window.location.href='user_list.php'</script>";
            }
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
                            <input type="hidden" name="_token" value="<?= $_SESSION['_token'] ?>">
                            <input type="hidden" name="id" value="<?= $result['id'] ?>">
                            <div class="form-group">
                                <label for="name">Name</label><p class="text-danger"><?php echo empty($nameError) ? "" : "*".$nameError ?></p>
                                <input type="text" name="name" id="name" class="form-control" value="<?= escape($result['name']) ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label><p class="text-danger"><?php echo empty($emailError) ? "" : "*".$emailError ?></p>
                                <input type="email" name="email" id="email" class="form-control" value="<?= escape($result['email']) ?>">
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label><p class="text-danger"><?php echo empty($passwordError) ? "" : "*".$passwordError ?></p>
                                <span class="small">The user already has a password</span>
                                <input type="password" name="password" id="password" class="form-control">
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
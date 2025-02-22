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

if($_POST) {

    if (empty($_POST["name"]) || empty($_POST["email"]) || empty($_POST["password"]) || strlen($_POST["password"]) < 4 ) {
        if(empty($_POST["name"])) {
            $nameError = "Name cannot be null";
        }

        if(empty($_POST["email"])) {
            $emailError = "Email cannot be null";
        }

        if(empty($_POST["password"])) {
            $passwordError = "Password cannot be null";
        }

        if(!empty($_POST["password"]) && strlen($_POST["password"]) < 4 ) {
            $passwordError = "Password shoule be long.";
        }
        
    } else {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        if(isset($_POST["role"])){
            $role = $_POST["role"];
        } else {
            $role = 0;
        }

        $statement = $db->prepare("SELECT * FROM users WHERE email=:email");

        $statement->execute([
            "email" => $email,
        ]);

        $user = $statement->fetch();

        if($user) {
            echo "<script>alert('Email duplicated!!');</script>";
            
        } else {

            $statement = $db->prepare("INSERT INTO users (name,email,password,role) VALUE (:name,:email,:password,:role)");
        
            $result = $statement->execute([
                "name" => $name,
                "email" => $email,
                "password" => $password,
                "role" => $role,
            ]);
                
            if($result) {
                echo "<script>alert('Successfully added.');window.location.href='user_list.php'</script>";
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
                        <form action="user_add.php" method="post">
                            <div class="form-group">
                                <label for="name">Name</label><p class="text-danger"><?php echo empty($nameError) ? "" : "*".$nameError ?></p>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label><p class="text-danger"><?php echo empty($emailError) ? "" : "*".$emailError ?></p>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label><p class="text-danger"><?php echo empty($passwordError) ? "" : "*".$passwordError ?></p>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="role">Role</label><br>
                                <input type="checkbox" name="role" id="role" class="" value="1">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">SUBMIT</button>
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

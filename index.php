<?php 
$page_title = "User Authentication - Homepage";
include_once 'partials/headers.php';
?>

    <div class="container">
    

      <div class="flag">
        <h1>User Authentication System</h1>
        <p class="lead">Use this document as a way to quickly start any new project.<br> All you get is this text and a mostly barebones HTML document.</p>

        <?php if(!isset($_SESSION['username'])): ?>
            <P class="lead">You are currently not signin <a href="login.php">Login</a> Not yet a member? <a href="signup.php">Signup</a> </P>
        <?php else: ?>
            <p class="lead">You are logged in as <?php if(isset($_SESSION['username'])) echo $_SESSION['username']; ?> <a href="logout.php">Logout</a> </p>
        <?php endif ?>
      </div>

    </div>



<?php include_once 'partials/footers.php' ?>

</body>
</html>
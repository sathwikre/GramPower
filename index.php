<?php
// Handle login form submission
$adminEmail = "sathwik@gmail.com";
$adminPass = "sathwik";
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($email === $adminEmail && $password === $adminPass) {
        header("Location: main.php");
        exit();
    } else {
        $loginError = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome Page</title>
    <style>
        body {
            background: linear-gradient(to right, #a1c4fd, #c2e9fb);
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            padding-top: 100px;
            color: #333;
        }

        h2 {
            font-size: 28px;
            margin-bottom: 30px;
        }

        button {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
            color: white;
            padding: 12px 24px;
            font-size: 18px;
            border-radius: 30px;
            cursor: pointer;
            transition: 0.3s ease;
            margin: 10px;
        }

        button:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
            transform: scale(1.05);
        }

        form {
            display: inline-block;
        }

        #loginForm {
            display: none;
            margin-top: 30px;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            width: 300px;
            margin-left: auto;
            margin-right: auto;
        }

        input[type="email"],
        input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h2>Welcome! Choose an Option:</h2>

    <!-- User button -->
    <form action="user.php" method="get">
        <button type="submit">Go to User Page</button>
    </form>

    <!-- Admin login trigger -->
    <button onclick="document.getElementById('loginForm').style.display='block'">Admin Login</button>

    <!-- Admin login form -->
    <div id="loginForm">
        <form method="post" action="">
            <input type="email" name="email" placeholder="Admin Email" required><br>
            <input type="password" name="password" placeholder="Admin Password" required><br>
            <button type="submit">Login</button>
        </form>
        <?php if ($loginError): ?>
            <p class="error"><?= $loginError ?></p>
        <?php endif; ?>
    </div>

</body>
</html>

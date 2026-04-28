<?php
session_start();
require_once 'api/db.php';

$error='';
if($_SERVER['REQUEST_METHOD']==='POST')
    {
        $username=trim($_POST['username'] ?? '');
        $password=trim($_POST['password'] ?? '');
        if($username===''||$password==='')
            {
                $error='please enter a username and a password.';
            }
        else
            {
                $stmt=$conn->prepare
                ('SELECT id, username, password FROM admins WHERE username = ? LIMIT 1');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result=$stmt->get_result();

                if($result && $result->num_rows===1)
                    {
                        $row=$result->fetch_assoc();
                        if(password_verify($password,$row['password']))
                            {
                                $_SESSION['admin_id']=$row['id'];
                                $_SESSION['admin_username']=$row['username'];
                                header('location: index.php');

                                exit();

                            }
                        else $error='Invalid username or password.';
                    }
                else $error='Invalid username or password.';
                $stmt->close();
            }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            
        }
        .login-title {
            font-size:38px;
            font-weight: 900;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-card
        {
            backdrop-filter: blur(12px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.4);

            display: flex;
            flex-direction: column;
            gap: 10px;

            padding: 20px;
            width: 300px;
            
            width: 320px;
            padding: 20px;
            
        }

        .btn{
            margin-top: 10px;
            padding: 10px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #8B5CF6, #22D3EE);
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .input {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(0,0,0,0.2);
            color: white;
        }

        .alert{
            margin-bottom: 12px;
            padding: 10px;
            border-radius: 10px;

            color: #ff6b6b;
            background: rgba(255, 0, 0, 0.08);
            border: 1px solid rgba(255, 0, 0, 0.2);

            opacity: 0;
            transform: translateY(-10px);
            animation: fadeIn 0.5s ease forwards;
            }

        @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
        }

        
    </style>
</head>
<body>
    <form method="POST" class=login-card>
        <?php
        if($error)
            {
            echo "<div class='alert'>$error</div>";
            }
        ?>
        <div class="login-title">Login</div>
        <label for="username">Username</label>
        <input id="username" type="text" name="username" class="input"/>
        <label for="password">Password</label>
        <input id="password" type="text" name="password" class="input"/>
        <button type="submit" class="btn">Login</button>
    </form>
</body>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Zanzibar Tour Drivers</title>
    <link rel="icon" type="text/css" href="./src/img/ztdlogo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .message {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .success {
            border-color: #d4edda;
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            border-color: #f8d7da;
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <?php
    if (isset($_GET['message'])) {
        $message = htmlspecialchars($_GET['message']);
        $message_class = strpos($message, 'error') !== false ? 'error' : 'success';
        echo "<div class='message $message_class'>$message</div>";
    } else {
        echo "<div class='message'>No message received.</div>";
    }
    ?>
    <br>
    <a href="index.html">Go back to the form</a>
</body>
</html>


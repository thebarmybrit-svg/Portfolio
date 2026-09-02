<?php

// 1. Helper Function to Parse .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split by the first '=' character
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Load environment variables (assumes .env is in the same directory)
loadEnv(__DIR__ . '/.env');

// 2. Database Connection using .env values
$host    = $_ENV['DB_HOST'] ?? 'localhost';
$db      = $_ENV['DB_NAME'] ?? 'portfolio';
$user    = $_ENV['DB_USER'] ?? 'root';
$pass    = $_ENV['DB_PASSWORD'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log error securely in production, avoid exposing database info
    die("Database connection failed. Please try again later.");
}

// 3. Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $fname   = isset($_POST['fname']) ? trim($_POST['fname']) : '';
    $lname   = isset($_POST['lname']) ? trim($_POST['lname']) : '';
    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : null;

    $errors = [];

    // Server-Side Validation
    if (empty($fname)) {
        $errors[] = "First Name is required.";
    }
    if (empty($lname)) {
        $errors[] = "Surname is required.";
    }
    if (empty($email)) {
        $errors[] = "Email Address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Handle errors or insert into database
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
        }
        echo "<p><a href='javascript:history.back()'>Go Back</a></p>";
    } else {
        $sql = "INSERT INTO contact_submissions (first_name, surname, email, subject, message) 
                VALUES (:first_name, :surname, :email, :subject, :message)";
        
        $stmt = $pdo->prepare($sql);
        
        $success = $stmt->execute([
            ':first_name' => htmlspecialchars($fname),
            ':surname'    => htmlspecialchars($lname),
            ':email'      => filter_var($email, FILTER_SANITIZE_EMAIL),
            ':subject'    => $subject ? htmlspecialchars($subject) : null,
            ':message'    => $message ? htmlspecialchars($message) : null
        ]);

        if ($success) {
            echo "<p style='color:green;'>Thank you! Your message has been sent successfully.</p>";
        } else {
            echo "<p style='color:red;'>Something went wrong. Please try again later.</p>";
        }
    }
} else {
    header("Location: index.html");
    exit;
}

require base_path('views/index.view.php');
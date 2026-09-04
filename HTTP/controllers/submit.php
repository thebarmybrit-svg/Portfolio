<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload PHPMailer classes
require __DIR__ . '/vendor/autoload.php';

// Helper Function to Parse .env file
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

    // Handle AJAX Responses for validation errors
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => implode('<br>', $errors)
        ]);
        exit; 
    } else {
        try {
            // Insert data into DB
            $sql = "INSERT INTO contact_submissions (first_name, surname, email, subject, message) 
                    VALUES (:first_name, :surname, :email, :subject, :message)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                ':first_name' => $fname,
                ':surname'    => $lname,
                ':email'      => filter_var($email, FILTER_SANITIZE_EMAIL),
                ':subject'    => $subject ?: null,
                ':message'    => $message ?: null
            ]);

            //  Dispatch SMTP email notification via Mailtrap
            $mail = new PHPMailer(true);

            // Updated SMTP Server Settings matching your Mailtrap specifications
            $mail->isSMTP();                                            
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'live.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $_ENV['SMTP_USER'] ?? 'api';
            $mail->Password   = $_ENV['SMTP_PASS'] ?? 'YOUR_MAILTRAP_API_TOKEN'; // Set your actual key here or in .env
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? 'tls'; 
            $mail->Port       = $_ENV['SMTP_PORT'] ?? 2525;                                    

            // Email Headers
            // Note: Mailtrap live streams enforce that 'From' aligns with your validated sending domain!
            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@yourregistereddomain.com', 'Portfolio Contact Form');
            $mail->addAddress('alexander.brown@netmatters-scs.com', 'Alexander Brown'); 
            
            // Set Reply-To as the person who filled out the form
            $mail->addReplyTo($email, "$fname $lname");

            // Email HTML Content
            $mail->isHTML(true);                                  
            $mail->Subject = $subject ? "Contact Form: $subject" : "New Contact Submission from $fname $lname";
            
            $emailBody = "
                <h3>New Contact Form Submission</h3>
                <p><strong>Name:</strong> " . htmlspecialchars($fname . ' ' . $lname) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject ?? 'None') . "</p>
                <p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message ?? '')) . "</p>
            ";
            
            $mail->Body = $emailBody;
            $mail->send();

            // Return success response to AJAX handler
            echo json_encode([
                'success' => true, 
                'message' => 'Your message has been sent successfully!'
            ]);
            exit;

        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Database error. Please try again later.'
            ]);
            exit;
        } catch (Exception $e) {
            // Reaches here if DB works but Mailtrap rejects the delivery payload
            echo json_encode([
                'success' => false, 
                'message' => 'Data saved, but the email notification failed to send.'
            ]);
            exit;
        }
    }
} 
require base_path('views/index.view.php');
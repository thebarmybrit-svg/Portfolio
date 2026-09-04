<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload Composer classes (moves up 2 levels out of HTTP/controllers)
require dirname(__DIR__, 2) . '/vendor/autoload.php';

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

// Load environment variables (moves up 2 levels out of HTTP/controllers)
loadEnv(dirname(__DIR__, 2) . '/.env');

// Database Connection using your explicit .env variables
$host    = $_ENV['DB_HOST'] ?? 'localhost';
$db      = $_ENV['DB_NAME'] ?? 'database';
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

// Process form submission
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

            // =========================================================================
            // SMTP EMAIL DISPATCH (PHPMailer)
            // =========================================================================
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();                                            // Send via SMTP
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'live.smtp.mailtrap.io'; // Your SMTP server
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $_ENV['SMTP_USER'] ?? '';               // SMTP username
            $mail->Password   = $_ENV['SMTP_PASS'] ?? '';               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
            $mail->Port       = 2525;                                   // cPanel STARTTLS custom port

            // Recipients
            $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'verified-sender@demomailtrap.co';
            $mail->setFrom($fromEmail, 'Portfolio Contact Form');
            $mail->addAddress('alexander.brown@netmatters-scs.com', 'Alexander Brown');
            $mail->addReplyTo($email, "$fname $lname"); // Enables replying directly to the user

            // Content
            $emailSubject = $subject ? "Contact Form: $subject" : "New Contact Submission from $fname $lname";
            $mail->Subject = $emailSubject;
            
            $mail->isHTML(true);
            $mail->Body = "
                <h3>New Contact Form Submission</h3>
                <p><strong>Name:</strong> " . htmlspecialchars($fname . ' ' . $lname) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject ?? 'None') . "</p>
                <p><strong>Message:</strong><br/>" . nl2br(htmlspecialchars($message ?? '')) . "</p>
            ";
            
            // Plain text alternative
            $mail->AltBody = "New Contact Form Submission\n\nName: $fname $lname\nEmail: $email\nSubject: " . ($subject ?? 'None') . "\nMessage:\n" . ($message ?? '');

            $mail->send();
            // =========================================================================

            // Return success response after a successful DB entry and Email transmission
            echo json_encode([
                'success' => true, 
                'message' => 'Your message has been saved and sent successfully!'
            ]);
            exit;

        } catch (Exception $e) {
            // Catches PHPMailer specific transmission failures
            echo json_encode([
                'success' => false, 
                'message' => 'Database saved, but email notification failed. Error: ' . $mail->ErrorInfo
            ]);
            exit;
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Database error. Please try again later.'
            ]);
            exit;
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'An unexpected issue occurred.'
            ]);
            exit;
        }
    }
} 
require base_path('views/index.view.php');

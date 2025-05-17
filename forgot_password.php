<?php
session_start();
require_once "config.php";
require_once "mail_config.php";
require_once "includes/layout.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$email = $email_err = "";
$reset_link_sent = false;
$email_error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
        
        // Check if email exists
        $sql = "SELECT id, username FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $user_id, $username);
                
                if (mysqli_stmt_fetch($stmt)) {
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Store token in database
                    $update_sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
                    if ($update_stmt = mysqli_prepare($link, $update_sql)) {
                        mysqli_stmt_bind_param($update_stmt, "sss", $token, $expiry, $email);
                        
                        if (mysqli_stmt_execute($update_stmt)) {
                            // Send reset email
                            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
                            $to = $email;
                            $subject = "Password Reset Request - QuizMaster";
                            
                            // Send reset email using PHPMailer
                            $mail = new PHPMailer(true);

                            try {
                                // Server settings
                                $mail->isSMTP();
                                $mail->Host = SMTP_HOST;
                                $mail->SMTPAuth = true;
                                $mail->Username = SMTP_USERNAME;
                                $mail->Password = SMTP_PASSWORD;
                                $mail->SMTPSecure = SMTP_SECURE;
                                $mail->Port = SMTP_PORT;

                                // Recipients
                                $mail->setFrom(SMTP_USERNAME, MAIL_FROM_NAME);
                                $mail->addAddress($email, $username);

                                // Content
                                $mail->isHTML(true);
                                $mail->Subject = "Password Reset Request - QuizMaster";
                                $mail->Body = "
                                <html>
                                <head>
                                    <style>
                                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                        .button { display: inline-block; padding: 10px 20px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 5px; }
                                        .footer { margin-top: 20px; font-size: 12px; color: #666; }
                                    </style>
                                </head>
                                <body>
                                    <div class='container'>
                                        <h2>Hello $username,</h2>
                                        <p>We received a request to reset your password for your QuizMaster account.</p>
                                        <p>Click the button below to reset your password. This link will expire in 1 hour.</p>
                                        <p><a href='$reset_link' class='button' style='color: white; text-decoration: none;'>Reset Password</a></p>
                                        <p>If you did not request this reset, please ignore this email or contact support if you have concerns.</p>
                                        <div class='footer'>
                                            <p>Best regards,<br>QuizMaster Team</p>
                                            <p>If the button doesn't work, copy and paste this link into your browser:<br>$reset_link</p>
                                        </div>
                                    </div>
                                </body>
                                </html>";

                                $mail->send();
                                $reset_link_sent = true;
                            } catch (Exception $e) {
                                $email_error_message = "Error sending reset email. Please try again later. Error: {$mail->ErrorInfo}";
                            }
                        } else {
                            $email_error_message = "Something went wrong. Please try again later.";
                        }
                        mysqli_stmt_close($update_stmt);
                    }
                }
                // Always show success message even if email not found (security best practice)
                $reset_link_sent = true;
            } else {
                $email_error_message = "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Start the page layout
renderHeader("Forgot Password");

// Prepare the form content
$formContent = '';
if ($reset_link_sent) {
    $formContent .= '
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        If an account exists with this email address, we\'ve sent password reset instructions to it. Please check your inbox and follow the instructions.
                    </p>
                    <div class="mt-4">
                        <div class="-mx-2 -my-1.5 flex">
                            <a href="login.php" class="bg-green-50 px-2 py-1.5 rounded-md text-sm font-medium text-green-800 hover:bg-green-100">
                                Return to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
} else {
    $formContent .= '
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Forgot Password</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your email address and we\'ll send you a link to reset your password
            </p>
        </div>';

    if (!empty($email_error_message)) {
        $formContent .= '
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">' . $email_error_message . '</p>
                    </div>
                </div>
            </div>';
    }

    $formContent .= '
        <form class="mt-8 space-y-6" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($email_err) ? 'border-red-500' : '') . '"
                        value="' . $email . '" 
                        placeholder="Enter your email address">
                </div>
                ' . (!empty($email_err) ? '<p class="mt-2 text-sm text-red-600">' . $email_err . '</p>' : '') . '
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Send Reset Link
                </button>
            </div>

            <div class="text-sm text-center">
                <a href="login.php" class="font-medium text-primary-600 hover:text-primary-500">
                    Remember your password? Login here
                </a>
            </div>
        </form>';
}

// Render the form using the responsive container
renderFormContainer($formContent);

// End the page layout
renderFooter();
?> 
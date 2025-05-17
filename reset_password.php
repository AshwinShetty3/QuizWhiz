<?php
session_start();
require_once "config.php";
require_once "includes/layout.php";

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
$token = $token_err = "";
$token_valid = false;

// Verify token
if (isset($_GET["token"])) {
    $token = trim($_GET["token"]);
    
    // Check if token exists and is valid
    $sql = "SELECT id, email, username FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $user_id, $email, $username);
            
            if (mysqli_stmt_num_rows($stmt) == 1 && mysqli_stmt_fetch($stmt)) {
                $token_valid = true;
            } else {
                $token_err = "Invalid or expired reset token.";
            }
        } else {
            $token_err = "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";     
    } else {
        $new_password = trim($_POST["new_password"]);
        
        // Password strength validation
        if (strlen($new_password) < 8) {
            $new_password_err = "Password must be at least 8 characters long.";
        } elseif (!preg_match("/[A-Z]/", $new_password)) {
            $new_password_err = "Password must contain at least one uppercase letter.";
        } elseif (!preg_match("/[a-z]/", $new_password)) {
            $new_password_err = "Password must contain at least one lowercase letter.";
        } elseif (!preg_match("/[0-9]/", $new_password)) {
            $new_password_err = "Password must contain at least one number.";
        } elseif (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $new_password)) {
            $new_password_err = "Password must contain at least one special character.";
        }
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before updating the database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        // Update password
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
        
        if ($stmt = mysqli_prepare($link, $sql)) {
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ss", $param_password, $token);
            
            if (mysqli_stmt_execute($stmt)) {
                // Password updated successfully. Redirect to login page
                session_destroy();
                header("location: login.php?password_reset=success");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Start the page layout
renderHeader("Reset Password");

// Prepare the form content
$formContent = '';

if (!empty($token_err)) {
    $formContent .= '
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">' . $token_err . '</p>
                    <p class="mt-2">Please request a new password reset <a href="forgot_password.php" class="font-medium underline">here</a>.</p>
                </div>
            </div>
        </div>';
} elseif ($token_valid) {
    $formContent .= '
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Reset Password</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Hello ' . htmlspecialchars($username) . ', please enter your new password below
            </p>
        </div>

        <form class="mt-8 space-y-6" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . $token . '" method="post">
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <div class="mt-1">
                    <input id="new_password" name="new_password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($new_password_err) ? 'border-red-500' : '') . '"
                        value="' . $new_password . '">
                </div>
                ' . (!empty($new_password_err) ? '<p class="mt-2 text-sm text-red-600">' . $new_password_err . '</p>' : '') . '
                
                <div class="mt-4 bg-gray-50 rounded-md p-4 space-y-2">
                    <p class="text-sm font-medium text-gray-700">Password must contain:</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>At least 8 characters</li>
                        <li>At least one uppercase letter</li>
                        <li>At least one lowercase letter</li>
                        <li>At least one number</li>
                        <li>At least one special character (!@#$%^&*(),.?":{}|<>)</li>
                    </ul>
                </div>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-1">
                    <input id="confirm_password" name="confirm_password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($confirm_password_err) ? 'border-red-500' : '') . '">
                </div>
                ' . (!empty($confirm_password_err) ? '<p class="mt-2 text-sm text-red-600">' . $confirm_password_err . '</p>' : '') . '
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Reset Password
                </button>
            </div>
        </form>';
}

// Render the form using the responsive container
renderFormContainer($formContent);

// End the page layout
renderFooter();
?> 
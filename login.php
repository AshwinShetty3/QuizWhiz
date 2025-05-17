<?php
session_start();
require_once "config.php";
require_once "includes/layout.php";

// Initialize variables
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Process form data when submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password_hash FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $username, $password_hash);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $password_hash)){
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: index.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}

// Start the page layout
renderHeader("Login");

// Prepare the form content
$formContent = '
    <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Welcome back</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Sign in to your account
        </p>
    </div>';

// Add error messages if any
if (!empty($login_err)) {
    $formContent .= '
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">' . $login_err . '</p>
                </div>
            </div>
        </div>';
}

// Add success messages if any
if (isset($_GET["registration"]) && $_GET["registration"] == "success") {
    $formContent .= '
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">You have been successfully registered. You can now login.</p>
                </div>
            </div>
        </div>';
}

if (isset($_GET["password_reset"]) && $_GET["password_reset"] == "success") {
    $formContent .= '
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Your password has been successfully reset. You can now login with your new password.</p>
                </div>
            </div>
        </div>';
}

// Add the login form
$formContent .= '
    <form class="mt-8 space-y-6" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
        <div class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="mt-1">
                    <input id="username" name="username" type="text" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($username_err) ? 'border-red-500' : '') . '"
                        value="' . $username . '">
                    ' . (!empty($username_err) ? '<p class="mt-2 text-sm text-red-600">' . $username_err . '</p>' : '') . '
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($password_err) ? 'border-red-500' : '') . '">
                    ' . (!empty($password_err) ? '<p class="mt-2 text-sm text-red-600">' . $password_err . '</p>' : '') . '
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                Sign in
            </button>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-sm">
                <a href="forgot_password.php" class="font-medium text-primary-600 hover:text-primary-500">
                    Forgot your password?
                </a>
            </div>
            <div class="text-sm">
                <a href="register.php" class="font-medium text-primary-600 hover:text-primary-500">
                    Create an account
                </a>
            </div>
        </div>
    </form>';

// Render the form using the responsive container
renderFormContainer($formContent);

// End the page layout
renderFooter();
?> 
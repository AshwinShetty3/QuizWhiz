<?php
session_start();
require_once "config.php";
require_once "includes/layout.php";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

// Initialize variables
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Process form data when submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email address.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already registered.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
            
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if(mysqli_stmt_execute($stmt)){
                // Get the new user's ID
                $new_user_id = mysqli_insert_id($link);
                
                // Automatically log in the user
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $new_user_id;
                $_SESSION["username"] = $username;
                
                // Redirect to home page
                header("location: index.php");
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}

// Start the page layout
renderHeader("Register");

// Prepare the form content
$formContent = '
    <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Join QuizWhiz and start your learning journey
        </p>
    </div>

    <form class="mt-8 space-y-6" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">
        <div class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <div class="mt-1">
                    <input id="username" name="username" type="text" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($username_err) ? 'border-red-500' : '') . '"
                        value="' . $username . '"
                        placeholder="Choose a username">
                    ' . (!empty($username_err) ? '<p class="mt-2 text-sm text-red-600">' . $username_err . '</p>' : '') . '
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($email_err) ? 'border-red-500' : '') . '"
                        value="' . $email . '"
                        placeholder="Enter your email">
                    ' . (!empty($email_err) ? '<p class="mt-2 text-sm text-red-600">' . $email_err . '</p>' : '') . '
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($password_err) ? 'border-red-500' : '') . '"
                        placeholder="Create a strong password">
                    ' . (!empty($password_err) ? '<p class="mt-2 text-sm text-red-600">' . $password_err . '</p>' : '') . '
                </div>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-1">
                    <input id="confirm_password" name="confirm_password" type="password" required 
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm ' . 
                        (!empty($confirm_password_err) ? 'border-red-500' : '') . '"
                        placeholder="Confirm your password">
                    ' . (!empty($confirm_password_err) ? '<p class="mt-2 text-sm text-red-600">' . $confirm_password_err . '</p>' : '') . '
                </div>
            </div>
        </div>

        <div>
            <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                    </svg>
                </span>
                Create Account
            </button>
        </div>

        <div class="text-sm text-center">
            <p class="text-gray-600">
                Already have an account?
                <a href="login.php" class="font-medium text-primary-600 hover:text-primary-500">
                    Sign in here
                </a>
            </p>
        </div>
    </form>';

// Render the form using the responsive container
renderFormContainer($formContent);

// End the page layout
renderFooter();
?> 
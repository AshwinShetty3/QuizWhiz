<?php
function renderHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?> - QuizWhiz</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#f0f9ff',
                                100: '#e0f2fe',
                                200: '#bae6fd',
                                300: '#7dd3fc',
                                400: '#38bdf8',
                                500: '#0ea5e9',
                                600: '#0284c7',
                                700: '#0369a1',
                                800: '#075985',
                                900: '#0c4a6e',
                            },
                        },
                    },
                },
            }
        </script>
        <style>
            @media (max-width: 640px) {
                .mobile-menu {
                    display: none;
                    position: fixed;
                    top: 64px;
                    left: 0;
                    right: 0;
                    background: white;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                    z-index: 50;
                }
                .mobile-menu.active {
                    display: block;
                }
                .quiz-container {
                    padding: 1rem;
                }
                .quiz-option {
                    padding: 0.75rem;
                    margin-bottom: 0.5rem;
                }
            }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="/" class="text-2xl font-bold flex items-center">
                                <span class="text-primary-600">📚</span>
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 ml-1">QuizWhiz</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-4">
                        <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                            <a href="index.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-bold">Home</a>
                            <a href="about.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-bold">About</a>
                            <a href="scores.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-bold">Scores</a>
                            <a href="contact.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-bold">Contact</a>
                            <span class="text-gray-600 px-3 py-2 rounded-md text-sm font-medium">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                            <a href="logout.php" class="bg-primary-500 text-white hover:bg-primary-600 px-3 py-2 rounded-md text-sm font-bold">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="register.php" class="bg-primary-500 text-white hover:bg-primary-600 px-3 py-2 rounded-md text-sm font-medium">Register</a>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="mobile-menu sm:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 shadow-lg">
                    <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                        <div class="px-3 py-2 text-sm font-medium text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></div>
                        <a href="index.php" class="block px-3 py-2 rounded-md text-base font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-50">Home</a>
                        <a href="about.php" class="block px-3 py-2 rounded-md text-base font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-50">About</a>
                        <a href="scores.php" class="block px-3 py-2 rounded-md text-base font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-50">Scores</a>
                        <a href="contact.php" class="block px-3 py-2 rounded-md text-base font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-50">Contact</a>
                        <a href="logout.php" class="block px-3 py-2 rounded-md text-base font-bold bg-primary-500 text-white hover:bg-primary-600">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Login</a>
                        <a href="register.php" class="block px-3 py-2 rounded-md text-base font-medium bg-primary-500 text-white hover:bg-primary-600">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mt-16">
    <?php
}

function renderFooter() {
    ?>
        </main>
        
        <footer class="bg-white border-t mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    A fun and educational quiz platform
                </div>
            </div>
        </footer>

        <script>
            // Mobile menu toggle
            document.querySelector('.mobile-menu-button').addEventListener('click', function() {
                document.querySelector('.mobile-menu').classList.toggle('active');
            });
        </script>
    </body>
    </html>
    <?php
}

// Common function for responsive form containers
function renderFormContainer($content) {
    ?>
    <div class="min-h-[calc(100vh-14rem)] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-6 sm:p-8 rounded-xl shadow-lg">
            <?php echo $content; ?>
        </div>
    </div>
    <?php
}

// Common function for responsive card containers
function renderCard($content, $classes = '') {
    ?>
    <div class="bg-white rounded-xl shadow-lg p-6 <?php echo $classes; ?>">
        <?php echo $content; ?>
    </div>
    <?php
}

// Common function for responsive grid layouts
function renderGrid($items, $columns = '3') {
    $gridCols = [
        '1' => 'grid-cols-1',
        '2' => 'grid-cols-1 sm:grid-cols-2',
        '3' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        '4' => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    ];
    ?>
    <div class="grid <?php echo $gridCols[$columns]; ?> gap-6">
        <?php foreach ($items as $item): ?>
            <?php renderCard($item); ?>
        <?php endforeach; ?>
    </div>
    <?php
}
?> 
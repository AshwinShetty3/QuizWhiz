<?php
session_start();
require_once "includes/layout.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Start the page layout
renderHeader("About");
?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4">About QuizWhiz</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Welcome to our Quiz Website! This platform is designed to provide an engaging and educational experience through a series of carefully crafted questions.
            </p>
        </div>

        <!-- Mission Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-12">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
                <p class="text-gray-600">
                    Our mission is to make learning fun and interactive. We believe that quizzes are an excellent way to test knowledge and learn new information in an engaging manner.
                </p>
            </div>
        </div>

        <!-- Include SVG Icons -->
        <?php include 'icons/features.svg'; ?>

        <!-- Features Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-12">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Key Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-green-600">
                                <use href="#diverse-questions" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Diverse Questions</h3>
                        <p class="text-gray-600">10 diverse questions covering various topics</p>
                    </div>
                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 bg-red-50 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-yellow-600">
                                <use href="#instant-feedback" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Instant Feedback</h3>
                        <p class="text-gray-600">Immediate feedback on answers and detailed explanations</p>
                    </div>

                    <div class="p-6 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-center w-12 h-12 bg-red-50 rounded-lg mb-4">
                            <svg class="w-6 h-6 text-red-600">
                                <use href="#score-tracking" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Score Tracking</h3>
                        <p class="text-gray-600">Comprehensive score history and performance tracking</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- How to Use Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-12">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">How to Use</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold">1</div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Create an Account</h3>
                            <p class="text-gray-600">Sign up for a new account or log in if you already have one</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-200 flex items-center justify-center text-purple-600 font-bold">2</div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Start the Quiz</h3>
                            <p class="text-gray-600">Navigate to the quiz page and begin your learning journey</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-200 flex items-center justify-center text-green-600 font-bold">3</div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Answer Questions</h3>
                            <p class="text-gray-600">Select the most appropriate option for each question</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-600 font-bold">4</div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Review Results</h3>
                            <p class="text-gray-600">Submit your answers and review your score and feedback</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Contact Us</h2>
                <p class="text-gray-600">
                    If you have any questions, suggestions, or feedback, please don't hesitate to contact us. We're here to help!
                </p>
                <div class="mt-6">
                    <a href="contact.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Contact Support</a>
                </div>
            </div>
        </div>
    </main>

<?php renderFooter(); ?>
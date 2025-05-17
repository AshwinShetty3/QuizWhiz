<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - QuizWhiz</title>
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
</head>

<body class="bg-gray-50">
    <?php 
    require_once "includes/layout.php";
    renderHeader("Contact");
    ?>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="about.php" class="inline-flex items-center text-gray-600 hover:text-primary-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to About
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-6">Contact Support</h1>

                <form action="https://formspree.io/f/mpwzekgg" method="POST" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">
                            Message
                            <span class="text-gray-500 text-xs ml-1">(max 300 characters)</span>
                        </label>
                        <textarea name="message" id="message" rows="4" required maxlength="300"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                        <div class="mt-1 text-sm text-gray-500">
                            <span id="char-count">0</span>/300 characters
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- <div class="hidden">
        <?php renderFooter(); ?>
    </div> -->

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('[aria-label="Menu"]');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', () => {
                    mobileMenu.classList.toggle('active');
                });
            }
        });

        // Character counter
        const messageField = document.getElementById('message');
        const charCount = document.getElementById('char-count');

        function updateCharCount() {
            const length = messageField.value.length;
            charCount.textContent = length;
            charCount.classList.toggle('text-red-600', length > 300);
        }
        messageField.addEventListener('input', updateCharCount);
        updateCharCount();

        // AJAX Form Handling
        document.querySelector('form').addEventListener('submit', async (e) => {
            e.preventDefault();

            try {
                const response = await fetch('https://formspree.io/f/mpwzekgg', {
                    method: 'POST',
                    body: new FormData(e.target),
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const alertDiv = document.createElement('div');
                alertDiv.className = `p-4 mb-6 border-l-4 ${response.ok ? 'bg-green-50 border-green-400' : 'bg-red-50 border-red-400'}`;
                alertDiv.innerHTML = `
                <div class="flex">
                    <svg class="h-5 w-5 ${response.ok ? 'text-green-400' : 'text-red-400'}" fill="currentColor" viewBox="0 0 20 20">
                        ${response.ok ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>' : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>'}
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm ${response.ok ? 'text-green-700' : 'text-red-700'}">${response.ok ? 'Thanks! The message was sent successfully.' : 'An unexpected error occurred.'}</p>
                    </div>
                </div>
            `;

                const existingAlert = document.querySelector('.bg-green-50, .bg-red-50');
                if (existingAlert) existingAlert.remove();

                e.target.parentNode.insertBefore(alertDiv, e.target);
                e.target.reset();

                if (response.ok) setTimeout(() => alertDiv.remove(), 4000);
            } catch (error) {
                console.error('Submission failed:', error);
            }
        });
    </script>
</body>

</html>

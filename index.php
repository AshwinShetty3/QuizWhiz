<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "includes/config.php";

// All quiz questions
$all_questions = [
    [
        'question' => 'What is the capital of France?',
        'options' => ['London', 'Berlin', 'Paris', 'Madrid'],
        'correct' => 2
    ],
    [
        'question' => 'Which planet is known as the Red Planet?',
        'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
        'correct' => 1
    ],
    [
        'question' => 'What is the largest mammal in the world?',
        'options' => ['African Elephant', 'Blue Whale', 'Giraffe', 'Hippopotamus'],
        'correct' => 1
    ],
    [
        'question' => 'Who painted the Mona Lisa?',
        'options' => ['Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Michelangelo'],
        'correct' => 2
    ],
    [
        'question' => 'What is the chemical symbol for gold?',
        'options' => ['Ag', 'Fe', 'Au', 'Cu'],
        'correct' => 2
    ],
    [
        'question' => 'Which country is home to the kangaroo?',
        'options' => ['New Zealand', 'South Africa', 'Australia', 'Brazil'],
        'correct' => 2
    ],
    [
        'question' => 'What is the largest organ in the human body?',
        'options' => ['Heart', 'Brain', 'Liver', 'Skin'],
        'correct' => 3
    ],
    [
        'question' => 'Who wrote "Romeo and Juliet"?',
        'options' => ['Charles Dickens', 'William Shakespeare', 'Jane Austen', 'Mark Twain'],
        'correct' => 1
    ],
    [
        'question' => 'What is the square root of 144?',
        'options' => ['10', '12', '14', '16'],
        'correct' => 1
    ],
    [
        'question' => 'Which element has the chemical symbol "O"?',
        'options' => ['Gold', 'Silver', 'Oxygen', 'Osmium'],
        'correct' => 2
    ],
    [
        'question' => 'What is the currency of China?',
        'options' => ['Yen', 'Won', 'Rupee', 'Yuan'],
        'correct' => 3
    ],
    [
        'question' => 'Which planet is known for its prominent rings?',
        'options' => ['Jupiter', 'Mars', 'Saturn', 'Uranus'],
        'correct' => 2
    ],
    [
        'question' => 'What is the function of white blood cells?',
        'options' => ['Carry oxygen', 'Clot blood', 'Fight infection', 'Produce energy'],
        'correct' => 2
    ],
    [
        'question' => 'Who painted the "Sistine Chapel ceiling"?',
        'options' => ['Raphael', 'Michelangelo', 'Botticelli', 'Donatello'],
        'correct' => 1
    ],
    [
        'question' => 'What is the chemical symbol for sodium?',
        'options' => ['So', 'Sd', 'Na', 'K'],
        'correct' => 2
    ],
    [
        'question' => 'Which country is home to the Great Barrier Reef?',
        'options' => ['Philippines', 'Indonesia', 'Australia', 'Malaysia'],
        'correct' => 2
    ],
    [
        'question' => 'What is the smallest bone in the human body?',
        'options' => ['Femur', 'Tibia', 'Stapes', 'Radius'],
        'correct' => 2
    ],
    [
        'question' => 'Who wrote the novel "1984"?',
        'options' => ['Aldous Huxley', 'George Orwell', 'Ray Bradbury', 'H.G. Wells'],
        'correct' => 1
    ],
    [
        'question' => 'What is the value of √64?',
        'options' => ['6', '7', '8', '9'],
        'correct' => 2
    ],
    [
        'question' => 'Which element has the chemical symbol "N"?',
        'options' => ['Neon', 'Nickel', 'Nitrogen', 'Nobelium'],
        'correct' => 2
    ],
    [
        'question' => 'Which is the saltiest sea in the world?',
        'options' => ['Dead Sea', 'Mediterranean Sea', 'Red Sea', 'Caspian Sea'],
        'correct' => 0
    ],
    [
        'question' => 'What is the chemical symbol for copper?',
        'options' => ['Co', 'Cr', 'Cu', 'Ca'],
        'correct' => 2
    ],
    [
        'question' => 'Who wrote the play "Macbeth"?',
        'options' => ['Christopher Marlowe', 'Ben Jonson', 'William Shakespeare', 'John Milton'],
        'correct' => 2
    ],
    [
        'question' => 'What is the unit of electrical resistance?',
        'options' => ['Volt', 'Ampere', 'Ohm', 'Watt'],
        'correct' => 2
    ],
    [
        'question' => 'Which continent is known as the "Dark Continent"?',
        'options' => ['Asia', 'Africa', 'South America', 'Antarctica'],
        'correct' => 1
    ]
];

// Randomly select 10 questions
function getRandomQuestions($questions, $count) {
    $indexes = range(0, count($questions) - 1);
    shuffle($indexes);
    $selected = array_slice($indexes, 0, $count);
    $result = [];
    foreach ($selected as $index) {
        $result[] = $questions[$index];
    }
    return $result;
}

// Get 10 random questions for this session
if (!isset($_SESSION['quiz_questions']) || isset($_GET['new_quiz'])) {
    $questions = getRandomQuestions($all_questions, 10);
    $_SESSION['quiz_questions'] = $questions;
} else {
    $questions = $_SESSION['quiz_questions'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - QuizWhiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <?php require_once "includes/layout.php"; ?>
    <?php renderHeader("Quiz"); ?>

    <div class="quiz-container max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6 my-8">
        <div class="mb-8">
            <h1 class="text-lg sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-2">Challenge Your Knowledge with QuizWhiz</h1>
            <p class="text-gray-600">Test your knowledge with these questions!</p>
        </div>

        <form method="post" action="submit_quiz.php" class="space-y-8">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-container bg-gray-50 rounded-lg p-4 sm:p-6">
                    <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-4">
                        Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($question['question']); ?>
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($question['options'] as $optionIndex => $option): ?>
                            <label class="quiz-option flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-primary-500 cursor-pointer transition-colors">
                                <input type="radio" name="question_<?php echo $index; ?>" value="<?php echo $optionIndex; ?>" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <span class="ml-3 text-gray-700"><?php echo htmlspecialchars($option); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="flex justify-end pt-6">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Submit Answers
                </button>
            </div>
        </form>
    </div>

    <?php renderFooter(); ?>

    <script>
        // Add touch feedback for mobile
        const quizOptions = document.querySelectorAll('.quiz-option');
        quizOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active state from all options in the same question
                const questionContainer = this.closest('.question-container');
                questionContainer.querySelectorAll('.quiz-option').forEach(opt => {
                    opt.classList.remove('border-primary-500', 'bg-primary-50');
                });
                // Add active state to clicked option
                this.classList.add('border-primary-500', 'bg-primary-50');
            });
        });
    </script>
</body>

</html>
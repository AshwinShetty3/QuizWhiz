<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Get the questions from the session
if (!isset($_SESSION['quiz_questions'])) {
    header("location: index.php");
    exit;
}

$questions = $_SESSION['quiz_questions'];
$score = 0;
$answers = [];

// Calculate score
foreach ($questions as $index => $question) {
    $selected_answer = isset($_POST["question_$index"]) ? (int)$_POST["question_$index"] : -1;
    $correct_index = $question['correct'];
    
    if ($selected_answer === $correct_index) {
        $score++;
    }
    
    $answers[$index] = [
        'question' => $question['question'],
        'selected' => $selected_answer >= 0 ? $question['options'][$selected_answer] : 'No answer',
        'correct' => $question['options'][$correct_index],
        'is_correct' => $selected_answer === $correct_index
    ];
}

// Store result in database
require_once "includes/config.php";

$sql = "INSERT INTO quiz_results (user_id, score) VALUES (?, ?)";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION["id"], $score);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['quiz_submitted'] = true;
        $_SESSION['last_score'] = $score;
    }
    mysqli_stmt_close($stmt);
}

require_once "includes/layout.php";
renderHeader("Quiz Results");
?>

<main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <h1 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-8">Quiz Results</h1>
            
            <div class="mb-8">
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <h2 class="text-3xl font-bold text-gray-900">Your Score: <?php echo $score; ?>/<?php echo count($questions); ?></h2>
                    <p class="mt-2 text-gray-600">
                        <?php
                        $percentage = ($score / count($questions)) * 100;
                        if ($percentage >= 90) echo "Excellent!";
                        elseif ($percentage >= 70) echo "Good job!";
                        elseif ($percentage >= 50) echo "Not bad!";
                        else echo "Keep practicing!";
                        ?>
                    </p>
                </div>
            </div>

            <div class="space-y-6">
                <?php foreach ($answers as $index => $answer): ?>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Question <?php echo $index + 1; ?></h3>
                        <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($answer['question']); ?></p>
                        
                        <div class="space-y-2">
                            <p class="text-gray-600">
                                Your answer: 
                                <span class="<?php echo $answer['is_correct'] ? 'text-green-600' : 'text-red-600'; ?> font-medium">
                                    <?php echo htmlspecialchars($answer['selected']); ?>
                                </span>
                            </p>
                            <?php if (!$answer['is_correct']): ?>
                                <p class="text-gray-600">
                                    Correct answer: 
                                    <span class="text-green-600 font-medium">
                                        <?php echo htmlspecialchars($answer['correct']); ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 flex justify-between">
                <a href="index.php?new_quiz=1" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Take Another Quiz
                </a>
                <a href="scores.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    View Score History
                </a>
            </div>
        </div>
    </div>
</main>

<?php renderFooter(); ?>
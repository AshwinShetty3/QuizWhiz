<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "includes/config.php";

// Fetch the latest 15 quiz results for the current user
$sql = "SELECT score, completed_at FROM quiz_results WHERE user_id = ? ORDER BY completed_at DESC LIMIT 15";
$results = [];

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }
    
    mysqli_stmt_close($stmt);
}

// Get all scores for statistics
$all_scores_sql = "SELECT score FROM quiz_results WHERE user_id = ?";
$all_scores = [];

if ($stmt = mysqli_prepare($link, $all_scores_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $all_scores[] = $row['score'];
    }
    
    mysqli_stmt_close($stmt);
}

// Calculate statistics
$total_quizzes = count($all_scores);
$average_score = $total_quizzes > 0 ? array_sum($all_scores) / $total_quizzes : 0;
$highest_score = $total_quizzes > 0 ? max($all_scores) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Score History - QuizWhiz</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50">
    <?php 
    require_once "includes/layout.php";
    renderHeader("Scores");
    ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8">Your Quiz Performance</h1>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-900">Total Quizzes</h3>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $total_quizzes; ?></p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-900">Average Score</h3>
                        <p class="text-3xl font-bold text-green-600"><?php echo number_format($average_score, 1); ?>/10</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-purple-900">Highest Score</h3>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $highest_score; ?>/10</p>
                    </div>
                </div>

                <!-- Performance Chart -->
                <div class="mb-8">
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>

                <!-- Score History Table -->
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Score History (Latest 15)</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($results as $quiz): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M d, Y H:i', strtotime($quiz['completed_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo $quiz['score']; ?>/10
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $performance = '';
                                            $color_class = '';
                                            if ($quiz['score'] == 10) {
                                                $performance = 'Excellent';
                                                $color_class = 'text-green-600';
                                            } elseif ($quiz['score'] >= 8) {
                                                $performance = 'Very Good';
                                                $color_class = 'text-blue-600';
                                            } elseif ($quiz['score'] >= 6) {
                                                $performance = 'Good';
                                                $color_class = 'text-yellow-600';
                                            } else {
                                                $performance = 'Needs Improvement';
                                                $color_class = 'text-red-600';
                                            }
                                            ?>
                                            <span class="text-sm font-medium <?php echo $color_class; ?>">
                                                <?php echo $performance; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php renderFooter(); ?>

    <script>
        // Initialize performance chart
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const scores = <?php echo json_encode(array_column(array_reverse($results), 'score')); ?>;
        const dates = <?php echo json_encode(array_map(function($result) {
            return date('M d', strtotime($result['completed_at']));
        }, array_reverse($results))); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Quiz Scores',
                    data: scores,
                    borderColor: 'rgb(79, 70, 229)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html> 
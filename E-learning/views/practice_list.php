<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['new']) && $_GET['new'] === '1') {
    unset($_SESSION['practice']);
}

if (!isset($_SESSION['practice']['submit_token'])) {
    $_SESSION['practice']['submit_token'] = bin2hex(random_bytes(16));
}

$submitToken = $_SESSION['practice']['submit_token'];

$list_id = $_GET['list_id'];
$language = $_GET['language'] ?? null;

if ($language == null || !in_array($language, ["dutch", "english"])) {
    header('Location: home.php');
    exit();
}

if (isset($_GET['restart']) && $_GET['restart'] === '1') {
    setIndextoZero();
    header('Location: practice_list.php?list_id=' . urlencode($list_id) . '&language=' . urlencode((string)$language));
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

require_once '../classes/practice_list_action.php';
$words = $words ?? [];

if (!isset($_SESSION['practice']) || ($_SESSION['practice']['list_id'] ?? null) !== $list_id || ($_SESSION['practice']['language'] ?? null) !== $language) {
    shuffle($words);

    $_SESSION['practice'] = [
        'list_id' => $list_id,
        'language' => $language,
        'words' => $words,
        'given' => [],
        'index' => 0,
        'score' => 0,
        'last_feedback' => null,
        'submit_token' => $submitToken
    ];
}
function normalizeAnswer(string $value): string
{
    $value = trim(mb_strtolower($value, 'UTF-8'));
    $value = preg_replace('/\s+/', ' ', $value);
    return $value;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $quiz = &$_SESSION['practice'];
    $postedIndex = isset($_POST['question_index']) ? (int)$_POST['question_index'] : -1;
    $postedToken = $_POST['submit_token'] ?? '';
    $sessionToken = $quiz['submit_token'] ?? '';
    $index = $quiz['index'];
    $words = $quiz['words'];

    if ($postedIndex !== (int)$quiz['index'] || !hash_equals($sessionToken, $postedToken)) {
        header('Location: practice_list.php?list_id=' . urlencode($list_id) . '&language=' . urlencode((string)$language));
        exit();
    }

    if (isset($words[$index])) {
        $displayLang = $quiz['language'];
        $answerLang = $displayLang === 'dutch' ? 'english' : 'dutch';

        $expected = normalizeAnswer($words[$index][$answerLang] ?? '');
        $given = normalizeAnswer($_POST['answer']);
        $givenunnormalized = $_POST['answer'];

        $isCorrect = $given === $expected;

        if ($given == '' && $expected != '') {
            $_SESSION['error'] = 'Enter an answer before submitting.';
            $quiz['last_feedback'] = null;
            header('Location: practice_list.php?list_id=' . urlencode($list_id) . '&language=' . urlencode((string)$language));
            exit();
        }

        if ($isCorrect) {
            $quiz['score']++;
            $quiz['index']++;
            $quiz['given'][$index] = $givenunnormalized;
        } else {
            $quiz['index']++;
            $quiz['given'][$index] = $givenunnormalized;
        }

        $quiz['last_feedback'] = [
            'correct' => $isCorrect,
            'expected' => $words[$index][$answerLang] ?? ''
        ];

    }
    $quiz['submit_token'] = bin2hex(random_bytes(16));
    header('Location: practice_list.php?list_id=' . urlencode($list_id) . '&language=' . urlencode((string)$language));
    exit();
}
function setIndextoZero()
{
    if (isset($_SESSION['practice'])) {
        $_SESSION['practice']['index'] = 0;
        $_SESSION['practice']['score'] = 0;
        $_SESSION['practice']['last_feedback'] = null;
    }
}

$quiz = $_SESSION['practice'];
$total = count($quiz['words']);
$currentIndex = $quiz['index'];
$isFinished = $currentIndex >= $total;

$_SESSION['error'] ?? null;
$_SESSION['success'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="../../tailwind/tailwind.css" rel="stylesheet">
    <title>Practice list</title>
</head>

<body class="font-poppins bg-blue-500">
    <?php include 'layout.php'; ?>
    <?php include '../classes/database.php'; ?>
    <div class="w-full min-h-[calc(100dvh-4rem)] flex flex-row justify-center items-center">
        <div class="flex flex-col justify-center items-center w-full h-full rounded-lg text-white">
            <div class="flex flex-col justify-center items-center w-full h-full">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mt-6 bg-red-500 rounded-lg text-center px-6 py-3 text-white"><?php echo $_SESSION['error'] ?? '';
                                                                                                unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mt-6 bg-green-500 rounded-lg text-center px-6 py-3 text-white"><?php echo $_SESSION['success'] ?? '';
                                                                                                unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <form method="POST" action="practice_list.php?list_id=<?php echo htmlspecialchars($list_id); ?>&language=<?php echo htmlspecialchars($language); ?>" class="flex flex-col justify-center items-center w-full">
                    <input type="hidden" name="list_id" value="<?php echo htmlspecialchars($list_id); ?>">
                    <input type="hidden" name="language" value="<?php echo htmlspecialchars($language); ?>">
                    <input type="hidden" name="question_index" value="<?php echo (int)$currentIndex; ?>">
                    <input type="hidden" name="submit_token" value="<?php echo htmlspecialchars($submitToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if ($isFinished): ?>
                        <div class="text-center bg-white max-w-3xl w-full text-black py-8 px-12 rounded-lg">
                            <div class="text-2xl font-semibold"><?php echo "Your score: {$quiz['score']} / {$total}"; ?></div>
                            <div class="w-full text-sm font-medium flex flex-col justify-between items-center ">
                                <?php foreach ($quiz['words'] as $index => $word): ?>
                                    <div class="w-full flex flex-row justify-between items-center mt-4">
                                        <div class="w-48 flex flex-col justify-center items-center">
                                            <div class="text-sm">Correct answer:</div>
                                            <div class="text-green-500"><?php echo htmlspecialchars($word[$quiz['language']  === 'english' ? 'dutch' : 'english']); ?></div>
                                        </div>
                                        <span class="text-2xl font-semibold text-center w-auto flex flex-row items-center justify-center"><i class='bx bx-right-arrow-alt'></i></span>
                                        <div class="w-48 flex flex-col justify-center items-center">
                                            <div class="mx-4">Your answer:</div>
                                            <div class="<?php echo normalizeAnswer($quiz['given'][$index]) === normalizeAnswer($word[$quiz['language'] === 'dutch' ? 'english' : 'dutch']) ? 'text-green-400' : 'text-red-500'; ?>">
                                                <?php echo htmlspecialchars($quiz['given'][$index] ?? ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex flex-row justify-around items-center mt-4">
                                <a href="home.php" class="mt-4 mr-8 bg-pink-500 text-white py-2 px-4 rounded-md hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Back to Home</a>
                                <button type="button" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="window.location.href='practice_list.php?list_id=<?php echo htmlspecialchars($list_id); ?>&language=<?php echo htmlspecialchars($language); ?>&restart=1'">Practice Again</button>
                            </div>
                        </div>
            </div>
        <?php else: ?>
            <?php if (isset($quiz['last_feedback'])): ?>
                <div class="mt-4 text-black font-semibold text-lg bg-white py-2 px-8 rounded-lg text-center w-auto">
                    <?php if ($quiz['last_feedback']['correct']): ?>
                        <span class="text-green-400">Correct!</span>
                    <?php else: ?>
                        <span class="text-red-500">Last answer was incorrect.</span>
                        <div class="flex flex-row justify-center items-center">
                            Correct answer was: <span class="ml-2 text-green-400"><?php echo htmlspecialchars($quiz['last_feedback']['expected']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="h-auto my-8">
                Question <?php echo ($currentIndex + 1); ?> of <?php echo $total; ?>
                <div class="w-76 bg-white rounded-full h-6 mt-4">
                    <div class="bg-green-600 h-6 rounded-full text-center" style="width: <?php echo ($currentIndex / $total) * 100; ?>%;"><?php echo round(($currentIndex / $total) * 100); ?>%</div>
                </div>
            </div>
            <div class="flex flex-col justify-around items-center w-full">
                <div class="w-64 text-5xl mb-12 font-semibold text-center"><?php echo htmlspecialchars($quiz['words'][$currentIndex][$language]); ?></div>
                <input type="text" name="answer" placeholder="Enter your answer..." class="w-64 bg-white mb-8 text-black text-center border border-gray-300 rounded-md py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="mt-4 mb-6 bg-pink-500 text-white py-2 px-4 rounded-md hover:bg-pink-600 focus:outline-none">Submit</button>
        <?php endif; ?>
        </form>
        </div>
    </div>
</body>

</html>
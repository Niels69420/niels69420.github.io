<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$_SESSION['error'] = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$list_id = $_GET['id'] ?? null;
if ($list_id) {
    require_once '../classes/database.php';
    $stmt = $conn->prepare("SELECT name, public FROM WordLists_E_learning WHERE id = ? AND userId = ?");
    $stmt->execute([$list_id, $user_id]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$list) {
        $_SESSION['error'] = 'You cannot edit this list as it does not belong to you';
        header('Location: home.php');
        exit();
    }
    $dutch_words = [];
    $english_words = [];
    $stmt = $conn->prepare("SELECT dutch, english FROM Words WHERE wordListId = ?");
    $stmt->execute([$list_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dutch_words[] = $row['dutch'];
        $english_words[] = $row['english'];
    }
} else {
    $_SESSION['error'] = 'No list ID provided';
    header('Location: home.php');
    exit();
}
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
    <title>Edit list</title>
</head>

<body class="font-poppins bg-blue-500">
    <?php include 'layout.php'; ?>
    <?php include '../classes/database.php'; ?>
    <form action="../classes/edit_list_action.php?id=<?php echo $list_id; ?>" method="POST">
        <div class="flex flex-col justify-center items-center w-full">
            <div class="text-red-500"><?php echo $_SESSION['error'] ?? ''; ?></div>
            <input type="text" name="list_name" value="<?php echo htmlspecialchars($list['name']) ?>" class="w-[1024px] mt-6 bg-white rounded-lg shadow px-6 py-4 font-semibold border-b border-gray-200 text-[18px] focus:outline-none">
            <div class="w-[1024px] mt-6 bg-white rounded-lg shadow">
                <div class="text-[15px] px-6 py-4 border-b border-gray-200 bg-gray-100 rounded-t-lg flex justify-between items-center">
                    <h1 class="font-semibold text-gray-900">Dutch</h1>
                    <div class="font-semibold text-gray-900">English</div>
                </div>
                <div id="WordsContainer">
                    <?php for ($i = 0; $i < count($dutch_words); $i++): ?>
                        <div class="flex flex-row items-center justify-center border-b border-gray-300">
                            <button type="button" class="DeleteWord hover:cursor-pointer pl-4 flex flex-row items-center justify-center"><i class='text-lg text-red-500 bx bx-x-circle'></i></button>
                            <div class="w-full overflow-x-auto p-6 flex justify-between items-center">
                                <input class="text-black focus:outline-none" placeholder="Type dutch word here" type="text" name="dutch-word[]" value="<?php echo htmlspecialchars($dutch_words[$i]); ?>">
                                <i class='text-2xl bx bx-right-arrow-alt'></i>
                                <input class="text-right text-black focus:outline-none" placeholder="Type english word here" type="text" name="english-word[]" value="<?php echo htmlspecialchars($english_words[$i]); ?>">
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="text-[16px] px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-b-lg flex flex-col justify-center items-center">
                    <button type="button" id="AddWord" class="text-gray-500 bg-white border-2 border-dashed border-gray-400 rounded-2xl flex flex-row justify-center items-center py-3 w-full hover:cursor-pointer">
                        <i class='text-2xl bx bx-plus'></i>
                        Add another word
                    </button>
                    <div class="flex flex-row justify-between items-center w-48 mt-4">
                        <?php if ($list['public']): ?>
                            <div class="flex flex-row items-center justify-center">
                                <input type="radio" checked name="list-type" value="public" class="mr-2 hover:cursor-pointer"></input>
                                <p class="">Public</p>
                            </div>
                            <div class="flex flex-row items-center justify-center">
                                <input type="radio" name="list-type" value="private" class="mr-2 hover:cursor-pointer"></input>
                                <p class="">Private</p>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-row items-center justify-center">
                                <input type="radio" name="list-type" value="public" class="mr-2 hover:cursor-pointer"></input>
                                <p class="">Public</p>
                            </div>
                            <div class="flex flex-row items-center justify-center">
                                <input type="radio" checked name="list-type" value="private" class="mr-2"></input>
                                <p class="">Private</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="w-full max-w-5xl flex flex-row justify-between items-center mb-8">
                <a href="home.php" class="bg-white px-4 py-2 text-gray-700 rounded-lg mt-6 hover:cursor-pointer">Cancel</a>
                <button type="submit" class="bg-pink-400 px-4 py-2 text-white rounded-lg mt-6 hover:cursor-pointer">Save changes</button>
            </div>
        </div>
    </form>
    <script>
        document.getElementById('AddWord').addEventListener('click', function() {
            const wordsContainer = document.getElementById('WordsContainer');
            const outerContainer = document.createElement('div');
            outerContainer.className = 'flex flex-row items-center justify-center border-b border-gray-300';
            const container = document.createElement('div');
            container.className = 'w-full overflow-x-auto p-6 flex justify-between items-center';
            
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'DeleteWord hover:cursor-pointer pl-4 flex flex-row items-center justify-center';
            deleteButton.innerHTML = "<i class='text-lg text-red-500 bx bx-x-circle'></i>";
            outerContainer.appendChild(deleteButton);
            outerContainer.appendChild(container);

            const dutchInput = document.createElement('input');
            dutchInput.type = 'text';
            dutchInput.name = 'dutch-word[]';
            dutchInput.placeholder = 'Type dutch word here';
            dutchInput.className = 'text-black focus:outline-none';

            const arrowIcon = document.createElement('i');
            arrowIcon.className = 'text-2xl bx bx-right-arrow-alt';

            const englishInput = document.createElement('input');
            englishInput.type = 'text';
            englishInput.name = 'english-word[]';
            englishInput.placeholder = 'Type english word here';
            englishInput.className = 'text-black focus:outline-none text-right';

            container.appendChild(dutchInput);
            container.appendChild(arrowIcon);
            container.appendChild(englishInput);

            wordsContainer.appendChild(outerContainer);
        });
        document.getElementById('WordsContainer').addEventListener('click', function(e) {
            const deleteButton = e.target.closest('.DeleteWord');
            if (deleteButton) {
                deleteButton.closest('.flex.flex-row.items-center.justify-center.border-b.border-gray-300').remove();
            }
        });
    </script>
</body>

</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$_SESSION['error'] = $_SESSION['error'] ?? null;
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
    <title>Create new list</title>
</head>

<body class="font-poppins bg-blue-500">
    <?php include 'layout.php'; ?>
    <?php include '../classes/database.php'; ?>
    <form action="../classes/create_list_action.php" method="POST">
        <div class="flex flex-col justify-center items-center w-full">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mt-6 bg-red-500 rounded-lg text-center px-6 py-3 text-white"><?php echo $_SESSION['error'] ?? '';
                                                                                            unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <input type="text" name="list_name" placeholder="Type list name here" class="w-[1024px] mt-6 bg-white rounded-lg shadow px-6 py-4 font-semibold border-b border-gray-200 text-[18px] focus:outline-none">
            <div class="w-[1024px] mt-6 bg-white rounded-lg shadow">
                <div class="text-[15px] px-6 py-4 border-b border-gray-200 bg-gray-100 rounded-t-lg flex justify-between items-center">
                    <h1 class="font-semibold text-gray-900">Dutch</h1>
                    <div class="font-semibold text-gray-900">English</div>
                </div>
                <div id="WordsContainer">
                    <div class="flex flex-row items-center justify-center border-b border-gray-300">
                        <button type="button" class="DeleteWord hover:cursor-pointer pl-4 flex flex-row items-center justify-center"><i class='text-lg text-red-500 bx bx-x-circle'></i></button>
                        <div class="overflow-x-auto w-full p-6 flex justify-between items-center">
                            <input class="text-black focus:outline-none" placeholder="Type dutch word here" type="text" name="dutch-word[]">
                            <i class='text-2xl bx bx-right-arrow-alt'></i>
                            <input class="text-right text-black focus:outline-none" placeholder="Type english word here" type="text" name="english-word[]">
                        </div>
                    </div>
                </div>
                <div class="text-[16px] px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-b-lg flex flex-col justify-center items-center">
                    <button type="button" id="AddWord" class="text-gray-500 bg-white border-2 border-dashed border-gray-400 rounded-2xl flex flex-row justify-center items-center py-3 w-full hover:cursor-pointer">
                        <i class='text-2xl bx bx-plus'></i>
                        Add another word
                    </button>
                    <div class="flex flex-row justify-between items-center w-48 mt-4">
                        <div class="flex flex-row items-center justify-center">
                            <input type="radio" name="list-type" value="public" class="mr-2 hover:cursor-pointer"></input>
                            <p class="">Public</p>
                        </div>
                        <div class="flex flex-row items-center justify-center">
                            <input type="radio" checked name="list-type" value="private" class="mr-2 hover:cursor-pointer"></input>
                            <p class="">Private</p>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="bg-pink-400 px-4 py-2 text-white rounded-lg mt-6 hover:cursor-pointer">Create list</button>
        </div>
    </form>
    <script>
        document.getElementById('AddWord').addEventListener('click', function() {
            const wordsContainer = document.getElementById('WordsContainer');
            const outerContainer = document.createElement('div');
            outerContainer.className = 'flex flex-row items-center justify-center border-b border-gray-300';
            const container = document.createElement('div');
            container.className = 'overflow-x-auto p-6 flex justify-between items-center w-full';

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
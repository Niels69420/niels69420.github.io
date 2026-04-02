<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$_SESSION['error'] ?? null;
$_SESSION['success'] ?? null;
include '../classes/database.php';
    $publicLists = [];
    $privateLists = [];
    $creators = [];
    $loadError = null;

    try {
        require_once '../classes/wordlists.php';
        $privateLists = getWordListsByUserId();
        $publicLists = getPublicWordLists();
        $creators = getAllCreators();
    } catch (PDOException $e) {
        $loadError = 'No lists could be found.';
        error_log('Database error in views/home.php: ' . $e->getMessage());
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
    <title>Homepage</title>
</head>

<body class="font-poppins bg-blue-500">
    <?php include 'layout.php'; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="closeSessionMsg mt-6 flex flex-row items-center justify-center w-full">
            <div class="bg-red-500 rounded-lg text-center px-6 py-3 flex flex-row items-center">
                <div class="text-white"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
                <a onclick="closeSessionMessage()" class="hover:cursor-pointer flex items-center justify-center ml-2 text-white"><i class='font-semibold text-2xl bx bx-x'></i></a>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="closeSessionMsg mt-6 flex flex-row items-center justify-center w-full">
            <div class="bg-green-500 rounded-lg text-center px-6 py-3 flex flex-row items-center">
                <div class="text-white"><?php echo htmlspecialchars($_SESSION['success']); ?></div>
                <?php unset($_SESSION['success']); ?>
                <a onclick="closeSessionMessage()" class="hover:cursor-pointer flex items-center justify-center ml-2 text-white"><i class='font-semibold text-2xl bx bx-x'></i></a>
            </div>
        </div>
    <?php endif; ?>
    <div class="max-w-7xl mx-auto mt-6 mb-12 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Vocabulary Lists</h1>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">List Name</th>
                        <th class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Words</th>
                        <th class="text-center px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                        <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($loadError !== null): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 w-full text-gray-500"><?php echo htmlspecialchars($loadError); ?></td>
                        </tr>
                    <?php else: ?>
                        <?php if (count($publicLists) === 0 && count($privateLists) === 0): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 w-full text-gray-500">No lists could be found.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($publicLists as $list): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="w-1/5 px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-medium"><?php echo htmlspecialchars($list['name']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="ml-2 bg-gray-100 rounded-xl px-3 py-1 inline-block"><?php echo (int) $list['word_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="bg-gray-100 rounded-xl px-3 py-1 inline-block"><?php echo htmlspecialchars($creators[$list['userId']] ?? 'Unknown Creator'); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    <span class="bg-gray-100 rounded-xl px-3 py-1 inline-block">Public</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2 justify-end">
                                    <?php if ((int) $list['userId'] === (int) $user_id): ?>
                                        <a href="edit_list.php?id=<?php echo (int) $list['id']; ?>" class="px-4 py-2 text-sm font-medium text-blue-600 border border-blue-300 bg-white rounded-md hover:bg-blue-50 hover:cursor-pointer">Edit list</a>
                                        <a onclick="deleteList(<?php echo (int) $list['id']; ?>)" class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50 hover:cursor-pointer">Delete</a>
                                    <?php endif; ?>
                                    <a href="practice_list_language.php?id=<?php echo (int) $list['id']; ?>" class="px-4 py-2 text-sm font-medium text-green-600 bg-white border border-green-300 rounded-md hover:bg-green-50 hover:cursor-pointer">Practice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php foreach ($privateLists as $list): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="w-1/5 px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-medium"><?php echo htmlspecialchars($list['name']); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="ml-2 bg-gray-100 rounded-xl px-3 py-1 inline-block"><?php echo (int) $list['word_count']; ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="bg-gray-100 rounded-xl px-3 py-1 inline-block"><?php echo htmlspecialchars($creators[$list['userId']] ?? 'Unknown Creator'); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    <span class="bg-gray-100 rounded-xl px-3 py-1 inline-block">Private</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2 justify-end">
                                    <a href="edit_list.php?id=<?php echo (int) $list['id']; ?>" class="px-4 py-2 text-sm font-medium text-blue-600 border border-blue-300 bg-white rounded-md hover:bg-blue-50 hover:cursor-pointer">Edit list</a>
                                    <a onclick="deleteList(<?php echo (int) $list['id']; ?>)" class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-300 rounded-md hover:bg-red-50 hover:cursor-pointer">Delete</a>
                                    <a href="practice_list_language.php?id=<?php echo (int) $list['id']; ?>" class="px-4 py-2 text-sm font-medium text-green-600 bg-white border border-green-300 rounded-md hover:bg-green-50 hover:cursor-pointer">Practice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="rounded-lg px-6 py-3 border-t bg-gray-100 border-gray-200 flex justify-between items-center">
        </div>
    </div>
</body>
<script>
    closeSessionMessage = () => {
        const msgBox = document.querySelector('.closeSessionMsg');
        msgBox.classList.add('hidden');
    };

    function deleteList(listId) {
        const confirmationPanel = document.createElement('div');
        confirmationPanel.className = 'fixed inset-0 bg-black/60 flex items-center justify-center z-50';
        confirmationPanel.innerHTML = `
            <div class="bg-white rounded-lg p-6 text-center">
                <p class="text-lg font-medium mb-4">Are you sure you want to delete this list?</p>
                <div class="flex justify-center gap-4">
                    <a href="../classes/delete_list.php?id=${listId}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Yes, delete</a>
                    <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                </div>
            </div>
        `;
        document.body.appendChild(confirmationPanel);

        function closePanel() {
            document.body.removeChild(confirmationPanel);
        }
        confirmationPanel.addEventListener('click', (e) => {
            if (e.target === confirmationPanel) {
                closePanel();
            }
        });
        confirmationPanel.querySelector('button').addEventListener('click', closePanel);
    }
</script>

</html>
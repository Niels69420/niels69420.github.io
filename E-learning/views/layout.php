<nav class="sticky top-0 bg-white p-4 h-16">
    <ul class="text-xl flex flex-row items-center justify-between">
        <div class="text-xl bg-gray-100 py-1 px-3 rounded-lg font-semibold">
            <?php echo htmlspecialchars($username); ?>
        </div>
        <div class="text-[15px] flex flex-row items-center justify-between w-auto font-medium text-white">
            <li><a class="bg-pink-400 rounded-lg mr-2 px-3 py-2" href="../views/home.php">Home</a></li>
            <li><a class="bg-pink-400 rounded-lg mx-2 px-3 py-2" href="../views/create_list.php">Create new list</a></li>
            <li><a class="bg-pink-400 rounded-lg ml-2 px-3 py-2" href="../views/logout.php">Logout</a></li>
        </div>
    </ul>
</nav>
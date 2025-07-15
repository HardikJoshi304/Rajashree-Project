<?php 
  session_start();
  require '../conection/conn.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rajashree Enterprise</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="bg-gray-100" x-data="{ open: false }">

  <!-- ✅ Top Navbar -->
  <nav class="bg-black/90 text-white flex justify-between items-center p-2 pb-8 relative">
    <!-- Logo -->
    <a href="index.php">
      <img src="../images/logo.png" class="w-36 h-20 object-contain" alt="Logo" />
    </a>

    <!-- Desktop Menu -->
    <ul class="hidden md:flex items-center gap-6 text-lg mr-5">
      <li><a href="index.php" class="hover:text-blue-400">Home</a></li>
      <li><a href="e_about.php" class="hover:text-blue-400">About</a></li>
      <li><a href="e_product.php" class="hover:text-blue-400">Products</a></li>
      <li><a href="#" class="hover:text-blue-400">Cart</a></li>
      <li><a href="e_contact.php" class="hover:text-blue-400">Contact</a></li>
      <li><a href="../login/e_login_signup.php" class="hover:text-blue-400">➔ / 👤</a></li>
    </ul>

    <!-- Mobile Hamburger -->
    <button class="md:hidden mr-3" @click="open = !open">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <!-- ✅ Responsive User Info (One Copy Only) -->
    <?php if (isset($_SESSION['user_name'])): ?>
      <div class="fixed top-16 right-2 sm:fixed sm:top-24 sm:right-4 sm:-translate-y-1/2 sm:transform flex items-center space-x-2 sm:space-x-3 bg-white/10 px-2 py-1.5 rounded-full shadow-lg backdrop-blur-sm border border-white/20 z-50">
        <span class="text-white text-xs sm:text-sm leading-tight whitespace-nowrap">
          👋 <span class="font-semibold text-blue-300"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
        </span>
        <a href="../login/e_logout.php" class="text-xs sm:text-sm bg-red-600 hover:bg-red-700 text-white px-2 sm:px-3 py-0.5 rounded-full transition duration-200 shadow-md">
          Logout
        </a> 
      </div>
    <?php endif; ?><?php
if (isset($_SESSION['user_id'])):
    // Include DB connection

    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($userName);
    $stmt->fetch();
    $stmt->close();
?>
  <div class="fixed top-16 right-2 sm:fixed sm:top-24 sm:right-4 sm:-translate-y-1/2 sm:transform flex items-center space-x-2 sm:space-x-3 bg-white/10 px-2 py-1.5 rounded-full border border-white/20 z-50">
    <span class="text-white text-xs sm:text-sm leading-tight whitespace-nowrap">
      👋 <span class="font-semibold text-blue-300"><?= htmlspecialchars($userName) ?></span>
    </span>
    <a href="../login/e_logout.php" class="text-xs sm:text-sm bg-red-600 hover:bg-red-700 text-white px-2 sm:px-3 py-0.5 rounded-full transition duration-200 shadow-md">
      Logout
    </a>
  </div>
<?php endif; ?>

  </nav>

  <!-- 📱 Sidebar Menu (Mobile Only) -->
  <div class="fixed top-0 right-0 w-64 max-w-full h-full bg-black text-white transform transition-transform duration-300 ease-in-out z-40"
       x-show="open"
       x-transition:enter="translate-x-full"
       x-transition:enter-start="translate-x-0"
       x-transition:leave="translate-x-0"
       x-transition:leave-end="translate-x-full"
       @click.away="open = false"
       x-cloak>

    <div class="p-6 flex flex-col gap-5 text-base sm:text-lg">
      <!-- Close Button -->
      <button class="self-end text-xl" @click="open = false">✕</button>

      <!-- Sidebar Links -->
      <a href="index.php" @click="open = false" class="hover:text-blue-400">Home</a>
      <a href="e_about.php" @click="open = false" class="hover:text-blue-400">About</a>
      <a href="e_product.php" @click="open = false" class="hover:text-blue-400">Products</a>
      <a href="#" @click="open = false" class="hover:text-blue-400">Cart</a>
      <a href="e_contact.php" @click="open = false" class="hover:text-blue-400">Contact</a>
      <a href="../login/e_login_signup.php" @click="open = false" class="hover:text-blue-400">➔ / 👤</a>
    </div>
  </div>

</body>
</html>

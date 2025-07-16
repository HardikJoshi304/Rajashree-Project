<?php
require '../connection/conn.php'; 
require 'encdec.php';

$toastMessage = $_SESSION['toast_message'] ?? null;
unset($_SESSION['toast_message']);

$search = trim($_GET['search'] ?? '');
$category_id_encrypt = htmlspecialchars($_GET['id'] ?? '');
$output = '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rajashree Furniture</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <style>
    .swiper-slide { transition: all 0.3s ease-in-out; }
  </style>
</head>
<body class="bg-white text-black">

<?php require 'f_nav.php'; ?>

<!-- Navigation Buttons -->
 <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<div class="flex justify-center gap-2 p-2 py-3 bg-black/90">
  <a href="index.php" class="<?= $currentPage === 'index.php' ? 'bg-blue-500 text-white' : 'bg-black text-white' ?> px-4 py-1.5 border border-white">Rajashree Enterprise</a>
  <a href="furniture.php" class="<?= $currentPage === 'furniture.php' ? 'bg-blue-500 text-white' : 'bg-black text-white' ?> px-4 py-1.5 border border-white">Rajashree Furniture</a>
</div>

<!-- Hero -->
<section class="text-center">
  <img src="../images/furniture.png" class="w-full h-80" alt="Poster">
</section>

<section class="bg-black py-8">
  <div class="max-w-7xl mx-auto px-4">

    <!-- Search Bar -->
     <div class="shadow p-4 sticky z-10 mx-10">
      <form method="GET" class="flex flex-col md:flex-row items-center gap-3">
        <input type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Search for a category..." class="w-full md:w-3/4 border border-blue-300 bg-blue-50 px-5 py-2 rounded-full shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500" />
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">Search</button>
      </form>
    </div>

    <!-- Category Heading -->
   <h2 class="inline-flex items-center gap-2 bg-white text-black px-4 py-1 text-xl font-semibold rounded-tr-md rounded-br-md absolute left-0 top-90 ml-0">
        <span class="text-2xl">»</span> Category
    </h2> 



    <!-- Swiper Carousel -->
    <div class="relative mx-4 sm:mx-14 mt-20 mb-20">
  <!-- Swiper container -->
       <div class="swiper-button-prev inline-flex items-center text-white py-1 mx-9 text-xl font-semibold rounded-tr-md rounded-br-md absolute -left-6 top-90 ml-0"></div>

  <div class="swiper category-swiper">
    <div class="swiper-wrapper mx-7">
              <?php
              if (!empty($search)) {
            $stmt = $conn->prepare("SELECT * FROM f_category WHERE name LIKE ?");
            $like = '%' . $search . '%';
            $stmt->bind_param("s", $like);
        } else {
            $stmt = $conn->prepare("SELECT * FROM f_category");
        }

        $stmt->execute();
        $result = $stmt->get_result();

      while ($row = $result->fetch_assoc()) {
        echo '<div class="swiper-slide justify-center">
                <a href="e_category.php?id=' . encrypt_id($row['id']) . '" 
                  class="bg-white rounded-lg shadow-lg p-3 block hover:shadow-xl transition w-[240px]">
                  <img src="' . $row['image'] . '" alt="' . $row['name'] . '" 
                    class="w-full h-40 object-cover rounded">
                  <p class="mt-2 text-center text-black font-medium">' . $row['name'] . '</p>
                </a>
              </div>';
      }
      ?>
    </div>
  </div>
          <div class="swiper-button-next inline-flex items-center text-white -mx-5 sm:-mx-5 py-1 text-xl font-semibold rounded-tr-md rounded-br-md absolute right-0 top-90 ml-0"></div>

</div>

  </div>

</section>

<!-- Footer -->
<?php require 'f_footer.php'; ?>
<script>
  const searchInput = document.querySelector('input[name="search"]');
  let debounce;

  searchInput.addEventListener('input', function () {
    clearTimeout(debounce);

    debounce = setTimeout(() => {
      const value = this.value.trim();
      const url = new URL(window.location.href);

      if (value) {
        url.searchParams.set('search', value);
      } else {
        url.searchParams.delete('search');
      }

      window.location.href = url.toString();
    }, 400); // Waits 400ms before redirect
  });
</script>
  

<!-- Swiper Init -->
<script>
<?php if (empty($search)): ?>
    new Swiper('.category-swiper', {
    loop: true,
    autoplay: { 
    delay: 3000, 
    disableOnInteraction: false 
  },
   navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    slidesPerView: 1,
    breakpoints: {
      640: { slidesPerView: 1 },
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3},
      1280: { slidesPerView: 4 }
    }
  });
<?php endif; ?>
</script>

<!-- Toastr Message -->
<?php if ($toastMessage): ?>
  <script>
    $(function() { toastr.error("<?= htmlspecialchars($toastMessage) ?>"); });
  </script>
<?php endif; ?>

</body>
</html>

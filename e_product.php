<?php
require 'encdec.php'; 

$conn = new mysqli("localhost", "root", "", "rajashree");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$category_id = isset($_GET['category_id']) ? (array)$_GET['category_id'] : [];


$search = $_GET['search'] ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$brands = $_GET['brand'] ?? [];
$screen_sizes = $_GET['screen_size'] ?? [];
$hdmi = $_GET['hdmi_ports'] ?? '';
$usb = $_GET['usb_ports'] ?? '';
$category_id = array_filter((array)$category_id, function($id) {
    return is_numeric($id) && $id > 0;
});

$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "e_product.name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= 's';
}

if (is_numeric($price_min)) {
    $where[] = "e_product.price >= ?";
    $params[] = $price_min;
    $types .= 'd';
}

if (is_numeric($price_max)) {
    $where[] = "e_product.price <= ?";
    $params[] = $price_max;
    $types .= 'd';
}

if (!empty($brands)) {
    $placeholders = implode(',', array_fill(0, count($brands), '?'));
    $where[] = "e_product.brand IN ($placeholders)";
    $params = array_merge($params, $brands);
    $types .= str_repeat('s', count($brands));
}

if (!empty($screen_sizes)) {
    $placeholders = implode(',', array_fill(0, count($screen_sizes), '?'));
    $where[] = "e_product.screen_size IN ($placeholders)";
    $params = array_merge($params, $screen_sizes);
    $types .= str_repeat('s', count($screen_sizes));
}

if (!empty($category_id)) {
    $placeholders = implode(',', array_fill(0, count($category_id), '?'));
    $where[] = "e_product.category_id IN ($placeholders)";
    $params = array_merge($params, $category_id);
    $types .= str_repeat('i', count($category_id));
}

if (is_numeric($hdmi)) {
    $where[] = "e_product.hdmi_ports >= ?";
    $params[] = $hdmi;
    $types .= 'i';
}

if (is_numeric($usb)) {
    $where[] = "e_product.usb_ports >= ?";
    $params[] = $usb;
    $types .= 'i';
}

$sql = "SELECT e_product.id, e_product.name, e_product.price, e_product.image_url, e_category.name AS ecategory_name
        FROM e_product
        JOIN e_category ON e_product.category_id = e_category.id";

if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY e_product.id DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$categoriesQuery = $conn->query("SELECT id, name FROM e_category ORDER BY name");
$brandsQuery = $conn->query("SELECT DISTINCT brand FROM e_product ORDER BY brand");
$sizesQuery = $conn->query("SELECT DISTINCT screen_size FROM e_product ORDER BY screen_size");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Rajashree Enterprise - Product Catalog</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
    .filter-scrollable::-webkit-scrollbar { width: 8px; }
    .filter-scrollable::-webkit-scrollbar-thumb { background: #888; }
    .filter-scrollable::-webkit-scrollbar-thumb:hover { background: #555; }
  </style>
</head>
<body>
<?php require 'e_nav.php'; ?>

<!-- Search Bar -->
<div class="bg-white shadow p-4 sticky top-0 z-10">
  <div class="max-w-7xl mx-auto px-4">
    <form method="GET" class="flex flex-col md:flex-row items-center gap-3">
      <input
        type="search"
        name="search"
        value="<?= htmlspecialchars($search) ?>"
        placeholder="🔍 Search for a product..."
        class="w-full md:w-3/4 border border-blue-300 bg-blue-50 px-5 py-2 rounded-full shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition">
        Search
      </button>
    </form>
  </div>
</div>

<!-- Main Layout -->
<div class="max-w-full mx-auto px-4 py-6">
  <div class="flex flex-col lg:flex-row gap-6">
    <!-- Filters -->
    <div class="lg:w-1/4 bg-white p-4 rounded-lg shadow filter-scrollable max-h-[90vh] overflow-y-auto">
      <h2 class="text-xl font-semibold mb-4">Filter Products</h2>
      <form method="GET" id="filterForm">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">

        <!-- Category Filter -->
        <label for="category_id" class="block text-gray-700 mt-2 font-medium">Select Category</label>
        <select name="category_id[]" id="category_id" onchange="submitForm()" class="w-full border p-2 rounded bg-white">
          <option value="">-- All Categories --</option>
          <?php while ($cat = $categoriesQuery->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= in_array($cat['id'], (array)$category_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label class="block text-gray-700 mt-4">Price Range</label>
        <div class="flex gap-2">
          <input type="number" name="price_min" placeholder="Min" class="w-full border p-2 rounded" value="<?= htmlspecialchars($price_min) ?>" onchange="submitForm()">
          <input type="number" name="price_max" placeholder="Max" class="w-full border p-2 rounded" value="<?= htmlspecialchars($price_max) ?>" onchange="submitForm()">
        </div>

        <label class="block text-gray-700 mt-4">Brand</label>
        <?php while ($b = $brandsQuery->fetch_assoc()): ?>
          <div><input type="checkbox" name="brand[]" value="<?= $b['brand'] ?>" <?= in_array($b['brand'], $brands) ? 'checked' : '' ?> onchange="submitForm()"> <?= $b['brand'] ?></div>
        <?php endwhile; ?>

        <label class="block text-gray-700 mt-4">Screen Size</label>
        <?php while ($s = $sizesQuery->fetch_assoc()): ?>
          <div><input type="checkbox" name="screen_size[]" value="<?= $s['screen_size'] ?>" <?= in_array($s['screen_size'], $screen_sizes) ? 'checked' : '' ?> onchange="submitForm()"> <?= $s['screen_size'] ?></div>
        <?php endwhile; ?>

        <label class="block text-gray-700 mt-4">Min HDMI Ports</label>
        <input type="number" name="hdmi_ports" class="w-full border p-2 rounded" value="<?= htmlspecialchars($hdmi) ?>" onchange="submitForm()">

        <label class="block text-gray-700 mt-4">Min USB Ports</label>
        <input type="number" name="usb_ports" class="w-full border p-2 rounded" value="<?= htmlspecialchars($usb) ?>" onchange="submitForm()">
      </form>
    </div>

    <!-- Product Listings -->
    <div class="lg:w-3/4">
      <h1 class="text-2xl font-bold mb-4">Product Listings</h1>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
          <div class="bg-white rounded shadow p-4 hover:shadow-lg transition">
            <?php $firstImage = explode(',', $row['image_url'])[0]; ?>
            <img src="<?= htmlspecialchars(trim($firstImage)) ?>" class="w-full h-40 object-cover mb-2 rounded">
            <h3 class="text-lg font-semibold"><?= htmlspecialchars($row['name']) ?></h3>
            <p class="text-sm text-gray-500 mb-1">Category: <?= htmlspecialchars($row['ecategory_name']) ?></p>
            <p class="text-blue-600 font-bold">₹<?= number_format($row['price'], 2) ?></p>
            <a href="e_product_detail.php?id=<?= encrypt_id($row['id']) ?>" class="mt-2 block text-center bg-blue-500 text-white py-1 rounded hover:bg-blue-600 transition">
              View Details
            </a>
          </div>
        <?php endwhile; else: ?>
          <p>No products found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  function submitForm() {
    document.getElementById('filterForm').submit();
  }
</script>

<?php $stmt->close(); $conn->close(); ?>
<?php require 'e_footer.php'; ?>

</body>
</html>
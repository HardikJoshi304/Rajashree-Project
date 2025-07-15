    <?php
    require '../conection/conn.php';
    require 'encdec.php';

    $product_id = isset($_GET['id']) ? $_GET['id'] : 0;
    $actual_id = decrypt_id($product_id);

    if ($actual_id <= 0) {
        die("Invalid product ID");
    }

    $stmt = $conn->prepare("SELECT * FROM f_product WHERE id = ?");
    $stmt->bind_param("i", $actual_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }

    // Convert comma-separated image URLs to array
    $images = explode(',', $product['image_url']);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($product['name']) ?> - Rajashree Enterprise</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            .thumbnail:hover { border: 2px solid #3b82f6; }
        </style>
        <script>
            function changeImage(src) {
                document.getElementById('mainImage').src = src;
            }

            function adjustQty(delta) {
                let input = document.getElementById('qtyInput');
                let value = parseInt(input.value);
                if (isNaN(value)) value = 1;
                value += delta;
                if (value < 1) value = 1;
                input.value = value;
            }
        </script>
    </head>
    <body class="bg-white text-black">
    <?php require 'f_nav.php'; ?>

    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col lg:flex-row gap-10">
        <!-- Left: Product Image Section -->
        <div class="lg:w-1/2 flex flex-col items-center">
            <!-- Main Image -->
            <img id="mainImage" src="<?= htmlspecialchars(trim($images[0])) ?>" class="w-full max-h-[400px] object-contain rounded mb-4" alt="Main Image">

            <!-- Thumbnails -->
            <div class="flex gap-3 flex-wrap justify-center">
                <?php
                $maxThumbnails = 5;
                $count = 0;
                foreach ($images as $img):
                    if ($count++ >= $maxThumbnails) break;
                ?>
                    <img src="<?= htmlspecialchars(trim($img)) ?>"
        class="w-20 h-20 object-cover rounded cursor-pointer thumbnail hover:ring-2 ring-blue-500"
        onmouseover="changeImage('<?= htmlspecialchars(trim($img)) ?>')">

                <?php endforeach; ?>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-6">
                <button class="px-6 py-2 border border-gray-800 text-lg">Add to Cart</button>
                <button class="px-6 py-2 bg-orange-500 text-white font-bold text-lg">BUY NOW</button>
            </div>
        </div>

        <!-- Right: Product Info -->
        <div class="lg:w-1/2">
            <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-green-700 font-semibold text-xl mb-4">₹ <?= number_format($product['price'], 2) ?></p>

            <!-- Quantity -->
            <label for="qty" class="font-semibold mr-4">Quantity:</label>
            <div class="inline-flex items-center border border-gray-400 px-3 py-1 rounded mb-4">
                <button onclick="adjustQty(-1)" class="px-2">−</button>
                <input id="qtyInput" type="number" value="1" min="1" class="w-12 text-center outline-none">
                <button onclick="adjustQty(1)" class="px-2">+</button>
            </div>

            <!-- Display Size -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold">Display Size:</h2>
                <div class="flex gap-2 mt-2">
                    <?php foreach (explode(',', $product['screen_size']) as $size): ?>
                        <span class="px-2 py-1 border border-gray-400 rounded text-sm"><?= htmlspecialchars(trim($size)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Highlights -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold">Highlights:</h2>
                <ul class="list-disc ml-6 text-sm text-gray-700 mt-2">
                    <?php foreach (explode("\n", $product['highlights']) as $line): ?>
                        <li><?= htmlspecialchars($line) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- General Info -->
            <div class="mt-6">
                <h2 class="text-lg font-semibold">General:</h2>
                <ul class="list-disc ml-6 text-sm text-gray-700 mt-2">
                    <li>Brand: <?= htmlspecialchars($product['brand']) ?></li>
                    <li>Model: <?= htmlspecialchars($product['model_name']) ?></li>
                    <li>Launch Year: <?= htmlspecialchars($product['launch_year']) ?></li>
                    <li>Series: <?= htmlspecialchars($product['series']) ?></li>
                </ul>
            </div>
        </div>
    </div>

    </body>
    </html>

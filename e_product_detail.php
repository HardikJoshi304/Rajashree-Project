    <?php
    require '../conection/conn.php';
    require 'encdec.php'; 

    $product_id = isset($_GET['id']) ? $_GET['id'] : 0;
    $actual_id = decrypt_id($product_id);

    if ($actual_id <= 0) {
        die("Invalid product ID");
    }

    $stmt = $conn->prepare("SELECT * FROM e_product WHERE id = ?");
    $stmt->bind_param("i", $actual_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }


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
    <?php require 'e_nav.php'; ?>

    <div class="max-w-7xl mx-auto px-6 py-10 flex flex-col lg:flex-row gap-10">

    <div class="lg:w-1/2 flex flex-col items-center">

        <img id="mainImage" src="<?= htmlspecialchars(trim($images[0])) ?>" class="w-full max-h-[400px] object-contain rounded mb-4" alt="Main Image">


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


            <div class="flex gap-4 mt-6">
    <!-- Share Button -->
    <button onclick="shareProduct()"
        class="flex items-center gap-2 px-6 py-2 border border-blue-500 text-blue-600 font-medium hover:bg-blue-50">
        <!-- Share Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 12l-4-4m0 0l-4 4m4-4v12" />
        </svg>
        Share
    </button>

    <!-- Add to Cart Button -->
    <button class="flex items-center gap-2 px-6 py-2 border border-gray-800 text-gray-800 hover:bg-gray-100">
        <!-- Cart Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 7M7 13l-2 5h12a1 1 0 001-1v-1M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
        </svg>
        Add to Cart
    </button>

    <!-- Buy Now Button -->
    <button class="flex items-center gap-2 px-6 py-2 bg-orange-500 text-white font-bold hover:bg-orange-600">
        <!-- Lightning Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        BUY NOW
    </button>
</div>

        </div>

 
        <div class="lg:w-1/2">
            <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-green-700 font-semibold text-xl mb-4">₹ <?= number_format($product['price'], 2) ?></p>


            <label for="qty" class="font-semibold mr-4">Quantity:</label>
            <div class="inline-flex items-center border border-gray-400 px-3 py-1 rounded mb-4">
                <button onclick="adjustQty(-1)" class="px-2">−</button>
                <input id="qtyInput" type="number" value="1" min="1" class="w-12 text-center outline-none">
                <button onclick="adjustQty(1)" class="px-2">+</button>
            </div>


            <div class="mt-6">
                <h2 class="text-lg font-semibold">Display Size:</h2>
                <div class="flex gap-2 mt-2">
                    <?php foreach (explode(',', $product['screen_size']) as $size): ?>
                        <span class="px-2 py-1 border border-gray-400 rounded text-sm"><?= htmlspecialchars(trim($size)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="mt-6">
                <h2 class="text-lg font-semibold">Highlights:</h2>
                <ul class="list-disc ml-6 text-sm text-gray-700 mt-2">
                    <?php foreach (explode("\n", $product['highlights']) as $line): ?>
                        <li><?= htmlspecialchars($line) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>


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

    <script>
    function shareProduct() {
        const title = "<?= addslashes($product['name']) ?>";
        const price = "₹ <?= number_format($product['price'], 2) ?>";
        const productURL = window.location.href;

        if (navigator.share) {
            navigator.share({
                title: title,
                text: `${title}\nPrice: ${price}`,
                url: productURL
            }).then(() => {
                console.log('Shared successfully');
            }).catch((error) => {
                console.error('Error sharing:', error);
            });
        } else {
            alert("Sharing is not supported on this browser.");
        }
    }
</script>



    </body>
    </html>

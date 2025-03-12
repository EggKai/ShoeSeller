<?php
$title = "Add product";
include_once __DIR__ . '/../inc/header.php';
?>
<form id="authForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
    <h1>Add Product:</h1>
    <div class="tab">Name:
        <p><input placeholder="Name" oninput="this.className = ''" name="name"
                value="<?php echo htmlspecialchars($_POST['name'] ?? "") ?>"></p>
        <p><input placeholder="Price" oninput="this.className = ''" name="price"
                value="<?php echo htmlspecialchars($_POST['price'] ?? "") ?>"></p>
        <p><input placeholder="brand" oninput="this.className = ''" name="brand" type="brand"
                value="<?php echo ($_POST['brand'] ?? "") ?>"></p>
    </div>
    <div class="tab">Brand:
        <p><input placeholder="Price" oninput="this.className = ''" name="price"
                value="<?php echo htmlspecialchars($_POST['price'] ?? "") ?>"></p>
    </div>
    <div class="tab">Visuals:
        <p><input placeholder="Trailer ID" oninput="this.className = ''" name="trailerId"
                value="<?php echo ($_POST['trailerId'] ?? "") ?>"></p>
        <p><input type="file" name="thumbnail" accept="image/png, image/jpeg" oninput="this.className = ''"></p>
        <p><textarea name="description" id="" cols="30" rows="10"
                placeholder="Description"><?php echo ($_POST['description'] ?? "") ?></textarea></p>
    </div>
    <div style="overflow:auto;">
        <div style="float:right;">
            <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
            <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
        </div>
    </div>
    <div style="text-align:center;margin-top:40px;">
        <span class="step"></span>
        <span class="step"></span>
        <span class="step"></span>
    </div>
</form>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
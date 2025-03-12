<?php
$title = "Add product";
include_once __DIR__ . '/../inc/header.php';
?>
<form id="authForm" action="index.php?url=admin/doAddProduct" method="POST" enctype="multipart/form-data">
    <h1>Add Product:</h1>
    <div class="tab">Name:
        <p><input placeholder="Name" oninput="this.className = ''" name="name"
                value="<?php echo htmlspecialchars($data['name'] ?? "") ?>"></p>
        <p><input placeholder="brand" oninput="this.className = ''" name="brand" type="brand"
                value="<?php echo htmlspecialchars($data['brand'] ?? "") ?>"></p>
    </div>
    <div class="tab">Price:
        <p><input placeholder="Price" oninput="this.className = ''" name="price"
                value="<?php echo htmlspecialchars($data['price'] ?? "") ?>"></p>
        <div id="sizesContainer">
            <h3>Sizes & Stock:</h3>
            <div class="size-row">
                <input type="text" name="sizes[]" placeholder="Size" oninput="this.className=''">
                <input type="number" name="stock[]" placeholder="Stock" min="0" oninput="this.className=''">
                <button type="button" class="remove-size" onclick="removeSizeRow(this)">Remove</button>
            </div>
        </div>
        <button type="button" id="addSizeBtn">Add Size</button>
    </div>
    </div>
    <div class="tab">Visuals:

        <p><input type="file" name="thumbnail" accept="image/png, image/jpeg" oninput="this.className = ''"></p>
        <p><textarea name="description" id="" cols="30" rows="10"
                placeholder="Description"><?php echo htmlspecialchars($data['description'] ?? "") ?></textarea></p>
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
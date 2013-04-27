<div id="editAlert_<?php echo $product_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $product_ID; ?>" method="post" enctype="multipart/form-data" onsubmit="layercms.webscrp.doEdit('products', <?php echo $product_ID; ?>, 'formUpdate_<?php echo $product_ID; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" id="product_ID" name="product_ID" value="<?php echo $product_ID; ?>">
    <input type="hidden" id="oldFileName" name="oldFileName" value="<?php echo $product['product_image']; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td class="tdleft"><label for="category_ID">Category:</label></td>
                        <td class="tdright">
                            <select id="category_ID" name="category_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach ($categories as $category) {
                                        if ($category['category_ID'] == $product['category_ID']) {
                                            echo '<option value="' . $category['category_ID'] . '" selected>' . $category['category_name'] . '</option>';
                                        } else {
                                            echo '<option value="' . $category['category_ID'] . '">' . $category['category_name'] . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdleft"><label for="product_name">Name:</label></td>
                        <td class="tdright"><input type="text" id="product_name" name="product_name" required value="<?php echo $product['product_name']; ?>"></td>
                    </tr>
                    <tr>
                        <td class="tdleft"><label for="product_description">Description:</label></td>
                        <td class="tdright"><textarea id="product_description" name="product_description"><?php echo $product['product_description']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td class="tdleft"><label for="product_condition">Condition:</label></td>
                        <td class="tdright">
                            <select id="product_condition" name="product_condition" required>
                                <option value="">-- None --</option>
                                <option value="New" <?php if ($product['product_condition'] == 'New') echo 'selected'; ?>>New</option>
                                <option value="Refurbished" <?php if ($product['product_condition'] == 'Refurbished') echo 'selected'; ?>>Refurbished</option>
                                <option value="Used - Like New" <?php if ($product['product_condition'] == 'Used - Like New') echo 'selected'; ?>>Used - Like New</option>
                                <option value="Used - Very Good" <?php if ($product['product_condition'] == 'Used - Very Good') echo 'selected'; ?>>Used - Very Good</option>
                                <option value="Used - Good" <?php if ($product['product_condition'] == 'Used - Good') echo 'selected'; ?>>Used - Good</option>
                                <option value="Used - Acceptable" <?php if ($product['product_condition'] == 'Used - Acceptable') echo 'selected'; ?>>Used - Acceptable</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tdleft"><label for="product_price">Price:</label></td>
                        <td class="tdright"><?php echo $currencySymbol; ?> <input type="text" id="product_price" name="product_price" required value="<?php echo $product['product_price']; ?>" pattern="\d+(\.\d{2})?"></td>
                    </tr>
                    <tr>
                        <td class="tdleft"><label for="product_stock">Stock:</label></td>
                        <td class="tdright"><input type="text" id="product_stock" name="product_stock" required value="<?php echo $product['product_stock']; ?>" pattern="[0-9]+"></td>
                    </tr>
                    <tr>
                        <td><label for="product_images">Image:</label></td>
                        <td><input type="file" id="product_image" name="product_image" onchange="layercms.webscrp.uploadFile()"></td>
                    </tr>
                    <tr>
                        <td><label for="progressBar">Progress:</label></td>
                        <td>
                            <progress id="progressBar" max="100" value="0"></progress>
                            <div id="percentageCalc"></div>
                        </td>
                    </tr>
                    <?php if ($product['product_image'] != null) { ?>
                    <tr>
                        <td class="tdleft"></td>
                        <td class="tdright">
                            <p>&nbsp;</p>
                            <div id="currentImage">
                                <img src="<?php echo BASE_PATH . '/templates/img/products/' . $product_ID . $product['product_image']; ?>">
                                <a href="javascript:void(0)" onclick="layercms.webscrp.deleteImage(<?php echo $product_ID; ?>)" class="deleteImage"><img src="<?php echo BASE_PATH . '/templates/img/delete.png'; ?>"></a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </td>
            <td>
                <input type="submit" class="btn" value="Submit">
            </td>
        </tr>
    </table>
    
</form>

<hr class="nopadding">
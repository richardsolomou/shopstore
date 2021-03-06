<div id="addAlert" class="showHide"></div>

<form id="formInsert" method="post" enctype="multipart/form-data" onsubmit="layercms.webscrp.doAdd('products', 'formInsert');return false;">

    <input type="hidden" name="operation" id="operation" value="add">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="category_ID">Category:</label></td>
                        <td>
                            <select id="category_ID" name="category_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach ($categories as $category) echo '<option value="' . $category['category_ID'] . '">' . $category['category_name'] . '</option>';
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="product_name">Name:</label></td>
                        <td><input type="text" id="product_name" name="product_name" required value=""></td>
                    </tr>
                    <tr>
                        <td><label for="product_description">Description:</label></td>
                        <td><textarea id="product_description" name="product_description"></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="product_condition">Condition:</label></td>
                        <td>
                            <select id="product_condition" name="product_condition" required>
                                <option value="">-- None --</option>
                                <option value="New">New</option>
                                <option value="Refurbished">Refurbished</option>
                                <option value="Used - Like New">Used - Like New</option>
                                <option value="Used - Very Good">Used - Very Good</option>
                                <option value="Used - Good">Used - Good</option>
                                <option value="Used - Acceptable">Used - Acceptable</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="product_price">Price:</label></td>
                        <td><?php echo $currencySymbol ?> <input type="text" id="product_price" name="product_price" required value="" pattern="\d+(\.\d{2})?"></td>
                    </tr>
                    <tr>
                        <td><label for="product_stock">Stock:</label></td>
                        <td><input type="text" id="product_stock" name="product_stock" required value="" pattern="[0-9]+"></td>
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
                </table>
            </td>
            <td>
                <input type="submit" class="btn" value="Submit">
            </td>
        </tr>
    </table>

</form>
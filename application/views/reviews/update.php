<div id="editAlert_<?php echo $review_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $review_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('reviews', <?php echo $review_ID; ?>, 'formUpdate_<?php echo $review_ID; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" id="review_ID" name="review_ID" value="<?php echo $review_ID; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="product_ID">Product:</label></td>
                        <td>
                            <select id="product_ID" name="product_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach($products as $product) {
                                        if ($product['product_ID'] == $review['product_ID']) {
                                            echo '<option value="' . $product['product_ID'] . '" selected>' . $product['product_name'] . '</option>';
                                        } else {
                                            echo '<option value="' . $product['product_ID'] . '">' . $product['product_name'] . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="review_subject">Subject:</label></td>
                        <td><input type="text" id="review_subject" name="review_subject" required value="<?php echo $review['review_subject']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="review_description">Description:</label></td>
                        <td><textarea id="review_description" name="review_description" required><?php echo $review['review_description']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="review_rating">Rating:</label></td>
                        <td>
                            <select id="review_rating" name="review_rating" required>
                                <option value="">-- None --</option>
                                <option value="1" <?php if ($review['review_rating'] == 1) echo 'selected'; ?>>1</option>
                                <option value="2" <?php if ($review['review_rating'] == 2) echo 'selected'; ?>>2</option>
                                <option value="3" <?php if ($review['review_rating'] == 3) echo 'selected'; ?>>3</option>
                                <option value="4" <?php if ($review['review_rating'] == 4) echo 'selected'; ?>>4</option>
                                <option value="5" <?php if ($review['review_rating'] == 5) echo 'selected'; ?>>5</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="customer_ID">Customer:</label></td>
                        <td>
                            <select id="customer_ID" name="customer_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach($customers as $customer) {
                                        if ($customer['customer_ID'] == $review['customer_ID']) {
                                            echo '<option value="' . $customer['customer_ID'] . '" selected>' . $customer['customer_firstname'] . ' ' . $customer['customer_lastname'] . '</option>';
                                        } else {
                                            echo '<option value="' . $customer['customer_ID'] . '">' . $customer['customer_firstname'] . ' ' . $customer['customer_lastname'] . '</option>';
                                        }
                                    }
                                ?>
                            </select>
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

<hr class="nopadding">
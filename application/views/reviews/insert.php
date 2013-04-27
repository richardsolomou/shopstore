<?php if (isset($product_ID)) echo '<article>'; ?>

<div id="addAlert" class="showHide"></div>

<form id="formInsert" method="post" onsubmit="layercms.webscrp.doAdd('reviews', 'formInsert');return false;">

    <input type="hidden" name="operation" id="operation" value="add">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <?php if (isset($product_ID)) { echo '<input type="hidden" name="product_ID" id="product_ID" value="' . $product_ID . '">'; } else { ?>
                    <tr>
                        <td><label for="product_ID">Product:</label></td>
                        <td>
                            <select id="product_ID" name="product_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach ($products as $product) echo '<option value="' . $product['product_ID'] . '">' . $product['product_name'] . '</option>';
                                ?>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td><label for="review_subject">Subject:</label></td>
                        <td><input type="text" id="review_subject" name="review_subject" required value=""></td>
                    </tr>
                    <tr>
                        <td><label for="review_description">Description:</label></td>
                        <td><textarea id="review_description" name="review_description" required></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="review_rating">Rating:</label></td>
                        <td>
                            <select id="review_rating" name="review_rating" required>
                                <option value="">-- None --</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </td>
                    </tr>
                    <?php if (isset($product_ID)) { echo '<input type="hidden" name="customer_ID" id="customer_ID" value="' . $_SESSION['SESS_CUSTOMERID'] . '">'; } else { ?>
                    <tr>
                        <td><label for="customer_ID">Customer:</label></td>
                        <td>
                            <select id="customer_ID" name="customer_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach ($customers as $customer) echo '<option value="' . $customer['customer_ID'] . '">' . $customer['customer_firstname'] . ' ' . $customer['customer_lastname'] . '</option>';
                                ?>
                            </select>
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

<?php if (isset($product_ID)) echo '</article>'; ?>
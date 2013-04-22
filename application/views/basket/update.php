<div id="editAlert_<?php echo $basket_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $basket_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('basket', <?php echo $basket_ID; ?>, 'formUpdate_<?php echo $basket_ID; ?>', null, '<?php echo BASE_PATH . '/basket/update/' . $basket_ID . '/admin'; ?>'); return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" id="basket_ID" name="basket_ID" value="<?php echo $basket_ID; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="basket_quantity">Quantity:</label></td>
                        <td><input type="text" id="basket_quantity" name="basket_quantity" required value="<?php echo $basket['basket_quantity']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="product_ID">Product:</label></td>
                        <td>
                            <select id="product_ID" name="product_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach($products as $product) {
                                        if ($product['product_ID'] == $basket['product_ID']) {
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
                        <td><label for="customer_ID">Customer:</label></td>
                        <td>
                            <select id="customer_ID" name="customer_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach($customers as $customer) {
                                        if ($customer['customer_ID'] == $basket['customer_ID']) {
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
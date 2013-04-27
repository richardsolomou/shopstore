<div id="editAlert_<?php echo $order_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $order_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('orders', <?php echo $order_ID; ?>, 'formUpdate_<?php echo $order_ID; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" id="order_ID" name="order_ID" value="<?php echo $order_ID; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="order_total">Order Total:</label></td>
                        <td><?php echo $currencySymbol; ?> <input type="text" id="order_total" name="order_total" required value="<?php echo $order['order_total']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="customer_ID">Customer:</label></td>
                        <td>
                            <select id="customer_ID" name="customer_ID" required>
                                <option value="">-- None --</option>
                                <?php
                                    foreach ($customers as $customer) {
                                        if ($customer['customer_ID'] == $order['customer_ID']) {
                                            echo '<option value="' . $customer['customer_ID'] . '" selected>' . $customer['customer_firstname'] . ' ' . $customer['customer_lastname'] . '</option>';
                                        } else {
                                            echo '<option value="' . $customer['customer_ID'] . '">' . $customer['customer_firstname'] . ' ' . $customer['customer_lastname'] . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="bordered hoverRed nomargin">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="lefted">Quantity</th>
                                        <th class="lefted">Product</th>
                                        <th class="lefted">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php
                                foreach ($orderedProducts as $orderedProduct) {
                                    foreach ($products as $product) {
                                        if ($orderedProduct['product_ID'] == $product['product_ID']) {
                                            $productName = $product['product_name']; ?>
                                    <tr>
                                        <td><?php echo $orderedProduct['item_ID']; ?></td>
                                        <td><?php echo $orderedProduct['item_quantity']; ?></td>
                                        <td><a href="<?php echo BASE_PATH . '/products/getById/' . $product['product_ID']; ?>" class="productLink"><?php echo $product['product_name']; ?></a></td>
                                        <td><?php echo $product['product_price']; ?></td>
                                    </tr>
                            <?php       }
                                    }
                                }
                            ?>
                                </tbody>
                            </table>
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
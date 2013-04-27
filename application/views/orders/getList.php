<article>

    <h1>Completed Orders</h1>

    <div id="operationAlert" class="showHide"></div>

    <div id="message" class="showHide adminBar"></div>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Amount</th>
                <th class="lefted">Customer</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($orders as $order) {
                    foreach ($customers as $customer) {
                        if ($customer['customer_ID'] == $order['customer_ID']) {
                            $customerName = $customer['customer_firstname'] . ' ' . $customer['customer_lastname'];
                        }
                    }
            ?>
                    <tr class="centered">
                        <td><?php echo $order['order_ID']; ?></td>
                        <td class="lefted"><?php echo $currencySymbol . $order['order_total']; ?></td>
                        <td class="lefted"><?php echo $customerName; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $order['order_ID']; ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $order['order_ID']; ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="4"><div class="showHide" id="edit_<?php echo $order['order_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

    <p>&nbsp;</p>

    <h1>Pending Orders</h1>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Product</th>
                <th class="lefted">Customer</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($pendingOrders as $pendingOrder) {
                    foreach ($customers as $customer) {
                        if ($customer['customer_ID'] == $pendingOrder['customer_ID']) {
                            $customerName = $customer['customer_firstname'] . ' ' . $customer['customer_lastname'];
                        }
                    }
                    foreach ($products as $product) {
                        if ($product['product_ID'] == $pendingOrder['product_ID']) {
                            $productName = $product['product_name'];
                        }
                    }
            ?>
                    <tr class="centered">
                        <td><?php echo $pendingOrder['basket_ID']; ?></td>
                        <td class="lefted"><?php echo $productName; ?></td>
                        <td class="lefted"><?php echo $customerName; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $pendingOrder['basket_ID']; ?>, '<?php echo BASE_PATH . '/basket/update/' . $pendingOrder['basket_ID'] . '/admin'; ?>', 'editPending_<?php echo $pendingOrder['basket_ID']; ?>')" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $pendingOrder['basket_ID']; ?>, '<?php echo BASE_PATH . '/basket/delete/' . $pendingOrder['basket_ID'] . '/admin'; ?>', 'editPending_<?php echo $pendingOrder['basket_ID']; ?>')" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="4"><div class="showHide" id="editPending_<?php echo $pendingOrder['basket_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>
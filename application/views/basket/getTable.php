<table class="bordered hoverRed">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $totalPrice = 0;
            $numberOfItems = 0;
            foreach($basketItems as $item) {
                $numberOfItems += 1;
                foreach ($products as $product) {
                    if ($item['product_ID'] == $product['product_ID']) {
                        $productName = $product['product_name'];
                        $productPrice = $product['product_price'];
                        $productImage = $product['product_image'];
                        $productStock = $product['product_stock'];
                    }
                }
                $totalPrice += $productPrice * $item['basket_quantity'];
        ?>
        <tr class="centered">
            <td><?php echo $productName; ?></td>
            <td class="width25">
                <form id="updateBasket_<?php echo $item['basket_ID']; ?>" method="post">
                    <input type="hidden" name="basket_ID" id="basket_ID" value="<?php echo $item['basket_ID']; ?>">
                    <input type="hidden" name="customer_ID" id="customer_ID" value="<?php echo $item['customer_ID']; ?>">
                    <input type="hidden" name="product_ID" id="product_ID" value="<?php echo $item['product_ID']; ?>">
                    <input type="text" class="smaller" id="basket_quantity" placeholder="Quantity" name="basket_quantity" onfocusout="layercms.webscrp.doEdit('basket', <?php echo $item['basket_ID']; ?>, 'updateBasket_<?php echo $item['basket_ID']; ?>', 'operationAlert', null, '<?php echo BASE_PATH . '/basket/getTable/'; ?>')" required value="<?php echo $item['basket_quantity']; ?>" pattern="[0-9]+">
                    <br><span id="productStockLeft_<?php echo $item['basket_ID']; ?>"><?php echo $productStock; ?></span> left
                </form>
            </td>
            <td><?php echo $currencySymbol . $productPrice; ?></td>
            <td><?php echo $currencySymbol . $productPrice * $item['basket_quantity']; ?>
        </tr>
        <?php } ?>
        <tr class="noHover">
            <td colspan="4">
                <p class="big small"><strong>Total Price:</strong> <?php echo $currencySymbol; ?><span id="totalPrice"><?php echo $totalPrice; ?></span></p>
            </td>
        </tr>
    </tbody>
</table>
<?php if ($numberOfItems != 0) { ?>
<div class="centered">
    <form id="submitOrder" method="post" onsubmit="layercms.webscrp.submitOrder('submitOrder', '<?php echo BASE_PATH . '/orders/insert'; ?>', '<?php echo BASE_PATH . '/basket/getTable/'; ?>'); return false;">
        <input type="hidden" name="operation" id="operation" value="true">
        <input type="hidden" name="customer_ID" id="customer_ID" value="<?php echo $_SESSION['SESS_CUSTOMERID']; ?>">
        <input type="hidden" name="order_total" id="order_total" value="<?php echo $totalPrice; ?>">
        <input type="submit" class="highlight big" value="Submit Order">
    </form>
</div>
<?php } ?>
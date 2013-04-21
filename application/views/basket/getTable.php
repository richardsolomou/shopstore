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
            foreach($basketItems as $item) {
                foreach ($products as $product) {
                    if ($item['product_ID'] == $product['product_ID']) {
                        $productName = $product['product_name'];
                        $productPrice = $product['product_price'];
                        $productImage = $product['product_image'];
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
                            <input type="text" class="smaller" id="basket_quantity" placeholder="Quantity" name="basket_quantity" onfocusout="layercms.webscrp.doEdit('basket', <?php echo $item['product_ID']; ?>, 'updateBasket_<?php echo $item['basket_ID']; ?>', 'operationAlert', null, '<?php echo BASE_PATH . '/basket/getTable/' . $item['product_ID']; ?>')" required value="<?php echo $item['basket_quantity']; ?>" pattern="[0-9]+">
                            <br><span id="productStockLeft"><?php echo $product['product_stock']; ?></span> left
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
<p class="centered">
    <form id="finaliseOrder" method="post">
        <input type="hidden" name="customer_ID" id="customer_ID" value="<?php echo $item['customer_ID']; ?>">
        <button class="highlight big">Finalise Order</button>
    </form>
</p>
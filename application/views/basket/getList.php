<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <div id="message" class="showHide adminBar"></div>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="lefted">Quantity</th>
                <th class="lefted">Price</th>
                <th class="lefted">Subtotal</th>
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
                        <td class="width25 lefted"><input type="text" class="smaller" id="basket_quantity" placeholder="Quantity" name="basket_quantity" onfocusout="layercms.webscrp.updateBasket(<?php echo $item['product_ID']; ?>, this.value, <?php echo BASE_PATH . '/basket/update/' . $item['basket_ID']; ?>)" required value="<?php echo $item['basket_quantity']; ?>" pattern="[0-9]+"></td>
                        <td class="lefted"><?php echo $currencySymbol . $productPrice; ?></td>
                        <td class="lefted"><?php echo $currencySymbol . $productPrice * $item['basket_quantity']; ?>
                    </tr>
            <?php } ?>
            <tr class="noHover">
                <td colspan="4">
                    <p class="big small"><strong>Total Price:</strong> <?php echo $currencySymbol . $totalPrice; ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <p class="centered">
        <button class="highlight big">Finalise Order</button>
    </p>

</article>
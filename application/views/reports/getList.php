<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <div id="message" class="showHide adminBar"></div>

    <h2 class="nomargin">Statistics</h2>
    <h3>Some numbers..</h3>

    <table class="bordered">
        <thead>
            <tr>
                <th>Customers</th>
                <th>Products</th>
                <th>Reviews</th>
                <th>Pending Orders</th>
                <th>Orders</th>
            </tr>
        </thead>
        <tbody>
            <tr class="centered">
                <td><?php echo $numberOfCustomers; ?></td>
                <td><?php echo $numberOfProducts; ?></td>
                <td><?php echo $numberOfReviews; ?></td>
                <td><?php echo $numberOfPendingOrders; ?></td>
                <td><?php echo $numberOfOrders; ?></td>
            </tr>
        </tbody>
    </table>

    <p>&nbsp;</p>
    <h2 class="nomargin">Stock</h2>
    <h3>Products running low on stock (below 15).</h3>

    <table class="bordered">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Available Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($productsLowOnStock != array()) {
                    foreach ($productsLowOnStock as $productLowOnStock) {
            ?>
            <tr class="centered">
                <td><?php echo $productLowOnStock['product_ID']; ?></td>
                <td><?php echo $productLowOnStock['product_name']; ?></td>
                <td><?php echo $productLowOnStock['product_stock']; ?></td>
            </tr>
            <?php
                    }
                } else {
            ?>
            <tr>
                <td class="centered" colspan="3">No products running low on stock.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <p>&nbsp;</p>
    <h2 class="nomargin">Orders</h2>
    <h3>Where all that money is coming from.</h3>

    <table class="bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Amount Paid</th>
                <th>Customer Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalSales = 0;
                if ($orders != array()) {
                    foreach ($orders as $order) {
                        foreach ($customers as $customer) {
                            if ($customer['customer_ID'] == $order['customer_ID']) {
                                $customerName = $customer['customer_firstname'] . ' ' . $customer['customer_lastname'];
                            }
                        }
                        $totalSales += $order['order_total'];
            ?>
            <tr class="centered">
                <td><?php echo $order['order_ID']; ?></td>
                <td><?php echo $currencySymbol . $order['order_total']; ?></td>
                <td><?php echo $customerName; ?></td>
            </tr>
            <?php
                    }
                } else {
            ?>
            <tr>
                <td class="centered" colspan="3">No orders available.</td>
            </tr>
            <?php } ?>
            <tr class="noHover">
                <td colspan="3">
                    <p class="big small"><strong>Total Sales:</strong> <?php echo $currencySymbol; ?><span id="totalPrice"><?php echo $totalSales; ?></span></p>
                </td>
            </tr>
        </tbody>
    </table>

    <p>&nbsp;</p>
    <h2 class="nomargin">Customers</h2>
    <h3>Because the customer is always right.</h3>

    <table class="bordered">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($customers != array()) {
                    foreach ($customers as $customer) {
            ?>
            <tr class="centered">
                <td><?php echo $customer['customer_ID']; ?></td>
                <td><?php echo $customer['customer_username']; ?></td>
                <td><?php echo $customer['customer_firstname']; ?></td>
                <td><?php echo $customer['customer_lastname']; ?></td>
            </tr>
            <?php
                    }
                } else {
            ?>
            <tr>
                <td class="centered" colspan="4">No customers available.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <p>&nbsp;</p>
    <h2 class="nomargin">Products</h2>
    <h3>Competition brings out the best in products.</h3>

    <table class="bordered">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($products != array()) {
                    foreach ($products as $product) {
                        foreach ($categories as $category) {
                            if ($category['category_ID'] == $product['category_ID']) {
                                $productCategory = $category['category_name'];
                            }
                        }
            ?>
            <tr class="centered">
                <td><?php echo $product['product_ID']; ?></td>
                <td><?php echo $product['product_name']; ?></td>
                <td><?php echo $productCategory; ?></td>
                <td><?php echo $currencySymbol . $product['product_price']; ?></td>
            </tr>
            <?php
                    }
                } else {
            ?>
            <tr>
                <td class="centered" colspan="4">No products available.</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

</article>
<aside>

    <div id="hideSidebar" class="highlight" onclick="layercms.webscrp.toggleSidebarCookies();layercms.webscrp.toggleSidebar();">Toggle</div>
    <div id="asideContent">

        <p></p>

        <h3>Customer.</h3>
        <div id="login_2"></div>
        
        <?php if(!isset($_SESSION['SESS_LOGGEDIN']) || !isset($_SESSION['SESS_CUSTOMERID'])) { ?>

            <form method="post" id="loginForm_2">
                
                <input type="hidden" name="operation" id="operation" value="true">
                <input type="hidden" name="admin" id="admin" value="0">

                <table class="table3">
                    <tr><td class="nopadding nomargin"><input type="text" id="username" placeholder="Username" name="username" required value="" pattern="[a-zA-Z0-9]+"></td></tr>
                    <tr><td class="nopadding nomargin"><input type="password" id="password" placeholder="Password" name="password" required value="" pattern="[a-zA-Z0-9]+"></td></tr>
                    <tr>
                        <td>
                            <input type="submit" class="btn" name="login" value="Login" onclick="layercms.webscrp.login(2, '<?php echo BASE_PATH; ?>'); return false;">&nbsp;&raquo;&nbsp;
                            <a href="<?php echo BASE_PATH . '/customers/add'; ?>" class="highlight big">Register</a>
                        </td>
                    </tr>
                </table>
            </form>

        <?php } else { ?>

            <p class="centered"><button onclick="layercms.webscrp.logout(2, '<?php echo BASE_PATH; ?>', 0); return false;" class="btn">Logout</button></p>
        
        <?php } ?>

        <hr>

        <div id="sideBarAlert"></div>

        <h3>Shopping Basket</h3>
        <div id="sideBarBasket">
            <table class="shoppingBasket hoverRed bordered">
                <thead>
                    <th class="table2">Name</th>
                    <th class="width25">Price</th>
                </thead>
                <tbody>
                    <?php
                        $totalPrice = 0;
                        if ($basketItems != array()) {
                            foreach ($basketItems as $item) {
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
                        <td><?php echo $currencySymbol . $productPrice; ?></td>
                    </tr>
                    <?php
                            }
                        }
                    ?>
                    <tr class="noHover"><td colspan="2"><hr class="nomargin"></td></tr>
                    <tr class="noHover">
                        <td class="righted">Sub-Total:</td>
                        <td>&pound;0.00</td>
                    </tr>
                    <tr class="noHover">
                        <td class="righted">Shipping:</td>
                        <td>&pound;0.00</td>
                    </tr>
                    <tr class="noHover">
                        <td class="righted">Total:</td>
                        <td><?php echo $currencySymbol . $totalPrice; ?></td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="2" class="centered">
                            <a href="<?php echo BASE_PATH . '/basket'; ?>" class="btn">Go to your Shopping Basket</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</aside>
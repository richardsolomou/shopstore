<aside>

    <?php if(!isset($_SESSION['SESS_LOGGEDIN']) || !isset($_SESSION['SESS_CUSTOMERID'])) { ?>
        
        <form action="<?php echo BASE_PATH . '/login'; ?>" id="login" method="post">

            <input type="hidden" name="login" value="true">

            <table class="table3">
                <tr><td><label for="customer_username">Username:</label></td></tr>
                <tr><td class="nopadding nomargin"><input type="text" id="customer_username" name="customer_username" required value="" pattern="[a-zA-Z0-9]+"></td></tr>
                <tr><td><label for="customer_password">Password:</label></td></tr>
                <tr><td class="nopadding nomargin"><input type="password" id="customer_password" name="customer_password" required value="" pattern="[a-zA-Z0-9]+"></td></tr>
                <tr><td><input type="submit" class="btn" name="login" value="Login"></td></tr>
            </table>

        </form>

    <?php } else { ?>

        <p>Currently logged in.</p>
        <p><a href="<?php echo BASE_PATH . '/logout'; ?>" class="btn">Logout</a></p>
    
    <?php } ?>

    <section id="headerShoppingBasket">
        Drag &amp; Drop Items Here!
    </section>

    <h3>Shopping Basket</h3>
    <table class="shoppingBasket">
        <tr>
            <td>Placeholder Item</td>
            <td class="price">&pound;59.90</td>
        </tr>
        <tr>
            <td class="righted">Sub-Total:</td>
            <td class="price">&pound;0.00</td>
        </tr>
        <tr>
            <td class="righted">Shipping:</td>
            <td class="price">&pound;0.00</td>
        </tr>
        <tr>
            <td class="righted">Total:</td>
            <td class="price">&pound;0.00</td>
        </tr>
        <tr>
            <td colspan="2" class="nopadding centered"><a href="<?php echo BASE_PATH . '/basket'; ?>">Go to your Shopping Basket</a></td>
        </tr>
    </table>

</aside>
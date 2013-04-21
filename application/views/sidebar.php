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
                    <tr><td><input type="submit" class="btn" name="login" value="Login" onclick="layercms.webscrp.login(2, '<?php echo BASE_PATH; ?>'); return false;"></td></tr>
                </table>

            </form>

        <?php } else { ?>

            <p class="centered"><button onclick="layercms.webscrp.logout(2, '<?php echo BASE_PATH; ?>', 0); return false;" class="btn">Logout</button></p>
        
        <?php } ?>

        <hr>

        <p>&nbsp;</p>

        <div id="sideBarAlert"></div>

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

    </div>

</aside>
<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="addAlert" class="showHide"></div>

    <form id="formInsert" method="post" onsubmit="layercms.webscrp.doAdd('customers', 'formInsert', null, '<?php echo BASE_PATH . '/customers/add'; ?>');return false;">

        <input type="hidden" name="operation" id="operation" value="add">

        <table class="table3">
            <tr>
                <td>
                    <table class="table3">
                        <tr>
                            <td><label for="customer_username">Username:</label></td>
                            <td><input type="text" id="customer_username" name="customer_username" required value="" pattern="[a-zA-Z0-9]+"></td>
                        </tr>
                        <tr>
                            <td><label for="customer_password">Password:</label></td>
                            <td><input type="password" id="customer_password" name="customer_password" required value="" pattern="[a-zA-Z0-9]+"></td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td><label for="customer_firstname">First Name:</label></td>
                            <td><input type="text" id="customer_firstname" name="customer_firstname" required value="" pattern="[a-zA-Z ]+"></td>
                        </tr>
                        <tr>
                            <td><label for="customer_lastname">Last Name:</label></td>
                            <td><input type="text" id="customer_lastname" name="customer_lastname" required value="" pattern="[a-zA-Z ]+"></td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td><label for="customer_address1">Address 1:</label></td>
                            <td><input type="text" id="customer_address1" name="customer_address1" required value=""></td>
                        </tr>
                        <tr>
                            <td><label for="customer_address2">Address 2:</label></td>
                            <td><input type="text" id="customer_address2" name="customer_address2" value=""></td>
                        </tr>
                        <tr>
                            <td><label for="customer_postcode">Postcode:</label></td>
                            <td><input type="text" id="customer_postcode" name="customer_postcode" required value=""></td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td><label for="customer_phone">Phone Number:</label></td>
                            <td><input type="text" id="customer_phone" name="customer_phone" required value="" pattern="[0-9 ]+"></td>
                        </tr>
                        <tr>
                            <td><label for="customer_email">E-mail Address:</label></td>
                            <td><input type="text" id="customer_email" name="customer_email" required value="" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$"></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <input type="submit" class="btn" value="Submit">
                </td>
            </tr>
        </table>

    </form>

</article>
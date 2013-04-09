<?php if (!isset($_POST['operation'])) { ?>

    <div id="editAlert_<?php echo $customer_ID; ?>" class="showHide"></div>

    <hr class="nomargin nopadding">

    <form id="formUpdate_<?php echo $customer_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('customers', <?php echo $customer_ID; ?>, 'formUpdate_<?php echo $customer_ID; ?>');return false;">

        <input type="hidden" id="operation" name="operation" value="edit">
        <input type="hidden" id="customer_ID" name="customer_ID" value="<?php echo $customer_ID; ?>">
        <input type="hidden" id="oldPassword" name="oldPassword" value="<?php echo $customer['customer_password'] ?>">

        <table class="table3">
            <tr>
                <td>
                    <table class="table3">
                        <tr>
                            <td class="tdleft"><label for="customer_username">Username:</label></td>
                            <td class="tdright"><input type="text" id="customer_username" name="customer_username" required value="<?php echo $customer['customer_username']; ?>" pattern="[a-zA-Z0-9]+"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_password">Password:</label></td>
                            <td class="tdright"><input type="password" id="customer_password" name="customer_password" value="" pattern="[a-zA-Z0-9]+"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_firstname">First Name:</label></td>
                            <td class="tdright"><input type="text" id="customer_firstname" name="customer_firstname" required value="<?php echo $customer['customer_firstname']; ?>" pattern="[a-zA-Z ]+"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_lastname">Last Name:</label></td>
                            <td class="tdright"><input type="text" id="customer_lastname" name="customer_lastname" required value="<?php echo $customer['customer_lastname']; ?>" pattern="[a-zA-Z ]+"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_address1">Address 1:</label></td>
                            <td class="tdright"><input type="text" id="customer_address1" name="customer_address1" required value="<?php echo $customer['customer_address1']; ?>"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_address2">Address 2:</label></td>
                            <td class="tdright"><input type="text" id="customer_address2" name="customer_address2" value="<?php echo $customer['customer_address2']; ?>"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_postcode">Postcode:</label></td>
                            <td class="tdright"><input type="text" id="customer_postcode" name="customer_postcode" required value="<?php echo $customer['customer_postcode']; ?>"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_phone">Store Phone:</label></td>
                            <td class="tdright"><input type="text" id="customer_phone" name="customer_phone" required value="<?php echo $customer['customer_phone']; ?>" pattern="[0-9 ]+"></td>
                        </tr>
                        <tr>
                            <td class="tdleft"><label for="customer_email">E-mail Address:</label></td>
                            <td class="tdright"><input type="text" id="customer_email" name="customer_email" required value="<?php echo $customer['customer_email']; ?>" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$"></td>
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

<?php } else { ?>

    <div class="alert <?php echo $alert ?>"><?php echo $message ?></div>
    <?php if ($alert == 'alert-success nomargin') echo '<div class="alert alert-info "><a href="' . BASE_PATH . '/customers/getList' . '">Refresh</a> to see the changes.</div>'; ?>

<?php } ?>
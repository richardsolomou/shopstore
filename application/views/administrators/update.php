<div id="editAlert_<?php echo $admin_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $admin_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('administrators', <?php echo $admin_ID; ?>, 'formUpdate_<?php echo $admin_ID; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" id="admin_ID" name="admin_ID" value="<?php echo $admin_ID; ?>">
    <input type="hidden" id="oldPassword" name="oldPassword" value="<?php echo $administrator['customer_password'] ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="admin_username">Username:</label></td>
                        <td><input type="text" id="admin_username" name="admin_username" required value="<?php echo $administrator['admin_username']; ?>" pattern="[a-zA-Z0-9]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_password">Password:</label></td>
                        <td><input type="password" id="admin_password" name="admin_password" value="" pattern="[a-zA-Z0-9]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_firstname">First Name:</label></td>
                        <td><input type="text" id="admin_firstname" name="admin_firstname" required value="<?php echo $administrator['admin_firstname']; ?>" pattern="[a-zA-Z ]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_lastname">Last Name:</label></td>
                        <td><input type="text" id="admin_lastname" name="admin_lastname" required value="<?php echo $administrator['admin_lastname']; ?>" pattern="[a-zA-Z ]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_email">E-mail Address:</label></td>
                        <td><input type="text" id="admin_email" name="admin_email" required value="<?php echo $administrator['admin_email']; ?>" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$"></td>
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
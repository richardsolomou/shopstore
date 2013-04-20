<div id="addAlert" class="showHide"></div>

<form id="formInsert" method="post" onsubmit="layercms.webscrp.doAdd('administrators', 'formInsert');return false;">

    <input type="hidden" name="operation" id="operation" value="add">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="admin_username">Username:</label></td>
                        <td><input type="text" id="admin_username" name="admin_username" required value="" pattern="[a-zA-Z0-9]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_password">Password:</label></td>
                        <td><input type="password" id="admin_password" name="admin_password" required value="" pattern="[a-zA-Z0-9]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_firstname">First Name:</label></td>
                        <td><input type="text" id="admin_firstname" name="admin_firstname" required value="" pattern="[a-zA-Z ]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_lastname">Last Name:</label></td>
                        <td><input type="text" id="admin_lastname" name="admin_lastname" required value="" pattern="[a-zA-Z ]+"></td>
                    </tr>
                    <tr>
                        <td><label for="admin_email">E-mail Address:</label></td>
                        <td><input type="text" id="admin_email" name="admin_email" required value="" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$"></td>
                    </tr>
                </table>
            </td>
            <td>
                <input type="submit" class="btn" value="Submit">
            </td>
        </tr>
    </table>

</form>
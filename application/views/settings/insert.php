<?php if (!isset($_POST['operation'])) { ?>

	<div id="addAlert" class="showHide"></div>

    <form id="formInsert" method="post" onsubmit="layercms.webscrp.doAdd('settings', 'formInsert');return false;">

        <input type="hidden" name="operation" id="operation" value="add">

        <table class="table3">
            <tr>
                <td>
                    <table class="table3">
                        <tr>
                            <td><label for="setting_column">Column:</label></td>
                            <td><input type="text" id="setting_column" name="setting_column" required value=""></td>
                        </tr>
                        <tr>
                            <td><label for="setting_value">Value:</label></td>
                            <td><input type="text" id="setting_value" name="setting_value" required value=""></td>
                        </tr>
                        <tr>
                    </table>
                </td>
                <td>
                    <input type="submit" class="btn" value="Submit">
                </td>
            </tr>
        </table>

    </form>

<?php } else { ?>

    <div class="alert <?php echo $alert ?>"><?php echo $message ?></div>
    <?php if ($alert == 'alert-success nomargin') echo '<div class="alert alert-info "><a href="' . BASE_PATH . '/settings/getList' . '">Refresh</a> to see the changes.</div>'; ?>

<?php } ?>
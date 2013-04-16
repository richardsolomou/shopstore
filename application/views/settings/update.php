<div id="editAlert_<?php echo $setting_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $setting_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('settings', <?php echo $setting_ID; ?>, 'formUpdate_<?php echo $setting_ID; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" name="setting_ID" id="setting_ID" value="<?php echo $setting_ID; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="setting_column">Column:</label></td>
                        <td><input type="text" id="setting_column" name="setting_column" required disabled value="<?php echo $setting['setting_column']; ?>"></td>
                    </tr>
                    <tr>
                        <td><label for="setting_value">Value:</label></td>
                        <td>
                            <?php if ($setting_ID == 2) { ?>
                                <select id="setting_value" name="setting_value" required>
                                    <?php
                                        foreach($currencies as $currency) {
                                            if ($currency['currency_ID'] == $currency_ID) {
                                                echo '<option value="' . $currency['currency_ID'] . '" selected>' . $currency['currency_code'] . ' - ' . $currency['currency_symbol'] . '</option>';
                                            } else {
                                                echo '<option value="' . $currency['currency_ID'] . '">' . $currency['currency_code'] . ' - ' . $currency['currency_symbol'] . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            <?php } else { ?>
                                <input type="text" id="setting_value" name="setting_value" required value="<?php echo $setting['setting_value']; ?>">
                            <?php } ?>
                        </td>
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
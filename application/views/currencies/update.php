<div id="editAlert_<?php echo $currency_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $currency_ID; ?>" method="post" onsubmit="layercms.webscrp.doEdit('currencies', <?php echo $currency_ID; ?>, 'formUpdate_<?php echo $currency_ID; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" name="currency_ID" id="currency_ID" value="<?php echo $currency_ID; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="currency_name">Name:</label></td>
                        <td><input type="text" id="currency_name" name="currency_name" required value="<?php echo $currency['currency_name']; ?>" pattern="[a-zA-Z ]+"></td>
                    </tr>
                    <tr>
                        <td><label for="currency_code">Code:</label></td>
                        <td><input type="text" id="currency_code" name="currency_code" required value="<?php echo $currency['currency_code']; ?>" pattern="[a-zA-Z]+"></td>
                    </tr>
                    <tr>
                        <td><label for="currency_symbol">Symbol:</label></td>
                        <td><input type="text" id="currency_symbol" name="currency_symbol" required value="<?php echo $currency['currency_symbol']; ?>"></td>
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
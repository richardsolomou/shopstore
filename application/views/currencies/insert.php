<div id="addAlert" class="showHide"></div>

<form id="formInsert" method="post" onsubmit="layercms.webscrp.doAdd('currencies', 'formInsert');return false;">

    <input type="hidden" name="operation" id="operation" value="add">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="currency_name">Name:</label></td>
                        <td><input type="text" id="currency_name" name="currency_name" required value="" pattern="[a-zA-Z ]+"></td>
                    </tr>
                    <tr>
                        <td><label for="currency_code">Code:</label></td>
                        <td><input type="text" id="currency_code" name="currency_code" required value="" pattern="[a-zA-Z]+"></td>
                    </tr>
                    <tr>
                        <td><label for="currency_symbol">Symbol:</label></td>
                        <td><input type="text" id="currency_symbol" name="currency_symbol" required value=""></td>
                    </tr>
                </table>
            </td>
            <td>
                <input type="submit" class="btn" value="Submit">
            </td>
        </tr>
    </table>

</form>
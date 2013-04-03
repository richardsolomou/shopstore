<article id="full">

    <h1><?php echo $pageTitle ?></h1>

    <form action="<?php echo BASE_PATH . DS . 'installer' . DS . 'second'; ?>" method="post">
        
        <input type="hidden" name="first" value="true">

        <p>Please input the website preferences and the preferred administrator account details.</p>

        <table>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <tr>
                <td class="righted"><label for="website_name">Website Name:</label></td>
                <td>
                    <input type="text" id="website_name" name="website_name" required value="LayerCMS">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <tr>
                <td class="righted"><label for="admin_username">Administrator Username:</label></td>
                <td>
                    <input type="text" id="admin_username" name="admin_username" required value="" pattern="[a-zA-Z0-9]+">
                </td>
            </tr>
            <tr>
                <td class="righted"><label for="admin_password">Administrator Password:</label></td>
                <td>
                    <input type="password" id="admin_password" name="admin_password" required value="" pattern="[a-zA-Z0-9]+">
                </td>
            </tr>
            <tr>
                <td class="righted"><label for="admin_firstname">First Name:</label></td>
                <td>
                    <input type="text" id="admin_firstname" name="admin_firstname" required value="" pattern="[a-zA-Z ]+">
                </td>
            </tr>
            <tr>
                <td class="righted"><label for="admin_lastname">Last Name:</label></td>
                <td>
                    <input type="text" id="admin_lastname" name="admin_lastname" required value="" pattern="[a-zA-Z ]+">
                </td>
            </tr>
            <tr>
                <td class="righted"><label for="admin_email">E-mail Address:</label></td>
                <td>
                    <input type="text" id="admin_email" name="admin_email" required value="" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
        </table>

        <a href="javascript:void(0)" onclick="layercms.webscrp.toggleShowHide('advancedOptions')" class="showHide"><span>Toggle Advanced Options</span><img src="images/showhide.png" alt=""></a>

        <div id="advancedOptions" class="showHide">

            <table>

                <tr>
                    <td class="righted"><label for="db_host">Database Host:</label></td>
                    <td>
                        <input type="text" id="db_host" name="db_host" required value="">
                    </td>
                </tr>
                <tr>
                    <td class="righted"><label for="db_user">Database User:</label></td>
                    <td>
                        <input type="text" id="db_user" name="db_user" required value="">
                    </td>
                </tr>
                <tr>
                    <td class="righted"><label for="db_pass">Database Password:</label></td>
                    <td>
                        <input type="password" id="db_pass" name="db_pass" value="">
                    </td>
                </tr>
                <tr>
                    <td class="righted"><label for="db_name">Database Name:</label></td>
                    <td>
                        <input type="text" id="db_name" name="db_name" required value="">
                    </td>
                </tr>
                <tr>
                    <td class="righted"><label for="dropExisting">Drop Existing Database?</label></td>
                    <td>
                        <input type="checkbox" id="dropExisting" name="dropExisting" checked value="Yes">
                    </td>
                </tr>

            </table>

        </div>

        <table>

            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <tr>
                <td class="table2 righted"><label for="sampleData">Install Sample Data?</label></td>
                <td>
                    <input type="checkbox" id="sampleData" name="sampleData" checked value="Yes">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <tr>
                <td class="centered" colspan="2">
                    <input type="submit" class="btn" name="setup" value="Setup">
                </td>
            </tr>
            
        </table>

    </form>

</article>
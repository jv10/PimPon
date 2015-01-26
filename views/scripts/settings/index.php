<?php


$allowReplaceValue           = PimPon_Plugin::ALLOW_REPLACE;
$checked['replaceobjects']   = ($this->config->replaceobjects === $allowReplaceValue )
        ? 'checked' : '';
$checked['replacedocuments'] = ($this->config->replacedocuments === $allowReplaceValue)
        ? 'checked' : '';
$checked['replaceroutes']    = ($this->config->replaceroutes === $allowReplaceValue)
        ? 'checked' : '';
$checked['replaceusers'] = ($this->config->replaceusers === $allowReplaceValue)
        ? 'checked' : '';
$checked['replaceroles']    = ($this->config->replaceroles === $allowReplaceValue)
        ? 'checked' : '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      

    </head>

    <body>

        <form action="" method="post">
            <input type="hidden" name="save" id="save" value="yes" />

            <h2>PimPon Plugin - Import/Export Pimcore Tool</h2>

            <fieldset>
                <legend>Settings:</legend>
                <ul>
                    <li>
                        <label>Replace:</label>
                        <ul>
                            <li><input type="checkbox" <?php echo $checked['replaceobjects']; ?> value="<?php echo $allowReplaceValue; ?>" id="replaceobjects" name="replaceobjects" /> Allow Replace Objects</li>
                            <li><input type="checkbox" <?php echo $checked['replacedocuments']; ?> value="<?php echo $allowReplaceValue; ?>" id="replacedocuments" name="replacedocuments" /> Allow Replace Documents</li>
                            <li><input type="checkbox" <?php echo $checked['replaceroutes']; ?> value="<?php echo $allowReplaceValue; ?>" id="replaceroutes" name="replaceroutes" /> Allow Replace Routes</li>
                            <li><input type="checkbox" <?php echo $checked['replaceusers']; ?> value="<?php echo $allowReplaceValue; ?>" id="replaceusers" name="replaceusers" /> Allow Replace Users</li>
                            <li><input type="checkbox" <?php echo $checked['replaceroles']; ?> value="<?php echo $allowReplaceValue; ?>" id="replaceroles" name="replaceroles" /> Allow Replace Roles</li>

                        </ul>

                    </li>
                </ul>
            </fieldset>
            <br />
            <br />
            <input type="submit" name="submit" value="Save" />
        </form>

    </body>
</html>

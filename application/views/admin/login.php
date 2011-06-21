<?php 
/**
 * Login view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo Kohana::lang('ui_main.ushahidi_admin');?></title>
<?php 
//Custom stylesheet
echo html::stylesheet(url::site().'themes/'.Kohana::config('settings.site_style')."/admin/login.css");
?>
</head>

<body>
<div id="login_div">
    <div class="login_header"></div>
    <div class="fields">
        <form method="POST" name="frm_login" style="line-height: 100%; margin-top: 0; margin-bottom: 0">     
        <fieldset>
            <p>
                <label for="username">Nombre de Usuario</label><br />
                <input type="text" id="username" name="username" class="textfield" />
            </p>
            <p>
                <label for="password">Contrase&ntilde;a</label><br />
                <input type="password" id="password" name="password" class="textfield" />
            </p>
            <p>
              <input type="checkbox" id="remember" name="remember" value="1" checked="checked" /><span class="note"><?php echo Kohana::lang('ui_main.password_save');?></span>
            </p>
            <p>
                <input type="submit" name="submit" value="Ingresar" class="boton" />
                <!--
                &nbsp;
                <a href="<?php echo url::site()?>login/resetpassword"> <?php echo Kohana::lang('ui_main.forgot_password');?></a>
                -->
            </p>
        </fieldset>
        </form>
    </div>
</div>
</body>
</html>

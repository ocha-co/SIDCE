<?php 
/**
 * Login view page.
 *
 * @package    SIHCESAR
 * @module     Home Controller
 * @author     OCHA Colombia
 * @copyright  (c) 2010 OCHA Colombia
 */
?>
<div id="login_div">
    <div class="login_header"></div>
    <div class="fields">
        <form method="post">
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
                <input type="submit" name="submit" value="Ingresar" class="boton" />
            </p>
        </fieldset>
        </form>
    </div>
</div>

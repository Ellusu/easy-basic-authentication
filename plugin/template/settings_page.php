<?php 
$dir_file = realpath(dirname(__FILE__) . '/../class/easy-basic-authentication-class.php'); 
$content_file = "add_action( 'init', array(\$this,'basic_auth_admin') );";
?>

<div class="wrap easy-basic-settings">
    <div class="error-message">
        <?php settings_errors(); ?>
    </div>
    <form method="post" action="options.php" class="easy-basic-form">
        <?php
        settings_fields( 'basic-auth-plugin-settings' );
        do_settings_sections( 'basic-auth-plugin-settings' );
        submit_button(__('save', 'easy-basic-authentication'), 'primary', 'eba_submit', false);
        ?>
    </form>
    <div id="password-recovery-alert" class="container">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><?php echo esc_html__('Attention:', 'easy-basic-authentication'); ?></strong> <?php echo esc_html__('To recover your password, you need to comment out a line in a file via FTP.', 'easy-basic-authentication'); ?>
            <?php echo esc_html__('Please follow the instructions', 'easy-basic-authentication'); ?> <a href="#" class="alert-link" onclick="toggleInstructions()"><?php echo esc_html__('here.', 'easy-basic-authentication');?></a>
        </div>

        <div id="instructions" style="display: none;" class="mt-3">
            <p><strong><?php echo esc_html__('Instructions:', 'easy-basic-authentication'); ?></strong></p>
            <ol>
            <li><?php echo esc_html__('Connect to your website via FTP.', 'easy-basic-authentication'); ?></li>
            <li><?php echo esc_html__('Navigate to the plugin directory.', 'easy-basic-authentication'); ?></li>
            <li><?php 
                // translators: %s è il percorso del file del plugin
                echo sprintf(esc_html__('Locate the file named %s.', 'easy-basic-authentication'), esc_attr($dir_file)); ?>
            </li>
            <li><?php
                // translators: %s is the content file path
                echo sprintf(esc_html__('Find the line containing  %s.', 'easy-basic-authentication'), esc_attr($content_file)); ?>
            </li>
            <li><?php echo esc_html__('Comment out that line by adding "#" at the beginning.', 'easy-basic-authentication'); ?></li>
            <li><?php echo esc_html__('Save the file and re-upload it to your server.', 'easy-basic-authentication'); ?></li>
            </ol>
        </div>
    </div>

    <script>
        function toggleInstructions() {
            var instructions = document.getElementById("instructions");
            if (instructions.style.display === "none") {
                instructions.style.display = "block";
            } else {
                instructions.style.display = "none";
            }
        }
    </script>

</div>

<style>
.easy-basic-settings {
    background-color: #f9f9f9;
    padding: 20px;
    border: 1px solid #ddd;
}

.easy-basic-form {
    margin-top: 20px;
}

.easy-basic-form input[type="text"],
.easy-basic-form input[type="password"],
.easy-basic-form textarea {
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.easy-basic-form .submit {
    background-color: #0073aa;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
}

.easy-basic-form .submit:hover {
    background-color: #005b82;
}

.easy-basic-form input[type=checkbox],
.easy-basic-form input[type=radio] {
    padding: 10px 20px;
    height: 1.5625rem;
    width: 1.5625rem;
}

.easy-basic-form input[type=checkbox]:checked:before {
    width: 1.875rem;
    height: 1.875rem;
    margin: -0.1875rem -0.3125rem;
}

.easy-basic-settings #password-recovery-alert {
    margin-top: 30px;
}
</style>
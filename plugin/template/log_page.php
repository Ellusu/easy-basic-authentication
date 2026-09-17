<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h2><?php echo esc_html__('Unauthorized access log', 'easy-basic-authentication'); ?></h2>
    <p>
    <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=basic-auth-login')); ?>">
        <?php wp_nonce_field('eba_clear_logs', 'eba_clear_nonce'); ?>
        <input type="hidden" name="clear_mode" value="1">
        <button type="submit" class="button button-primary">
            <?php echo esc_html__('Clear log', 'easy-basic-authentication'); ?>
        </button>
    </form>
    </p>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php echo esc_html__('ID', 'easy-basic-authentication'); ?></th>
                <th><?php echo esc_html__('IP Address', 'easy-basic-authentication'); ?></th>
                <th><?php echo esc_html__('Date', 'easy-basic-authentication'); ?></th>
                <th><?php echo esc_html__('Browser', 'easy-basic-authentication'); ?></th>
                <th><?php echo esc_html__('Request URI', 'easy-basic-authentication'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($user_log_data as $basic_auth_entry) {
                $basic_auth_new = $basic_auth_entry['viewed']?'':' ('.esc_html__('new', 'easy-basic-authentication').')';
                echo "<tr>";
                echo "<td>" . esc_html($basic_auth_entry['id']) .esc_attr($basic_auth_new). "</td>";
                echo "<td>" . esc_html($basic_auth_entry['ip']) . "</td>";
                echo "<td>" . esc_html($basic_auth_entry['data']) . "</td>";
                echo "<td>" . esc_html($basic_auth_entry['browser']) . "</td>";
                echo "<td>" . esc_html(isset($basic_auth_entry['request_uri'])?$basic_auth_entry['request_uri']:'') . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

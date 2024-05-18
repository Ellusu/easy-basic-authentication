<div class="wrap">
    <h2><?php echo esc_html__('Unauthorized access log', 'easy-basic-authentication'); ?></h2>
    <p>
    <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=basic-auth-login')); ?>">
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
            foreach ($user_log_data as $entry) {
                $new = $entry['viewed']?'':' ('.esc_html__('new', 'easy-basic-authentication').')';
                echo "<tr>";
                echo "<td>" . esc_html($entry['id']) .esc_attr($new). "</td>";
                echo "<td>" . esc_html($entry['ip']) . "</td>";
                echo "<td>" . esc_html($entry['data']) . "</td>";
                echo "<td>" . esc_html($entry['browser']) . "</td>";
                echo "<td>" . esc_html(isset($entry['request_uri'])?$entry['request_uri']:'') . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

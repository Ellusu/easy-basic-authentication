<?php

class easy_basic_authentication_compatcheck_class {

    public function register_hooks() {
        add_action('wp_ajax_eba_test_auth', array($this, 'ajax_test_auth'));
    }

    public function ajax_test_auth() {
        header('Content-Type: application/json');

        $response = [
            'success' => false,
            'message' => __('Unknown error.', 'easy-basic-authentication'),
        ];

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            $response['message'] = __('Authorization header not received. It may have been blocked by the browser or the server (especially on HTTP).', 'easy-basic-authentication');
        } else {
            $expected_user = get_option('basic_auth_plugin_username');
            $expected_pass = get_option('basic_auth_plugin_password');

            if (
                $_SERVER['PHP_AUTH_USER'] === $expected_user &&
                wp_unslash($_SERVER['PHP_AUTH_PW']) === $expected_pass
            ) {
                $response['success'] = true;
                $response['message'] = __('Authentication received successfully. Basic Auth is compatible!', 'easy-basic-authentication');
            } else {
                $response['message'] = __('Credentials received but incorrect.', 'easy-basic-authentication');
            }
        }

        echo json_encode($response);
        exit;
    }

    public static function render_button_html() {
        ?>
        <h2><?php echo __('Compatibility Test', 'easy-basic-authentication'); ?></h2>
        <p><?php echo __('Click the button below to check if your server correctly supports Basic Authentication.', 'easy-basic-authentication'); ?></p>
        <button id="eba-test-button" class="button button-secondary"><?php echo __('Run Compatibility Test', 'easy-basic-authentication'); ?></button>
        <div id="eba-test-result" style="margin-top: 1em;"></div>
        <script>
        document.getElementById('eba-test-button').addEventListener('click', function(e) {
            e.preventDefault();

            const username = <?php echo json_encode(get_option('basic_auth_plugin_username')); ?>;
            const password = <?php echo json_encode(wp_unslash(get_option('basic_auth_plugin_password'))); ?>;
            const resultBox = document.getElementById('eba-test-result');

            resultBox.innerHTML = '⌛ ' + <?php echo json_encode(__('Waiting for response...', 'easy-basic-authentication')); ?>;

            fetch(ajaxurl + '?action=eba_test_auth', {
                headers: {
                    'Authorization': 'Basic ' + btoa(username + ':' + password)
                }
            })
            .then(response => response.text())
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    resultBox.innerHTML = '<span style="color: red;">✘ ' + <?php echo json_encode(__('Invalid JSON response.', 'easy-basic-authentication')); ?> + '</span>';
                    console.error('Invalid JSON:', text);
                    return;
                }

                if (data.success) {
                    resultBox.innerHTML = '<span style="color: green;">✔ ' + data.message + '</span>';
                } else {
                    resultBox.innerHTML = '<span style="color: red;">✘ ' + data.message + '</span>';
                }
            })
            .catch(error => {
                resultBox.innerHTML = '<span style="color: red;">' + <?php echo json_encode(__('Error: ', 'easy-basic-authentication')); ?> + error.message + '</span>';
            });
        });
        </script>
        <?php
    }
}

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
            // Qui la "password" e un nonce alfanumerico generato da noi, quindi a
            // differenza del percorso di autenticazione vero si puo sanitizzare.
            $received_user  = sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) );
            $received_token = isset( $_SERVER['PHP_AUTH_PW'] )
                ? sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_PW'] ) )
                : '';

            if (
                $received_user === 'eba-compat-test' &&
                wp_verify_nonce($received_token, 'eba_compat_test')
            ) {
                $response['success'] = true;
                $response['message'] = __('Authentication received successfully. Basic Auth is compatible!', 'easy-basic-authentication');
            } else {
                $response['message'] = __('Authorization header received but could not be verified. Please try again.', 'easy-basic-authentication');
            }
        }

        echo json_encode($response);
        exit;
    }

    public static function render_button_html() {
        $test_nonce = wp_create_nonce('eba_compat_test');
        ?>
        <h2><?php esc_html_e('Compatibility Test', 'easy-basic-authentication'); ?></h2>
        <p><?php esc_html_e('Click the button below to check if your server correctly supports Basic Authentication.', 'easy-basic-authentication'); ?></p>
        <button id="eba-test-button" class="button button-secondary"><?php esc_html_e('Run Compatibility Test', 'easy-basic-authentication'); ?></button>
        <div id="eba-test-result" style="margin-top: 1em;"></div>
        <script>
        document.getElementById('eba-test-button').addEventListener('click', function(e) {
            e.preventDefault();

            const testToken = <?php echo json_encode($test_nonce); ?>;
            const resultBox = document.getElementById('eba-test-result');

            resultBox.innerHTML = '⌛ ' + <?php echo json_encode(__('Waiting for response...', 'easy-basic-authentication')); ?>;

            fetch(ajaxurl + '?action=eba_test_auth', {
                headers: {
                    'Authorization': 'Basic ' + btoa('eba-compat-test:' + testToken)
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

document.addEventListener('DOMContentLoaded', function () {
    const testButton = document.getElementById('revivetosky_test_connection_button');
    const resultSpan = document.getElementById('revivetosky_test_connection_result');
    const handleInput = document.getElementById('revivetosky_bluesky_handle');
    const passwordInput = document.getElementById('revivetosky_bluesky_app_password');

    testButton.addEventListener('click', async function () {
        const handle = handleInput.value.trim();
        const appPassword = passwordInput.value.trim();

        if (!handle || !appPassword) {
            resultSpan.textContent = 'Error: Please fill in both handle and app password.';
            return;
        }

        testButton.disabled = true;
        resultSpan.textContent = 'Testing connection...';
        resultSpan.className = '';
        resultSpan.textContent = 'Testing connection...';

        const spinner = document.createElement('span');
        spinner.className = 'spinner is-active';
        spinner.style.float = 'left';
        spinner.style.marginRight = '5px';
        testButton.appendChild(spinner);
        try {
            const response = await fetch(revivetosky_test_connection_obj.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'revivetosky_test_connection',
                    handle: handle,
                    app_password: appPassword,
                    nonce: revivetosky_test_connection_obj.nonce
                }),
            });
            const data = await response.json();

            console.log( data );

            if (data.success) {
                resultSpan.className = 'notice notice-success';
                resultSpan.textContent = data.data.message;
            } else {
                resultSpan.className = 'notice notice-error';
                resultSpan.textContent = data.data.message;
            }

        } catch (err) {
            console.log(err);
            resultSpan.className = 'notice notice-error';
            resultSpan.textContent = 'Unexpected error connecting to Bluesky.';
        } finally {
            testButton.disabled = false;
        }
        testButton.removeChild(spinner);
    });
});
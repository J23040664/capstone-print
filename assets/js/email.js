// EmailJS SDK
// Dont share this secret key to others, because have quota
// const emailjsSecretKey = 'xqOuw3Z-26loqt4Fg';
// const emailjsserviceKey = 'service_gkayr3w';
// const emailjsTemplateKey = 'template_scesfvy';
// emailjs.init(emailjsSecretKey);

function generateVerificationCode() {
    return Math.floor(100000 + Math.random() * 900000).toString();
}

function verifyCode(email) {
    if (!email) {
        alert('Please enter your email before sending the code.');
        return;
    }

    generatedCode = generateVerificationCode();
    sessionStorage.setItem('verificationCode', generatedCode);

    const templateParams = {
        email: email,
        code: generatedCode
    };

    emailjs.send(emailjsserviceKey, emailjsTemplateKey, templateParams).then(function (response) {
        alert("Verification code sent!");
        // Disable button for 60 seconds
        const sendBtn = document.getElementById('send_code_btn');
        sendBtn.disabled = true;
        let countdown = 60;
        sendBtn.textContent = `Send code (${countdown}s)`;

        const interval = setInterval(() => {
            countdown--;
            sendBtn.textContent = `Send code (${countdown}s)`;
            if (countdown <= 0) {
                clearInterval(interval);
                sendBtn.disabled = false;
                sendBtn.textContent = "Send code";
            }
        }, 1000);
    }, function (error) {
        console.error('EmailJS Error:', error);
        alert("Failed to send code. Try again.");
    });
}
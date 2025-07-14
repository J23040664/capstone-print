// EmailJS SDK
// Dont share this secret key to others, because have quota
const emailjsSecretKey = 'xqOuw3Z-26loqt4Fg';
const emailjsserviceKey = 'service_gkayr3w';
const emailjsTemplateKey1 = 'template_scesfvy';
const emailjsTemplateKey2 = 'template_sfes04c';
emailjs.init(emailjsSecretKey);

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

    emailjs.send(emailjsserviceKey, emailjsTemplateKey1, templateParams).then(function (response) {
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

function sendOrderStatusEmail(email, order_id, customer_name, order_status) {
    if (!email) {
        alert('Error Email');
        return;
    }

    const templateParams = {
        customer_name: customer_name,
        order_id: order_id,
        order_status: order_status,
        email: email
    };

    emailjs.send(emailjsserviceKey, emailjsTemplateKey2, templateParams).then(function (response) {
        alert("Order Status have been sent to user!");
    }, function (error) {
        console.error('EmailJS Error:', error);
        alert("Failed to send order status email. Please Try again.");
    });
}
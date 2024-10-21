<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link href="https://fonts.cdnfonts.com/css/segoe-ui-4" rel="stylesheet">
    <link rel="stylesheet" href="./static/style.css">
</head>
<body>
    <main class="login-container">
        <img class="go-back" src="./resources/logo.png" alt="logo" onclick="window.location.href = '../index.php'">
            <section class="login-box" style="max-width: 600px;">
            <h1>Contact Us</h1>
                <form action="../index.php" method="POST" class="form-container">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your Name" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your Email" required>

                    <label for="message">Message</label>
                    <textarea   id="message" name="message" placeholder="Your Message" rows="5" required></textarea>

                    <button type="submit" class="contrast">Send Message</button>
                </form>
            </section>
    </main>
</body>
</html>

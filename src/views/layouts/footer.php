
<footer class="footer">
    <div class="footer-p">
        <p>Contact Us</p>
        <p>Get in touch at (1) 999-999-9999 or
            in social media with our
            award-winning customer service
            support for any inquiries
            about our products.</p>
    </div>
    <div class="footer-icons">
        <img src="<?= ASSETS_URL ?>img/facebookIcon.png" class="icons">
        <img src="<?= ASSETS_URL ?>img/twitterIcon.png" class="icons">
    </div>
</footer>
<script>
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navbar');

    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('show');
        hamburger.classList.toggle('active');
    });

</script>
</body>

</html>
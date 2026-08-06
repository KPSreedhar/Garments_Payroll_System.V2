  </main>

  <footer class="footer" role="contentinfo">
    <div class="footer-container">
      <div class="footer-about">
        <div class="footer-logo">
          <i class="fas fa-tshirt"></i>
          <span class="footer-logo-text">Garment Payroll</span>
        </div>
        <p>Streamlining payroll operations for garment manufacturers.</p>
      </div>
      <div class="footer-links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="home.php">Dashboard</a></li>
          <li><a href="workers.php">Add Workers</a></li>
          <li><a href="work-entry.php">Submit Work</a></li>
          <li><a href="salary-report.php">Salary Reports</a></li>
        </ul>
      </div>
      <div class="footer-contact">
        <h3>Contact</h3>
        <p><i class="fas fa-envelope"></i> sreedhark@gmail.com</p>
        <p><i class="fas fa-map-marker-alt"></i> Tiruppur, India</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> Garment Payroll System. All rights reserved.</p>
    </div>
  </footer>

  <button id="backToTop" class="back-to-top" title="Back to Top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>

  <script>
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const navLinks = document.getElementById("navLinks");
    mobileMenuBtn.addEventListener("click", () => {
      navLinks.classList.toggle("active");
      mobileMenuBtn.innerHTML = navLinks.classList.contains("active") ?
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });

    const backToTopBtn = document.getElementById("backToTop");
    window.onscroll = () => {
      backToTopBtn.style.display = window.scrollY > 300 ? "flex" : "none";
    };
    backToTopBtn.onclick = () => window.scrollTo({ top: 0, behavior: "smooth" });

    document.getElementById("logoutBtn").addEventListener("click", () => {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
      }
    });
  </script>
</body>
</html>

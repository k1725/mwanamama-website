// script.js

// --- Careers Loader ---
let careersOffset = 0;
const careersLimit = 6;

async function loadCareers(reset = false) {
  const careersList = document.getElementById("careersList");
  const noJobs = document.getElementById("noJobs");
  const loadMoreBtn = document.getElementById("loadMoreCareers");

  if (!careersList || !noJobs) {
    console.error("careersList or noJobs element not found!");
    return;
  }

  if (reset) {
    careersOffset = 0;
    careersList.innerHTML = `
      <div class="col-12">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-3 fs-5">Loading job openings...</p>
        </div>
      </div>`;
  }

  // Disable load more button during loading
  if (loadMoreBtn) {
    loadMoreBtn.disabled = true;
    loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
  }

  try {
    const apiUrl = `../backend/api/careers.php?limit=${careersLimit}&offset=${careersOffset}`;

    console.log("Fetching careers from:", apiUrl);
    
    const res = await fetch(apiUrl);

    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }

    const data = await res.json();
    console.log("Careers data received:", data);

    if (reset) {
      careersList.innerHTML = ""; // Remove loader
    }

    if (!data.success || !data.data || data.data.length === 0) {
      console.log("No jobs found");
      if (careersOffset === 0) {
        noJobs.classList.remove("d-none");
        careersList.innerHTML = "";
      }
      if (loadMoreBtn) {
        loadMoreBtn.style.display = "none";
      }
      return;
    }

    noJobs.classList.add("d-none");

    data.data.forEach(job => {
      console.log("Adding job card:", job.title);
      
      // Calculate days remaining
      const deadlineDate = new Date(job.deadline);
      const today = new Date();
      const daysRemaining = Math.ceil((deadlineDate - today) / (1000 * 60 * 60 * 24));
      
      // Create column wrapper
      const col = document.createElement("div");
      col.className = "col-md-6 col-lg-4";

      // Create card
      const card = document.createElement("div");
      card.className = "job-card";

      // Build job card HTML
      card.innerHTML = `
        <span class="badge-type">${job.employment_type || 'Full-time'}</span>
        
        <h3 class="job-card-title">${job.title}</h3>
        
        <div class="job-card-meta">
          <div class="job-card-meta-item">
            <i class="bi bi-building"></i>
            <span>${job.department || 'General'}</span>
          </div>
          <div class="job-card-meta-item">
            <i class="bi bi-geo-alt"></i>
            <span>${job.location || 'Not specified'}</span>
          </div>
          ${job.experience_level ? `
          <div class="job-card-meta-item">
            <i class="bi bi-graph-up"></i>
            <span>${job.experience_level}</span>
          </div>
          ` : ''}
        </div>

        <p class="job-card-description">
          ${job.description ? job.description.substring(0, 150) + '...' : 'No description available'}
        </p>

        <div class="job-card-footer">
          <div class="job-card-deadline">
            <i class="bi bi-clock"></i>
            <strong>${daysRemaining > 0 ? daysRemaining + ' days left' : 'Expired'}</strong>
          </div>
          <a href="/Mwanamama-Website/html-partials/careers-details.php?id=${job.id}" 
             class="apply-btn" 
             target="_blank"
             onclick="console.log('Link clicked:', this.href); return true;">
            Read More
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      `;

      col.appendChild(card);
      careersList.appendChild(col);
    });

    careersOffset += data.data.length;
    console.log("New offset:", careersOffset);

    // Hide load more button if fewer results than limit
    if (loadMoreBtn) {
      if (data.data.length < careersLimit) {
        loadMoreBtn.style.display = "none";
      } else {
        loadMoreBtn.style.display = "block";
        loadMoreBtn.disabled = false;
        loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle me-2"></i>Load More Jobs';
      }
    }
  } catch (err) {
    console.error("API Error:", err);
    if (careersOffset === 0) {
      noJobs.classList.remove("d-none");
      careersList.innerHTML = `
        <div class="col-12">
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Error loading careers:</strong> ${err.message}
            <br><small class="text-muted">Please try again later or contact support.</small>
          </div>
        </div>
      `;
    }
    if (loadMoreBtn) {
      loadMoreBtn.disabled = false;
      loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle me-2"></i>Load More Jobs';
    }
  }
}

// --- Initialize Features After Content Loads ---
function initializeFeatures() {
  // Smooth scrolling for navigation links
  const navbar = document.querySelector(".navbar");
  const navLinks = document.querySelectorAll(".navbar-nav .nav-link");
  const sections = document.querySelectorAll("section[id]");
  const navHeight = navbar ? navbar.offsetHeight : 0;

  navLinks.forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      const target = document.querySelector(link.getAttribute("href"));
      if (target) {
        const top = target.offsetTop - navHeight;
        window.scrollTo({ top, behavior: "smooth" });
      }
    });
  });

  // Active nav link on scroll
  window.addEventListener("scroll", () => {
    let current = "";
    sections.forEach(section => {
      if (window.scrollY >= section.offsetTop - navHeight - 100) {
        current = section.getAttribute("id");
      }
    });
    navLinks.forEach(link => {
      link.classList.remove("active");
      if (link.getAttribute("href") === "#" + current) {
        link.classList.add("active");
      }
    });
  });

  // Initialize Careers page if exists - Use a small delay to ensure DOM is ready
  setTimeout(() => {
    const careersListElement = document.getElementById("careersList");
    if (careersListElement) {
      console.log("✅ Careers section found, loading careers...");
      loadCareers(true);
      
      // Add Load More button event listener
      const loadMoreBtn = document.getElementById("loadMoreCareers");
      if (loadMoreBtn) {
        console.log("✅ Load More button found, adding event listener");
        loadMoreBtn.addEventListener("click", () => {
          console.log("Load More button clicked");
          loadCareers(false);
        });
      }
    } else {
      console.log("ℹ️ Careers section not found on this page");
    }
  }, 100);
}

// --- Load Page Partials ---
document.addEventListener("DOMContentLoaded", () => {

  const routes = {
    home: [
      "html-partials/hero.html",
      "html-partials/why-choose-us.html",
      "html-partials/about.html",
      "html-partials/services.html",
      "html-partials/how-it-works.html",
      "html-partials/testimonials.html",
      "html-partials/faqs.html",
      "html-partials/blog.html",
      "html-partials/gallery.html",
      "html-partials/contact.html"
    ],
    careers: [
      "html-partials/careers.html"
    ],
    about: [
      "html-partials/about.html"
    ],
    services: [
      "html-partials/services.html"
    ],
    contact: [
      "html-partials/contact.html"
    ]
  };

  const nav = { id: "nav-placeholder", file: "html-partials/nav.html" };
  const footer = { id: "footer-placeholder", file: "html-partials/footer.html" };

  async function loadPage(page = "home") {
    console.log("📄 Loading page:", page);
    const container = document.getElementById("content-placeholder");
    if (!container) {
      console.error("Content placeholder not found!");
      return;
    }

    container.innerHTML = "";

    const files = routes[page] || routes.home;

    for (const file of files) {
      try {
        console.log("📥 Fetching:", file);
        const res = await fetch(file);
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status} for ${file}`);
        const html = await res.text();
        container.insertAdjacentHTML("beforeend", html);
        console.log("✅ Loaded:", file);
      } catch (err) {
        console.error("❌ Error loading partial:", file, err);
      }
    }

    // IMPORTANT: Wait for browser to render the inserted HTML before initializing
    await new Promise(resolve => setTimeout(resolve, 50));
    initializeFeatures();
  }

  // Load Nav & Footer
  fetch(nav.file)
    .then(r => r.text())
    .then(html => document.getElementById(nav.id).innerHTML = html)
    .catch(err => console.error("Nav load error:", err));
    
  fetch(footer.file)
    .then(r => r.text())
    .then(html => document.getElementById(footer.id).innerHTML = html)
    .catch(err => console.error("Footer load error:", err));

  // Detect page from URL
  const params = new URLSearchParams(window.location.search);
  const page = params.get("page") || "home";
  console.log("🎯 Current page:", page);
  loadPage(page);

});
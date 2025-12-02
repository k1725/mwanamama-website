const API_URL = '../backend/api/careers.php';
let currentPage = 0;
const jobsPerPage = 6; // Customization Point: loadCareers(6)

// Function to fetch and display jobs
async function loadCareers(limit, offset, append = true) {
    const listingsDiv = document.getElementById('job-listings');
    const loadingIndicator = document.getElementById('loading-indicator');
    const loadMoreBtn = document.getElementById('load-more-btn');
    
    loadingIndicator.style.display = 'block';

    try {
        const response = await fetch(`${API_URL}?limit=${limit}&offset=${offset}`);
        const jobs = await response.json();

        if (!append) {
            listingsDiv.innerHTML = ''; // Clear existing jobs if not appending
        }
        
        if (jobs.length === 0 && offset === 0) {
            listingsDiv.innerHTML = '<p class="text-center">No active job postings available at this time.</p>';
        } else {
            jobs.forEach(job => {
                const card = createJobCard(job);
                listingsDiv.appendChild(card);
            });
            
            // Show/Hide Load More button
            if (jobs.length < limit) {
                loadMoreBtn.style.display = 'none';
            } else {
                loadMoreBtn.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error fetching careers:', error);
        listingsDiv.innerHTML = '<p class="text-danger">Failed to load jobs. Please check the API path.</p>';
    } finally {
        loadingIndicator.style.display = 'none';
    }
}

// Helper function to create a job card
function createJobCard(job) {
    const card = document.createElement('div');
    card.className = 'job-card col-md-6 col-lg-4';
    card.dataset.type = job.employment_type; // For filtering
    card.innerHTML = `
        <h5>${job.job_title}</h5>
        <p class="text-muted">${job.department} - ${job.location}</p>
        <p class="badge bg-info">${job.employment_type}</p>
        <p>Deadline: ${job.application_deadline}</p>
        <button class="btn btn-sm btn-outline-primary view-job-btn" data-job-id="${job.id}">View Details</button>
    `;
    card.querySelector('.view-job-btn').onclick = () => openJobModal(job);
    return card;
}

// Function to open the detail modal
function openJobModal(job) {
    document.getElementById('modal-title').textContent = job.job_title;
    document.getElementById('modal-department').textContent = job.department;
    document.getElementById('modal-location').textContent = job.location;
    document.getElementById('modal-type').textContent = job.employment_type;
    document.getElementById('modal-exp').textContent = job.experience_level;
    document.getElementById('modal-deadline').textContent = `Deadline: ${job.application_deadline}`;
    document.getElementById('modal-description').textContent = job.description;

    // Split and list responsibilities/requirements (assuming they are stored as comma/newline separated)
    const renderList = (id, text) => {
        const ul = document.getElementById(id);
        ul.innerHTML = '';
        if (text) {
             text.split('\n').forEach(item => {
                if (item.trim()) {
                    const li = document.createElement('li');
                    li.textContent = item.trim();
                    ul.appendChild(li);
                }
            });
        }
    };
    renderList('modal-responsibilities', job.responsibilities);
    renderList('modal-requirements', job.requirements);
    
    // Attach apply function to button
    document.getElementById('modal-apply-btn').onclick = () => applyForJob(job.id);

    document.getElementById('job-detail-modal').style.display = 'block';
}

// Function to handle the apply button click
function applyForJob(careerId) {
    // Customization Point: Redirect to your application form/process
    window.location.href = `apply.php?job=${careerId}`; // Example
    // OR: window.location.href = `mailto:careers@yourcompany.com?subject=Application for Job ID ${careerId}`;
}

// Function to handle filtering
function handleFilter(filterType) {
    const cards = document.querySelectorAll('.job-card');
    cards.forEach(card => {
        if (filterType === 'all' || card.dataset.type === filterType) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initial setup and event listeners
document.addEventListener('DOMContentLoaded', () => {
    // Initial Load
    loadCareers(jobsPerPage, 0, false);
    currentPage = 0;

    // Load More functionality
    document.getElementById('load-more-btn').addEventListener('click', () => {
        currentPage++;
        loadCareers(jobsPerPage, currentPage * jobsPerPage, true);
    });

    // Modal Close
    const modal = document.getElementById('job-detail-modal');
    document.querySelector('.close-btn').onclick = () => { modal.style.display = 'none'; };
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };

    // Filter Buttons
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            handleFilter(e.target.dataset.filter);
        });
    });
});
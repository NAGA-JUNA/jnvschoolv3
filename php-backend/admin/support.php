<?php
$pageTitle='Support';require_once __DIR__.'/../includes/auth.php';requireAdmin();
require_once __DIR__.'/../includes/header.php';?>

<div class="row g-4">
  <!-- Header -->
  <div class="col-12">
    <div class="card border-0 rounded-3" style="background:linear-gradient(135deg,#1e3a5f,#2563eb)">
      <div class="card-body text-center text-white py-5">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:80px;height:80px;background:rgba(255,255,255,0.15)">
          <i class="bi bi-gear-wide-connected" style="font-size:2.5rem"></i>
        </div>
        <h3 class="fw-bold mb-1">JNV Tech</h3>
        <p class="mb-0" style="font-size:1.1rem;opacity:0.9">Journey to New Value</p>
        <p class="mb-0 mt-2" style="font-size:.8rem;opacity:0.75">School Management System v2.0</p>
      </div>
    </div>
  </div>

  <!-- Contact Cards -->
  <div class="col-md-4">
    <div class="card border-0 rounded-3 h-100">
      <div class="card-body text-center p-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:56px;height:56px;background:#25D366">
          <i class="bi bi-whatsapp text-white" style="font-size:1.5rem"></i>
        </div>
        <h6 class="fw-semibold">WhatsApp</h6>
        <p class="text-muted mb-3" style="font-size:.85rem">Chat with us directly for quick support</p>
        <a href="https://wa.me/918106811171" target="_blank" class="btn btn-success btn-sm w-100">
          <i class="bi bi-whatsapp me-1"></i>+91 81068 11171
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 rounded-3 h-100">
      <div class="card-body text-center p-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:56px;height:56px;background:#EA4335">
          <i class="bi bi-envelope-fill text-white" style="font-size:1.5rem"></i>
        </div>
        <h6 class="fw-semibold">Email</h6>
        <p class="text-muted mb-3" style="font-size:.85rem">Send us a detailed email for complex queries</p>
        <a href="mailto:contact@jnvtech.com" class="btn btn-danger btn-sm w-100">
          <i class="bi bi-envelope me-1"></i>contact@jnvtech.com
        </a>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card border-0 rounded-3 h-100">
      <div class="card-body text-center p-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:56px;height:56px;background:#1e40af">
          <i class="bi bi-clock-fill text-white" style="font-size:1.5rem"></i>
        </div>
        <h6 class="fw-semibold">Support Hours</h6>
        <p class="text-muted mb-3" style="font-size:.85rem">We're available during business hours</p>
        <div class="text-start" style="font-size:.8rem">
          <div class="d-flex justify-content-between mb-1"><span>Mon - Sat</span><strong>9:00 AM - 7:00 PM</strong></div>
          <div class="d-flex justify-content-between"><span>Sunday</span><strong class="text-muted">Closed</strong></div>
        </div>
      </div>
    </div>
  </div>

  <!-- About & Quick Links -->
  <div class="col-lg-7">
    <div class="card border-0 rounded-3 h-100">
      <div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-info-circle me-2"></i>About JNV Tech</h6></div>
      <div class="card-body">
        <p style="font-size:.9rem;line-height:1.7">
          <strong>JNV Tech</strong> (Journey to New Value) is a technology company specializing in building modern, efficient, and user-friendly school management solutions. Our mission is to simplify school administration through innovative software that empowers educators and institutions.
        </p>
        <p style="font-size:.9rem;line-height:1.7">
          The <strong>JSchoolAdmin</strong> system provides comprehensive tools for student management, teacher coordination, attendance tracking, exam management, gallery hosting, event scheduling, and much more — all in a single integrated platform designed for ease of use.
        </p>
        <hr>
        <div class="row g-2 text-center">
          <div class="col-4">
            <div class="bg-light rounded-3 p-3">
              <i class="bi bi-mortarboard-fill text-primary" style="font-size:1.5rem"></i>
              <div class="fw-semibold mt-1" style="font-size:.8rem">Student Mgmt</div>
            </div>
          </div>
          <div class="col-4">
            <div class="bg-light rounded-3 p-3">
              <i class="bi bi-shield-check text-success" style="font-size:1.5rem"></i>
              <div class="fw-semibold mt-1" style="font-size:.8rem">Secure & Fast</div>
            </div>
          </div>
          <div class="col-4">
            <div class="bg-light rounded-3 p-3">
              <i class="bi bi-phone text-info" style="font-size:1.5rem"></i>
              <div class="fw-semibold mt-1" style="font-size:.8rem">Mobile Ready</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card border-0 rounded-3 mb-3">
      <div class="card-header bg-white border-0"><h6 class="fw-semibold mb-0"><i class="bi bi-lightning me-2"></i>Quick Links</h6></div>
      <div class="card-body p-0">
        <a href="https://wa.me/918106811171?text=Hi%2C%20I%20need%20help%20with%20JSchoolAdmin" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 px-3 py-3">
          <i class="bi bi-chat-dots text-success" style="font-size:1.1rem"></i>
          <div><div class="fw-semibold" style="font-size:.85rem">Report a Bug</div><small class="text-muted">Send us a message on WhatsApp</small></div>
          <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="https://wa.me/918106811171?text=Hi%2C%20I%20want%20to%20request%20a%20new%20feature" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 px-3 py-3">
          <i class="bi bi-lightbulb text-warning" style="font-size:1.1rem"></i>
          <div><div class="fw-semibold" style="font-size:.85rem">Request a Feature</div><small class="text-muted">Tell us what you need</small></div>
          <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </a>
        <a href="mailto:contact@jnvtech.com?subject=JSchoolAdmin%20Support" class="list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 px-3 py-3">
          <i class="bi bi-envelope text-primary" style="font-size:1.1rem"></i>
          <div><div class="fw-semibold" style="font-size:.85rem">Email Support</div><small class="text-muted">For detailed inquiries</small></div>
          <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </a>
      </div>
    </div>

    <!-- Version Info -->
    <div class="card border-0 rounded-3">
      <div class="card-body text-center p-4">
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2" style="font-size:.85rem">v2.0</span>
        <p class="text-muted mb-0" style="font-size:.75rem">JSchoolAdmin — Built with ❤️ by JNV Tech</p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__.'/../includes/footer.php';?>

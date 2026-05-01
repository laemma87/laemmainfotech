<?php
include 'includes/header.php';

$service_key = $_GET['id'] ?? '';

$services = [
    'software-development' => [
        'title' => 'Software Development',
        'icon' => 'fa-laptop-code',
        'desc' => 'We create robust, scalable, and high-performance software solutions tailored to your business needs.',
        'features' => ['Custom Business Applications', 'Mobile App Development', 'System Integration', 'Legacy Code Modernization']
    ],
    'database-analysis' => [
        'title' => 'Database Analysis',
        'icon' => 'fa-database',
        'desc' => 'Professional database design, optimization, and analysis to ensure your data is secure and accessible.',
        'features' => ['Architecture Design', 'Query Optimization', 'Data Migration', 'Security Audits']
    ],
    'computer-maintenance' => [
        'title' => 'Computer Maintenance',
        'icon' => 'fa-tools',
        'desc' => 'Keep your hardware in top shape with our professional maintenance and repair services.',
        'features' => ['Hardware Repair', 'Preventive Maintenance', 'OS Installation', 'Diagnostics']
    ],
    'cyber-security' => [
        'title' => 'Cyber Security',
        'icon' => 'fa-shield-alt',
        'desc' => 'Protect your digital assets with our advanced cybersecurity solutions and monitoring.',
        'features' => ['Penetration Testing', 'Security Awareness', 'Firewall Management', 'Incident Response']
    ],
    'website-development' => [
        'title' => 'Website Development',
        'icon' => 'fa-globe',
        'desc' => 'Stunning, responsive, and SEO-friendly websites that help your brand stand out online.',
        'features' => ['Responsive Design', 'E-commerce Solutions', 'CMS Integration', 'UI/UX Design']
    ],
    'website-deployment' => [
        'title' => 'Website Deployment',
        'icon' => 'fa-cloud-upload-alt',
        'desc' => 'Reliable hosting and deployment services to ensure your website is always live and fast.',
        'features' => ['Cloud Hosting', 'Domain Registration', 'SSL Certificates', 'Performance Monitoring']
    ]
];

$service = $services[$service_key] ?? null;
?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container">
        <?php if ($service): ?>
            <!-- Detail View -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
                <div>
                    <nav style="margin-bottom: 20px; color: var(--text-muted);">
                        <a href="index.php">Home</a> / <a href="service.php">Services</a> / <?php echo $service['title']; ?>
                    </nav>
                    <div class="icon-box" style="width: 80px; height: 80px; border-radius: 20px; font-size: 2.5rem; background: var(--gradient); color: white; margin-bottom: 30px;">
                        <i class="fas <?php echo $service['icon']; ?>"></i>
                    </div>
                    <h1 style="font-size: 3.5rem; line-height: 1.1; margin-bottom: 25px;"><?php echo $service['title']; ?></h1>
                    <p style="font-size: 1.25rem; color: var(--text-muted); line-height: 1.8; margin-bottom: 40px;">
                        <?php echo $service['desc']; ?>
                    </p>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <?php foreach ($service['features'] as $feature): ?>
                            <div style="display: flex; align-items: center; gap: 15px; background: var(--glass); padding: 15px; border-radius: 12px; border: 1px solid var(--glass-border);">
                                <i class="fas fa-check-circle" style="color: var(--primary);"></i>
                                <span style="font-weight: 600; font-size: 0.9rem;"><?php echo $feature; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top: 50px;">
                        <a href="#inquiry-form" class="btn-primary" style="padding: 15px 40px; text-decoration: none;">Send Inquiry for this Service</a>
                    </div>
                </div>

                <div style="background: var(--glass); border: 1px solid var(--glass-border); border-radius: 30px; padding: 20px; position: sticky; top: 120px;">
                    <img src="https://via.placeholder.com/600x400?text=<?php echo urlencode($service['title']); ?>" style="width: 100%; border-radius: 20px; filter: grayscale(50%); transition: 0.5s;" onmouseover="this.style.filter='grayscale(0%)'" onmouseout="this.style.filter='grayscale(50%)'">
                    
                    <div id="inquiry-form" style="margin-top: 40px; padding: 30px; background: rgba(0,0,0,0.2); border-radius: 20px;">
                        <h3 style="margin-bottom: 20px;">Interested in <span style="color: var(--primary);"><?php echo $service['title']; ?></span>?</h3>
                        
                        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'sent'): ?>
                            <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88; font-size: 0.9rem;">
                                <i class="fas fa-check-circle"></i> Your inquiry for <?php echo $service['title']; ?> has been sent! We will contact you soon.
                            </div>
                        <?php endif; ?>

                        <form action="contact_process.php" method="POST">
                            <input type="hidden" name="interest" value="<?php echo $service['title']; ?>">
                            <input type="hidden" name="redirect" value="service.php?id=<?php echo $service_key; ?>&msg=sent#inquiry-form">
                            <div class="form-group" style="margin-bottom: 20px;">
                                <input type="text" name="name" placeholder="Full Name" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <input type="email" name="email" placeholder="Email Address" required style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                            </div>
                            <div class="form-group" style="margin-bottom: 20px;">
                                <textarea name="message" placeholder="Tell us about your requirements for <?php echo strtolower($service['title']); ?>..." rows="4" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;"></textarea>
                            </div>
                            <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; border-radius: 50px;">Send Requirement</button>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Catalog View -->
            <div class="section-title">
                <nav style="margin-bottom: 20px; color: var(--text-muted); display: block; text-align: left;">
                    <a href="index.php">Home</a> / Web Development Solutions
                </nav>
                <h1 style="font-size: 3.5rem; margin-bottom: 20px;">Our <span>Specialties</span></h1>
                <p style="color: var(--text-muted); font-size: 1.2rem;">Choose a specific field to see more details and send us your requirements.</p>
            </div>

            <div class="grid-4">
                <?php foreach ($services as $key => $s): ?>
                    <div class="card" style="display: flex; flex-direction: column; padding: 30px;">
                        <div class="icon-box" style="margin-bottom: 20px;">
                            <i class="fas <?php echo $s['icon']; ?>"></i>
                        </div>
                        <h3 style="font-size: 1.3rem; margin-bottom: 10px;"><?php echo $s['title']; ?></h3>
                        <p style="font-size: 0.9rem; margin-bottom: 20px; opacity: 0.8;"><?php echo substr($s['desc'], 0, 80); ?>...</p>
                        <a href="service.php?id=<?php echo $key; ?>" class="btn-primary" style="margin-top: auto; padding: 10px 20px; font-size: 0.9rem;">
                            View Details <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

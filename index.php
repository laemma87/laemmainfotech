<?php 
include 'includes/db.php';
include 'includes/header.php'; 

// Fetch Latest 3 Blogs
try {
    $latest_blogs = $pdo->query("SELECT * FROM blogs WHERE status='published' ORDER BY created_at DESC LIMIT 3")->fetchAll();
} catch (Exception $e) {
    $latest_blogs = [];
}

// Fetch Partners
try {
    $partners = $pdo->query("SELECT * FROM partners ORDER BY name ASC")->fetchAll();
} catch (Exception $e) {
    $partners = [];
}
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; text-align: left;">
                <div>
                    <h1>LAEMMA <span>INFO TECH</span></h1>
                    <p style="margin: 0;">Leading IT solutions provider in Rusizi District. Specialized in Software Development, Hybrid Platforms, and Professional Internships.</p>
                    <div class="hero-btns" style="margin-top: 40px;">
                        <a href="login.php" class="btn-login">Get Started (Login)</a>
                        <a href="#services" class="btn-secondary" style="margin-left: 20px; border: 1px solid var(--primary); padding: 10px 25px; border-radius: 50px;">Our Services</a>
                    </div>
                </div>
                <!-- Student Gallery -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; background: rgba(26, 21, 77, 0.4); padding: 20px; border-radius: 30px; border: 1px solid var(--border); backdrop-filter: blur(10px);">
                    <img src="assets/images/p1.png" style="width: 100%; border-radius: 15px; border: 2px solid var(--primary);">
                    <img src="assets/images/p2.png" style="width: 100%; border-radius: 15px; border: 2px solid var(--secondary);">
                    <img src="assets/images/p3.png" style="width: 100%; border-radius: 15px; border: 2px solid var(--secondary);">
                    <div style="background: var(--gradient); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; color: white;">50+ Pros</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Stats Section -->
    <section class="container">
        <div class="stats-bar">
            <div class="stat-item">
                <h3>50+</h3>
                <p>Projects Completed</p>
            </div>
            <div class="stat-item">
                <h3>10+</h3>
                <p>Happy Clients</p>
            </div>
            <div class="stat-item">
                <h3>2+</h3>
                <p>Years Experience</p>
            </div>
            <div class="stat-item">
                <h3>4+</h3>
                <p>Team Members</p>
            </div>
        </div>
    </section>

    <!-- Service Grid -->
    <section id="services" class="container" style="padding: 100px 0;">
        <div class="section-title">
            <h2>Our <span>Services</span></h2>
            <p style="color: var(--text-muted);">Innovating technology solutions for a digital future.</p>
        </div>
        
        <div class="grid-4">
            <!-- Web Development -->
            <div class="card" style="display: flex; flex-direction: column;">
                <div class="icon-box">
                    <i class="fas fa-code"></i>
                </div>
                <h3>Web Development</h3>
                <p>Enterprise-grade digital solutions tailored for your business growth.</p>
                <div class="service-tags">
                    <a href="service.php?id=software-development"><i class="fas fa-code"></i> Software</a>
                    <a href="service.php?id=database-analysis"><i class="fas fa-database"></i> Databases</a>
                    <a href="service.php?id=cyber-security"><i class="fas fa-lock"></i> Security</a>
                    <a href="service.php?id=website-development"><i class="fas fa-laptop-code"></i> Web Design</a>
                    <a href="service.php?id=website-deployment"><i class="fas fa-cloud-upload-alt"></i> Deployment</a>
                    <a href="service.php?id=computer-maintenance"><i class="fas fa-tools"></i> Maintenance</a>
                </div>
                <a href="service.php" class="btn-primary" style="margin-top: auto;">Explore More <i class="fas fa-arrow-right"></i></a>
            </div>

            <!-- Internship Services -->
            <div class="card" style="display: flex; flex-direction: column;">
                <div class="icon-box">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Internship Prep</h3>
                <p>Kickstart your career with our 2-month intensive professional training programs.</p>
                <div style="margin: 20px 0; font-weight: 800; color: var(--primary); font-size: 1.2rem;">
                    20,000 RWF <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400;">/ Month</span>
                </div>
                <a href="internship/apply.php" class="btn-primary" style="margin-top: auto;">Apply Now <i class="fas fa-pen-nib"></i></a>
            </div>

            <!-- Our Products -->
            <div class="card" style="display: flex; flex-direction: column;">
                <div class="icon-box">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h3>Premium Store</h3>
                <p>High-quality laptops, electronics, and professional tech accessories at best prices.</p>
                <div style="margin: 20px 0; display: flex; gap: 5px; color: #ffca28;">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                </div>
                <a href="shop/index.php" class="btn-primary" style="margin-top: auto;">Browse Store <i class="fas fa-shopping-bag"></i></a>
            </div>

            <!-- Marketing Services -->
            <div class="card" style="display: flex; flex-direction: column;">
                <div class="icon-box">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3>Digital Growth</h3>
                <p>Boost your brand visibility with professional advertising and media production.</p>
                <div class="service-tags">
                    <span style="background: rgba(0, 132, 255, 0.1); color: var(--primary); padding: 5px 12px; border-radius: 5px; font-size: 0.8rem;">Ad Partnerships</span>
                    <span style="background: rgba(0, 132, 255, 0.1); color: var(--primary); padding: 5px 12px; border-radius: 5px; font-size: 0.8rem;">Video Production</span>
                </div>
                <a href="#contact" class="btn-primary" style="margin-top: auto;">Partner With Us <i class="fas fa-handshake"></i></a>
            </div>
        </div>
    </section>

    <!-- Latest News Section -->
    <section id="news" class="container" style="padding: 100px 0;">
        <div class="section-title">
            <h2>Latest <span>News</span></h2>
            <p style="color: var(--text-muted);">Updates from our team and the tech world.</p>
        </div>

        <div class="grid-3" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <?php foreach ($latest_blogs as $b): ?>
                <article style="background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; overflow: hidden; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <a href="single_blog.php?slug=<?php echo $b['slug']; ?>" style="text-decoration: none; color: inherit;">
                        <div style="height: 200px; overflow: hidden;">
                            <?php if ($b['image']): ?>
                                <img src="<?php echo htmlspecialchars($b['image']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: var(--glass-border); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 25px;">
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 10px;">
                                <i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($b['created_at'])); ?>
                            </div>
                            <h3 style="font-size: 1.1rem; margin-bottom: 15px; line-height: 1.4;"><?php echo htmlspecialchars($b['title']); ?></h3>
                            <span style="color: var(--primary); font-size: 0.9rem; font-weight: 600;">Read More <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($latest_blogs) === 0): ?>
            <div style="text-align: center; color: var(--text-muted);">No news updates available.</div>
        <?php else: ?>
            <div style="text-align: center; margin-top: 40px;">
                <a href="blog.php" class="btn-secondary" style="border: 1px solid var(--glass-border); padding: 10px 30px; border-radius: 50px; color: white;">View All News</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Team Section -->
    <section id="team" class="container" style="padding: 100px 0; background: rgba(26, 21, 77, 0.3); border-radius: 32px; border: 1px solid var(--border);">
        <div class="section-title">
            <h2>Our <span>Team</span></h2>
        </div>
        
        <div class="grid-4" style="text-align: center;">
            <div class="team-card">
                <div style="width: 150px; height: 150px; border-radius: 50%; background: var(--purple-gradient); margin: 0 auto 20px; overflow: hidden; border: 4px solid var(--border);">
                    <img src="assets/images/lae.jpg" alt="CEO" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/150'">
                </div>
                <h4>HAKIZIMANA Emmanuel</h4>
                <p style="color: var(--primary); font-weight: 600;">CEO & Founder</p>
                <div class="social-links" style="justify-content: center;">
                    <a href="https://www.linkedin.com/in/emmanuel-hakizimana-0116653b0"><i class="fab fa-linkedin"></i></a>
                    <a href="laemma50@gmail.com"><i class="fas fa-envelope"></i></a>
                    <a href="https://instagram.com/laemma87" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="team-card">
                <div style="width: 150px; height: 150px; border-radius: 50%; background: var(--glass); margin: 0 auto 20px; overflow: hidden; border: 4px solid var(--glass-border);">
                    <img src="assets/images/mnelson.jpeg" alt="CEO" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/150'">
                </div>
                <h4>MUNYANEZA Nelson</h4>
                <p style="color: var(--text-muted);">Web Specialist</p>
                                <div class="social-links" style="justify-content: center;">
                    <a href="https://www.linkedin.com/in/munyaneza-nelson-8250403b0/"><i class="fab fa-linkedin"></i></a>
                    <a href="nelsonbussiness07@gmail.com"><i class="fas fa-envelope"></i></a>
                    <a href="https://www.instagram.com/mnelson079" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="team-card">
                <div style="width: 150px; height: 150px; border-radius: 50%; background: var(--glass); margin: 0 auto 20px; overflow: hidden; border: 4px solid var(--glass-border);">
                    <img src="assets/images/tyga.jpg" alt="CEO" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/150'">
                </div>
                <h4>HAGENIMANA Erneste</h4>
                <p style="color: var(--text-muted);">Database Analyst</p>
            </div>

            <div class="team-card">
                <div style="width: 150px; height: 150px; border-radius: 50%; background: var(--glass); margin: 0 auto 20px; overflow: hidden; border: 4px solid var(--glass-border);">
                    <img src="assets/images/fel.jpg" alt="CEO" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/150'">
                </div>
                <h4>IRAGENA Felicien</h4>
                <p style="color: var(--text-muted);">Network Support</p>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faqs" class="container" style="padding: 100px 0;">
        <div class="section-title">
            <h2>Common <span>FAQs</span></h2>
        </div>
        
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="faq-item" style="background: rgba(26, 21, 77, 0.4); border: 1px solid var(--border); border-radius: 20px; padding: 20px; margin-bottom: 15px;">
                <h4 style="cursor: pointer;"><i class="fas fa-plus-circle" style="color: var(--primary); margin-right: 10px;"></i> How can I partner with Laemma?</h4>
            </div>
            <div class="faq-item" style="background: rgba(26, 21, 77, 0.4); border: 1px solid var(--border); border-radius: 20px; padding: 20px; margin-bottom: 15px;">
                <h4 style="cursor: pointer;"><i class="fas fa-plus-circle" style="color: var(--primary); margin-right: 10px;"></i> Where is your office located?</h4>
            </div>
            <div class="faq-item" style="background: rgba(26, 21, 77, 0.4); border: 1px solid var(--border); border-radius: 20px; padding: 20px; margin-bottom: 15px;">
                <h4 style="cursor: pointer;"><i class="fas fa-plus-circle" style="color: var(--primary); margin-right: 10px;"></i> What do I need for the internship?</h4>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section id="partners" class="container" style="padding: 100px 0;">
        <div class="section-title">
            <h2>Trusted <span>Partners</span></h2>
            <p style="color: var(--text-muted);">We work with the best to deliver excellence.</p>
        </div>
        
        <div class="grid-4" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            <?php foreach ($partners as $p): ?>
                <div style="background: var(--glass); padding: 25px; border-radius: 20px; border: 1px solid var(--glass-border); text-align: center; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div style="width: 80px; height: 80px; margin: 0 auto 15px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
                        <?php if ($p['logo']): ?>
                            <img src="<?php echo htmlspecialchars($p['logo']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <?php else: ?>
                            <i class="fas fa-handshake" style="font-size: 2rem; color: var(--darker);"></i>
                        <?php endif; ?>
                    </div>
                    <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($p['name']); ?></h4>
                    <div style="margin-bottom: 15px;">
                        <?php 
                        $color = '#00ff88';
                        $icon = 'check-circle';
                        if($p['status']=='Maintenance') { $color = '#ffa502'; $icon = 'tools'; }
                        if($p['status']=='Development') { $color = '#ff4757'; $icon = 'code'; }
                        ?>
                        <span style="color: <?php echo $color; ?>; font-size: 0.8rem; font-weight: bold;">
                            <i class="fas fa-<?php echo $icon; ?>"></i> <?php echo $p['status']; ?>
                        </span>
                    </div>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        <?php echo htmlspecialchars($p['description']); ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($p['website_url']); ?>" target="_blank" class="btn-sm" style="background: rgba(255,255,255,0.05); color: white; text-decoration: none; border: 1px solid var(--glass-border); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem;">Visit Website</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="container" style="padding: 100px 0;">
        <div class="section-title">
            <h2>Contact <span>Us</span></h2>
            <p style="color: var(--text-muted);">Get in touch for any inquiries or support.</p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px;">
            <div style="background: rgba(26, 21, 77, 0.4); padding: 40px; border-radius: 30px; border: 1px solid var(--border);">
                <form action="contact_process.php" method="POST">
                    <div style="margin-bottom: 20px;">
                        <label>Your Name</label>
                        <input type="text" name="name" required style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; margin-top: 5px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label>Email Address</label>
                        <input type="email" name="email" required style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; margin-top: 5px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label>Interest</label>
                        <select name="interest" style="width: 100%; padding: 15px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white; margin-top: 5px;">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Software Development">Software Development</option>
                            <option value="Internship">Internship Program</option>
                            <option value="Products">Product Inquiry</option>
                            <option value="Partnership">Partnership</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label>Message</label>
                        <textarea name="message" required rows="5" style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; margin-top: 5px; resize: vertical;"></textarea>
                    </div>
                    <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; border-radius: 50px;">Send Message</button>
                    
                    <?php if(isset($_GET['msg']) && $_GET['msg']=='sent'): ?>
                        <div style="margin-top: 15px; color: #00ff88; text-align: center;">Message sent successfully!</div>
                    <?php endif; ?>
                </form>
            </div>
            
            <div style="display: flex; flex-direction: column; justify-content: center;">
                <h3 style="margin-bottom: 30px;">Let's discuss your project</h3>
                <div style="display: flex; gap: 20px; mb-30px; align-items: center; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: rgba(0, 132, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0;">Email Us</h4>
                        <p style="color: var(--text-muted); margin: 5px 0;">laemma50@gmail.com</p>
                    </div>
                </div>
                <div style="display: flex; gap: 20px; mb-30px; align-items: center; margin-bottom: 30px;">
                    <div style="width: 60px; height: 60px; background: rgba(0, 132, 255, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0;">Call Us</h4>
                        <p style="color: var(--text-muted); margin: 5px 0;">+250 789 011 738</p>
                    </div>
                </div>
                <!-- Social Media -->
                <div style="margin-top: 20px;">
                    <h4>Follow Us</h4>
                    <div style="display: flex; gap: 15px; margin-top: 15px;">
                        <a href="https://x.com/laemma87" style="color: white; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/in/emmanuel-hakizimana-0116653b0" style="color: white; font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                        <a href="https://instagram.com/laemma87" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="https://facebook.com/laemma250" style="color: white; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

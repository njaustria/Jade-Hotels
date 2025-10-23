<?php include 'admin/db_connect.php'; ?>
<style>
.section-heading-about { color: #000; font-size: 2.8rem; font-weight: 700; font-family: 'Merriweather', serif; margin-bottom: 30px; letter-spacing: -0.5px; }
.team-heading { color: #000; font-size: 2.5rem; font-weight: 700; font-family: 'Merriweather', serif; margin-bottom: 15px; }
.team-subtitle { font-size: 1.2rem; color: #666; margin-bottom: 0; }
.team-card { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; }
.team-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
.team-image { position: relative; overflow: hidden; height: 300px; }
.team-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
.team-card:hover .team-image img { transform: scale(1.05); }
.team-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(240, 95, 64, 0.9); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
.team-card:hover .team-overlay { opacity: 1; }
.social-links a { color: white; font-size: 1.5rem; margin: 0 10px; transition: transform 0.3s ease; }
.social-links a:hover { transform: scale(1.2); color: white; }
.team-info { padding: 1.5rem; text-align: center; }
.team-info h4 { font-size: 1.3rem; font-weight: 600; margin-bottom: 0.5rem; color: #000; }
.position { color: #f05f40; font-weight: 500; font-size: 1rem; margin-bottom: 1rem; }
.bio { color: #666; font-size: 0.9rem; line-height: 1.5; margin-bottom: 0; }
.no-team-placeholder { text-align: center; padding: 4rem 0; color: #666; }
.no-team-placeholder i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
.masthead { background: linear-gradient(rgba(240, 95, 64, 0.8), rgba(240, 95, 64, 0.8)), url('assets/img/hotel-bg.jpg') center center/cover; color: white; }
.page-section { padding: 4rem 0; }
.team-section { padding: 4rem 0; background: #f8f9fa; }

@media (max-width: 768px) {
    .section-heading-about { font-size: 2.5rem; }
    .team-heading { font-size: 2rem; }
    .team-subtitle { font-size: 1rem; }
    .team-image { height: 250px; }
    .team-info { padding: 1rem; }
    .masthead { padding: 3rem 0; }
    .page-section { padding: 3rem 0; }
    .team-section { padding: 3rem 0; }
}
</style>

<section class="page-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-heading-about">About Jade Hotels</h2>
            <h2 class="section-heading-about">Our story, values, and the dedicated team behind your exceptional experience</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php 
                // Get about content from database settings
                if(isset($_SESSION['setting_about_content']) && !empty($_SESSION['setting_about_content'])) {
                    echo html_entity_decode($_SESSION['setting_about_content']);
                } else {
                    // Default content if none set in database
                    echo '<div class="text-center">
                            <p class="lead mb-4">At Jade Hotel, we believe in creating unforgettable experiences that blend luxury, comfort, and personalized service. Since our establishment, we have been committed to providing our guests with exceptional hospitality in an elegant and welcoming environment.</p>
                            <p class="mb-4">Our dedication to excellence is reflected in every aspect of our service, from our meticulously designed rooms and suites to our world-class amenities and dining options. We take pride in our attention to detail and our commitment to making every stay memorable.</p>
                            <p class="mb-0">Whether you\'re traveling for business or leisure, our team is here to ensure that your experience with us exceeds your expectations. Welcome to Jade Hotel – where luxury meets comfort, and every guest is treated like family.</p>
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>

<section class="team-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="team-heading">Meet Our Leadership Team</h2>
            <p class="team-subtitle">Dedicated professionals committed to your exceptional experience</p>
        </div>
        <div class="row">
            <?php 
            // Get team members from database instead of static array
            $team_query = $conn->query("SELECT * FROM team_members WHERE status = 1 ORDER BY display_order ASC, id ASC");
            
            if($team_query && $team_query->num_rows > 0):
                while($member = $team_query->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-image">
                            <?php 
                            $image_path = !empty($member['image']) ? 'assets/img/team/'.$member['image'] : 'assets/img/team/default-avatar.jpg';
                            ?>
                            <img src="<?php echo $image_path; ?>" 
                                 alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                 class="img-fluid"
                                 onerror="this.src='assets/img/team/default-avatar.jpg'">
                            <div class="team-overlay">
                                <div class="social-links">
                                    <?php if(!empty($member['linkedin_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($member['linkedin_url']); ?>" target="_blank" rel="noopener">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(!empty($member['twitter_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($member['twitter_url']); ?>" target="_blank" rel="noopener">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(empty($member['linkedin_url']) && empty($member['twitter_url'])): ?>
                                    <a href="#" target="_blank" rel="noopener">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                    <a href="#" target="_blank" rel="noopener">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="team-info">
                            <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                            <p class="position"><?php echo htmlspecialchars($member['position']); ?></p>
                            <p class="bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endwhile;
            else: ?>
                <!-- Fallback to static data if no team members in database -->
                <?php 
                $static_team = [
                    [
                        'img' => 'ceo.jpg', 
                        'name' => 'Jade Tolentino', 
                        'position' => 'Chief Executive Officer', 
                        'bio' => 'Leading Jade Hotel with 15+ years of hospitality excellence and vision for luxury service.'
                    ],
                    [
                        'img' => 'co-ceo.jpg', 
                        'name' => 'Aldrich Jobog', 
                        'position' => 'Co-Chief Executive Officer', 
                        'bio' => 'Strategic operations leader focused on innovation and sustainable hospitality practices.'
                    ],
                    [
                        'img' => 'manager.jpg', 
                        'name' => 'Neian Austria', 
                        'position' => 'General Manager', 
                        'bio' => 'Ensuring seamless daily operations and exceptional guest experiences across all departments.'
                    ]
                ];
                
                foreach($static_team as $member): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="team-card">
                        <div class="team-image">
                            <img src="assets/img/team/<?php echo $member['img']; ?>" 
                                 alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                 class="img-fluid"
                                 onerror="this.src='assets/img/team/default-avatar.jpg'">
                            <div class="team-overlay">
                                <div class="social-links">
                                    <a href="#" target="_blank" rel="noopener">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                    <a href="#" target="_blank" rel="noopener">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="team-info">
                            <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                            <p class="position"><?php echo htmlspecialchars($member['position']); ?></p>
                            <p class="bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
// Add smooth scrolling and animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate team cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Apply animation to team cards
    const teamCards = document.querySelectorAll('.team-card');
    teamCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>
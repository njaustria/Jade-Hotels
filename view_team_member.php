<?php
include 'db_connect.php';
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM team_members WHERE id = ".$_GET['id']);
    if($qry->num_rows > 0){
        $member = $qry->fetch_array();
    }
}
?>

<div class="container-fluid">
    <?php if(isset($member)): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="text-center">
                <?php if(!empty($member['image'])): ?>
                    <img src="../assets/img/team/<?php echo $member['image'] ?>" alt="<?php echo $member['name'] ?>" class="img-fluid img-thumbnail" style="max-height: 300px; border-radius: 10px;">
                <?php else: ?>
                    <div style="width: 200px; height: 200px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="fa fa-user fa-5x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="team-member-details">
                <h3 class="mb-3"><?php echo $member['name'] ?></h3>
                <p class="position mb-3" style="color: #f05f40; font-weight: 600; font-size: 1.1rem;">
                    <?php echo $member['position'] ?>
                </p>
                <div class="bio mb-4">
                    <h5>Biography</h5>
                    <p style="line-height: 1.6; color: #666;">
                        <?php echo nl2br($member['bio']) ?>
                    </p>
                </div>
                
                <div class="member-info">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Display Order:</strong> <?php echo $member['display_order'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <?php if($member['status'] == 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactive</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if(!empty($member['linkedin_url']) || !empty($member['twitter_url'])): ?>
                    <div class="social-links mt-3">
                        <h6>Social Media</h6>
                        <?php if(!empty($member['linkedin_url'])): ?>
                            <a href="<?php echo $member['linkedin_url'] ?>" target="_blank" class="btn btn-outline-primary btn-sm mr-2">
                                <i class="fab fa-linkedin"></i> LinkedIn
                            </a>
                        <?php endif; ?>
                        <?php if(!empty($member['twitter_url'])): ?>
                            <a href="<?php echo $member['twitter_url'] ?>" target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="member-dates mt-4">
                    <small class="text-muted">
                        <strong>Created:</strong> <?php echo date('M d, Y h:i A', strtotime($member['date_created'])) ?><br>
                        <strong>Last Updated:</strong> <?php echo date('M d, Y h:i A', strtotime($member['date_updated'])) ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> Team member not found.
    </div>
    <?php endif; ?>
</div>

<style>
.team-member-details h3 {
    color: #000;
    font-weight: 700;
}

.position {
    color: #f05f40 !important;
}

.bio {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #f05f40;
}

.member-info {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.social-links a {
    text-decoration: none;
}

.social-links a:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>
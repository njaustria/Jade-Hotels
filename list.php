<?php
$date_in = date('Y-m-d');
$date_out = date('Y-m-d', strtotime('+3 days'));
?>

<style>
.item-rooms img { width: 100%; height: 200px; object-fit: cover; }
.item-rooms { transition: transform 0.2s ease-in-out; border: none; }
.item-rooms:hover { transform: translateY(-5px); }
.section-heading { color: #000; font-size: 2.8rem; font-weight: 700; font-family: 'Merriweather', serif; margin-bottom: 20px; }
.divider { border-top: 3px solid #007bff; width: 60px; margin: 0 auto; }
.book_now { transition: all 0.3s ease; }
.book_now:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3); }
@media (max-width: 768px) {
    .item-rooms img { height: 150px; margin-bottom: 15px; }
    .col-md-2 { margin-top: 15px; }
}
</style>

<section class="page-section bg-light py-5">
    <div class="container">	
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5">
                    <h1 class="section-heading">Our Available Rooms</h1>
                    <hr class="divider my-4">
                </div>
                
                <?php 
                $cat = $conn->query("SELECT * FROM room_categories");
                $cat_arr = array();
                while($row = $cat->fetch_assoc()) $cat_arr[$row['id']] = $row;
                
                $qry = $conn->query("SELECT distinct(category_id),category_id from rooms where id not in (SELECT room_id from checked where '$date_in' BETWEEN date(date_in) and date(date_out) and '$date_out' BETWEEN date(date_in) and date(date_out))");
                
                if($qry->num_rows > 0):
                    while($row = $qry->fetch_assoc()):
                        $cat = $cat_arr[$row['category_id']];
                ?>
                <div class="card item-rooms mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">   
                            <div class="col-md-4">
                                <img src="assets/img/<?php echo $cat['cover_img'] ?>" alt="<?php echo $cat['name'] ?>" class="img-fluid rounded">
                            </div>
                            <div class="col-md-6">
                                <h3 class="text-primary mb-2"><b>₱ <?php echo number_format($cat['price'],2) ?></b></h3>
                                <h4 class="mb-3"><b><?php echo $cat['name'] ?></b></h4>
                                <p class="text-muted mb-0"><i class="fas fa-clock"></i> Check-in: 12:00 PM | Check-out: 12:00 PM</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <button class="btn btn-primary btn-lg book_now" type="button" data-id="<?php echo $row['category_id'] ?>">Book Now</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <div class="alert alert-info text-center" role="alert">
                    <h4 class="alert-heading">No Available Rooms</h4>
                    <p>Sorry, there are no available rooms for the selected dates. Please try different dates.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>	
    </div>	
</section>

<script>
$('.book_now').click(function(){
    uni_modal('Book','admin/book.php?in=<?php echo $date_in ?>&out=<?php echo $date_out ?>&cid='+$(this).attr('data-id'))
})
</script>
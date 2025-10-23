<?php
include 'db_connect.php';
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM team_members WHERE id = ".$_GET['id']);
    foreach($qry->fetch_array() as $k => $val){
        $$k = $val;
    }
}
?>
<div class="container-fluid">
    <form action="" id="manage-team-member">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="position" class="control-label">Position</label>
            <input type="text" class="form-control" id="position" name="position" value="<?php echo isset($position) ? $position : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="bio" class="control-label">Bio</label>
            <textarea rows="4" name="bio" id="bio" class="form-control" required><?php echo isset($bio) ? $bio : '' ?></textarea>
        </div>
        <div class="form-group">
            <label for="linkedin_url" class="control-label">LinkedIn URL (Optional)</label>
            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" value="<?php echo isset($linkedin_url) ? $linkedin_url : '' ?>" placeholder="https://linkedin.com/in/username">
        </div>
        <div class="form-group">
            <label for="twitter_url" class="control-label">Twitter URL (Optional)</label>
            <input type="url" class="form-control" id="twitter_url" name="twitter_url" value="<?php echo isset($twitter_url) ? $twitter_url : '' ?>" placeholder="https://twitter.com/username">
        </div>
        <div class="form-group">
            <label for="display_order" class="control-label">Display Order</label>
            <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo isset($display_order) ? $display_order : 0 ?>" min="0">
            <small class="form-text text-muted">Lower numbers appear first</small>
        </div>
        <div class="form-group">
            <label for="image" class="control-label">Image</label>
            <input type="file" class="form-control" name="image" id="image" onchange="displayImg(this,$(this))" accept="image/*">
        </div>
        <div class="form-group d-flex justify-content-center">
            <img src="<?php echo isset($image) && !empty($image) ? '../assets/img/team/'.$image : '' ?>" alt="" id="cimg" class="img-fluid img-thumbnail" style="max-height: 20vh; <?php echo !isset($image) || empty($image) ? 'display: none;' : '' ?>">
        </div>
    </form>
</div>

<style>
    img#cimg{
        max-height: 20vh;
        max-width: 15vw;
    }
</style>

<script>
    function displayImg(input,_this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#manage-team-member').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url:'ajax.php?action=save_team_member',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp == 1){
                    alert_toast("Data successfully saved",'success');
                    setTimeout(function(){
                        location.reload();
                    },1500);
                } else {
                    alert_toast("An error occurred",'error');
                    end_load();
                }
            }
        })
    })
</script>
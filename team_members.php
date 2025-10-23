<?php include 'db_connect.php' ?>
<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                
            </div>
        </div>
        <div class="row">
            <!-- FORM Panel -->
            <div class="col-md-4">
            <form action="" id="manage-team">
                <div class="card">
                    <div class="card-header">
                        Team Member Form
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="id">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Position</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Bio</label>
                            <textarea rows="3" name="bio" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">LinkedIn URL (Optional)</label>
                            <input type="url" class="form-control" name="linkedin_url" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Twitter URL (Optional)</label>
                            <input type="url" class="form-control" name="twitter_url" placeholder="https://twitter.com/username">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Image</label>
                            <input type="file" class="form-control" name="image" onchange="displayImg(this,$(this))" accept="image/*">
                        </div>
                        <div class="form-group text-center">
                            <img src="" alt="" id="cimg" style="max-height: 15vh; max-width: 10vw; display: none;">
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
                                <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="_reset()"> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
            <!-- FORM Panel -->

            <!-- Table Panel -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <b>Team Members List</b>
                        <span class="float:right">
                            <a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" id="new_team">
                                <i class="fa fa-plus"></i> New Entry
                            </a>
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-condensed table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="">Image</th>
                                    <th class="">Name</th>
                                    <th class="">Position</th>
                                    <th class="">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $team = $conn->query("SELECT * FROM team_members ORDER BY display_order ASC, id ASC");
                                while($row = $team->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++ ?></td>
                                    <td class="text-center">
                                        <?php if($row['image']): ?>
                                            <img src="../assets/img/team/<?php echo $row['image'] ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #ddd; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <p><b><?php echo $row['name'] ?></b></p>
                                    </td>
                                    <td>
                                        <p class="truncate"><?php echo $row['position'] ?></p>
                                    </td>
                                    <td class="text-center">
                                        <?php if($row['status'] == 1): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary view_team" type="button" data-id="<?php echo $row['id'] ?>">View</button>
                                        <button class="btn btn-sm btn-outline-primary edit_team" type="button" data-id="<?php echo $row['id'] ?>">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger delete_team" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                                        <?php if($row['status'] == 1): ?>
                                            <button class="btn btn-sm btn-outline-secondary toggle_status" type="button" data-id="<?php echo $row['id'] ?>" data-status="0">Deactivate</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-success toggle_status" type="button" data-id="<?php echo $row['id'] ?>" data-status="1">Activate</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Table Panel -->
        </div>
    </div>    
</div>

<style>
    img#cimg{
        max-height: 10vh;
        max-width: 6vw;
    }
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 200px;
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

    $('#new_team').click(function(){
        uni_modal("New Team Member","team_member_form.php")
    })

    $('.view_team').click(function(){
        uni_modal("Team Member Details","view_team_member.php?id="+$(this).attr('data-id'))
    })

    $('.edit_team').click(function(){
        uni_modal("Manage Team Member","team_member_form.php?id="+$(this).attr('data-id'))
    })

    $('.delete_team').click(function(){
        _conf("Are you sure to delete this team member?","delete_team",[$(this).attr('data-id')])
    })

    $('.toggle_status').click(function(){
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');
        var action = status == 1 ? 'activate' : 'deactivate';
        _conf("Are you sure to " + action + " this team member?", "toggle_status", [id, status])
    })

    function delete_team($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_team_member',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    }

    function toggle_status($id, $status){
        start_load()
        $.ajax({
            url:'ajax.php?action=toggle_team_member_status',
            method:'POST',
            data:{id:$id, status:$status},
            success:function(resp){
                if(resp==1){
                    alert_toast("Status updated successfully",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    }

    function _reset(){
        $('#manage-team').get(0).reset()
        $('#manage-team input,#manage-team textarea').val('')
        $('#cimg').hide()
    }

    $('#manage-team').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_team_member',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    })
</script>
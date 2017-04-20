<?php include('header.php');?>
    <div class="container">
        <h4>补全队员信息</h4>
        <hr />
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-info" role="alert" style="display: none;" id="bindplayer-alert">
                </div>

                <div class="input-group">
                    <span class="input-group-addon active">真实姓名</span>
                    <input type="text" class="form-control" value="" placeholder="Name" id="bindplayer-playername" required/>
                </div>
                <div class="input-group">
                    <span class="input-group-addon">所属小队</span>
                    <select class="form-control" id="bindplayer-groupname">
                        <?php
                        foreach($all_group as &$one)
                        {
                            echo '<option value="'. $one['group_id']  .'">' . $one['group_name'] . '</option>';
                        }
                        ?>
                    </select>                </div>
                <button class="btn btn-info btn-default pull-left" href="#" id="bindplayer-btn" onclick="submitBindplayer()">确认提交</button>
            </div>
        </div>

    </div>
    <script>

        function submitBindplayer()
        {
            var group_id=$('#bindplayer-groupname').val();
            var player_name=$('#bindplayer-playername').val();
            $('#bindplayer-btn').attr("disabled","disabled");
            $.post("<?php echo _Http;?>user/goBindPlayer","bindplayer-groupid=" + encodeURI(group_id) +
                "&bindplayer-playername="  + encodeURI(player_name)
                ,function(response){

                    if(response.status==1)
                    {
                        $("#bindplayer-alert").html("绑定成功！").show();
                    }
                    else
                    {
                        $('#bindplayer-btn').removeAttr("disabled");
                        $("#bindplayer-alert").html(decodeURI(response.message)).show();
                    }

                });
        }
    </script>
<?php include('footer.php');?>
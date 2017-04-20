<?php include('header.php');?>
    <div class="container">
        <h4>添加小队</h4>
        <hr />
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-info" role="alert" style="display: none;" id="addgroup-alert">
                </div>

                <div class="input-group">
                    <span class="input-group-addon active">小队名称</span>
                    <input type="text" class="form-control" value="" placeholder="Team name" id="addgroup-groupname" required/>
                </div>
                    <div class="input-group">
                        <span class="input-group-addon">VJ账号</span>
                        <input type="text" class="form-control" value="" placeholder="Vjudge username" id="addgroup-vjusername" required/>
                    </div>
                    <label class="form-label label label-danger">请准确填写小队在VJudge上的账号名，区分大小写</label>

                <button class="btn btn-info btn-default pull-left" href="#" id="addgroup-btn" onclick="submitAddgroup()">添加小队</button>
            </div>
        </div>

    </div>
<script>
    function submitAddgroup()
    {
        var group_name=$('#addgroup-groupname').val();
        var vjusername=$('#addgroup-vjusername').val();
        $('#addgroup-btn').attr("disabled","disabled");
        $.post("<?php echo _Http;?>user/goAddTeam","addgroup-groupname=" + encodeURI(group_name) +
            "&addgroup-vjusername="  + encodeURI(vjusername)
            ,function(response){

                if(response.status==1)
                {
                    $("#addgroup-alert").html("添加成功！").show();
                }
                else
                {
                    $('#addgroup-btn').removeAttr("disabled");
                    $("#addgroup-alert").html(decodeURI(response.message)).show();
                }

            });
    }
</script>
<?php include('footer.php');?>
<div id="footer">
    <div class="container">
        <p class="text-muted text-left">Power By <a href="https://yxz.me/">YXZ</a>@SCUT</p>

    </div>
</div>
<div class="modal fade" id="login_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document"  style="width:500px; margin-top:10%;">
        <div class="modal-content">

            <div class="modal-body login-form">
                <div class="alert alert-info" role="alert" style="display: none;" id="login-alert">

                </div>
                    <div class="form-group">
                        <input type="text" class="form-control login-field" value="" placeholder="Username" id="login-username" />
                        <label class="login-field-icon fui-user" for="login-name"></label>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control login-field" value="" placeholder="Password" id="login-password" />
                        <label class="login-field-icon fui-lock" for="login-pass"></label>
                    </div>
                <label class="form-group">
                    <input type="checkbox" value="remember-me" class="login-field" name="remember" id="login-remember" />Remember me!
                </label>
                    <button class="btn btn-primary btn-lg btn-block" href="#" id="login-btn" onclick="submitLogin()">Log in</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="register_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document"  style="width:500px; margin-top:10%;">
        <div class="modal-content">

            <div class="modal-body login-form">
                <div class="alert alert-info" role="alert" style="display: none;" id="register-alert">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control login-field" value="" placeholder="Username" id="register-username" />
                    <label class="login-field-icon fui-user" for="login-name"></label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control login-field" value="" placeholder="Password" id="register-password" />
                    <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control login-field" value="" placeholder="Retype Password" id="register-repassword" />
                    <label class="login-field-icon fui-lock" for="login-pass"></label>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control login-field" value="" placeholder="E-mali" id="register-email" />
                    <label class="login-field-icon fui-mail"></label>
                </div>
                <button class="btn btn-primary btn-lg btn-block" href="#" id="register-btn" onclick="submitRegister()">Register Now!</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
    });
    function submitRegister()
    {
        var user_name=$('#register-username').val();
        var password=$('#register-password').val();
        var repassword=$('#register-repassword').val();
        var email=$('#register-email').val();
        $('#register-btn').attr("disabled","disabled");
        $.post("<?php echo _Http;?>user/goRegister","register-username=" + encodeURI(user_name) +
            "&register-password="  + encodeURI(password) +
            "&register-repassword="+encodeURI(repassword) +
            "&register-email="+encodeURI(email)
            ,function(response){

                if(response.status==1)
                {
                    $("#register-alert").html("注册成功！").show();
                    delayRefresh(1000);
                }
                else
                {
                    $('#register-btn').removeAttr("disabled");
                    $("#register-alert").html(decodeURI(response.message)).show();
                }

            });
    }
    function submitLogin()
    {
        var user_name=$('#login-username').val();
        var password=$('#login-password').val();
        var remember=$('#login-remember')[0].checked;
        $('#login-btn').attr("disabled","disabled");
        $.post("<?php echo _Http;?>user/goLogin","login-username=" + encodeURI(user_name) +
            "&login-password="  + encodeURI(password)+
            "&login-remember="  + encodeURI(remember)
            ,function(response){

                if(response.status==1)
                {
                    $("#login-alert").html("登录成功！").show();
                    delayRefresh(1000);
                }
                else
                {
                    $('#login-btn').removeAttr("disabled");
                    $("#login-alert").html(decodeURI(response.message)).show();
                }

            });
    }
</script>
</body>
</html>
<div id="footer">
    <div class="container">
        <p class="text-muted text-left">Power By <a href="https://yxz.me/">YXZ</a>@SCUT</p>

    </div>
</div>
<script>
    $(document).ready(function(){
        $(".button-collapse").sideNav();
        $('.datepicker').pickadate({
            selectMonths: true,
            selectYears: 15,
            format: 'yyyy/mm/dd'
        });
        $('.timepicker').pickatime({
            default: 'now',
            fromnow: 0,
            twelvehour: false,
            donetext: 'OK',
            cleartext: '清空',
            canceltext: '关闭',
            autoclose: false,
            ampmclickable: true,
            aftershow: function(){}
        });
        $('.tabs-transparent').each(function(){
            $(this).unbind("click");
        });
        var target="<?php echo $isLogin?'getUserBgPic':'getBgPic';?>";
        axios.get('<?php echo _Http;?>userAPI/' + target +'/')
            .then(function(response)
            {
                if(response.data.status==0)
                {
                    $('body').css("background-image","url("+ response.data.pic_url + ")");
                    $('body').css("background-repeat","no-repeat");
                    $('body').css("background-attachment","fixed");
                    $('body').css("background-size","cover");

                    $('body').css("opacity",0.9);
                }
            })
    })
</script>
</body>
</html>
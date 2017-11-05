<?php include('header.php');?>
    <div class="container" id="contest_list_table">

        <div class="card-panel hoverable" >
            <table class="highlight bordered">
                <thead>
                <tr>
                    <th class="left-align">比赛名称</th>
                    <th class="left-align">描述</th>
                    <th class="center-align">操作</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="one in contest_info">
                    <td class="red-text left-align long-text">{{ one.name }}</td>
                    <td class="light-blue-text left-align long-text">{{ one.desc }}</td>
                    <td class="">
                        <a class="btn-floating" :href="one.id | contestURL"><i class="material-icons">search</i></a>
                    </td>
                </tr>
                </tbody>
            </table>
            <div style="position: fixed;top:80px;width:100%;left:0;">
                <div v-show="loading" class="center-align" id="loading" style="position: relative">
                    <div class="preloader-wrapper small active">
                        <div class="spinner-layer spinner-green-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div><div class="gap-patch">
                                <div class="circle"></div>
                            </div><div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>
<script>
    var contest_list_table=new Vue(
        {
            el: "#contest_list_table",
            data: {
                contest_info:[],
                loading: true
            },
            filters:{
                contestURL :function(id)
                {
                    return '<?php echo _Http;?>contest/summary/id/' + id;
                }
            },
            created: function(){
                axios.get('<?php echo _Http;?>contestAPI/getAllContest/')
                    .then(function(response)
                    {
                        contest_list_table.contest_info=response.data.contest;
                        setTimeout(function(){contest_list_table.loading=false;},1000);

                    });
            }

        }
    );
</script>
<?php include('footer.php');?>
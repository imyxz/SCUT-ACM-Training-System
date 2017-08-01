<?php include('header.php');?>
    <div class="container" id="problem_list_table">
    <div class="row">
        <div class="col l3 s12">
            <div class="card">
                <div class="card-content red lighten-3 white-text">
                    <span class="card-title">{{ list_title }}</span>
                </div>
            </div>
            <div class="card">
                <div class="card-content blue-grey white-text">
                    <span class="card-title">{{ list_desc }}</span>

                </div>
            </div>
        </div>
        <div class="col l9 s12">
            <div class="card-panel hoverable" style="position: relative">
                <table class="highlight bordered" style="font-size: 20px;">
                    <thead>
                    <tr>
                        <th class="center-align"></th>
                        <th class="center-align">problem id</th>
                        <th class="center-align">problem</th>
                        <th class="center-align">Title</th>
                        <th class="center-align">go</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="problem in problem_list">
                        <td class="center-align"><span v-if="problem.aced" class="green-text"><i class="material-icons">done</i></span></td>
                        <td class="center-align">{{ problem.problem_id }}</td>
                        <td class="center-align"><a :href="problem.problem_id | generate_url" target="_blank">{{ problem.problem_identity }}</a></td>
                        <td class="center-align green-text">{{ problem.problem_title }}</td>
                        <td class="center-align" ><a class="btn-floating" :href="problem.problem_id | generate_url" target="_blank"><i class="material-icons">search</i></a></td>

                    </tr>
                    </tbody>
                </table>
                <a class="btn-floating btn waves-effect waves-light red" :href="list_id | generate_url_for_edit" style="position:absolute;left:40px;top:30px"><i class="material-icons">mode_edit</i></a>

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
    </div>

    </div>
    <script>
        var editor;
        var problem_list_table=new Vue(
            {
                el: "#problem_list_table",
                data: {
                    status_info:[],
                    loading: true,
                    waiting:0,
                    basic_url:'<?php echo _Http;?>',
                    page:0,
                    problem_list:[],
                    list_id:<?php echo $list_id;?>,
                    list_title:'',
                    list_desc:''
                },
                filters:{
                    generate_url:function(val)
                    {
                        return problem_list_table.basic_url+"vJudge/viewProblem/id/"+val;
                    },
                    generate_url_for_edit:function(val)
                    {
                        return problem_list_table.basic_url + "vJudge/editList/id/"+val;
                    }
                },
                created: function(){
                    axios.get(this.basic_url+'vJudgeAPI/getListProblems/id/'+this.list_id)
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                problem_list_table.problem_list=response.data.problem_list;
                                problem_list_table.is_end=response.data.is_end;
                                problem_list_table.loading=false;
                                problem_list_table.list_title=response.data.list_title;
                                problem_list_table.list_desc=response.data.list_desc;
                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }

                        });
                },
                methods:{

                }


            }
        );
    </script>
<?php include('footer.php');?>
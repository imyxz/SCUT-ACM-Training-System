<?php include('header.php');?>
    <div class="container" id="problem_list_table">

        <div class="card-panel hoverable over-flow-auto" >
            <div class="row">
                <div class="col s6"  style="margin-top:15px;">
                    <ul class="pagination">
                        <li :class="{'disabled':page<=1,'waves-effect':page>1}"><a @click="goPage(page-1)" ><i class="material-icons">chevron_left</i></a></li>
                        <li v-for="pg in page_pagination" :class="{'active':pg==page,'waves-effect':pg!=page}"><a @click="goPage(pg)">{{pg}}</a></li>
                        <li :class="{'disabled':is_end,'waves-effect':!is_end}"><a @click="goPage(page+1)"><i class="material-icons">chevron_right</i></a></li>
                    </ul>
                </div>

            </div>


            <table class="highlight bordered" style="font-size: 20px;">
                <thead>
                <tr>
                    <th class="center-align">list id</th>
                    <th class="center-align">list title</th>
                    <th class="center-align">add time</th>
                    <th class="center-align">go</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="list in lists">
                    <td class="center-align">{{ list.list_id }}</td>
                    <td class="center-align"><a :href="list.list_id | generate_url" target="_blank">{{ list.list_title }}</a></td>
                    <td class="center-align">{{ list.list_create_time }}</td>
                    <td class="center-align" ><a class="btn-floating" :href="list.list_id | generate_url" target="_blank"><i class="material-icons">search</i></a></td>

                </tr>
                </tbody>
            </table>
            <ul class="pagination">
                <li :class="{'disabled':page<=1,'waves-effect':page>1}"><a @click="goPage(page-1)" ><i class="material-icons">chevron_left</i></a></li>
                <li v-for="pg in page_pagination" :class="{'active':pg==page,'waves-effect':pg!=page}"><a @click="goPage(pg)">{{pg}}</a></li>
                <li :class="{'disabled':is_end,'waves-effect':!is_end}"><a @click="goPage(page+1)"><i class="material-icons">chevron_right</i></a></li>
            </ul>
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
        <div id="code-modal" class="modal" style="width:90%;overflow: visible;height:100%">
            <div class="modal-content">
                <div id="editor" ></div>
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
                    lists:[],
                    page_pagination:[],
                    is_end:false
                },
                filters:{
                    ram_filter:function(val)
                    {
                        var unit='B';
                        if(val>=1000)
                        {
                            val/=1024;
                            unit='KB';
                            if(val>=1000)
                            {
                                val/=1024;
                                unit='MB';
                                if(val>=1000)
                                {
                                    val/=1024;
                                    unit='GB';
                                    if(val>=1000)
                                    {
                                        val/=1024;
                                        unit='TB';
                                    }
                                }
                            }
                        }
                        return "" +parseFloat(val).toFixed(2)+ " "+unit;
                    },
                    time_filter:function(val)
                    {
                        var unit='ms';
                        if(val>=1000)
                        {
                            val/=1000;
                            unit='s';
                        }
                        return "" + parseFloat(val).toFixed(2)+ " "+unit;
                    },
                    generate_url:function(val)
                    {
                        return problem_list_table.basic_url+"vJudge/problemList/id/"+val;
                    }
                },
                created: function(){
                    this.page=1;
                },
                methods:{
                    goPage:function(newpage)
                    {
                        if(newpage>=1 && !(newpage>this.page && this.is_end))
                            this.page=newpage;
                    }
                },
                watch:{
                    page:function(newpage)
                    {
                        axios.get(this.basic_url+'vJudgeAPI/getAllList/page/'+newpage)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    problem_list_table.lists=response.data.lists;
                                    problem_list_table.is_end=response.data.is_end;
                                    problem_list_table.loading=false;
                                    var start;
                                    start=newpage-5;
                                    if(start<1)
                                        start=1;
                                    var tmp=[];
                                    var end=start+10;
                                    if(problem_list_table.is_end)
                                        end=newpage;
                                    for(var i=start;i<=end;i++)
                                    {
                                        tmp.push(i);
                                    }
                                    problem_list_table.page_pagination=tmp;

                                }
                                else
                                {
                                    Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                }

                            });
                    }
                }

            }
        );
    </script>
<?php include('footer.php');?>
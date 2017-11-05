<?php include('header.php');?>
    <div class="container" id="vm">

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
                    <th class="center-align">spider id</th>
                    <th class="center-align">oj</th>
                    <th class="center-align">watching jobs</th>
                    <th class="center-align">last alive time</th>
                    <th class="center-align">last login time</th>
                    <th class="center-align">enable</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="spider in spiders">
                    <td class="center-align">{{ spider.spider_id }}</td>
                    <td class="center-align">{{ spider.oj_name }}</td>
                    <td class="center-align">{{ spider.spider_looking_job }}</td>
                    <td class="center-align">{{ spider.last_alive_time }}</td>
                    <td class="center-align">{{ spider.oj_logintime }}</td>
                    <td class="center-align" >{{ spider.spider_enable==1?"enable":"disable" }}</td>

                </tr>
                </tbody>
            </table>
            <ul class="pagination">
                <li :class="{'disabled':page<=1,'waves-effect':page>1}"><a @click="goPage(page-1)" ><i class="material-icons">chevron_left</i></a></li>
                <li v-for="pg in page_pagination" :class="{'active':pg==page,'waves-effect':pg!=page}"><a @click="goPage(pg)">{{pg}}</a></li>
                <li :class="{'disabled':is_end,'waves-effect':!is_end}"><a @click="goPage(page+1)"><i class="material-icons">chevron_right</i></a></li>
            </ul>
        </div>
        <div id="code-modal" class="modal" style="width:90%;overflow: visible;height:100%">
            <div class="modal-content">
                <div id="editor" ></div>
            </div>
        </div>
    </div>
    <script>
        var editor;
        var vm=new Vue(
            {
                el: "#vm",
                data: {
                    status_info:[],
                    loading: true,
                    waiting:0,
                    basic_url:'<?php echo _Http;?>',
                    page:0,
                    page_pagination:[],
                    is_end:false,
                    spiders:[]
                },
                filters:{

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
                        axios.get(this.basic_url+'vJudgeAPI/getSpiderStatus/page/'+newpage)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    vm.spiders=response.data.spiders;
                                    vm.is_end=response.data.is_end;
                                    vm.loading=false;
                                    var start;
                                    start=newpage-5;
                                    if(start<1)
                                        start=1;
                                    var tmp=[];
                                    var end=start+10;
                                    if(vm.is_end)
                                        end=newpage;
                                    for(var i=start;i<=end;i++)
                                    {
                                        tmp.push(i);
                                    }
                                    vm.page_pagination=tmp;

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
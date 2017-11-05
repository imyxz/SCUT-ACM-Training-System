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
                    <th class="center-align">contest id</th>
                    <th class="center-align">contest title</th>
                    <th class="center-align">contest type</th>
                    <th class="center-align">start time</th>
                    <th class="center-align">duration</th>
                    <th class="center-align">go</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="contest in contests">
                    <td class="center-align">{{ contest.contest_id }}</td>
                    <td class="center-align"><a :href="contest.contest_id | generate_url" target="_blank">{{ contest.contest_title }}</a></td>
                    <td class="center-align">{{ contest.contest_type | get_type }}</td>
                    <td class="center-align">{{ contest.contest_start_time }}</td>
                    <td class="center-align">{{ contest.contest_last_seconds | fromSeconds }}</td>
                    <td class="center-align" ><a class="btn-floating" :href="contest.contest_id | generate_url" target="_blank"><i class="material-icons">search</i></a></td>

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
                    contests:[],
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
                        return vm.basic_url+"vJudge/contest/id/"+val;
                    },
                    fromSeconds:function(val)
                    {
                        var tmp=parseInt(val);
                        tmp=tmp/60;
                        tmp=tmp.toFixed(0);
                        return (tmp/60).toFixed(1)+" 小时"

                    }
                    ,
                    get_type:function(val)
                    {
                        if(val==0)
                            return "同步开始";
                        else
                            return "异步开始";

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
                        axios.get(this.basic_url+'vJudgeAPI/getAllContest/page/'+newpage)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    vm.contests=response.data.contests;
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
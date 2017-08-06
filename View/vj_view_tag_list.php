<?php include('header.php');?>
    <div class="container" id="problem_list_table">

        <div class="card-panel hoverable" >
            <div class="">
                <a class="chip" v-for="tag in tag_list" href="#" @click="changeTag(tag.tag_name)" :class="{'green lighten-2':tag.tag_name==tag_name}">{{ tag.tag_name }} <i class="green-text">{{tag.tag_count}}</i></a>
            </div>
            <div class="row">

                <div class="col s6"  style="margin-top:15px;">
                    <ul class="pagination">
                        <li :class="{'disabled':page<=1,'waves-effect':page>1}"><a @click="goPage(page-1)" ><i class="material-icons">chevron_left</i></a></li>
                        <li v-for="pg in page_pagination" :class="{'active':pg==page,'waves-effect':pg!=page}"><a @click="goPage(pg)">{{pg}}</a></li>
                        <li :class="{'disabled':is_end,'waves-effect':!is_end}"><a @click="goPage(page+1)"><i class="material-icons">chevron_right</i></a></li>
                    </ul>
                </div>
                <div class="input-field col s6">
                    <input id="search" type="search" @keyup.enter="goProblem(search_value)" v-model="search_value">
                    <label  for="search"><i class="material-icons">search</i>精确查找 格式：oj/problem id 如:gym/100002/A</label>
                    <i class="material-icons">close</i>
                </div>

            </div>


            <table class="highlight bordered" style="font-size: 20px;">
                <thead>
                <tr>
                    <th class="center-align">problem id</th>
                    <th class="center-align">oj</th>
                    <th class="center-align">problem</th>
                    <th class="center-align">Title</th>
                    <th class="center-align">add time</th>
                    <th class="center-align">go</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="problem in problem_list">
                    <td class="center-align">{{ problem.problem_id }}</td>
                    <td class="center-align yellow-text text-darken-3">{{ problem.oj_name }}</td>
                    <td class="center-align"><a :href="problem.problem_id | generate_url" target="_blank">{{ problem.problem_identity }}</a></td>
                    <td class="center-align green-text">{{ problem.problem_title }}</td>
                    <td class="center-align">{{ problem.add_time }}</td>
                    <td class="center-align" ><a class="btn-floating" :href="problem.problem_id | generate_url" target="_blank"><i class="material-icons">search</i></a></td>

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
                    page_pagination:[],
                    is_end:false,
                    search_value:'',
                    tag_name:'<?php echo $tag_name;?>',
                    tag_list:''
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
                        return problem_list_table.basic_url+"vJudge/viewProblem/id/"+val;
                    },
                    generate_tag_url:function(val)
                    {
                        return problem_list_table.basic_url+"vJudge/viewTag/name/"+ encodeURIComponent( val);
                    }
                },
                created: function(){
                    this.page=1;
                    axios.get(this.basic_url+'vJudgeAPI/getAllTags/')
                        .then(function(response)
                        {
                            if(response.data.status==0){
                                problem_list_table.tag_list=response.data.tags;

                            }
                            else
                            {
                                Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                            }

                        });
                },
                methods:{
                    goPage:function(newpage)
                    {
                        if(newpage>=1 && !(newpage>this.page && this.is_end))
                            this.page=newpage;
                    },
                    goProblem:function(id)
                    {
                        var pos1=id.indexOf("/",-1);
                        if(pos1>=0)
                        {
                            var oj_name=id.substr(0,pos1);
                            var problem_name=id.substr(pos1+1,id.length-pos1-1);
                            var obj=new Object();
                            obj.oj_name=oj_name;
                            obj.problem_identity=problem_name;
                            axios.post(this.basic_url+'vJudgeAPI/getProblemByInfo/',JSON.stringify(obj))
                                .then(function(response)
                                {
                                    if(response.data.status==0){
                                        delayJump(problem_list_table.basic_url+"vJudge/viewProblem/id/"+response.data.problem_id);

                                    }
                                    else
                                    {
                                        Materialize.toast('<span class="">'+response.data.err_msg+'</span>' , 2000);
                                    }

                                });
                        }
                    },
                    changeTag:function(tag)
                    {
                        this.tag_name=tag;
                    },
                    refreshList:function(page,tag_name)
                    {
                        if(tag_name=='')
                            return;
                        axios.get(this.basic_url+'vJudgeAPI/getTagList/name/'+ encodeURIComponent(tag_name) +'/page/'+page)
                            .then(function(response)
                            {
                                if(response.data.status==0){
                                    problem_list_table.problem_list=response.data.problem_list;
                                    problem_list_table.is_end=response.data.is_end;
                                    problem_list_table.loading=false;
                                    var start;
                                    start=page-5;
                                    if(start<1)
                                        start=1;
                                    var tmp=[];
                                    var end=start+10;
                                    if(problem_list_table.is_end)
                                        end=page;
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
                },
                watch:{
                    page:function(newpage)
                    {
                        this.refreshList(newpage,this.tag_name);
                    },
                    tag_name:function(new_tag)
                    {
                        if(this.page!=1)
                            this.page=1;
                        else
                        {
                            this.refreshList(this.page,new_tag);

                        }
                    }
                }

            }
        );
    </script>
<?php include('footer.php');?>
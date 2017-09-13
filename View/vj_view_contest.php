<?php include('header.php');?>
    <style type="text/css" media="screen">
        #editor {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        #editor-view {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        #container .tabs .tab a{
            color:deepskyblue!important;
            font-size: 18px;

        }
        #container .tabs .tab a:hover,.tabs .tab a.active {
            background-color:transparent;
            color:deepskyblue;
        }
        #container  .tabs .tab.disabled a,.tabs .tab.disabled a:hover {
            color:rgba(255,255,255,0.7);
        }
        #container  .tabs .indicator {
            background-color:deepskyblue;
        }
    </style>
    <div id="container">
        <div class="row">
            <div class="container">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <h1 class="center-align">{{ contest_name }}</h1>
                            <div class="progress">
                                <div class="determinate" :style="'width: ' + (running_time/contest_long)*100 + '%'"></div>
                            </div>
                            <div style="position: relative;min-height: 22px;font-size: 18px;">
                                <span style="position: absolute;left: 0" >{{contestStartTime}}</span>
                                <span style="position: absolute;right: 0">{{contestEndTime}}</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col s12" v-if="err_info!=''">
                    <div class="card">
                        <div class="card-content white lighten-3 red-text">
                            <span class="card-title center-align">{{ err_info }}</span>
                            <div class="center-align"><button class=" btn" v-if="need_participant" @click="startContest()">点此开始比赛</button></div>
                        </div>
                    </div>
                </div>
                <div class="col s12">
                    <ul class="tabs" style="overflow-x: hidden">
                        <li class="tab col s3"><a class="active" href="#contest_info">比赛详情</a></li>
                        <li class="tab col s3"><a  href="#problem_info">题目列表</a></li>
                        <li class="tab col s3"><a href="#status_info">提交状态</a></li>
                        <li class="tab col s3"><a href="#rank_info">排行榜</a></li>
                    </ul>
                </div>
            </div>
            <div class="container">
                <div id="contest_info" class="col s12">
                    <div class="row">
                        <div class="col l3 s12">
                            <div class="card">
                                <div class="card-content red lighten-3 white-text">
                                    <span class="card-title">{{ contest_name }}</span>
                                    <p>{{ contest_desc }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col l9 s12">
                            <div class="card-panel hoverable over-flow-auto" style="position: relative">
                                <table class="highlight bordered" style="font-size: 20px;">
                                    <thead>
                                    <tr>
                                        <th class="center-align">AC状态</th>
                                        <th class="center-align">State</th>
                                        <th class="center-align">#</th>
                                        <th class="center-align">Title</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="problem in problem_list">
                                        <td class="center-align"><span v-show="problem_status[problem.problem_index].is_try"
                                                                                 :class="{'yellow-text text-darken-3':!problem_status[problem.problem_index].is_ac,'green-text text-darken-2':problem_status[problem.problem_index].is_ac}"
                                                                                 style="font-size: 20px;"
                                                                                 data-badge-caption="">
                                                {{problem_status[problem.problem_index].is_ac?'Accept':'Try'}}
                                            </span></td>
                                        <td class="center-align">{{ getProbmelStatus(problem.problem_index) }}</td>
                                        <td class="center-align">{{String.fromCharCode(problem.problem_index+64)}}</td>
                                        <td class="center-align blue-text"><a href="#" @click="goProblem(problem.problem_index)">{{ problem.problem_title }}</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
                <div id="problem_info" class="col s12">
                    <div class="row">
                        <div class="col l3 s12">
                            <div class="card-panel " >
                                <ul class="pagination">
                                    <li v-for="index in problem_count" :class="{'active':index==cur_problem_index,'waves-effect':index!=cur_problem_index}"><a @click="goProblem(index)">{{String.fromCharCode(index+64)}}</a></li>
                                </ul>
                            </div>
                            <div class="card-panel " >
                                <div class="center-align row"><button class="btn blue btn-float" style="width: 90%" onclick="vm.openCode();">提交</button></div>
                            </div>

                        </div>
                        <div class="col l8 s12">
                            <div class="card-panel hoverable" >
                                <div class="title">
                                    <p>{{ cur_problem_info.problem_title }}</p>
                                </div>
                                <div class="limits center-align">
                                    <div class="memory-limit">Memory: {{ cur_problem_info.memory_limit | ram_filter }}</div>
                                    <div class="time-limit">Time: {{ cur_problem_info.time_limit | time_filter}}</div>
                                </div>
                                <div class="content" data-disable-for-latex-v-html="cur_problem_info.problem_desc" id="problem_desc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="code-modal" class="modal modal-fixed-footer" style="width:90%;overflow: visible;">
                        <div class="modal-content">
                            <div id="editor"></div>
                        </div>
                        <div class="modal-footer" id="modal_footer">
                            <template v-if="!submited">
                                <div class="row" key="div_1">
                                    <div class="col l3">
                                        <select id="select_compiler" onchange="vm.compiler=$('#select_compiler').val();" >
                                            <option value="0" disabled selected>选择编译器</option>
                                            <option v-for="(name,id) in cur_problem_info.compiler_info" :value="id">{{ name }}</option>
                                        </select>
                                    </div>
                                    <div class="col l9">
                                        <button class="waves-effect waves-green btn blue" :class="{'disabled':compiler==-1}" @click="submitCode">提交</button>
                                    </div>
                                </div>
                            </template>
                            <template v-if="submited">
                                <div class="row" key="div_2" style="font-size: 20px; padding:8px;">
                                    <div class="col l4 left-align" >
                                        <span class="" style="margin:auto">时间：{{ job_status.time_usage | time_filter }}</span>

                                    </div>
                                    <div class="col l4 left-align" >
                                        <span class="" style="margin:auto" :class="{'green-text':job_status.ac_status==1,'red-text':job_status.ac_status!=1}">状态：{{ job_status.wrong_info  }}</span>

                                    </div>
                                    <div class="col l4 right-align" >
                                        <span class="" style="margin:auto">内存：{{ job_status.ram_usage | ram_filter }}</span>

                                    </div>
                                </div>
                            </template>


                        </div>
                    </div>
                </div>
                <div id="status_info" class="col s12">
                    <div class="card-panel hoverable over-flow-auto" >
                        <table class="highlight bordered">
                            <thead>
                            <tr>
                                <th class="center-align">job id</th>
                                <th class="center-align">user name</th>
                                <th class="center-align">problem</th>
                                <th class="center-align">Result</th>
                                <th class="center-align">Time</th>
                                <th class="center-align">Mem</th>
                                <th class="center-align">submit time</th>
                                <th class="center-align">view source</th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="status in status_info">
                                <td class="center-align">{{ status.run_job_id }}</td>
                                <td class="center-align">{{ status.user_nickname }}</td>
                                <td class="center-align"><a href="#" @click="goProblem(status.problem_index)">{{ String.fromCharCode(status.problem_index+64) }}</a></td>
                                <td class="center-align" :class="{'green-text':status.ac_status==1,'red-text':status.ac_status!=1}">{{ status.wrong_info  }}</td>
                                <td class="center-align">{{ status.time_usage | time_filter }}</td>
                                <td class="center-align">{{ status.ram_usage | ram_filter }}</td>
                                <td class="center-align">{{ status.submit_time |fromSeconds }}</td>
                                <td class="center-align" >
                                    <a class="btn-floating"  @click="displaySourceCode(status.run_job_id)"><i class="material-icons">search</i></a>
                                    <a class="btn-floating" :class="{'red':status.is_shared,'blue':!status.is_shared }" @click="setJobShare(status.run_job_id,!status.is_shared,status)"  ><i class="material-icons">share</i></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="code-modal-view" class="modal"  style="width:90%;overflow: visible;height:100%">
                        <div class="modal-content">
                            <div id="editor-view" ></div>
                        </div>
                    </div>
                    <div id="share-modal" class="modal">
                        <div class="modal-content">
                            <h4>代码链接</h4>
                            <h5><a :href="cur_share_id | share_url">{{cur_share_id | share_url}}</a></h5>
                        </div>
                        <div class="modal-footer">
                            <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">ok</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="rank_info" class="col s12">
                <div class="card">
                    <div class="card-content" style="overflow-x:auto">
                        <table class="highlight bordered medium-text">
                            <thead>
                            <tr>
                                <th class="center-align">Rank</th>
                                <th class="left-align">Team</th>
                                <th class="center-align">Solve</th>
                                <th class="center-align">Penalty</th>
                                <th class="center-align" v-for="x in problem_count"><a href="#" @click="goProblem(x)">{{ String.fromCharCode(x+64) }}</a></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="user in rank_board" v-if="user!=null" :class="{'blue lighten-5':user.user_id==user_id}">
                                <td class="center-align">{{ user.rank_index }}</td>
                                <td class="left-align">{{ user.user_name }}</td>
                                <td class="center-align">{{ user.problem_solved }}</td>
                                <td class="center-align">{{ user.penalty |fromSeconds }}</td>
                                <td v-for="problem in user.problems" v-if=" problem!=null"
                                    :class="{
                            'green':problem.is_ac,
                            'red':!problem.is_ac && problem.is_try}"
                                    class="center-align lighten-4" style="padding: 3px 10px;">
                            <span v-if="problem.is_ac" style="display: block">
                                {{ problem.ac_time | getAcTime}}
                            </span>
                            <span v-if="problem.trys!=0" style="display: block">
                                (-{{ problem.trys }})
                            </span>
                                    <span style="min-width: 60px;display:block;"></span>
                                </td>

                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var editor;
        var editor_view;
        var all_submissions=[];
        var already_in_submissioins={};
        var user_status=[];
        var basic_url='<?php echo _Http;?>';
        var contest_id=<?php echo $contest_id;?>;
        var vm;
    </script>
    <script src="<?php echo _Http;?>js/view_contest.js?201709131"></script>

<?php include('footer.php');?>
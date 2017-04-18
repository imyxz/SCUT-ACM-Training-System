<?php

class api extends AmysqlController
{
    function temp()
    {
        $_POST['board_json']='{"id":156598,"title":"大一组队前比赛最后一波","begin":1491105600000,"length":18000000,"isReplay":false,"participants":{"87544":["ScutCad","周昭育"],"111501":["zhc","植浩聪"],"111511":["briny_fish","宗文智"],"111515":["roffen","罗凡"],"111525":["leitingjiang","李庭章"],"111535":["yangyangyang123","y"],"111537":["shenfuLL","林安然"],"114166":["zhangyinqi","xwl"],"114203":["Loycine","SCUT_我拿buff"],"115441":["imyxz","余信志"],"115697":["kunchenghuang","黄锟城"],"115719":["w468750","吴潘安"],"117092":["IllL","SCUT_我拿buff"],"123204":["madfff","王建锋"],"128082":["stud30","Juuso King"],"135243":["201630664871","码农"],"138789":["16acmsujingze","16acmsujingze"]},"submissions":[[111537,8,1,897],[111525,8,1,1104],[115719,8,1,1312],[115697,3,0,1382],[111515,3,0,1388],[111515,3,0,1404],[128082,6,1,1920],[87544,8,1,2052],[117092,8,1,2326],[111525,0,0,2359],[111535,8,0,3203],[123204,8,1,3224],[111535,8,1,3246],[128082,8,1,3726],[115719,9,0,3879],[117092,1,1,3947],[111525,6,0,4001],[115719,9,0,4052],[115697,8,1,4085],[111501,8,1,4197],[111537,2,0,4890],[115719,9,0,5079],[114166,8,0,5107],[115719,9,0,5360],[87544,9,0,5372],[87544,9,0,5597],[111515,8,0,5745],[115719,9,0,5762],[114166,8,0,5781],[111515,8,1,5855],[87544,9,0,5942],[114166,8,1,5959],[111525,10,0,6254],[87544,9,0,6424],[111525,10,0,6504],[111525,10,0,6538],[117092,7,1,6560],[115719,6,1,6701],[111525,10,0,6964],[135243,1,0,6985],[128082,10,0,7060],[111525,10,0,7119],[138789,8,1,7129],[111511,8,1,7310],[111525,10,0,7367],[115441,8,1,7395],[111525,10,0,7489],[128082,10,0,7583],[111525,10,0,7656],[111525,10,0,7677],[123204,9,1,7761],[114166,5,0,7767],[114166,5,0,7781],[114166,5,0,7826],[128082,10,0,7991],[135243,1,0,8023],[111525,10,0,8061],[111535,3,0,8063],[114166,5,0,8078],[111535,3,0,8186],[111537,2,0,8373],[128082,10,0,8639],[87544,9,0,8911],[111525,9,0,9048],[111525,9,0,9158],[135243,8,1,9167],[111525,9,0,9234],[87544,9,0,9243],[138789,4,0,9423],[138789,4,1,9434],[117092,9,1,9616],[111535,3,0,9734],[111537,2,0,10347],[115719,7,0,10619],[115719,7,1,10708],[111511,6,1,10757],[135243,6,0,10900],[111537,2,0,10956],[114203,4,0,10977],[114203,4,1,11003],[128082,4,0,11305],[128082,4,0,11333],[128082,4,1,11439],[117092,6,0,11467],[117092,6,0,11492],[123204,6,1,11659],[115697,6,0,11790],[117092,6,1,11799],[111537,6,0,11950],[111537,6,1,11968],[87544,6,0,12075],[87544,6,0,12394],[114203,8,1,12471],[87544,6,0,12633],[115697,6,1,12639],[135243,6,0,12969],[87544,6,0,13456],[114203,6,1,13796],[111537,2,0,13861],[115719,4,1,14314],[111535,6,1,14353],[115441,6,1,14358],[115697,7,1,15041],[87544,9,0,15176],[87544,9,0,15231],[138789,6,1,15268],[117092,4,1,15491],[87544,6,0,15715],[87544,9,0,15751],[114203,9,0,16571],[114203,9,0,17399],[114203,9,0,17745],[115697,9,1,17819],[114203,9,0,18096],[115441,4,1,18586],[111501,6,0,22637],[111501,6,0,22997]]}';
        $json=json_decode($_POST['board_json'],true);

        $groups=array();
        $contest_id=1;
        $end_time=intval ($json['length']/1000);
        foreach($json['participants'] as $key=> &$one)
        {
            $row=array();
            $row['group_id']=$this->_model("group_model")->getGroupIDByVJUsername(addslashes($one[0]));
            var_dump($row['group_id']);
            $row['group_username']=$one[0];
            if(!$row['group_id'])
                $row['group_id']=rand(10,100);
            $row['submission']=array();
            $row['total_ac']=0;
            $row['total_ac_time']=0;
            $row['total_penalty']=0;
            $groups[$key]=$row;
        }
        foreach($json['submissions'] as &$one) {
            $vj_id=intval($one[0]);
            $problem_id = intval($one[1]) + 1;
            $ac_status=intval($one[2]);
            $ac_time=intval($one[3]);
            if (isset($groups[$vj_id]) && $ac_time<=$end_time) {
                $row = array();
                $row['ac_status'] = $ac_status;
                $row['submit_time'] = $ac_time;
                $groups[$vj_id]['submission'][$problem_id]['info'][] = $row;
                if ($row['ac_status'] == 1) {
                    if (!isset($groups[$vj_id]['submission'][$problem_id]['ac'])) {
                        $groups[$vj_id]['submission'][$problem_id]['ac'] = true;
                        $groups[$vj_id]['submission'][$problem_id]['ac_time'] = $ac_time;
                        $groups[$vj_id]['total_ac_time'] += $ac_time;
                        $groups[$vj_id]['total_ac']++;
                    }

                } else {
                    $groups[$vj_id]['submission'][$problem_id]['try'] += 1;

                }
                //$this->_model('contest_model')->insertBoardSubmission($contest_id, $groups[$one[0]]['group_id'], $one[1], $row['ac_status'], $row['submit_time']);
            }
        }
        foreach($groups as &$one)
        {
            foreach($one['submission'] as $problem)
            {
                if($problem['ac'])
                {
                    foreach($problem['info'] as $three)
                    {
                        if($three['ac_status']==0)
                            $one['total_penalty']+=20;
                        else
                            $one['total_penalty']+=$problem['ac_time']/60;
                    }
                }
            }
        }
        uasort($groups,function($a,$b)
        {

            if($a['total_ac']==$b['total_ac'])
                return $a['total_penalty']>=$b['total_penalty'];
            return $a['total_ac']<$b['total_ac'];
        });
        $i=1;
        foreach($groups as &$one)
        {
            uksort($one['submission'],function($a,$b)
            {
                return $a>=$b;
            });
            $this->_model('contest_model')->insertBoardInfo($contest_id, $one['group_id'], $one['total_ac'], $i, intval($one['total_penalty']),addslashes(json_encode($one)));
            $i++;

        }




    }

}
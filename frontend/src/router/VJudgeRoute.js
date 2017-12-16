import VJudge from '@/pages/VJudge'
import AllProblem from '@/pages/VJudge/AllProblem'
import ViewProblem from '@/pages/VJudge/ViewProblem'
import AllContest from '@/pages/VJudge/AllContest'
import VJudgeContestRoute from './VJudgeContestRoute'
import OnlineIDE from '@/pages/VJudge/OnlineIDE'
import AllTag from '@/pages/VJudge/AllTag'
export default {
  path: '/vJudge',
  children: [
    VJudgeContestRoute,
    {
      path: 'allProblem/:page',
      name: 'vjudge.allProblem',
      component: AllProblem,
      meta: {
        title: '所有题目'
      }
    },
    {
      path: 'allProblem',
      redirect: '/vJudge/allProblem/1',
      name: 'vjudge.index'
    },
    {
      path: 'problem/:problem_id',
      name: 'vjudge.viewProblem',
      component: ViewProblem,
      meta: {
        title: '查看题目'
      }
    },
    {
      path: 'allContest/:page',
      name: 'vjudge.allContest',
      component: AllContest,
      meta: {
        title: '比赛列表'
      }
    },
    {
      path: 'allContest',
      redirect: '/vJudge/allContest/1',
      name: 'vjudge.allContest.index'
    },
    {
      path: 'onlineIDE',
      name: 'vjudge.onlineIDE',
      component: OnlineIDE,
      meta: {
        title: 'onlineIDE'
      }
    },
    {
      path: 'tag',
      name: 'vjudge.viewTagIndex',
      component: AllTag,
      meta: {
        title: '标签列表'
      }
    },
    {
      path: 'tag/:tag_name/:page',
      name: 'vjudge.viewTag',
      component: AllTag,
      meta: {
        title: '标签列表'
      }
    }
  ],
  component: VJudge
}

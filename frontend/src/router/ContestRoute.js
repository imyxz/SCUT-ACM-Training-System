import Contest from '@/pages/Contest'
import ContestList from '@/pages/Contest/ContestList'
import ContestSummary from '@/pages/Contest/ContestSummary'
import AddContest from '@/pages/Contest/AddContest'
export default {
  path: '/contest',
  children: [
    {
      path: 'viewList/:page',
      name: 'contest.viewList',
      component: ContestList,
      meta: {
        title: '历史比赛列表'
      }
    },
    {
      path: 'viewSummary/:contest_id',
      name: 'contest.viewSummary',
      component: ContestSummary,
      meta: {
        title: '比赛详情'
      }
    },
    {
      path: 'addContest',
      name: 'contest.addContest',
      component: AddContest,
      meta: {
        title: '添加比赛'
      }
    },
    {
      path: 'viewList',
      redirect: '/contest/viewList/1'
    }
  ],
  component: Contest
}

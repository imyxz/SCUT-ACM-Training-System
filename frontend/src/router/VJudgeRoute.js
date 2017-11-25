import VJudge from '@/pages/VJudge'
import AllProblem from '@/pages/VJudge/AllProblem'
import ViewProblem from '@/pages/VJudge/ViewProblem'
export default {
  path: '/vJudge',
  children: [
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
      redirect: '/vJudge/allProblem/1'
    },
    {
      path: 'viewProblem/:problem_id',
      name: 'vjudge.viewProblem',
      component: ViewProblem,
      meta: {
        title: '查看题目'
      }
    }
  ],
  component: VJudge
}

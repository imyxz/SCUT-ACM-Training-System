import ViewContest from '@/pages/VJudge/ViewContest'
import ContestInfo from '@/pages/VJudge/Contest/ContestInfo'
import ContestProblem from '@/pages/VJudge/Contest/ContestProblem'
export default {
  path: 'contest/:contest_id',
  name: 'vjudge.contest',
  component: ViewContest,
  meta: {
    title: '比赛'
  },
  children: [{
    path: 'info',
    name: 'vjudge.contest.info',
    component: ContestInfo,
    meta: {
      title: '比赛'
    }
  },
  {
    path: 'problem',
    name: 'vjudge.contest.problem.index',
    redirect: to => {
      // eslint-disable-next-line
      const { hash, params, query } = to
      return '/vJudge/contest/' + params.contest_id + '/problem/A'
    }
  },
  {
    path: 'problem/:problem_index',
    name: 'vjudge.contest.problem',
    component: ContestProblem,
    meta: {
      title: '比赛'
    }
  }]
}

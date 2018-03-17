import Rating from '@/pages/Rating'
import AddRatingFromContest from '@/pages/Rating/AddRatingFromContest'
import User from '@/pages/Rating/User'
import Rank from '@/pages/Rating/Rank'
import Contest from '@/pages/Rating/Contest'

export default {
  path: '/rating',
  children: [
    {
      path: 'add/fromContest/:contest_id',
      name: 'rating.addfromcontest',
      component: AddRatingFromContest,
      meta: {
        title: '从历史比赛添加'
      }
    },
    {
      path: 'rank',
      name: 'rating.rank',
      component: Rank,
      meta: {
        title: '排行榜'
      }
    },
    {
      path: 'user/:user_id',
      name: 'rating.userHistory',
      component: User,
      meta: {
        title: '选手历史Rating'
      }
    },
    {
      path: 'contest/:rating_id',
      name: 'rating.contestHistory',
      component: Contest,
      meta: {
        title: '排位赛Rating详情'
      }
    }
  ],
  component: Rating
}

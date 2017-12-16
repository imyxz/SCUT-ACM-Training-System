import User from '@/pages/User'
import BindTeam from '@/pages/User/BindTeam'
import AddTeam from '@/pages/User/AddTeam'
import EditInfo from '@/pages/User/EditInfo'
export default {
  path: '/user',
  children: [
    {
      path: 'addTeam',
      name: 'user.addTeam',
      component: AddTeam,
      meta: {
        title: '添加队伍'
      }
    },
    {
      path: 'bindTeam',
      name: 'user.bindTeam',
      component: BindTeam,
      meta: {
        title: '绑定小队'
      }
    },
    {
      path: 'editInfo',
      name: 'user.editInfo',
      component: EditInfo,
      meta: {
        title: '修改信息'
      }
    }
  ],
  component: User
}

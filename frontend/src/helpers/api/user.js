import {postJson} from '../network'
import API from '../constants/api'
export function bindPlayer (realName, teamID) {
  return postJson(API.user.bindPlayer, {}, {real_name: realName, team_id: teamID})
}
export function addTeam (teamName, vjUserName) {
  return postJson(API.user.addTeam, {}, {team_name: teamName, team_vj_account: vjUserName})
}
export function updateUserInfo (userNickname, userBgPic) {
  return postJson(API.user.updateUserInfo, {}, {nick_name: userNickname, bg_url: userBgPic})
}
export function logOut () {
  return postJson(API.user.logOut)
}

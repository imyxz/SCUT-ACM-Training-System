import {
  get
} from '../network'
import API from '../constants/api'
export function getAllTeam (cache = true) {
  return get(API.user.getAllTeam, {}, cache)
}
export function getUserInfo (cache = true) {
  return get(API.user.getUserInfo, {}, cache)
}

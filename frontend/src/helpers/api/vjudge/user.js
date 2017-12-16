import {get} from '../../network'
import API from '../../constants/api'
export function getMyStatus (page, cache = true) {
  return get(API.vjudge.status.getMyStatus, {page: page}, cache)
}

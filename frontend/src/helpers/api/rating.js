import {
  postJson,
  get
} from '../network'
import API from '../constants/api'
export function getRank (cache = true) {
  return get(API.rating.getRank, {}, cache)
}
export function getGroupRank (cache = true) {
  return get(API.rating.getGroupRank, {}, cache)
}
export function getGroupPlayers (groupId, cache = true) {
  return get(API.rating.getGroupPlayers, {
    id: groupId
  }, cache)
}
export function getList (cache = true) {
  return get(API.rating.getList, {}, cache)
}
export function getRatingInfo (ratingId, cache = true) {
  return get(API.rating.getRatingInfo, {id: ratingId}, cache)
}
export function getUserRatingHistory (userId, cache = true) {
  return get(API.rating.getUserRatingHistory, {id: userId}, cache)
}
export function getContestRatingHistory (ratingId, cache = true) {
  return get(API.rating.getContestRatingHistory, {id: ratingId}, cache)
}
export function addNewRating (contestId, contestName, isInSystem, isPreview, rank) {
  return postJson(API.rating.addNewRating, {}, {
    contest_id: contestId,
    contest_name: contestName,
    is_in_system: isInSystem,
    is_preview: isPreview,
    rank: rank
  })
}

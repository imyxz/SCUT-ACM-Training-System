import {get, postJson} from '../network'
import API from '../constants/api'
export function getContestList (page = 1, cache = true) {
  return get(API.contest.getContestList, {page: page}, cache)
}
export function getContestSummary (contestID, cache = true) {
  return get(API.contest.getContestSummary, {id: contestID}, cache)
}
export function updatePlayerAcStatus (contestID, playerAcStatus) {
  return postJson(API.contest.updatePlayerAcStatus, {id: contestID}, playerAcStatus)
}
export function addContest (contestName, contestDesc, problemCount, contestStarttime, contestEndtime, csvData) {
  return postJson(API.contest.addContestFromCSV, {}, {
    contest_name: contestName,
    contest_desc: contestDesc,
    problem_count: problemCount,
    contest_starttime: contestStarttime,
    contest_endtime: contestEndtime,
    csv_data: csvData
  })
}

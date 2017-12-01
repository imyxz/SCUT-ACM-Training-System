import {get, postJson} from '../../network'
import API from '../../constants/api'
export function getContestList (page, cache = true) {
  return get(API.vjudge.contest.getAllContest, {page: page}, cache)
}
export function getContestInfo (contestId, cache = true) {
  return get(API.vjudge.contest.getContestInfo, {id: contestId}, cache)
}
export function getContestSubmission (contestId, beginTime, cache = false) {
  return get(API.vjudge.contest.getContestSubmission, {id: contestId, beginTime: beginTime}, cache)
}
export function getContestProblem (contestId, problemIndex, cache = true) {
  return get(API.vjudge.contest.getContestProblem, {cid: contestId, pid: problemIndex}, cache)
}
export function submitContestJob (contestId, problemIndex, compilerId, sourceCode) {
  return postJson(API.vjudge.contest.submitContestJob, {cid: contestId, pid: problemIndex}, {
    compiler_id: compilerId,
    source_code: sourceCode
  })
}
export function getContestJobStatus (jobId, cache = true) {
  return get(API.vjudge.contest.getContestJobStatus, {id: jobId}, cache)
}
export function getContestStatus (contestId, page, cache = false) {
  return get(API.vjudge.contest.getContestStatus, {id: contestId, page: page}, cache)
}
export function joinContest (contestId) {
  return postJson(API.vjudge.contest.joinContest, {id: contestId}, {})
}

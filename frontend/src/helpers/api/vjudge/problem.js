import {postJson, get} from '../../network'
import API from '../../constants/api'
export function getAllProblem (page, cache = true) {
  return get(API.vjudge.problem.getAllProblem, {page: page}, cache)
}
export function getProblemInfo (problemID, cache = true) {
  return get(API.vjudge.problem.getProblemInfo, {id: problemID}, cache)
}
export function getProblemAcRank (problemID, cache = true) {
  return get(API.vjudge.problem.getProblemAcRank, {id: problemID}, cache)
}
export function getProblemByInfo (ojName, problemIdentity) {
  return postJson(API.vjudge.problem.getProblemByInfo, {}, {oj_name: ojName, problem_identity: problemIdentity})
}
export function submitCode (problemId, compilerId, sourceCode) {
  return postJson(API.vjudge.problem.submitCode, {}, {
    problem_id: problemId,
    compiler_id: compilerId,
    source_code: sourceCode
  })
}
